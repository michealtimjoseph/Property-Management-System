<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StaffLeasesController extends Controller
{
    /**
     * Display all leases.
     * Note: Uses synchronized advance token logic to determine if paid this month.
     */
    public function index(Request $request)
    {
        $query = DB::table('lease_agreement as l')
            ->join('property as p', 'l.propertyno', '=', 'p.propertyno')
            ->join('renter as r', 'l.renterno', '=', 'r.renterno');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('l.leaseno', 'ILIKE', "%{$search}%")
                  ->orWhere('p.street', 'ILIKE', "%{$search}%")
                  ->orWhere('r.firstname', 'ILIKE', "%{$search}%")
                  ->orWhere('r.lastname', 'ILIKE', "%{$search}%");
            });
        }

        $leases = $query->select(
                'l.*', 
                'p.street', 'p.city', 
                'r.firstname as r_fname', 'r.lastname as r_lname'
            )
            ->orderBy('l.startdate', 'desc')
            ->get();

        $currentMonthLabel = Carbon::now()->format('F Y');

        // Dynamically compute payment status for the current month across all leases using the Token Engine
        foreach ($leases as $lease) {
            if ($lease->balance <= 0 || $lease->payment_status === 'PAID') {
                $lease->is_paid_this_month = true;
                continue;
            }

            $payments = DB::table('payment')
                ->where('leaseno', $lease->leaseno)
                ->orderBy('payment_date', 'asc')
                ->get();

            $advanceCreditMonths = 0;
            $coveredSpecificMonths = [];

            foreach ($payments as $payment) {
                if (!empty($payment->notes)) {
                    if (preg_match('/Advance payment packages for (\d+) month\(s\)/i', $payment->notes, $matches)) {
                        $advanceCreditMonths += (int)$matches[1];
                    } elseif (preg_match('/Rent statement for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                        $coveredSpecificMonths[] = Carbon::parse($matches[1])->format('F Y');
                    } elseif (preg_match('/Overdue rent payment for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                        $coveredSpecificMonths[] = Carbon::parse($matches[1])->format('F Y');
                    }
                }
            }

            $leaseStart = Carbon::parse($lease->startdate)->startOfMonth();
            $leaseEnd = Carbon::parse($lease->enddate)->endOfMonth();
            $periods = CarbonPeriod::create($leaseStart, '1 month', $leaseEnd);
            
            $isPaidThisMonthCalculated = false;

            foreach ($periods as $date) {
                $monthLabel = $date->format('F Y');
                $isPaidForPeriod = in_array($monthLabel, $coveredSpecificMonths);

                if (!$isPaidForPeriod && $advanceCreditMonths > 0) {
                    $isPaidForPeriod = true;
                    $advanceCreditMonths--;
                }

                if (!$isPaidForPeriod) {
                    $isPaidForPeriod = $payments->contains(function ($payment) use ($date) {
                        $payDate = Carbon::parse($payment->payment_date);
                        return $payDate->between($date->copy()->startOfMonth(), $date->copy()->endOfMonth());
                    });
                }

                if ($monthLabel === $currentMonthLabel) {
                    $isPaidThisMonthCalculated = $isPaidForPeriod;
                    break;
                }
            }

            $lease->is_paid_this_month = $isPaidThisMonthCalculated;
        }

        return view('staff.leases.index', compact('leases'));
    }

    /**
     * Store New Lease.
     * Fixed: Improved duration calculation and initial balance logic.
     */
public function store(Request $request)
    {
        $request->validate([
            'leaseno'      => 'required|unique:lease_agreement,leaseno',
            'propertyno'   => 'required|exists:property,propertyno',
            'renterno'     => 'required|exists:renter,renterno',
            'staffno'      => 'required|exists:staff,staffno',
            'monthly_rent' => 'required|numeric|min:0',
            'paymentmethod'=> 'required|string',
            'deposit'      => 'required|numeric|min:0',
            'isdepositpaid'=> 'required|in:Yes,No',
            'startdate'    => 'required|date',
            'enddate'      => 'required|date|after:startdate',
            'from_app'     => 'nullable|exists:lease_application,applicationid', // Validate hidden field
        ]);

        $start = Carbon::parse($request->startdate);
        $end = Carbon::parse($request->enddate);
        
        $duration = max(1, $start->diffInMonths($end));

        // 1. Create the Lease First
        DB::table('lease_agreement')->insert([
            'leaseno'        => trim($request->leaseno),
            'propertyno'     => $request->propertyno,
            'renterno'       => $request->renterno,
            'staffno'        => $request->staffno,
            'monthly_rent'   => $request->monthly_rent,
            'paymentmethod'  => $request->paymentmethod,
            'deposit'        => $request->deposit,
            'isdepositpaid'  => $request->isdepositpaid,
            'startdate'      => $request->startdate,
            'enddate'        => $request->enddate,
            'duration'       => (int) $duration,
            'is_overdue'     => false,
            'payment_status' => 'Pending',
            'total_paid'     => 0,
            'balance'        => $request->monthly_rent * $duration,
            'created_at'     => now()
        ]);

        // 2. Finalize the Application Status (Only happens if lease creation succeeds)
        if ($request->filled('from_app')) {
            DB::table('lease_application')->where('applicationid', $request->from_app)->update([
                'status'      => 'Approved',
                'reviewed_by' => $request->staffno,
                'reviewed_at' => now(),
                'updated_at'  => now(),
            ]);
        }

        return redirect()->route('staff.leases.index')->with('success', 'New lease agreement successfully activated.');
    }
    /**
     * Detailed View: Month-by-month payment tracking with Advance Credit Token Engine.
     */
    public function show($id)
    {
        $lease = DB::table('lease_agreement as l')
            ->join('property as p', 'l.propertyno', '=', 'p.propertyno')
            ->join('renter as r', 'l.renterno', '=', 'r.renterno')
            ->leftJoin('staff as s', 'l.staffno', '=', 's.staffno')
            ->where('l.leaseno', $id)
            ->select(
                'l.*', 
                'p.street', 'p.city', 'p.area', 'p.postcode',
                'r.firstname as r_fname', 'r.lastname as r_lname', 'r.phone as r_phone',
                's.firstname as s_fname', 's.lastname as s_lname'
            )
            ->first();

        if (!$lease) abort(404);

        $payments = DB::table('payment')
            ->where('leaseno', $id)
            ->orderBy('payment_date', 'asc')
            ->get();

        // 1. CHRONOLOGICAL CREDIT POOL RECONCILIATION
        $advanceCreditMonths = 0;
        $coveredSpecificMonths = [];
        $paymentMap = []; // Maps unique month text labels to an explicit payment object for view rendering

        foreach ($payments as $payment) {
            if (!empty($payment->notes)) {
                if (preg_match('/Advance payment packages for (\d+) month\(s\)/i', $payment->notes, $matches)) {
                    $monthsCount = (int)$matches[1];
                    // Keep track of how many credits this specific transaction contains
                    for ($i = 0; $i < $monthsCount; $i++) {
                        $paymentMap['CREDIT_INDEX_' . ($advanceCreditMonths)] = $payment;
                        $advanceCreditMonths++;
                    }
                } elseif (preg_match('/Rent statement for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                    $label = Carbon::parse($matches[1])->format('F Y');
                    $coveredSpecificMonths[] = $label;
                    $paymentMap[$label] = $payment;
                } elseif (preg_match('/Overdue rent payment for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                    $label = Carbon::parse($matches[1])->format('F Y');
                    $coveredSpecificMonths[] = $label;
                    $paymentMap[$label] = $payment;
                }
            }
        }

        $start = Carbon::parse($lease->startdate)->startOfMonth();
        $end = Carbon::parse($lease->enddate)->startOfMonth();
        $periods = CarbonPeriod::create($start, '1 month', $end);

        $schedule = [];
        $consumedCreditsCount = 0;

        // 2. RUN SEQUENCER LOOKAHEAD LOOP
        foreach ($periods as $date) {
            $monthLabel = $date->format('F Y');
            $matchingPayment = null;
            $isPaid = false;

            // Priority A: Target by explicit label text
            if (in_array($monthLabel, $coveredSpecificMonths)) {
                $isPaid = true;
                $matchingPayment = $paymentMap[$monthLabel] ?? null;
            }

            // Priority B: Use generic pool advance credit tokens
            if (!$isPaid && $advanceCreditMonths > 0) {
                $isPaid = true;
                $matchingPayment = $paymentMap['CREDIT_INDEX_' . $consumedCreditsCount] ?? null;
                $consumedCreditsCount++;
                $advanceCreditMonths--;
            }

            // Priority C: Natural fallback matching system date range
            if (!$isPaid) {
                $matchingPayment = $payments->first(function ($payment) use ($date) {
                    $payDate = Carbon::parse($payment->payment_date);
                    $periodStart = $date->copy()->startOfMonth()->startOfDay();
                    $periodEnd = $date->copy()->endOfMonth()->endOfDay();
                    return $payDate->between($periodStart, $periodEnd);
                });
                $isPaid = !is_null($matchingPayment);
            }

            $schedule[] = [
                'month'   => $monthLabel,
                'is_paid' => $isPaid,
                'payment' => $matchingPayment,
                'due_date'=> $date->copy()->day(1)->format('Y-m-d')
            ];
        }

        return view('staff.leases.show', compact('lease', 'schedule'));
    }

    public function create()
    {
        // Generate a random lease number starting with 'L' followed by 4-6 random digits.
        // We use a simple while loop to ensure it doesn't already exist in the database.
        do {
            $generatedLeaseNo = 'L' . rand(1000, 99999);
            $exists = DB::table('lease_agreement')->where('leaseno', $generatedLeaseNo)->exists();
        } while ($exists);

        // Fetch unleased properties
        $properties = DB::table('property')
            ->whereNotIn('propertyno', function($query) {
                $query->select('propertyno')
                      ->from('lease_agreement')
                      ->where('enddate', '>=', Carbon::now());
            })
            ->orderBy('street', 'asc')
            ->get();

        // Fetch prospective renters
        $renters = DB::table('renter')
            ->orderBy('lastname', 'asc')
            ->get();

        // Fetch staff members capable of managing the agreement
        $staffMembers = DB::table('staff')
            ->orderBy('lastname', 'asc')
            ->get();

        return view('staff.leases.create', compact('properties', 'renters', 'staffMembers', 'generatedLeaseNo'));
    }
    /**
     * Staff-Initiated Payment Recording.
     * Modified to optionally support manual text tags if applicable.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'leaseno'        => 'required|exists:lease_agreement,leaseno',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:255'
        ]);

        $paymentId = 'PAY-' . strtoupper(uniqid());
        $notes = $request->notes ?? 'Manual staff entry';

        DB::table('payment')->insert([
            'paymentid'      => $paymentId,
            'leaseno'        => $request->leaseno,
            'payment_date'   => $request->payment_date,
            'amount_paid'    => $request->amount,
            'payment_method' => $request->payment_method,
            'notes'          => $notes,
        ]);

        return back()->with('success', "Payment {$paymentId} has been recorded.");
    }
}