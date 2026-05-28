<x-app-layout>
    <div class="py-10 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Lease Applications</h1>
                    <p class="text-sm font-bold text-[#853953] mt-1 uppercase tracking-widest">Renter Requests for Review</p>
                </div>
                <a href="{{ route('staff.leases.index') }}"
                   class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-slate-50 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    View Active Leases
                </a>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 px-5 py-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Pending Review</p>
                    <p class="text-3xl font-black text-slate-900 mt-1">{{ $counts['pending'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Approved</p>
                    <p class="text-3xl font-black text-emerald-600 mt-1">{{ $counts['approved'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Rejected</p>
                    <p class="text-3xl font-black text-rose-600 mt-1">{{ $counts['rejected'] }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-6">
                <form action="{{ route('staff.applications') }}" method="GET" class="p-4 flex flex-col md:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="block w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold placeholder-slate-400 focus:ring-2 focus:ring-[#853953] transition-all"
                               placeholder="Search by renter name, property, or application ID...">
                    </div>
                    <select name="status" class="bg-slate-50 border-none rounded-2xl px-4 py-3 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-[#853953] transition-all">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="px-6 py-3 bg-[#853953] text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-[#6e2e44] transition-all">Filter</button>
                </form>
            </div>

            {{-- Applications Table --}}
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                @if($applications->isEmpty())
                    <div class="py-20 text-center">
                        <svg class="w-12 h-12 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-bold text-slate-400">No applications found.</p>
                    </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Application</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Property</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Renter</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Viewing</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Start Date</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($applications as $app)
                            <tr class="hover:bg-slate-50/50 transition-colors">

                                {{-- Application ID + Date --}}
                                <td class="px-6 py-5">
                                    <p class="text-xs font-black text-[#853953] uppercase tracking-wider">{{ $app->applicationid }}</p>
                                    <p class="text-[11px] text-slate-400 font-bold mt-0.5">{{ \Carbon\Carbon::parse($app->created_at)->format('M d, Y') }}</p>
                                    @if($app->message)
                                        <p class="text-[11px] text-slate-500 mt-1 max-w-[180px] truncate italic">"{{ $app->message }}"</p>
                                    @endif
                                </td>

                                {{-- Property --}}
                                <td class="px-6 py-5">
                                    <p class="text-sm font-black text-slate-900">{{ $app->street }}</p>
                                    <p class="text-xs text-slate-400 font-bold">{{ $app->city }}</p>
                                    <p class="text-xs font-black text-[#853953] mt-0.5">₱{{ number_format($app->monthly_rate, 0) }}/mo</p>
                                </td>

                                {{-- Renter --}}
                                <td class="px-6 py-5">
                                    <p class="text-sm font-black text-slate-900">{{ $app->renter_name }}</p>
                                    <p class="text-xs text-slate-400 font-bold">{{ $app->renterno }}</p>
                                    @if($app->renter_phone)
                                        <p class="text-xs text-slate-500 font-bold mt-0.5">{{ $app->renter_phone }}</p>
                                    @endif
                                </td>

                                {{-- Viewing --}}
                                <td class="px-6 py-5 text-center">
                                    @if($app->viewingid)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Viewed
                                        </span>
                                        <p class="text-[10px] text-slate-400 font-bold mt-1">{{ \Carbon\Carbon::parse($app->view_date)->format('M d, Y') }}</p>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-lg">
                                            No viewing
                                        </span>
                                    @endif
                                </td>

                                {{-- Preferred Start Date --}}
                                <td class="px-6 py-5 text-center">
                                    <p class="text-sm font-black text-slate-700">{{ \Carbon\Carbon::parse($app->preferred_start_date)->format('M d, Y') }}</p>
                                </td>

                                {{-- Status badge --}}
                                <td class="px-6 py-5 text-center">
                                    @if($app->status === 'Pending')
                                        <span class="px-3 py-1.5 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-lg">Pending</span>
                                    @elseif($app->status === 'Approved')
                                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-lg">Approved</span>
                                        @if($app->reviewed_by_name)
                                            <p class="text-[10px] text-slate-400 font-bold mt-1">by {{ $app->reviewed_by_name }}</p>
                                        @endif
                                    @else
                                        <span class="px-3 py-1.5 bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg">Rejected</span>
                                        @if($app->reviewed_by_name)
                                            <p class="text-[10px] text-slate-400 font-bold mt-1">by {{ $app->reviewed_by_name }}</p>
                                        @endif
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-5 text-center">
                                    @if($app->status === 'Pending')
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Approve --}}
                                        <form action="{{ route('staff.applications.approve', $app->applicationid) }}" method="POST" onsubmit="confirmAction(event, this, 'approve')">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-95 shadow-sm">
                                                Approve
                                            </button>
                                        </form>
                                        
                                        {{-- Reject --}}
                                        <form action="{{ route('staff.applications.reject', $app->applicationid) }}" method="POST" onsubmit="confirmAction(event, this, 'reject')">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-95">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                        <span class="text-xs text-slate-300 font-bold">—</span>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- SweetAlert2 Library & Custom Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmAction(event, form, actionType) {
            // Stop the form from submitting immediately
            event.preventDefault(); 
            
            // Set up dynamic text/colors based on the action
            let title = actionType === 'approve' ? 'Approve Application?' : 'Reject Application?';
            let text = actionType === 'approve' 
                ? 'You will be redirected to finalize the lease agreement.' 
                : 'This application will be marked as rejected.';
            let confirmColor = actionType === 'approve' ? '#10b981' : '#f43f5e'; // emerald-500 or rose-500
            let confirmText = actionType === 'approve' ? 'Yes, approve it!' : 'Yes, reject it!';
            let iconType = actionType === 'approve' ? 'question' : 'warning';

            // Trigger the SweetAlert modal
            Swal.fire({
                title: title,
                text: text,
                icon: iconType,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#94a3b8', // slate-400
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-3xl' // Optional: rounds the modal corners to match your UI
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