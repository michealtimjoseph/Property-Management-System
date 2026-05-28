<x-app-layout>
    <style>[x-cloak] { display: none !important; }</style>

    <div class="py-12 bg-[#F3F4F6] min-h-screen" x-data="{ tab: 'active' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                {{-- MAIN WORKSPACE --}}
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Viewing Center</h1>
                            <p class="text-sm text-gray-500 font-medium">Manage visit schedules and history logs.</p>
                        </div>

                        {{-- SEARCH AND TABS --}}
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            {{-- SEARCH FORM --}}
                            <form action="{{ route('staff.viewings') }}" method="GET" class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#853953] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client or street..." 
                                       class="w-full md:w-64 pl-10 pr-4 py-3 bg-white border-none rounded-2xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-[#853953] shadow-sm transition-all">
                            </form>

                            {{-- TAB SWITCHER --}}
                            <div class="bg-white p-1 rounded-2xl shadow-sm border border-gray-100 flex gap-1 h-fit">
                                <button @click="tab = 'active'" :class="tab === 'active' ? 'bg-[#853953] text-white' : 'text-gray-400 hover:text-gray-600'" 
                                        class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Active</button>
                                <button @click="tab = 'completed'" :class="tab === 'completed' ? 'bg-[#853953] text-white' : 'text-gray-400 hover:text-gray-600'" 
                                        class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Completed</button>
                            </div>
                        </div>
                    </div>

                    {{-- ACTIVE SCHEDULE TAB --}}
                    <div x-show="tab === 'active'" x-transition>
                        <div class="space-y-6">
                            @forelse($activeViewings as $viewing)
                            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-white flex flex-col md:flex-row items-center gap-6 group">
                                <div class="w-16 h-16 bg-pink-50 rounded-2xl flex flex-col items-center justify-center text-[#853953] shadow-inner">
                                    <span class="text-[8px] font-black uppercase">Visit</span>
                                    <span class="text-sm font-black">{{ \Carbon\Carbon::parse($viewing->date)->format('d') }}</span>
                                </div>
                                <div class="flex-1 text-center md:text-left">
                                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">{{ $viewing->title }}</h3>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $viewing->addr }}</p>
                                </div>
                                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                    <div><p class="text-[8px] font-black text-gray-400 uppercase mb-1">Renter</p><p class="text-xs font-bold">{{ $viewing->r_fname }} {{ $viewing->r_lname }}</p></div>
                                    <div class="h-8 w-px bg-gray-200"></div>
                                    <div><p class="text-[8px] font-black text-gray-400 uppercase mb-1">Guide</p><p class="text-xs font-bold text-[#853953]">{{ $viewing->staff_name }}</p></div>
                                </div>
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase 
                                    {{ $viewing->status == 'Completed' ? 'bg-emerald-50 text-emerald-600' : 
                                    ($viewing->status == 'Closed' ? 'bg-slate-100 text-slate-500' : 'bg-amber-50 text-amber-600') }}">
                                    {{ $viewing->status ?? 'Pending' }}
                                </span>
                            </div>
                            @empty
                            <div class="bg-white rounded-[2rem] p-20 text-center border-2 border-dashed border-gray-100">
                                <p class="text-gray-400 font-black uppercase text-xs">No matching viewings found.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- COMPLETION HISTORY TAB --}}
                    <div x-show="tab === 'completed'" x-transition x-cloak>
                        <div class="space-y-6">
                            @forelse($completedViewings as $viewing)
                            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-white flex flex-col md:flex-row items-center gap-6 transition-all">
                                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex flex-col items-center justify-center text-emerald-600 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">{{ $viewing->title }}</h3>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">Finished on {{ \Carbon\Carbon::parse($viewing->date)->format('F d, Y') }}</p>
                                </div>
                                <div class="flex-1 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                    <p class="text-[8px] font-black text-[#853953] uppercase mb-1">Feedback Summary</p>
                                    <p class="text-[10px] text-gray-500 leading-tight italic">"{{ $viewing->comment ?? 'No comments recorded.' }}"</p>
                                </div>
                                <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase border border-emerald-100">Finalized ✓</span>
                            </div>
                            @empty
                            <div class="bg-white rounded-[2rem] p-20 text-center border-2 border-dashed border-gray-100">
                                <p class="text-gray-400 font-black uppercase text-xs">No matching history found.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- SIDE PANEL: VIEWING INBOX --}}
                <aside class="w-full lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-white sticky top-6">
                        <div class="mb-10 px-2">
                            <h2 class="text-xl font-black text-gray-800 tracking-tighter">Viewing Inbox</h2>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Unassigned Requests</p>
                        </div>

                        <div class="space-y-4 px-2 max-h-80 overflow-y-auto">
                            @forelse($requests as $req)
                            <a href="{{ route('staff.viewings.create', ['request_id' => $req->id]) }}" class="group block p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-[#853953] transition-all">
                                <p class="text-[11px] font-black text-gray-800 group-hover:text-white leading-tight">{{ $req->title }}</p>
                                <p class="text-[9px] font-bold text-gray-400 group-hover:text-pink-100 uppercase mt-1">{{ $req->firstname }} {{ $req->lastname }}</p>
                            </a>
                            @empty
                            <p class="text-center text-[10px] text-gray-400 font-bold uppercase py-4">Inbox Clear</p>
                            @endforelse
                        </div>

                        <div class="mt-10 pt-6 border-t border-gray-50">
                            <a href="{{ route('staff.viewings.create') }}" class="w-full bg-[#853953] text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-pink-100 hover:bg-pink-900 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                Log New Viewing
                            </a>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </div>
</x-app-layout>