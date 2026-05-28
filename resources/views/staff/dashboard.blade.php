<x-app-layout>
    <style>[x-cloak] { display: none !important; }</style>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ $isRegular ? __('My Workspace') : __('Staff Overview') }}
                </h2>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mt-1">DreamHome Management System</p>
            </div>

            @if(strtolower(Auth::guard('staff')->user()->position) !== 'manager' && strtolower(Auth::guard('staff')->user()->position) !== 'secretary' && strtolower(Auth::guard('staff')->user()->position) !== 'supervisor')
                <a href="{{ route('staff.dashboard.report') }}" 
                class="flex items-center justify-center gap-3 bg-[#853953] text-white px-8 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-[#853953]/20 hover:bg-[#6e2e44] transition-all group">
                    <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Generate Report
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- SUCCESS FEEDBACK --}}
            @if(session('success'))
                <div class="mb-8 p-4 bg-[#853953]/5 border border-[#853953]/10 text-[#853953] rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- 1. STAT CARDS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
                        @php
                            if($isRegular) {
                                // Regular Staff: Show only these 3 specific cards
                                $stats = [
                                    ['label' => 'Managed', 'value' => $totalProperties, 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                                    ['label' => 'Pending Viewings', 'value' => $assignedViewings->count(), 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                                    ['label' => 'Pending Inspections', 'value' => $pendingInspections, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                                    ['label' => 'Assigned Leases', 'value' => $assignedLeases->count(), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                                ];
                            } else {
                                // Manager/Admin: Keep the original 4 overview cards
                                $stats = [
                                    ['label' => 'Properties', 'value' => $totalProperties, 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                                    ['label' => 'Total Revenue', 'value' => '₱' . number_format($totalRevenue), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.67 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.67-1M12 16V5'],
                                    ['label' => 'Total Renters', 'value' => $totalRenters, 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                    ['label' => 'Unpaid This Month', 'value' => $unpaidLeases, 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z']
                                ];
                            }
                        @endphp

                        @foreach($stats as $stat)
                        <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100 group">
                            <div class="flex items-center">
                                <div class="p-3 rounded-xl bg-[#853953]/10 text-[#853953] group-hover:bg-[#853953] group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                                    <h4 class="text-2xl font-black text-gray-900">{{ $stat['value'] }}</h4>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

            {{-- 2. REGULAR STAFF VIEW --}}
            @if($isRegular)
                <div class="space-y-12 mb-12">
                    {{-- Portfolio Details --}}
                    <div>
                        <div class="flex items-center justify-between mb-6 px-2">
                            <div class="flex items-center gap-2 flex-1">
                                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#853953]">Properties Managed</h2>
                                <div class="h-px flex-1 bg-[#853953]/10"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($assignedProperties as $prop)
                                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col gap-5 group hover:border-[#853953]/30 transition-all">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="text-[8px] font-black text-gray-400 uppercase">ID: {{ $prop->propertyno }}</span>
                                            <h3 class="text-xl font-black text-gray-900 tracking-tighter">{{ $prop->street }}</h3>
                                        </div>
                                        <a href="{{ route('staff.properties.show', $prop->propertyno) }}" class="px-3 py-1 bg-[#853953]/5 text-[#853953] rounded-lg text-[9px] font-black uppercase border border-[#853953]/10 hover:bg-[#853953] hover:text-white transition-all">View Details</a>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-slate-50/60 p-3 rounded-xl border border-slate-100"><p class="text-[8px] font-black text-gray-400 uppercase mb-1">City</p><p class="text-xs font-bold text-gray-800">{{ $prop->city }}</p></div>
                                        <div class="bg-slate-50/60 p-3 rounded-xl border border-slate-100"><p class="text-[8px] font-black text-gray-400 uppercase mb-1">Rate</p><p class="text-xs font-black text-[#853953]">₱{{ number_format($prop->monthly_rate, 2) }}</p></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Feedback Forms (Viewings) --}}
                    <div>
                        <div class="flex items-center gap-2 mb-6 px-2">
                            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#853953]">Scheduled Viewings</h2>
                            <div class="h-px flex-1 bg-[#853953]/10"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @forelse($assignedViewings as $viewing)
                                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col gap-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-[#853953]/5 rounded-xl flex items-center justify-center text-[#853953] font-black text-xs">{{ \Carbon\Carbon::parse($viewing->view_date)->format('d') }}</div>
                                            <div><p class="text-[10px] font-black text-gray-900 uppercase">{{ \Carbon\Carbon::parse($viewing->view_date)->format('F Y') }}</p><p class="text-[9px] text-gray-400 font-bold uppercase">ID: {{ $viewing->viewingid }}</p></div>
                                        </div>
                                        <a href="{{ route('staff.viewings') }}" class="p-2 text-gray-300 hover:text-[#853953] transition-colors" title="View in List">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                    <form action="{{ route('staff.viewings.feedback') }}" method="POST" class="space-y-3" onsubmit="confirmAction(event, this, 'Finalize Viewing')">
                                        @csrf
                                        <input type="hidden" name="viewingid" value="{{ $viewing->viewingid }}">
                                        <div class="bg-slate-50/60 p-3 rounded-xl border border-slate-100">
                                            <p class="text-[8px] font-black text-gray-400 uppercase mb-1">Client / Property</p>
                                            <p class="text-xs font-bold text-slate-800">{{ $viewing->r_fname }} {{ $viewing->r_lname }} — {{ $viewing->street }}</p>
                                        </div>
                                        <textarea name="comment" rows="2" placeholder="Record viewing notes and feedback here..." class="w-full bg-slate-50/60 border-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-[#853953]/20 focus:border-[#853953] focus:bg-white p-3 shadow-inner transition-all resize-none" required></textarea>
                                        <button type="submit" class="w-full bg-[#853953] text-white py-3 rounded-xl font-black text-[9px] uppercase tracking-widest shadow-md shadow-[#853953]/10 hover:bg-[#6e2e44] transition-colors">Submit & Finalize</button>
                                    </form>
                                </div>
                            @empty
                                <div class="md:col-span-2 bg-white rounded-2xl p-10 text-center border-dashed border-slate-200 border-2">
                                    <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">No pending feedback sessions</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Inspections & Leases --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#853953] mb-4 ml-2">Scheduled Inspections</h3>
                            <div class="space-y-4">
                                @forelse($assignedInspections as $ins)
                                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col gap-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-11 h-11 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 border border-slate-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path></svg>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase">ID: {{ $ins->inspectionid }}</p>
                                                    <p class="text-xs font-black text-[#853953] uppercase tracking-wider">{{ \Carbon\Carbon::parse($ins->inspection_date)->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('staff.inspections') }}" class="p-2 bg-slate-50 hover:bg-[#853953]/5 text-[#853953] rounded-lg border border-slate-100 transition-all" title="View List">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                        
                                        <div class="bg-slate-50/60 p-3.5 rounded-xl border border-slate-100">
                                            <p class="text-[8px] font-black text-gray-400 uppercase mb-0.5">Location</p>
                                            <p class="text-xs font-bold text-gray-700 leading-tight">{{ $ins->street }}, {{ $ins->city }}</p>
                                        </div>

                                        <form action="{{ route('staff.inspections.complete', $ins->inspectionid) }}" method="POST" class="space-y-3 pt-2 border-t border-slate-50" onsubmit="confirmAction(event, this, 'Mark Inspection as Done')">
                                            @csrf
                                            @method('PATCH')
                                            <textarea name="comment" rows="2" required placeholder="Write structural findings here..." class="w-full bg-slate-50/60 border-slate-100 rounded-xl text-xs focus:ring-2 focus:ring-[#853953]/20 focus:border-[#853953] focus:bg-white p-3 shadow-inner transition-all resize-none"></textarea>
                                            <button type="submit" class="w-full bg-[#853953] hover:bg-[#6e2e44] text-white py-3 rounded-xl font-black text-[9px] uppercase tracking-widest shadow-md transition-all active:scale-95">Mark as Done</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="bg-white rounded-2xl p-8 text-center border-dashed border-slate-200 border-2">
                                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">No pending inspections</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#853953] mb-4 ml-2">Active Leases    </h3>
                            <div class="space-y-4">
                                @foreach($assignedLeases as $lease)
                                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col gap-4">
                                        <div class="flex justify-between items-start">
                                            <div><p class="text-[8px] font-black text-gray-400 uppercase">Lease #{{ $lease->leaseno }}</p><h4 class="text-sm font-black text-gray-900 leading-tight mt-0.5">{{ $lease->street }}</h4></div>
                                            <a href="{{ route('staff.leases.show', $lease->leaseno) }}" class="p-2 text-slate-300 hover:text-[#853953] transition-colors" title="Full Agreement Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                        </div>
                                        <div class="flex items-center gap-4 bg-slate-50/60 p-3 rounded-xl border border-slate-100 text-[10px]">
                                            <div class="flex-1"><p class="text-[8px] font-black text-gray-400 uppercase mb-0.5">Renter</p><p class="font-bold text-slate-800">{{ $lease->r_fname }} {{ $lease->r_lname }}</p></div>
                                            <div class="flex-1 text-right"><p class="text-[8px] font-black text-gray-400 uppercase mb-0.5">Expires</p><p class="font-bold text-gray-600">{{ \Carbon\Carbon::parse($lease->enddate)->format('M d, Y') }}</p></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. CHARTS SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                    <h5 class="text-xl font-black text-gray-900 tracking-tighter mb-6">{{ $chartLabel }}</h5>
                    <div id="line-chart"></div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
                    <h5 class="text-xl font-black text-gray-900 tracking-tighter mb-6">Inventory Mix</h5>
                    <div class="py-2" id="pie-chart"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartData = @json($chartData);
    const chartLabel = "{{ $chartLabel }}";
    const pieLabels = @json($inventoryMix->pluck('property_type'));
    const pieValues = @json($inventoryMix->pluck('total'));

    // Dynamically generate the labels for the last 7 days
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const labels = [];
    for (let i = 6; i >= 0; i--) {
        let date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(days[date.getDay()]);
    }

    new ApexCharts(document.querySelector("#line-chart"), {
        chart: { height: 250, type: "area", fontFamily: "Inter, sans-serif", toolbar: { show: false } },
        series: [{ name: chartLabel, data: chartData }],
        colors: ["#853953"],
        stroke: { width: 4, curve: 'smooth' },
        fill: { type: "gradient", gradient: { opacityFrom: 0.6, opacityTo: 0.05 } },
        xaxis: { 
            categories: labels, 
            labels: { style: { fontWeight: 600 } } 
        }
    }).render();
        new ApexCharts(document.querySelector("#pie-chart"), {
            series: pieValues,
            labels: pieLabels,
            colors: ["#853953", "#a85472", "#c47a94", "#e0a3b8"],
            chart: { height: 320, type: "donut" },
            legend: { position: "bottom", fontWeight: 700 },
            plotOptions: { pie: { donut: { size: '70%' } } }
        }).render();
    </script>

    {{-- SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmAction(event, form, actionName) {
            // Stop the form from submitting immediately
            event.preventDefault(); 
            
            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to ' + actionName.toLowerCase() + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#853953', // Matches your theme color
                cancelButtonColor: '#94a3b8', // slate-400
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-3xl' // Matches your existing UI styling
                }
            }).then((result) => {
                // If the user clicks "Yes", submit the form programmatically
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>