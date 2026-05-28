<x-app-layout>
<div class="py-10 bg-[#F3F4F6] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Property Listing Requests</h1>
                <p class="text-sm font-bold text-[#853953] mt-1 uppercase tracking-widest">Clients requesting to list their property</p>
            </div>
            <a href="{{ route('staff.properties.properties') }}"
               class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-slate-50 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                View Properties
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
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Pending</p>
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
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 mb-6">
            <form action="{{ route('staff.listing-requests.index') }}" method="GET" class="p-4 flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="block w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold placeholder-slate-400 focus:ring-2 focus:ring-[#853953] transition-all"
                           placeholder="Search by name, street, city, or request ID...">
                </div>
                <select name="status" class="bg-slate-50 border-none rounded-2xl px-4 py-3 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-[#853953] transition-all">
                    <option value="">All Statuses</option>
                    <option value="Pending"  {{ request('status') === 'Pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="px-6 py-3 bg-[#853953] text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-[#6e2e44] transition-all">Filter</button>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            @if($requests->isEmpty())
                <div class="py-20 text-center">
                    <svg class="w-12 h-12 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <p class="text-sm font-bold text-slate-400">No listing requests found.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Request</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Property Details</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Client</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Photo</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($requests as $req)
                        <tr class="hover:bg-slate-50/50 transition-colors">

                            {{-- Request ID + date --}}
                            <td class="px-6 py-5">
                                <p class="text-xs font-black text-[#853953] uppercase tracking-wider">{{ $req->requestid }}</p>
                                <p class="text-[11px] text-slate-400 font-bold mt-0.5">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</p>
                                @if($req->message)
                                    <p class="text-[11px] text-slate-500 italic mt-1 max-w-[160px] truncate">"{{ $req->message }}"</p>
                                @endif
                            </td>

                            {{-- Property details --}}
                            <td class="px-6 py-5">
                                <p class="text-sm font-black text-slate-900">{{ $req->street }}</p>
                                <p class="text-xs text-slate-400 font-bold">{{ $req->area }}, {{ $req->city }}</p>
                                <div class="flex flex-wrap gap-2 mt-1.5">
                                    <span class="text-[10px] font-black px-2 py-0.5 bg-pink-50 text-[#853953] rounded-lg">{{ $req->property_type }}</span>
                                    <span class="text-[10px] font-black px-2 py-0.5 bg-slate-100 text-slate-600 rounded-lg">{{ $req->no_of_rooms }} rooms</span>
                                    <span class="text-[10px] font-black px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-lg">₱{{ number_format($req->monthly_rate, 0) }}/mo</span>
                                </div>
                            </td>

                            {{-- Client --}}
                            <td class="px-6 py-5">
                                <p class="text-sm font-black text-slate-900">{{ $req->renter_name }}</p>
                                <p class="text-xs text-slate-400 font-bold">{{ $req->renterno }}</p>
                                @if($req->renter_phone)
                                    <p class="text-xs text-slate-500 font-bold mt-0.5">{{ $req->renter_phone }}</p>
                                @endif
                            </td>

                            {{-- Photo thumbnail --}}
                            <td class="px-6 py-5 text-center">
                                @if($req->main_image)
                                    <img src="{{ asset('storage/' . $req->main_image) }}"
                                         alt="Property photo"
                                         class="w-16 h-12 object-cover rounded-xl mx-auto border border-slate-100 shadow-sm">
                                @else
                                    <div class="w-16 h-12 bg-slate-100 rounded-xl mx-auto flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-5 text-center">
                                @if($req->status === 'Pending')
                                    <span class="px-3 py-1.5 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-lg">Pending</span>
                                @elseif($req->status === 'Approved')
                                    <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-lg">Approved</span>
                                    @if($req->reviewed_by_name)
                                        <p class="text-[10px] text-slate-400 font-bold mt-1">by {{ $req->reviewed_by_name }}</p>
                                    @endif
                                @else
                                    <span class="px-3 py-1.5 bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg">Rejected</span>
                                    @if($req->reviewed_by_name)
                                        <p class="text-[10px] text-slate-400 font-bold mt-1">by {{ $req->reviewed_by_name }}</p>
                                    @endif
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-5 text-center">
                                @if($req->status === 'Pending')
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('staff.listing-requests.approve', $req->requestid) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                onclick="return confirm('Approve this listing and proceed to create the property?')"
                                                class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-95 shadow-sm">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('staff.listing-requests.reject', $req->requestid) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                onclick="return confirm('Reject this listing request?')"
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
</x-app-layout>