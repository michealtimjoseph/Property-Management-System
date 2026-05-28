<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $reports = [
            [
                'title' => 'Staff Productivity',
                'desc' => 'Export total viewings and leases managed by individual staff members.',
                'slug' => 'productivity'
            ],
            [
                'title' => 'Revenue Report',
                'desc' => 'Consolidated monthly rent collection from all active lease agreements.',
                'slug' => 'revenue-report'
            ],
            [
                'title' => 'Property Inventory',
                'desc' => 'List of all properties and their current status.',
                'slug' => 'property-inventory'
            ],
            [
                'title' => 'Inspection Logs',
                'desc' => 'Full history of property inspections and comments.',
                'slug' => 'inspection-logs'
            ],
            [
                'title' => 'Payment History',
                'desc' => 'Comprehensive log of all payment submissions from the payment table.',
                'slug' => 'payment-history'
            ]
        ];

        return view('staff.reports', compact('reports'));
    }

    public function generate(Request $request)
    {
        switch ($request->type) {
            case 'productivity': return $this->staffProductivity();
            case 'revenue-report': return $this->revenueReport();
            case 'property-inventory': return $this->propertyInventory();
            case 'inspection-logs': return $this->inspectionLogs();
            case 'payment-history': return $this->paymentHistory();
            default: abort(404);
        }
    }

    private function paymentHistory()
    {
        $data = DB::table('payment as p')
            ->join('lease_agreement as la', 'p.leaseno', '=', 'la.leaseno')
            ->join('renter as r', 'la.renterno', '=', 'r.renterno')
            ->select(
                'p.paymentid', 
                'r.firstname', 
                'r.lastname', 
                'p.amount_paid', 
                'p.payment_method', 
                'p.payment_date'
            )
            ->orderBy('p.payment_date', 'desc')
            ->get();
            
        return $this->export('Payment_History_Report', $data);
    }

    private function staffProductivity()
    {
        $data = DB::table('staff as s')
            ->select('s.staffno', 's.firstname', 's.lastname')
            ->selectRaw('(SELECT COUNT(*) FROM viewing v WHERE v.staffno = s.staffno) as total_viewings')
            ->selectRaw('(SELECT COUNT(*) FROM lease_agreement l WHERE l.staffno = s.staffno) as total_leases')
            ->get();
        return $this->export('Staff_Productivity_Report', $data);
    }

    private function revenueReport()
    {
        $data = DB::table('lease_agreement as l')
            ->join('property as p', 'l.propertyno', '=', 'p.propertyno')
            ->select('l.leaseno', 'p.street', 'l.paymentmethod', 'l.monthly_rent', 'l.startdate')
            ->get();
        return $this->export('Revenue_Collection_Report', $data);
    }

    private function propertyInventory()
    {
        $data = DB::table('property')
            ->select('propertyno', 'street', 'city', 'property_type', 'monthly_rate', 'staffno')
            ->get();
        return $this->export('Property_Inventory_Report', $data);
    }

    private function inspectionLogs()
    {
        $data = DB::table('property_inspection as i')
            ->join('property as p', 'i.propertyno', '=', 'p.propertyno')
            ->select('i.inspectionid', 'p.street', 'i.inspection_date', 'i.comment')
            ->get();
        return $this->export('Inspection_History_Report', $data);
    }

    private function export($filename, $data)
    {
        $pdf = Pdf::loadView('staff.reports.template', [
            'title' => str_replace('_', ' ', $filename),
            'data' => $data,
            'generated_at' => now()->format('Y-m-d H:i')
        ]);
        return $pdf->download($filename . '.pdf');
    }
}