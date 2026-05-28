<x-app-layout>

{{-- ===== MODALS WRAPPER ===== --}}
{{-- FIX: merged search/type/matches into the outer x-data so modals and property cards share one Alpine scope --}}
<div x-data="{
    viewingModalOpen: false,
    leaseModalOpen: false,
    step: 1,
    property: '', propertyId: '', propertyType: '',
    search: '',
    type: 'all',

    openViewingModal(name, id, type) {
        this.leaseModalOpen = false;
        this.property = name; this.propertyId = id;
        this.propertyType = type; this.step = 1; this.viewingModalOpen = true;
    },

    openLeaseModal(name, id, type) {
        this.viewingModalOpen = false;
        this.property = name; this.propertyId = id;
        this.propertyType = type; this.step = 1; this.leaseModalOpen = true;
    },

    matches(street, area, city, postcode, propType) {
        const matchesSearch = !this.search ||
            street.toLowerCase().includes(this.search.toLowerCase()) ||
            area.toLowerCase().includes(this.search.toLowerCase()) ||
            city.toLowerCase().includes(this.search.toLowerCase()) ||
            postcode.toLowerCase().includes(this.search.toLowerCase());
        const matchesType = this.type === 'all' || propType.toLowerCase() === this.type.toLowerCase();
        return matchesSearch && matchesType;
    }
}">
    <style>[x-cloak] { display: none !important; }</style>

    {{-- Backdrop --}}
    <div x-show="viewingModalOpen || leaseModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="viewingModalOpen = false; leaseModalOpen = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"></div>

    {{-- ========================================== --}}
    {{-- MODAL 1: BOOK A VIEWING --}}
    {{-- ========================================== --}}
    <div x-show="viewingModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden" @click.stop>

            <div class="bg-gradient-to-r from-[#853953] to-[#5d273a] px-6 py-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black text-pink-200 uppercase tracking-[0.2em]">Book a Viewing</p>
                        <h3 class="text-white font-black text-base tracking-tight mt-0.5" x-text="property"></h3>
                        <p class="text-pink-200/70 text-[11px] font-bold mt-0.5" x-text="propertyId"></p>
                    </div>
                    <button @click="viewingModalOpen = false" class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black" :class="step >= 1 ? 'bg-white text-[#853953]' : 'bg-white/20 text-white'">1</div>
                        <span class="text-[10px] font-black text-white/80 uppercase tracking-wider">Your Info</span>
                    </div>
                    <div class="flex-1 h-px bg-white/20 mx-1"></div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black" :class="step >= 2 ? 'bg-white text-[#853953]' : 'bg-white/20 text-white'">2</div>
                        <span class="text-[10px] font-black text-white/80 uppercase tracking-wider">Preferences</span>
                    </div>
                </div>
            </div>

            {{-- FIX: novalidate stops browser trying to focus hidden Step 1 fields when submitting from Step 2 --}}
            <form action="{{ route('viewings.book') }}" method="POST" novalidate>
                @csrf

                {{-- Viewing Step 1 --}}
                <div x-show="step === 1" class="p-6 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Full Name</label>
                        <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-sm text-gray-500 font-bold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Contact Number</label>
                        {{-- FIX: removed `required` — validation handled server-side, avoids browser blocking submit from Step 2 --}}
                        <input type="tel" name="contact_no" placeholder="e.g. 0912-345-6789" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">General Comments <span class="text-gray-300 normal-case font-medium">(optional)</span></label>
                        <textarea rows="3" name="comment" placeholder="e.g. Looking to move by August..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="button" @click="viewingModalOpen = false" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                        <button type="button" @click="step = 2" class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all flex items-center justify-center gap-2">
                            Next <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Viewing Step 2 --}}
                <div x-show="step === 2" class="p-6 space-y-4">
                    <div class="bg-pink-50 border border-pink-100 rounded-xl px-4 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-[#853953]">Property Type</p>
                            <p class="text-sm font-black text-gray-800 mt-0.5" x-text="propertyType"></p>
                        </div>
                        <svg class="w-5 h-5 text-[#853953]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Preferred Viewing Date <span class="text-rose-400">*</span></label>
                        <input type="date" name="view_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Preferred Time <span class="text-gray-300 normal-case font-medium">(optional)</span></label>
                        <select name="preferred_time" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                            <option value="">Select a time slot...</option>
                            <option value="Morning (8AM - 12PM)">Morning (8AM – 12PM)</option>
                            <option value="Afternoon (1PM - 5PM)">Afternoon (1PM – 5PM)</option>
                            <option value="Evening (5PM - 7PM)">Evening (5PM – 7PM)</option>
                        </select>
                    </div>
                    {{-- FIX: hidden input is inside Step 2 (the active x-show block) so Alpine evaluates :value correctly --}}
                    <input type="hidden" name="propertyno" :value="propertyId">
                    <div class="flex gap-3 pt-1">
                        <button type="button" @click="step = 1" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg> Back
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all">Confirm Viewing</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 2: APPLY TO LEASE --}}
    {{-- ========================================== --}}
    <div x-show="leaseModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden" @click.stop>

            <div class="bg-gradient-to-r from-emerald-700 to-teal-800 px-6 py-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black text-emerald-200 uppercase tracking-[0.2em]">Lease Application</p>
                        <h3 class="text-white font-black text-base tracking-tight mt-0.5" x-text="property"></h3>
                        <p class="text-emerald-200/70 text-[11px] font-bold mt-0.5" x-text="propertyId"></p>
                    </div>
                    <button @click="leaseModalOpen = false" class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black" :class="step >= 1 ? 'bg-white text-emerald-700' : 'bg-white/20 text-white'">1</div>
                        <span class="text-[10px] font-black text-white/80 uppercase tracking-wider">Contact</span>
                    </div>
                    <div class="flex-1 h-px bg-white/20 mx-1"></div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black" :class="step >= 2 ? 'bg-white text-emerald-700' : 'bg-white/20 text-white'">2</div>
                        <span class="text-[10px] font-black text-white/80 uppercase tracking-wider">Lease Info</span>
                    </div>
                </div>
            </div>

            {{-- FIX: novalidate stops browser trying to focus hidden Step 1 fields when submitting from Step 2 --}}
            <form action="{{ route('applications.store') }}" method="POST" novalidate>
                @csrf

                {{-- Lease Step 1 --}}
                <div x-show="step === 1" class="p-6 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Full Name</label>
                        <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-sm text-gray-500 font-bold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Contact Number</label>
                        {{-- FIX: removed `required` — server-side validation handles this --}}
                        <input type="tel" name="contact_no" placeholder="e.g. 0912-345-6789" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 focus:border-emerald-700 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Message to Landlord <span class="text-gray-300 normal-case font-medium">(optional)</span></label>
                        <textarea rows="3" name="message" placeholder="Why are you a great fit for this property?" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 focus:border-emerald-700 transition-all resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="button" @click="leaseModalOpen = false" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                        <button type="button" @click="step = 2" class="flex-1 py-3 bg-emerald-700 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-800 active:scale-95 transition-all flex items-center justify-center gap-2">
                            Next <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Lease Step 2 --}}
                <div x-show="step === 2" class="p-6 space-y-4">
                    <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100 mb-4">
                        <p class="text-xs text-emerald-800 font-bold leading-relaxed">
                            You are applying to lease <span class="font-black" x-text="property"></span>. If approved, staff will contact you to finalize the agreement.
                        </p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Preferred Move-in Date</label>
                        <input type="date" name="preferred_start_date" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 focus:border-emerald-700 transition-all">
                    </div>
                    {{-- FIX: hidden input inside Step 2 so Alpine :value binding is evaluated --}}
                    <input type="hidden" name="propertyno" :value="propertyId">
                    <div class="flex gap-3 pt-2 mt-2">
                        <button type="button" @click="step = 1" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg> Back
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-emerald-700 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-800 active:scale-95 transition-all flex items-center justify-center gap-2">
                            Submit Application
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>


    {{-- ===== WELCOME HEADER ===== --}}
    <div class="bg-gradient-to-r from-[#853953] to-[#5d273a]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-200 text-xs font-black uppercase tracking-[0.2em] mb-1">{{ now()->format('l, F j, Y') }}</p>
                    <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                        Welcome back, <span class="text-pink-200">{{ Auth::user()->name }}!</span>
                    </h1>
                    <p class="text-pink-100/70 text-sm font-medium mt-2">Here's what's available for you in Cagayan de Oro today.</p>
                </div>
                <div class="hidden sm:flex w-16 h-16 rounded-2xl bg-white/20 items-center justify-center text-white text-2xl font-black shadow-inner">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-bold flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="px-5 py-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-bold shadow-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="px-5 py-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-bold shadow-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ===== STAT CARDS (real data) ===== --}}
    <div class="bg-white border-b border-gray-100 shadow-sm mt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="bg-[#F3F4F6] rounded-2xl p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-[#853953]/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#853953]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Active Lease</p>
                        <p class="text-2xl font-black text-gray-900 leading-none mt-1">{{ $activeLeaseCount }}</p>
                        <p class="text-[10px] {{ $activeLeaseCount > 0 ? 'text-emerald-500' : 'text-gray-400' }} font-bold mt-0.5">
                            {{ $activeLeaseCount > 0 ? '● Active' : '● None' }}
                        </p>
                    </div>
                </div>

                <div class="bg-[#F3F4F6] rounded-2xl p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">My Viewings</p>
                        <p class="text-2xl font-black text-gray-900 leading-none mt-1">{{ $viewingsCount }}</p>
                        <p class="text-[10px] text-blue-500 font-bold mt-0.5">● Scheduled</p>
                    </div>
                </div>

                <div class="bg-[#F3F4F6] rounded-2xl p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Available</p>
                        <p class="text-2xl font-black text-gray-900 leading-none mt-1">{{ $availableCount }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-0.5">● Properties</p>
                    </div>
                </div>

                <div class="bg-[#F3F4F6] rounded-2xl p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Inspections</p>
                        <p class="text-2xl font-black text-gray-900 leading-none mt-1">—</p>
                        <p class="text-[10px] text-amber-500 font-bold mt-0.5">● Last 6 months</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== MAIN CONTENT ===== --}}
    {{-- FIX: removed inner x-data — search/type/matches now live in the outer x-data wrapper above --}}
    <div class="py-10 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($featured)
            {{-- ===== FEATURED PROPERTY ===== --}}
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-[#853953] rounded-full inline-block"></span>
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Featured Property</h2>
                </div>

                <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 hover:border-pink-100 transition-all duration-300">
                    <div class="flex flex-col lg:flex-row">
                        <div class="relative lg:w-1/2 aspect-[4/3] overflow-hidden">
                            @if($featured->main_image)
                                <img src="{{ asset('storage/' . $featured->main_image) }}" alt="{{ $featured->street }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#853953]/20 to-[#5d273a]/20 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-[#853953]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="bg-[#853953] text-white text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-widest">Featured</span>
                                <span class="bg-black/40 backdrop-blur-sm text-white text-[10px] font-black px-3 py-1.5 rounded-lg">{{ $featured->propertyno }}</span>
                            </div>
                            <div class="absolute bottom-4 left-4">
                                <span class="bg-emerald-500 text-white text-[10px] font-black px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                    Available for Rent
                                </span>
                            </div>
                        </div>

                        <div class="lg:w-1/2 p-8 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between mb-4">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-[#853953] bg-pink-50 px-2.5 py-1 rounded-lg">{{ $featured->property_type }}</span>
                                    <div class="text-right shrink-0 ml-4">
                                        <p class="text-3xl font-black text-[#853953] leading-none">&#8369;{{ number_format($featured->monthly_rate, 0) }}</p>
                                        <p class="text-xs text-gray-400 font-semibold">/ month</p>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <h3 class="text-xl font-black text-gray-900 tracking-tight leading-snug">{{ $featured->street }}</h3>
                                    <p class="text-xs text-gray-400 font-bold mt-0.5">{{ $featured->area }}, {{ $featured->city }} {{ $featured->postcode }}</p>
                                </div>

                                <div class="grid grid-cols-3 gap-3 mb-5">
                                    <div class="bg-pink-50 rounded-xl p-3 text-center">
                                        <svg class="w-5 h-5 text-[#853953] mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        <p class="text-xs font-black text-[#853953]">{{ $featured->no_of_rooms }} Rooms</p>
                                    </div>
                                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                                        <svg class="w-5 h-5 text-blue-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <p class="text-xs font-black text-blue-500">Furnished</p>
                                    </div>
                                    <div class="bg-emerald-50 rounded-xl p-3 text-center">
                                        <svg class="w-5 h-5 text-emerald-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <p class="text-xs font-black text-emerald-500">Inspected</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button @click="openViewingModal('{{ $featured->street }}', '{{ $featured->propertyno }}', '{{ $featured->property_type }}')"
                                        class="w-full py-4 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl font-black text-sm uppercase tracking-widest hover:border-[#853953] hover:text-[#853953] hover:bg-pink-50 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Book Viewing
                                </button>

                                <button @click="openLeaseModal('{{ $featured->street }}', '{{ $featured->propertyno }}', '{{ $featured->property_type }}')"
                                        class="w-full py-4 bg-[#853953] text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all shadow-md shadow-pink-100 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Apply to Lease
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- SEARCH + FILTER --}}
            <div class="mb-5 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <div class="relative flex-1 max-w-md">
                    <input type="text" x-model="search" placeholder="Search by street, area or postcode..."
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="flex gap-2">
                    <button @click="type = 'all'" :class="type === 'all' ? 'bg-[#853953] text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all">All</button>
                    <button @click="type = 'flat'" :class="type === 'flat' ? 'bg-[#853953] text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-pink-50 hover:text-[#853953] hover:border-pink-100 transition-all">Flats</button>
                    <button @click="type = 'house'" :class="type === 'house' ? 'bg-[#853953] text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-pink-50 hover:text-[#853953] hover:border-pink-100 transition-all">Houses</button>
                </div>
            </div>

            {{-- Filter Section --}}
            <form action="{{ route('home') }}" method="GET" class="bg-white p-6 rounded-2xl shadow-sm mb-8 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="text-[10px] font-black uppercase text-gray-400">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" class="w-full rounded-xl border-gray-200">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-gray-400">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" class="w-full rounded-xl border-gray-200">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-gray-400">Min Rooms</label>
                    <input type="number" name="rooms" value="{{ request('rooms') }}" class="w-full rounded-xl border-gray-200">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-[#853953] text-white px-6 py-2.5 rounded-xl font-bold text-sm">Filter</button>
                    <a href="{{ route('home') }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-xl font-bold text-sm">Reset</a>
                </div>
            </form>

            {{-- MORE PROPERTIES --}}
            @if($rest->isNotEmpty())
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1 h-5 bg-[#853953] rounded-full inline-block"></span>
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">More Properties</h2>
                <span class="text-xs font-bold text-gray-400 ml-1">— {{ $rest->count() }} available</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($rest as $property)
                {{-- FIX: removed x-cloak so cards are visible on load; x-show handles filtering --}}
                <div x-show="matches('{{ addslashes($property->street) }}', '{{ addslashes($property->area) }}', '{{ addslashes($property->city) }}', '{{ addslashes($property->postcode) }}', '{{ $property->property_type }}')"
                     class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100 hover:border-pink-100">
                    <div class="relative h-48 overflow-hidden">
                        @if($property->main_image)
                            <img src="{{ asset('storage/' . $property->main_image) }}" alt="{{ $property->street }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#853953]/10 to-[#5d273a]/10 flex items-center justify-center">
                                <svg class="w-12 h-12 text-[#853953]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                        @endif
                        <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-xl shadow-sm">
                            <p class="text-[#853953] font-black text-sm leading-none">&#8369;{{ number_format($property->monthly_rate, 0) }}<span class="text-[10px] text-gray-400 font-semibold">/mo</span></p>
                        </div>
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <span class="bg-black/40 backdrop-blur-sm text-white text-[10px] font-black px-2.5 py-1 rounded-lg">{{ $property->propertyno }}</span>
                            <span class="bg-[#853953]/80 backdrop-blur-sm text-white text-[10px] font-black px-2.5 py-1 rounded-lg">{{ $property->property_type }}</span>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col justify-between h-[calc(100%-12rem)]">
                        <div>
                            <div class="mb-3">
                                <h3 class="text-sm font-black text-gray-900 group-hover:text-[#853953] transition-colors tracking-tight">{{ $property->street }}</h3>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-xs text-gray-400 font-bold">{{ $property->area }}, {{ $property->city }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex items-center gap-1.5 bg-pink-50 text-[#853953] px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span class="text-xs font-black">{{ $property->no_of_rooms }} Rooms</span>
                                </div>
                                <div class="flex items-center gap-1.5 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-black">Available</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-auto">
                            <button @click="openViewingModal('{{ $property->street }}', '{{ $property->propertyno }}', '{{ $property->property_type }}')"
                                    class="w-full py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-black text-xs uppercase tracking-widest hover:border-[#853953] hover:text-[#853953] hover:bg-pink-50 active:scale-95 transition-all shadow-sm">
                                View
                            </button>
                            <button @click="openLeaseModal('{{ $property->street }}', '{{ $property->propertyno }}', '{{ $property->property_type }}')"
                                    class="w-full py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all shadow-sm">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</div>{{-- end x-data wrapper --}}
</x-app-layout>