<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lease Agreement — {{ $lease->leaseno }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        .header { background: #853953; color: white; padding: 28px 36px; margin-bottom: 0; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .logo-text { font-size: 22px; font-weight: bold; letter-spacing: -0.5px; }
        .logo-text span { color: #f9a8c9; }
        .header-meta { text-align: right; font-size: 10px; color: rgba(255,255,255,0.7); }
        .doc-title { font-size: 11px; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px; }
        .doc-no { font-size: 20px; font-weight: bold; color: white; }
        .status-badge { display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 6px; border: 1px solid rgba(255,255,255,0.3); }

        .content { padding: 28px 36px; }

        .section { margin-bottom: 22px; }
        .section-label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #853953; border-left: 3px solid #853953; padding-left: 8px; margin-bottom: 12px; }

        .grid-2 { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
        .grid-3 { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
        .grid-row { display: table-row; }
        .grid-cell { display: table-cell; background: #F3F4F6; border-radius: 8px; padding: 10px 12px; border: 1px solid #e5e7eb; width: 50%; }
        .grid-cell-third { display: table-cell; background: #F3F4F6; border-radius: 8px; padding: 10px 12px; border: 1px solid #e5e7eb; width: 33%; }
        .cell-label { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 4px; }
        .cell-value { font-size: 11px; font-weight: bold; color: #111827; }
        .cell-value-lg { font-size: 14px; font-weight: bold; color: #853953; }
        .cell-sub { font-size: 8px; color: #9ca3af; margin-top: 2px; }

        .divider { border: none; border-top: 1px dashed #e5e7eb; margin: 18px 0; }

        .period-row { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
        .period-cell-start { display: table-cell; background: #F3F4F6; border-radius: 8px; padding: 12px 16px; width: 50%; border: 1px solid #e5e7eb; }
        .period-cell-end { display: table-cell; background: #853953; border-radius: 8px; padding: 12px 16px; width: 50%; }
        .period-label { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .period-label-dark { color: #9ca3af; }
        .period-label-light { color: rgba(255,255,255,0.6); }
        .period-value { font-size: 12px; font-weight: bold; }
        .period-value-dark { color: #111827; }
        .period-value-light { color: white; }

        .paid-badge { display: inline-block; background: #d1fae5; color: #059669; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: bold; }
        .unpaid-badge { display: inline-block; background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: bold; }

        .payment-row { display: table; width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 6px; }
        .payment-cell { display: table-cell; padding: 8px 10px; background: #f0fdf4; border: 1px solid #d1fae5; border-radius: 6px; font-size: 10px; }

        .footer { margin-top: 30px; padding-top: 16px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
        .footer-brand { font-weight: bold; color: #853953; }

        .signature-area { display: table; width: 100%; margin-top: 24px; }
        .sig-cell { display: table-cell; width: 50%; padding: 0 12px; text-align: center; }
        .sig-line { border-top: 1px solid #374151; margin-top: 36px; padding-top: 6px; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-top">
            <div>
                <div class="logo-text">Dream<span>Home</span></div>
                <div style="font-size:9px; color:rgba(255,255,255,0.6); margin-top:2px;">CDO Branch — B001 · Property Management</div>
            </div>
            <div class="header-meta">
                <div style="font-size:9px; color:rgba(255,255,255,0.5); margin-bottom:6px;">Generated: {{ now()->format('F d, Y') }}</div>
                <div class="doc-title">Lease Agreement</div>
                <div class="doc-no">No. {{ $lease->leaseno }}</div>
                <div class="status-badge">
                    @if($lease->payment_status === 'PAID') ✓ Fully Paid
                    @elseif($lease->is_overdue) ⚠ Overdue
                    @else ● Active @endif
                </div>
            </div>
        </div>
        <div style="font-size:9px; color:rgba(255,255,255,0.5); margin-top:8px;">
            {{ $lease->duration }} {{ $lease->duration == 1 ? 'month' : 'months' }} contract · Renter: {{ $lease->renter_name }}
        </div>
    </div>

    <div class="content">

        {{-- PROPERTY DETAILS --}}
        <div class="section">
            <div class="section-label">Property Details</div>
            <table class="grid-2" cellspacing="8">
                <tr>
                    <td class="grid-cell">
                        <div class="cell-label">Property No.</div>
                        <div class="cell-value">{{ $lease->propertyno }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Property Type</div>
                        <div class="cell-value">{{ $lease->property_type }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="grid-cell" colspan="2" style="width:100%;">
                        <div class="cell-label">Full Address</div>
                        <div class="cell-value">{{ $lease->street }}, {{ $lease->area }}, {{ $lease->city }} {{ $lease->postcode }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="grid-cell">
                        <div class="cell-label">No. of Rooms</div>
                        <div class="cell-value">{{ $lease->no_of_rooms }} Rooms</div>
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Renter Name</div>
                        <div class="cell-value">{{ $lease->renter_name }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <hr class="divider">

        {{-- LEASE TERMS --}}
        <div class="section">
            <div class="section-label">Lease Terms</div>
            <table width="100%" cellspacing="8">
                <tr>
                    <td class="grid-cell">
                        <div class="cell-label">Monthly Rent</div>
                        <div class="cell-value-lg">₱{{ number_format($lease->monthly_rent, 2) }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Method of Payment</div>
                        <div class="cell-value">{{ $lease->paymentmethod }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Lease Duration</div>
                        <div class="cell-value">{{ $lease->duration }} {{ $lease->duration == 1 ? 'month' : 'months' }}</div>
                        <div class="cell-sub">Min 3 months · Max 1 year</div>
                    </td>
                </tr>
                <tr>
                    <td class="grid-cell">
                        <div class="cell-label">Rental Deposit</div>
                        <div class="cell-value">₱{{ number_format($lease->deposit, 2) }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Deposit Paid</div>
                        @if($lease->isdepositpaid)
                            <span class="paid-badge">✓ Yes — Paid</span>
                        @else
                            <span class="unpaid-badge">✗ Not Paid</span>
                        @endif
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Arranged by Staff</div>
                        <div class="cell-value">{{ $lease->staff_name }}</div>
                        <div class="cell-sub">Staff No. {{ $lease->staffno }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <hr class="divider">

        {{-- CONTRACT PERIOD --}}
        <div class="section">
            <div class="section-label">Contract Period</div>
            <table class="period-row" cellspacing="8">
                <tr>
                    <td class="period-cell-start">
                        <div class="period-label period-label-dark">Rent Start Date</div>
                        <div class="period-value period-value-dark">{{ \Carbon\Carbon::parse($lease->startdate)->format('F d, Y') }}</div>
                    </td>
                    <td class="period-cell-end">
                        <div class="period-label period-label-light">Rent End Date</div>
                        <div class="period-value period-value-light">{{ \Carbon\Carbon::parse($lease->enddate)->format('F d, Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <hr class="divider">

        {{-- BILLING SUMMARY --}}
        <div class="section">
            <div class="section-label">Billing Summary</div>
            <table width="100%" cellspacing="8">
                <tr>
                    <td class="grid-cell">
                        <div class="cell-label">Total Paid</div>
                        <div class="cell-value" style="color:#059669;">₱{{ number_format($lease->total_paid, 2) }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Remaining Balance</div>
                        <div class="cell-value {{ $lease->balance == 0 ? '' : '' }}" style="color:{{ $lease->balance == 0 ? '#059669' : '#853953' }};">
                            ₱{{ number_format($lease->balance, 2) }}
                        </div>
                    </td>
                    <td class="grid-cell">
                        <div class="cell-label">Payment Status</div>
                        @if($lease->payment_status === 'PAID')
                            <span class="paid-badge">✓ Fully Paid</span>
                        @elseif($lease->is_overdue)
                            <span class="unpaid-badge">⚠ Overdue</span>
                        @else
                            <span style="color:#853953; font-weight:bold; font-size:10px;">● Active</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- PAYMENT HISTORY --}}
        @if($payments->isNotEmpty())
        <hr class="divider">
        <div class="section">
            <div class="section-label">Payment History</div>
            @foreach($payments as $payment)
            <table width="100%" style="margin-bottom:5px;">
                <tr>
                    <td style="padding:7px 10px; background:#f0fdf4; border:1px solid #d1fae5; border-radius:6px; font-size:10px;">
                        <span style="font-weight:bold; color:#111827;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</span>
                        &nbsp;&nbsp;
                        <span style="color:#059669; font-weight:bold;">₱{{ number_format($payment->amount_paid, 2) }} ✓</span>
                        &nbsp;&nbsp;
                        <span style="color:#9ca3af;">{{ $payment->payment_method }}</span>
                        @if($payment->notes)
                            &nbsp;·&nbsp;<span style="color:#9ca3af; font-style:italic;">{{ $payment->notes }}</span>
                        @endif
                    </td>
                    <td style="padding:7px 10px; background:#fdf2f8; border:1px solid #fce7f3; border-radius:6px; font-size:10px; text-align:right; width:30%;">
                        Balance: <span style="font-weight:bold; color:#853953;">₱{{ number_format($payment->running_balance, 2) }}</span>
                    </td>
                </tr>
            </table>
            @endforeach
        </div>
        @endif

        {{-- SIGNATURE AREA --}}
        <hr class="divider">
        <table class="signature-area" cellspacing="12">
            <tr>
                <td class="sig-cell">
                    <div class="sig-line">Renter's Signature · {{ $lease->renter_name }}</div>
                </td>
                <td class="sig-cell">
                    <div class="sig-line">Authorized Staff · {{ $lease->staff_name }}</div>
                </td>
            </tr>
        </table>

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <span class="footer-brand">DreamHome</span> Property Management · CDO Branch ·
        This document was system-generated on {{ now()->format('F d, Y \a\t h:i A') }}
    </div>

</body>
</html>