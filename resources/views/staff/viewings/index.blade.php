<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="py-12 bg-[#F3F4F6] min-h-screen" x-data="{ selectedDate: '{{ now()->format('Y-m-d') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Viewings</h1>
                            <p class="text-sm text-gray-500 mt-1 font-medium">Manage and monitor property visits.</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        {{-- MAIN FEED: Empty state handled by @forelse[cite: 9] --}}
                        @forelse($viewings as $viewing)
                        <div class="bg-white rounded-3xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-pink-50 flex flex-col md:flex-row items-center gap-6 group cursor-pointer">
                            <div class="w-24 h-24 bg-gradient-to-br from-[#853953] to-[#5d273a] rounded-2xl flex-shrink-0 flex items-center justify-center text-white font-bold text-2xl shadow-inner">
                                {{ substr($viewing->title, 0, 1) }}
                            </div>
                            
                            <div class="flex-1 text-center md:text-left">
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#853953] transition-colors">{{ $viewing->title }}</h3>
                                <p class="text-sm text-gray-500 font-medium">{{ $viewing->addr }}</p>
                                <div class="flex items-center justify-center md:justify-start gap-2 mt-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        {{ \Carbon\Carbon::parse($viewing->date)->format('F d, Y') }}[cite: 8]
                                    </p>
                                </div>
                            </div>

                            <div class="hidden md:block w-px h-16 bg-gray-100"></div>

                            <div class="flex-1 bg-gray-50 p-4 rounded-2xl border border-gray-100 group-hover:bg-pink-50 group-hover:border-pink-100 transition-colors">
                                <p class="text-[10px] font-black text-[#853953] uppercase mb-1 tracking-tighter opacity-60">Staff Feedback</p>
                                <p class="text-xs text-gray-600 italic leading-relaxed">
                                    {{ $viewing->comment ?: 'No feedback provided yet.' }}[cite: 8]
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-[2rem] p-12 text-center shadow-sm border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold uppercase text-xs tracking-widest">No viewing records found.</p>
                        </div>
                        @endforelse                
                    </div>
                </div>

                <aside class="w-full lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-white sticky top-6 flex flex-col min-h-[600px]">
                        <div class="mb-10 px-2">
                            <h2 class="text-xl font-black text-gray-800 tracking-tighter">Timeline</h2>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Upcoming Viewings</p>
                        </div>

                        <div class="flex-1 space-y-8 px-2">
                            {{-- SIDEBAR: Empty state handled by @forelse[cite: 9] --}}
                            @forelse($timeline as $date => $dayViewings)
                            <div class="relative">
                                <button @click="selectedDate = (selectedDate === '{{ $date }}' ? null : '{{ $date }}')" 
                                    class="flex items-center w-full transition-all group outline-none">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center transition-all mr-3 shadow-sm"
                                         :class="selectedDate === '{{ $date }}' ? 'bg-[#853953] text-white' : 'bg-gray-100 text-gray-400'">
                                        <svg class="w-3 h-3 transition-transform duration-300" :class="selectedDate === '{{ $date }}' ? 'rotate-0' : 'rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-black transition-colors" :class="selectedDate === '{{ $date }}' ? 'text-gray-900' : 'text-gray-400'">
                                        {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}[cite: 8]
                                    </span>
                                </button>

                                <div x-show="selectedDate === '{{ $date }}'" x-collapse x-cloak class="mt-4 ml-3.5 pl-6 border-l border-pink-100 space-y-5">
                                    @foreach($dayViewings as $item)
                                    <div class="relative group/item cursor-pointer">
                                        <div class="absolute -left-[29px] top-1 w-1.5 h-1.5 rounded-full bg-[#853953] ring-4 ring-white"></div>
                                        <div class="flex items-center justify-between">
                                            <p class="text-[11px] font-black text-gray-700 group-hover:text-[#853953] transition-colors leading-none">{{ $item->street }}</p>
                                        </div>
                                        <p class="text-[9px] text-gray-400 font-bold mt-1 uppercase">Staff: {{ $item->staff_name }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @empty
                            <p class="text-[10px] text-gray-400 font-bold italic text-center">No upcoming viewings scheduled.</p>
                            @endforelse
                        </div>

                        <div class="mt-10 pt-6 border-t border-gray-50 px-2">
                            <a href="{{ route('staff.viewings.create') }}" class="w-full bg-[#853953] text-white py-4 rounded-3xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-gray-200 hover:bg-pink-900 transition-all flex items-center justify-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Viewing
                            </a>
                        </div>                    
                    </div>
                </aside>

            </div>
        </div>
    </div>
</x-app-layout>