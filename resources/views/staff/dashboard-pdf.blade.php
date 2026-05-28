<!DOCTYPE html>
<html>
<head>
    <title>DreamHome Report - {{ $staff->staffno }}</title>
    <style>
        @page { margin: 40px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #000; line-height: 1.4; }
        
        /* Header Info */
        .report-header { border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .report-title { font-size: 18pt; font-weight: bold; }
        .report-meta { font-size: 10pt; margin-top: 5px; }
        
        /* Summary Section */
        .summary-table { width: 100%; margin-bottom: 30px; border: 1px solid #000; }
        .summary-table td { padding: 10px; border: 1px solid #000; width: 25%; }
        .label { font-size: 8pt; font-weight: bold; text-transform: uppercase; display: block; color: #444; }
        .value { font-size: 14pt; font-weight: bold; }

        /* Data Tables */
        .section-header { font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; background: #eee; padding: 5px; border: 1px solid #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { border: 1px solid #000; padding: 8px; font-size: 9pt; background-color: #f2f2f2; text-align: left; }
        td { border: 1px solid #000; padding: 8px; font-size: 9pt; vertical-align: top; }
        
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 8pt; text-align: center; border-top: 1px solid #ccc; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="report-title">DREAMHOME {{ $isRegular ? 'WORKSPACE' : 'SYSTEM' }} REPORT</div>
        <div class="report-meta">
            <strong>Staff Member:</strong> {{ $staff->firstname }} {{ $staff->lastname }} ({{ $staff->staffno }})<br>
            <strong>Position:</strong> {{ ucfirst($staff->position) }}<br>
            <strong>Date Generated:</strong> {{ $generated_at }}
        </div>
    </div>

    <div class="section-header">Executive Summary Metrics</div>
    <table class="summary-table">
        <tr>
            <td>
                <span class="label">Total Properties</span>
                <span class="value">{{ $totalProperties }}</span>
            </td>
            <td>
                <span class="label">{{ $isRegular ? 'Assigned Viewings' : 'Total System Renters' }}</span>
                <span class="value">{{ $isRegular ? $totalViewings : $totalRenters }}</span>
            </td>
            <td>
                <span class="label">{{ $isRegular ? 'Active Contracts' : 'Total System Revenue' }}</span>
                <span class="value">{{ $isRegular ? $totalLeases : '₱' . number_format($totalRevenue, 2) }}</span>
            </td>
            <td>
                <span class="label">{{ $isRegular ? 'Pending Reviews' : 'Action Required' }}</span>
                <span class="value">{{ $isRegular ? $pendingInspections : $pendingActions }}</span>
            </td>
        </tr>
    </table>

    @if($isRegular)
        {{-- PROPERTIES SECTION --}}
        <div class="section-header">Assigned Property Portfolio</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%">Property #</th>
                    <th style="width: 40%">Address</th>
                    <th style="width: 15%">Type</th>
                    <th style="width: 15%">Area</th>
                    <th style="width: 15%" class="text-right">Monthly Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignedProperties as $prop)
                <tr>
                    <td>{{ $prop->propertyno }}</td>
                    <td>{{ $prop->street }}, {{ $prop->city }}</td>
                    <td>{{ $prop->property_type }}</td>
                    <td>{{ $prop->area }}</td>
                    <td class="text-right">₱{{ number_format($prop->monthly_rate, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- LEASES SECTION --}}
        <div class="section-header">Managed Lease Agreements</div>
        <table>
            <thead>
                <tr>
                    <th>Lease #</th>
                    <th>Renter Name</th>
                    <th>Property</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th class="text-right">Rent</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignedLeases as $lease)
                <tr>
                    <td>{{ $lease->leaseno }}</td>
                    <td>{{ $lease->r_fname }} {{ $lease->r_lname }}</td>
                    <td>{{ $lease->street }}</td>
                    <td>{{ \Carbon\Carbon::parse($lease->startdate)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($lease->enddate)->format('Y-m-d') }}</td>
                    <td class="text-right">₱{{ number_format($lease->monthly_rent, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- INSPECTIONS SECTION --}}
        <div class="section-header">Inspection & Review Schedule</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Scheduled Date</th>
                    <th>Property Address</th>
                    <th>City</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignedInspections as $ins)
                <tr>
                    <td>{{ $ins->inspectionid }}</td>
                    <td>{{ \Carbon\Carbon::parse($ins->inspection_date)->format('F d, Y') }}</td>
                    <td>{{ $ins->street }}</td>
                    <td>{{ $ins->city }}</td>
                    <td>{{ $ins->status ?? 'Pending' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        {{-- MANAGER VIEW: GLOBAL DATA TABLES --}}
        <div class="section-header">System Inventory Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Property Type</th>
                    <th class="text-right">Total Units</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryMix as $mix)
                <tr>
                    <td>{{ $mix->property_type }}</td>
                    <td class="text-right">{{ $mix->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <p style="font-size: 9pt; color: #666 italic;">* Management view provides high-level system totals. For specific property or staff details, please refer to individual operational reports.</p>
    @endif

    <div class="footer">
        DreamHome Management System - Internal Confidential Report - Generated at {{ $generated_at }}
    </div>
</body>
</html>