<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LeasesController extends Controller
{
    // ===== SHARED DATA FETCHING =====

    private function fetchLease($renterno)
    {
        return DB::table('lease_agreement as la')
            ->join('property as p', 'la.propertyno', '=', 'p.propertyno')
            ->join('staff as s',    'la.staffno',    '=', 's.staffno')
            ->join('renter as r',   'la.renterno',   '=', 'r.renterno')
            ->where('la.renterno', $renterno)
            ->select(
                'la.leaseno', 'la.propertyno', 'la.renterno', 'la.staffno',
                'la.monthly_rent', 'la.paymentmethod', 'la.deposit', 'la.isdepositpaid',
                'la.startdate', 'la.enddate', 'la.duration', 'la.total_paid',
                'la.balance', 'la.payment_status', 'la.is_overdue',
                'p.street', 'p.area', 'p.city', 'p.postcode', 'p.property_type', 'p.no_of_rooms',
                DB::raw("s.firstname || ' ' || s.lastname as staff_name"),
                DB::raw("r.firstname || ' ' || r.lastname as renter_name")
            )
            ->orderByDesc('la.startdate')
            ->first();
    }

    private function fetchPayments($leaseno)
    {
        return DB::table('payment')
            ->where('leaseno', $leaseno)
            ->orderByDesc('payment_date')
            ->orderByDesc('paymentid')
            ->get();
    }

    private function calcProgress($lease)
    {
        $start   = strtotime($lease->startdate);
        $end     = strtotime($lease->enddate);
        $today   = time();
        $total   = $end - $start;
        $elapsed = max(0, min($today - $start, $total));
        return $total > 0 ? round(($elapsed / $total) * 100) : 0;
    }

    // ===== MAIN DASHBOARD =====

    public function index()
    {
        $user = Auth::user();

        if (!$user->renterno) {
            return view('leases', [
                'lease'            => null,
                'payments'         => collect(),
                'progress'         => 0,
                'branch'           => DB::table('branch')->where('branchno', 'B001')->first(),
                'next_due_date'    => 'N/A',
                'overdue_months'   => [],
                'hasPaidThisMonth' => false,
                'unpaid_months'    => [],
                'next_unpaid_month'=> Carbon::now()->format('F Y')
            ]);
        }

        $lease    = $this->fetchLease($user->renterno);
        $payments = $lease ? $this->fetchPayments($lease->leaseno) : collect();
        $progress = $lease ? $this->calcProgress($lease) : 0;
        $branch   = DB::table('branch')->where('branchno', 'B001')->first();

        $next_due_date = 'N/A';
        $next_unpaid_month = Carbon::now()->format('F Y');
        $overdue_months = [];
        $unpaid_months = [];
        $hasPaidThisMonth = false;

        if ($lease) {
            if ($lease->balance <= 0 || $lease->payment_status === 'PAID') {
                $next_due_date = 'Fully Paid';
                $next_unpaid_month = 'None';
            } else {
                $leaseStart = Carbon::parse($lease->startdate)->startOfMonth()->startOfDay();
                $leaseEnd   = Carbon::parse($lease->enddate)->endOfMonth()->endOfDay();
                $today      = Carbon::now()->startOfDay();
                $currentMonthLabel = Carbon::now()->format('F Y');

                $coveredSpecificMonths = [];
                $consumedPaymentIds = [];

                foreach ($payments as $payment) {
                    if (!empty($payment->notes)) {
                        if (preg_match('/Advance payment packages for:\s*(.+)/i', $payment->notes, $matches)) {
                            $parsedMonths = explode(',', $matches[1]);
                            foreach ($parsedMonths as $rawMonth) {
                                $cleanMonth = trim(explode('|', $rawMonth)[0]);
                                try {
                                    $coveredSpecificMonths[] = Carbon::parse($cleanMonth)->format('F Y');
                                } catch (\Exception $e) { /* Skip malformed tokens */ }
                            }
                            $consumedPaymentIds[] = $payment->paymentid;
                        } 
                        elseif (preg_match('/Rent statement for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                            $coveredSpecificMonths[] = Carbon::parse($matches[1])->format('F Y');
                            $consumedPaymentIds[] = $payment->paymentid;
                        }
                        elseif (preg_match('/Overdue rent payment for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                            $coveredSpecificMonths[] = Carbon::parse($matches[1])->format('F Y');
                            $consumedPaymentIds[] = $payment->paymentid;
                        }
                    }
                }

                $currentPeriod = $leaseStart->copy();
                $firstUnpaidDate = null;

                while ($currentPeriod->isBefore($leaseEnd)) {
                    $monthLabel = $currentPeriod->format('F Y');
                    $isPaidForPeriod = in_array($monthLabel, $coveredSpecificMonths);

                    if (!$isPaidForPeriod) {
                        $isPaidForPeriod = $payments->contains(function ($payment) use ($currentPeriod, $consumedPaymentIds) {
                            if (in_array($payment->paymentid, $consumedPaymentIds)) return false;
                            $payDate     = Carbon::parse($payment->payment_date);
                            $periodStart = $currentPeriod->copy()->startOfMonth()->startOfDay();
                            $periodEnd   = $currentPeriod->copy()->endOfMonth()->endOfDay();
                            return $payDate->between($periodStart, $periodEnd);
                        });
                    }

                    if ($monthLabel === $currentMonthLabel && $isPaidForPeriod) {
                        $hasPaidThisMonth = true;
                    }

                    if (!$isPaidForPeriod) {
                    $unpaid_months[] = $monthLabel;

                    if (is_null($firstUnpaidDate)) {
                        $firstUnpaidDate = $currentPeriod->copy()->startOfMonth();
                    }

                    // FIXED: was ->endOfMonth()->isBefore($today)
                    if ($currentPeriod->copy()->startOfMonth()->lte($today)) {
                        $overdue_months[] = $monthLabel;
                    }
                }

                    $currentPeriod->addMonth();
                }

                if ($firstUnpaidDate) {
                    $next_due_date = $firstUnpaidDate->format('M d, Y');
                    $next_unpaid_month = $firstUnpaidDate->format('F Y');
                } else {
                    $next_due_date = 'Term Completed';
                    $next_unpaid_month = 'None';
                }
            }
        }

        return view('leases', compact('lease', 'payments', 'progress', 'branch', 'next_due_date', 'overdue_months', 'hasPaidThisMonth', 'unpaid_months', 'next_unpaid_month'));
    }

    // ===== ACTIONS & TRANSACTIONS =====

    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_type'   => 'required|in:this_month,advance',
            'months'         => 'required_if:payment_type,advance|integer|min:1|max:12',
            'payment_method' => 'required|string',
            'reference_no'   => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:300',
        ]);

        $user  = Auth::user();
        $lease = $this->fetchLease($user->renterno);

        if (!$lease || $lease->balance <= 0) {
            return back()->with('error', 'Payment cannot be processed for this lease.');
        }

        // Reconstruct true unpaid chronological tracking list sequence
        $payments = $this->fetchPayments($lease->leaseno);
        $leaseStart = Carbon::parse($lease->startdate)->startOfMonth()->startOfDay();
        $leaseEnd   = Carbon::parse($lease->enddate)->endOfMonth()->endOfDay();
        
        $coveredSpecificMonths = [];
        $consumedPaymentIds = [];

        foreach ($payments as $payment) {
            if (!empty($payment->notes)) {
                if (preg_match('/Advance payment packages for:\s*(.+)/i', $payment->notes, $matches)) {
                    $parsedMonths = explode(',', $matches[1]);
                    foreach ($parsedMonths as $rawMonth) {
                        $cleanMonth = trim(explode('|', $rawMonth)[0]);
                        try {
                            $coveredSpecificMonths[] = Carbon::parse($cleanMonth)->format('F Y');
                        } catch (\Exception $e) {}
                    }
                    $consumedPaymentIds[] = $payment->paymentid;
                } 
                elseif (preg_match('/Rent statement for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                    $coveredSpecificMonths[] = Carbon::parse($matches[1])->format('F Y');
                    $consumedPaymentIds[] = $payment->paymentid;
                }
                elseif (preg_match('/Overdue rent payment for ([a-zA-Z]+ \d{4})/i', $payment->notes, $matches)) {
                    $coveredSpecificMonths[] = Carbon::parse($matches[1])->format('F Y');
                    $consumedPaymentIds[] = $payment->paymentid;
                }
            }
        }

        $unpaidMonthsList = [];
        $currentPeriod = $leaseStart->copy();

        while ($currentPeriod->isBefore($leaseEnd)) {
            $monthLabel = $currentPeriod->format('F Y');
            $isPaidForPeriod = in_array($monthLabel, $coveredSpecificMonths);

            if (!$isPaidForPeriod) {
                $isPaidForPeriod = $payments->contains(function ($payment) use ($currentPeriod, $consumedPaymentIds) {
                    if (in_array($payment->paymentid, $consumedPaymentIds)) return false;
                    $payDate     = Carbon::parse($payment->payment_date);
                    $periodStart = $currentPeriod->copy()->startOfMonth()->startOfDay();
                    $periodEnd   = $currentPeriod->copy()->endOfMonth()->endOfDay();
                    return $payDate->between($periodStart, $periodEnd);
                });
            }

            if (!$isPaidForPeriod) {
                $unpaidMonthsList[] = $monthLabel;
            }

            $currentPeriod->addMonth();
        }

        if (empty($unpaidMonthsList)) {
            return back()->with('error', 'There are no outstanding unpaid billing cycles remaining.');
        }

        // Determine slice parameters safely
        $requestedCount = $request->payment_type === 'advance' ? (int) $request->months : 1;

        if ($requestedCount > count($unpaidMonthsList)) {
            return back()->with('error', 'The requested number of billing cycles exceeds your remaining balance timeline.');
        }

        $targetedCycles = array_slice($unpaidMonthsList, 0, $requestedCount);
        $cyclesDescriptionString = implode(', ', $targetedCycles);

        // Derive financial totals dynamically based on targeted cycle length
        $amountPaid = min($lease->monthly_rent * $requestedCount, $lease->balance);
        $paymentId  = 'PAY' . strtoupper(uniqid());
        
        // Match string formats with regex configurations
        if ($request->payment_type === 'this_month') {
            // Check if the targeted index is a backlogged overdue period to retain historical parsing accuracy
            $isTargetOverdue = Carbon::parse($targetedCycles[0])->endOfMonth()->isBefore(Carbon::now()->startOfDay());
            $notes = $isTargetOverdue 
                ? 'Overdue rent payment for ' . $cyclesDescriptionString
                : 'Rent statement for ' . $cyclesDescriptionString;
        } else {
            $notes = 'Advance payment packages for: ' . $cyclesDescriptionString;
        }
        
        if ($request->reference_no && $request->payment_method !== 'Cash') {
            $notes .= ' | Ref: ' . $request->reference_no;
        }

        // Wrap procedure call in an explicit atomic database transaction block
        DB::beginTransaction();
        try {
            DB::statement("CALL insert_payment(
                CAST(:paymentid AS TEXT),
                CAST(:leaseno AS TEXT),
                CAST(:amount_paid AS NUMERIC),
                CAST(:payment_method AS TEXT),
                CAST(:notes AS TEXT)
            )", [
                'paymentid'      => $paymentId,
                'leaseno'        => $lease->leaseno,
                'amount_paid'    => $amountPaid,
                'payment_method' => $request->payment_method,
                'notes'          => $notes,
            ]);

            DB::commit();
            return back()->with('payment_success', "Payment of ₱" . number_format($amountPaid, 2) . " covering [" . $cyclesDescriptionString . "] has been successfully recorded.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Database Transaction Failed: ' . $e->getMessage());
        }
    }

    // ===== ADDITIONAL ACTIONS =====

    public function downloadPdf()
    {
        $user = Auth::user();
        if (!$user->renterno) return redirect()->route('leases');

        $lease    = $this->fetchLease($user->renterno);
        $payments = $lease ? $this->fetchPayments($lease->leaseno) : collect();
        $progress = $lease ? $this->calcProgress($lease) : 0;

        $pdf = Pdf::loadView('lease-pdf', compact('lease', 'payments', 'progress'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Lease_Agreement_' . $lease->leaseno . '.pdf');
    }

    public function requestRenewal(Request $request)
    {
        $request->validate(['reason' => 'required|string', 'message' => 'nullable|string|max:500']);
        $user = Auth::user();
        $lease = $this->fetchLease($user->renterno);

        DB::table('renewal_request')->insert([
            'requestid'  => 'RR' . time(),
            'leaseno'    => $lease->leaseno,
            'renterno'   => $user->renterno,
            'reason'     => $request->reason,
            'message'    => $request->message,
            'status'     => 'Pending',
            'created_at' => now(),
        ]);

        return back()->with('renewal_success', 'Your renewal request is now pending review.');
    }

    public function contactSupport(Request $request)
    {
        $request->validate(['issue_type' => 'required|string', 'message' => 'required|string|max:500']);
        $user = Auth::user();
        $lease = $this->fetchLease($user->renterno);

        DB::table('support_ticket')->insert([
            'ticketid'   => 'ST' . time(),
            'renterno'   => $user->renterno,
            'leaseno'    => $lease?->leaseno,
            'issue_type' => $request->issue_type,
            'message'    => $request->message,
            'status'     => 'Open',
            'created_at' => now(),
        ]);

        return back()->with('support_success', 'Support ticket submitted. We will contact you shortly.');
    }
}