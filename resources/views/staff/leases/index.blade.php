<x-app-layout>
    <div class="py-10 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Lease Management</h1>
                    <p class="text-sm font-bold text-[#853953] mt-1 uppercase tracking-widest">Master Agreement Overview</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Updated button to match the new #853953 theme --}}
                    <a href="{{ route('staff.leases.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-[#853953] text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-[#6e2e44] transition-all shadow-lg shadow-[#853953]/20 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                        New Lease Agreement
                    </a>
                </div>
            </div>

            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Leases</p>
                    <p class="text-3xl font-black text-slate-900 mt-1">{{ $leases->count() }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 ring-1 ring-[#853953]/10">
                    <p class="text-[10px] font-black text-[#853953] uppercase tracking-widest">Paid This Month</p>
                    <p class="text-3xl font-black text-[#853953] mt-1">{{ $leases->where('is_paid_this_month', true)->count() }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Awaiting Payment</p>
                    <p class="text-3xl font-black text-rose-600 mt-1">{{ $leases->where('is_paid_this_month', false)->count() }}</p>
                </div>
            </div>

            {{-- Filter & Search Bar --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-6">
                <form action="{{ route('staff.leases.index') }}" method="GET" class="p-4 flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold placeholder-slate-400 focus:ring-2 focus:ring-[#853953] transition-all"
                               placeholder="Search by Lease ID, Renter, or Property...">
                    </div>
                </form>
            </div>

            {{-- Leases Table --}}
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Lease & Renter</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Property Address</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Monthly Rent</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($leases as $lease)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-[#853953]/10 flex items-center justify-center font-black text-[#853953] text-xs">
                                            {{ substr($lease->leaseno, -3) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900">{{ $lease->r_fname }} {{ $lease->r_lname }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $lease->leaseno }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-bold text-slate-700">{{ $lease->street }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $lease->city }}</p>
                                </td>
                                <td class="px-6 py-5 text-center font-black text-slate-900">
                                    ₱{{ number_format($lease->monthly_rent, 2) }}
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($lease->is_paid_this_month)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#853953]/10 text-[#853953] text-[10px] font-black uppercase tracking-widest">
                                            Current
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black uppercase tracking-widest animate-pulse">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <a href="{{ route('staff.leases.show', $lease->leaseno) }}" 
                                       class="inline-flex p-2 text-slate-400 hover:text-[#853953] hover:bg-[#853953]/10 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            {{-- ... empty state ... --}}
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>