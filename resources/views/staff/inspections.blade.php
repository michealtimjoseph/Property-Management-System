<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @php
        $staff = Auth::guard('staff')->user();
        $isRegular = $staff && strtolower($staff->position) === 'regular';

        // Filter: Show only what belongs to the authenticated user
        $myInspections = $inspections->where('staffno', $staff->staffno);
        
        $pending = $myInspections->where('status', '!=', 'Completed');
        $completed = $myInspections->where('status', '==', 'Completed');
    @endphp

    <div class="py-12 bg-[#F8FAFC] min-h-screen font-sans antialiased" x-data="{ activeTab: 'pending', showModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Layout Wrapper: Single column for Regular, Flex-row for Managers --}}
            <div class="flex flex-col {{ $isRegular ? '' : 'lg:flex-row' }} gap-10">
                
                {{-- MAIN PANEL --}}
                <div class="flex-1 w-full">
                    
                    {{-- Personalized Header --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
                        <div>
                            <h1 class="text-4xl font-black text-slate-900 tracking-tight">
                                {{ $isRegular ? 'My Inspection Tasks' : 'Global Inspections' }}
                            </h1>
                            <p class="text-sm text-slate-500 mt-2 font-medium">
                                {{ $isRegular ? 'Manage your assigned property evaluations and logs.' : 'Oversee all system-wide property reviews.' }}
                            </p>
                            
                            {{-- Tab Switcher with #853953 Styling --}}
                            <div class="inline-flex p-1.5 bg-slate-100 rounded-2xl mt-6">
                                <button @click="activeTab = 'pending'" 
                                    :class="activeTab === 'pending' ? 'bg-white text-[#853953] shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                    class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                    Pending ({{ $pending->count() }})
                                </button>
                                <button @click="activeTab = 'completed'" 
                                    :class="activeTab === 'completed' ? 'bg-white text-[#853953] shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                    class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                    History ({{ $completed->count() }})
                                </button>
                            </div>
                        </div>

                        {{-- Search Bar --}}
                        <div class="relative group w-full md:w-80">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" placeholder="Search property or date..." 
                                class="w-full bg-white border-slate-200 rounded-2xl py-3 pl-12 text-sm font-bold placeholder-slate-400 focus:ring-2 focus:ring-[#853953]/20 focus:border-[#853953] transition-all shadow-sm">
                        </div>
                    </div>

                    {{-- GRID CONTENT --}}
                    <div class="space-y-6">
                        
                        {{-- PENDING TASKS --}}
                        <div x-show="activeTab === 'pending'" x-transition class="grid grid-cols-1 {{ $isRegular ? 'md:grid-cols-2 lg:grid-cols-3' : 'md:grid-cols-1' }} gap-6">
                            @forelse($pending as $item)
                                <div class="bg-white rounded-3xl p-6 border border-slate-200 hover:border-[#853953]/30 hover:shadow-xl hover:shadow-[#853953]/5 transition-all group">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="w-12 h-12 bg-[#853953]/10 rounded-2xl flex items-center justify-center text-[#853953]">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-amber-100">Pending</span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 leading-tight mb-1 group-hover:text-[#853953] transition-colors">{{ $item->property->street }}</h3>
                                    <p class="text-xs font-bold text-slate-400 mb-6 uppercase tracking-tighter">{{ $item->property->city }} • {{ $item->inspectionid }}</p>
                                    
                                    <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                                        <div>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Target Date</p>
                                            <p class="text-xs font-black text-slate-700">{{ \Carbon\Carbon::parse($item->inspection_date)->format('M d, Y') }}</p>
                                        </div>

                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-center">
                                    <p class="text-slate-400 font-bold text-sm uppercase tracking-widest">No assigned inspections found</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- COMPLETED HISTORY --}}
                        <div x-show="activeTab === 'completed'" x-transition class="space-y-4">
                            @forelse($completed as $item)
                                <div class="bg-white rounded-3xl p-6 border border-slate-100 flex flex-col md:flex-row items-center gap-6 group">
                                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex-shrink-0 flex items-center justify-center text-emerald-600 border border-emerald-100">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-black text-slate-900">{{ $item->property->street }}</h3>
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($item->inspection_date)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 italic text-xs text-slate-500 leading-relaxed">
                                            "{{ $item->evaluation }}"
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-center">
                                    <p class="text-slate-400 font-bold text-sm uppercase tracking-widest">No historical logs found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR: Hidden for Regular Staff --}}
                @if(!$isRegular)
                <aside class="w-full lg:w-80 flex-shrink-0 lg:sticky lg:top-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 flex flex-col min-h-[500px]">
                        <div class="mb-10">
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Timeline</h2>
                            <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest mt-1">Pending Reviews</p>
                        </div>

                        {{-- Timeline logic omitted for brevity, same as previous version --}}
                        
                        <div class="mt-auto pt-8 border-t border-slate-100">
                            <button @click="showModal = true" class="w-full bg-[#853953] text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-[#853953]/20 hover:bg-[#6e2e44] transition-all flex items-center justify-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                                New Review
                            </button>
                        </div>
                    </div>
                </aside>
                @endif
                
            </div>
        </div>

        @if(!$isRegular)
            <x-schedule-modal :properties="$properties" :staffMembers="$staffMembers" />
        @endif
    </div>
</x-app-layout>