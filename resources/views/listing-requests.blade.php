<x-app-layout>
<div x-data="{ formOpen: false }" class="py-10 bg-[#F3F4F6] min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">List my Property</h1>
                <p class="text-sm font-bold text-[#853953] mt-1 uppercase tracking-widest">Submit your property to be listed on DreamHome</p>
            </div>
            <button @click="formOpen = true"
                    class="inline-flex items-center px-5 py-2.5 bg-[#853953] text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-[#6e2e44] transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Submit a Property
            </button>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-5 py-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-bold">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 px-5 py-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-bold space-y-1">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        {{-- How it works banner --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-8">
            <p class="text-[10px] font-black text-[#853953] uppercase tracking-widest mb-3">How it works</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-[#853953] text-white text-xs font-black flex items-center justify-center shrink-0">1</div>
                    <div>
                        <p class="text-sm font-black text-slate-800">Submit your property</p>
                        <p class="text-xs text-slate-400 font-bold mt-0.5">Fill in the details and upload a photo</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-500 text-xs font-black flex items-center justify-center shrink-0">2</div>
                    <div>
                        <p class="text-sm font-black text-slate-800">Staff reviews it</p>
                        <p class="text-xs text-slate-400 font-bold mt-0.5">Our team verifies your submission</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-500 text-xs font-black flex items-center justify-center shrink-0">3</div>
                    <div>
                        <p class="text-sm font-black text-slate-800">Goes live for renters</p>
                        <p class="text-xs text-slate-400 font-bold mt-0.5">Approved properties are listed publicly</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Request list --}}
        @if($requests->isEmpty())
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 py-20 text-center">
                <svg class="w-14 h-14 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <p class="text-sm font-black text-slate-400">No listing requests yet.</p>
                <p class="text-xs text-slate-400 font-bold mt-1">Click <span class="text-[#853953]">Submit a Property</span> above to get started.</p>
            </div>
        @else
            <div class="mb-4 flex items-center gap-2">
                <span class="w-1 h-5 bg-[#853953] rounded-full inline-block"></span>
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">My Submissions</h2>
                <span class="text-xs font-bold text-gray-400 ml-1">— {{ $requests->count() }} total</span>
            </div>
            <div class="space-y-4">
                @foreach($requests as $req)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row">

                        {{-- Photo --}}
                        <div class="sm:w-40 h-32 sm:h-auto shrink-0 overflow-hidden bg-gradient-to-br from-[#853953]/10 to-[#5d273a]/10 flex items-center justify-center">
                            @if($req->main_image)
                                <img src="{{ asset('storage/' . $req->main_image) }}" alt="{{ $req->street }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-[#853953]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $req->requestid }}</span>
                                    <span class="text-slate-300 text-xs">·</span>
                                    <span class="text-[10px] text-slate-400 font-bold">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</span>
                                </div>
                                <h3 class="text-base font-black text-slate-900 tracking-tight">{{ $req->street }}</h3>
                                <p class="text-xs text-slate-400 font-bold mt-0.5">{{ $req->area }}, {{ $req->city }}, {{ $req->postcode }}</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="text-[10px] font-black px-2.5 py-1 bg-pink-50 text-[#853953] rounded-lg">{{ $req->property_type }}</span>
                                    <span class="text-[10px] font-black px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">{{ $req->no_of_rooms }} rooms</span>
                                    <span class="text-[10px] font-black px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg">₱{{ number_format($req->monthly_rate, 0) }}/mo</span>
                                </div>
                                @if($req->message)
                                    <p class="text-[11px] text-slate-400 italic mt-1.5">"{{ $req->message }}"</p>
                                @endif
                            </div>

                            {{-- Status badge --}}
                            <div class="text-center shrink-0">
                                @if($req->status === 'Pending')
                                    <span class="inline-block px-4 py-2 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-xl">Pending Review</span>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1.5">Waiting for staff</p>
                                @elseif($req->status === 'Approved')
                                    <span class="inline-block px-4 py-2 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-xl">Approved</span>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1.5">Property is being listed</p>
                                @else
                                    <span class="inline-block px-4 py-2 bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-xl">Rejected</span>
                                    @if($req->reviewed_by_name)
                                        <p class="text-[10px] text-slate-400 font-bold mt-1.5">by {{ $req->reviewed_by_name }}</p>
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

    {{-- ===== SUBMIT PROPERTY MODAL ===== --}}
    <div x-show="formOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="formOpen = false"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"></div>

    <div x-show="formOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden my-auto" @click.stop>

            {{-- Modal header --}}
            <div class="bg-gradient-to-r from-[#853953] to-[#5d273a] px-6 py-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-black text-pink-200 uppercase tracking-[0.2em]">New Listing Request</p>
                        <h3 class="text-white font-black text-base tracking-tight mt-0.5">Submit Your Property</h3>
                        <p class="text-pink-200/70 text-[11px] font-bold mt-0.5">Fill in the details and our team will review it</p>
                    </div>
                    <button @click="formOpen = false" class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('listing-requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                @csrf

                {{-- Address --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Street Address <span class="text-rose-400">*</span></label>
                        <input type="text" name="street" value="{{ old('street') }}" required placeholder="e.g. 12 Rosario St."
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Area / Barangay <span class="text-rose-400">*</span></label>
                        <input type="text" name="area" value="{{ old('area') }}" required placeholder="e.g. Uptown"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">City <span class="text-rose-400">*</span></label>
                        <input type="text" name="city" value="{{ old('city') }}" required placeholder="e.g. Cagayan de Oro"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Postcode <span class="text-rose-400">*</span></label>
                        <input type="text" name="postcode" value="{{ old('postcode') }}" required placeholder="e.g. 9000"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Property Type <span class="text-rose-400">*</span></label>
                        <select name="property_type" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                            <option value="">Select type...</option>
                            <option value="Flat"  {{ old('property_type') === 'Flat'  ? 'selected' : '' }}>Flat</option>
                            <option value="House" {{ old('property_type') === 'House' ? 'selected' : '' }}>House</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">No. of Rooms <span class="text-rose-400">*</span></label>
                        <input type="number" name="no_of_rooms" value="{{ old('no_of_rooms') }}" required min="1" max="20" placeholder="e.g. 3"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Monthly Rate (₱) <span class="text-rose-400">*</span></label>
                        <input type="number" name="monthly_rate" value="{{ old('monthly_rate') }}" required min="1" placeholder="e.g. 15000"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                    </div>
                </div>

                {{-- Photo upload --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Property Photo <span class="text-rose-400">*</span></label>
                    <input type="file" name="main_image" accept="image/*" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-[#853953] file:text-white hover:file:bg-[#6e2e44]">
                    <p class="text-[10px] text-slate-400 font-bold mt-1">JPG, PNG or WEBP — max 2MB</p>
                </div>

                {{-- Message --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Message to Staff <span class="text-gray-300 normal-case font-medium">(optional)</span></label>
                    <textarea rows="3" name="message" placeholder="e.g. Newly renovated unit, available starting June..."
                              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all resize-none">{{ old('message') }}</textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="formOpen = false"
                            class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all">
                        Submit Request
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
</x-app-layout>