<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Display the dynamic staff dashboard.
     */
    public function index()
    {
        $staff = Auth::guard('staff')->user();
        $data = $this->getDashboardData($staff);
        return view('staff.dashboard', $data);
    }

    /**
     * Shared logic to fetch dashboard data.
     * Regular staff see only their assignments; Managers see system-wide totals.
     */
/**
     * Shared logic to fetch dashboard data.
     * Regular staff see only their assignments; Managers see system-wide totals.
     */
private function getDashboardData($staff)
{
    $isRegular = $staff && strtolower($staff->position) === 'regular';
    $currentMonthStart = now()->startOfMonth();
    $currentMonthEnd = now()->endOfMonth();

    // Helper to get last 7 days (including today)
    $last7Days = collect(range(6, 0))->map(function ($i) {
        return now()->subDays($i)->format('Y-m-d');
    });

    if ($isRegular) {
        // Real Data: Viewing activity count per day for this staff
        $activityData = DB::table('viewing')
            ->where('staffno', $staff->staffno)
            ->where('view_date', '>=', now()->subDays(6))
            ->select(DB::raw('DATE(view_date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartData = $last7Days->map(fn($date) => $activityData[$date] ?? 0)->values()->toArray();
                    // Calculate Total Revenue from payments linked to this staff member's leases
            $myRevenue = DB::table('payment as pay')
                ->join('lease_agreement as l', 'pay.leaseno', '=', 'l.leaseno')
                ->where('l.staffno', $staff->staffno)
                ->sum('pay.amount_paid');

            // Count leases managed by this staff that HAVE NOT paid this month
            $unpaidCount = DB::table('lease_agreement as l')
                ->where('l.staffno', $staff->staffno)
                ->whereNotExists(function ($query) use ($currentMonthStart, $currentMonthEnd) {
                    $query->select(DB::raw(1))
                        ->from('payment as pay')
                        ->whereRaw('pay.leaseno = l.leaseno')
                        ->whereBetween('pay.payment_date', [$currentMonthStart, $currentMonthEnd]);
                })
                ->count();

            return [
                'isRegular'         => true,
                'totalProperties'   => DB::table('property')->where('staffno', $staff->staffno)->count(),
                'totalViewings'     => DB::table('viewing')->where('staffno', $staff->staffno)->count(),
                'totalLeases'       => DB::table('lease_agreement')->where('staffno', $staff->staffno)->count(),
                'totalRevenue'      => $myRevenue,
                'unpaidLeases'      => $unpaidCount,
                'pendingInspections'=> DB::table('property_inspection')
                                    ->where('staffno', $staff->staffno)
                                    ->where(function($query) {
                                        $query->where('status', '!=', 'Completed')
                                              ->orWhereNull('status');
                                    })
                                    ->count(),
                
                'assignedProperties'=> DB::table('property')
                                        ->where('staffno', $staff->staffno)
                                        ->select('propertyno', 'street', 'area', 'city', 'property_type', 'monthly_rate', 'main_image')
                                        ->get(),
                                        
                'assignedViewings'  => DB::table('viewing as v')
                                        ->join('property as p', 'v.propertyno', '=', 'p.propertyno')
                                        ->join('renter as r', 'v.renterno', '=', 'r.renterno')
                                        ->where('v.staffno', $staff->staffno)
                                        ->where('v.status', '!=', 'Completed')
                                        ->select('v.*', 'p.street', 'p.city', 'r.firstname as r_fname', 'r.lastname as r_lname')
                                        ->orderBy('v.view_date', 'asc')->get(),

                'assignedInspections'=> DB::table('property_inspection as i')
                                    ->join('property as p', 'i.propertyno', '=', 'p.propertyno')
                                    ->where('i.staffno', $staff->staffno)
                                    ->where(function($query) {
                                        $query->where('i.status', '!=', 'Completed')
                                              ->orWhereNull('i.status');
                                    })
                                    ->select('i.*', 'p.street', 'p.city')
                                    ->orderBy('i.inspection_date', 'asc')
                                    ->get(),

                'assignedLeases'    => DB::table('lease_agreement as l')
                                        ->join('property as p', 'l.propertyno', '=', 'p.propertyno')
                                        ->join('renter as r', 'l.renterno', '=', 'r.renterno')
                                        ->where('l.staffno', $staff->staffno)
                                        ->select('l.*', 'p.street', 'p.city', 'r.firstname as r_fname', 'r.lastname as r_lname')
                                        ->orderBy('l.enddate', 'asc')->get(),

                'inventoryMix'      => DB::table('property')->where('staffno', $staff->staffno)
                                        ->select('property_type', DB::raw('count(*) as total'))
                                        ->groupBy('property_type')->get(),
                'chartLabel'        => "Weekly Viewings",
                'chartData'         => $chartData
                
            ];
        }

// MANAGER VIEW DATA (Global Totals)
$totalRevenue = DB::table('payment')->sum('amount_paid');

// Fetch revenue grouped by date for the last 7 days
$revenueData = DB::table('payment')
    ->where('payment_date', '>=', now()->subDays(6))
    ->select(DB::raw('DATE(payment_date) as date'), DB::raw('sum(amount_paid) as total'))
    ->groupBy('date')
    ->pluck('total', 'date');

// FIX: Convert the collection to a flat array indexed by the last 7 days
$chartData = $last7Days->map(fn($date) => (float)($revenueData[$date] ?? 0))->values()->toArray();

// Global count of leases with no payment record for this month
$systemUnpaid = DB::table('lease_agreement as l')
    ->whereNotExists(function ($query) use ($currentMonthStart, $currentMonthEnd) {
        $query->select(DB::raw(1))
            ->from('payment as pay')
            ->whereRaw('pay.leaseno = l.leaseno')
            ->whereBetween('pay.payment_date', [$currentMonthStart, $currentMonthEnd]);
    })
    ->count();

return [
    'isRegular'         => false,
    'totalProperties'   => Properties::count(),
    'totalRenters'      => DB::table('renter')->count(),
    'totalRevenue'      => $totalRevenue,
    'unpaidLeases'      => $systemUnpaid,
    'inventoryMix'      => Properties::select('property_type', DB::raw('count(*) as total'))
                            ->groupBy('property_type')->get(),
    'chartLabel'        => "System Revenue",
    'chartData'         => $chartData // Now this is a flat array like [100, 200, 0, 50, ...]
];
    }
    /**
     * Mark a viewing as completed with staff feedback.
     */
    public function updateViewingFeedback(Request $request)
    {
        $request->validate([
            'viewingid' => 'required|exists:viewing,viewingid',
            'comment'   => 'required|string|max:500',
        ]);

        DB::table('viewing')
            ->where('viewingid', $request->viewingid)
            ->update([
                'comment' => $request->comment,
                'status'  => 'Completed' // Triggers disappearance from dashboard
            ]);

        return back()->with('success', 'Viewing feedback recorded and task finalized.');
    }

    public function completeInspection(Request $request, $id)
{
    // Validate the incoming comment
    $request->validate([
        'comment' => 'required|string|max:1000',
    ]);

    // Update the database using the ID passed from the route
    DB::table('property_inspection')
        ->where('inspectionid', $id)
        ->update([
            'evaluation' => $request->comment,
            'status'     => 'Completed' // This allows the dashboard to sync and hide the task
        ]);

    return back()->with('success', 'Inspection report finalized and archived successfully.');
}

    /**
     * Generate a detailed multi-section PDF report.
     */
    public function downloadReport()
    {
        $staff = Auth::guard('staff')->user();
        if (!$staff) return redirect()->route('staff.login');

        $data = $this->getDashboardData($staff);
        $data['staff'] = $staff;
        $data['generated_at'] = now()->format('F d, Y h:i A');

        $pdf = Pdf::loadView('staff.dashboard-pdf', $data);
        $filename = ($data['isRegular'] ? 'Operational_Report_' : 'Management_Report_') . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}