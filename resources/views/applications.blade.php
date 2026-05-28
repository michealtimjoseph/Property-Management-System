<x-app-layout>
<div class="py-10 bg-[#F3F4F6] min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">My Applications</h1>
                <p class="text-sm font-bold text-[#853953] mt-1 uppercase tracking-widest">Lease Application Status</p>
            </div>
            <a href="{{ route('home') }}"
               class="inline-flex items-center px-5 py-2.5 bg-[#853953] text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-[#6e2e44] transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Apply for a Property
            </a>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-5 py-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-bold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Empty state --}}
        @if($applications->isEmpty())
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 py-20 text-center">
                <svg class="w-14 h-14 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm font-black text-slate-400">No applications yet.</p>
                <p class="text-xs text-slate-400 font-bold mt-1">Browse properties and click <span class="text-[#853953]">Apply to Lease</span> to get started.</p>
                <a href="{{ route('home') }}" class="inline-block mt-5 px-6 py-3 bg-[#853953] text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-[#6e2e44] transition-all">
                    Browse Properties
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($applications as $app)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row">

                        {{-- Property image strip --}}
                        <div class="sm:w-36 h-28 sm:h-auto shrink-0 bg-gradient-to-br from-[#853953]/10 to-[#5d273a]/10 flex items-center justify-center overflow-hidden">
                            @if($app->main_image)
                                <img src="{{ asset('storage/' . $app->main_image) }}" alt="{{ $app->street }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-[#853953]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $app->applicationid }}</span>
                                    <span class="text-[10px] text-slate-300">·</span>
                                    <span class="text-[10px] text-slate-400 font-bold">{{ \Carbon\Carbon::parse($app->created_at)->format('M d, Y') }}</span>
                                </div>
                                <h3 class="text-base font-black text-slate-900 tracking-tight">{{ $app->street }}, {{ $app->city }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="text-xs font-black text-[#853953]">₱{{ number_format($app->monthly_rate, 0) }}/mo</span>
                                    <span class="text-slate-200 text-xs">|</span>
                                    <span class="text-xs text-slate-500 font-bold">{{ $app->property_type }}</span>
                                    <span class="text-slate-200 text-xs">|</span>
                                    <span class="text-xs text-slate-500 font-bold">Start: {{ \Carbon\Carbon::parse($app->preferred_start_date)->format('M d, Y') }}</span>
                                </div>
                                @if($app->viewingid)
                                    <div class="flex items-center gap-1 mt-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-[11px] font-bold text-emerald-600">Viewed on {{ \Carbon\Carbon::parse($app->view_date)->format('M d, Y') }}</span>
                                    </div>
                                @endif
                                @if($app->message)
                                    <p class="text-[11px] text-slate-400 italic mt-1">"{{ $app->message }}"</p>
                                @endif
                            </div>

                            {{-- Status --}}
                            <div class="text-center shrink-0">
                                @if($app->status === 'Pending')
                                    <span class="inline-block px-4 py-2 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-xl">
                                        Pending Review
                                    </span>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1.5">Waiting for staff</p>
                                @elseif($app->status === 'Approved')
                                    <span class="inline-block px-4 py-2 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-xl">
                                        Approved
                                    </span>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1.5">Lease being prepared</p>
                                @else
                                    <span class="inline-block px-4 py-2 bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-xl">
                                        Rejected
                                    </span>
                                    @if($app->reviewed_by_name)
                                        <p class="text-[10px] text-slate-400 font-bold mt-1.5">by {{ $app->reviewed_by_name }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
</x-app-layout>