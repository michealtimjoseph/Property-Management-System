<x-app-layout>
    @php
        // Identify if the staff member is "Regular" based on your Controller logic
        $staff = Auth::guard('staff')->user();
        $isRegular = $staff && strtolower($staff->position) === 'regular';
        
        // Set the back route dynamically
        $backRoute = $isRegular ? route('staff.dashboard') : route('staff.properties.properties');
    @endphp

    <div class="py-12 bg-[#F3F4F6] min-h-screen font-sans antialiased">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Property Information</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Detailed information about the selected property.</p>
                </div>
                <a href="{{ $backRoute }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    {{ $isRegular ? __('Back to Dashboard') : __('Back to Properties') }}
                </a>
            </div>
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-[#853953] text-white text-[9px] font-black uppercase tracking-widest rounded-lg">
                            {{ $property->propertyno }}
                        </span>
                        @if($isRegular)
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                Assigned To You
                            </span>
                        @endif
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 tracking-tighter">{{ $property->street }}</h1>
                    <p class="text-lg text-gray-500 font-medium">{{ $property->area }}, {{ $property->city }} {{ $property->postcode }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Media Gallery Section --}}
                <div class="lg:col-span-2">
                    <div class="bg-white p-3 rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                        @if($property->main_image)
                            <img src="{{ asset('storage/' . $property->main_image) }}" 
                                 alt="Property Hero Image" 
                                 class="w-full h-[600px] object-cover rounded-[2rem] shadow-inner">
                        @else
                            <div class="w-full h-[600px] bg-slate-100 flex flex-col items-center justify-center rounded-[2rem]">
                                <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-slate-400 font-black uppercase text-[10px] tracking-widest">No Image Available</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Information Sidebar --}}
                <div class="space-y-6">
                    <div class="bg-[#853953] p-6 rounded-3xl text-white shadow-xl shadow-[#853953]/10 flex items-start space-x-4">
                        <div class="bg-white/20 p-2 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-black text-[10px] uppercase tracking-widest opacity-70">Managing Branch</h4>
                            <p class="text-md font-bold">{{ $property->branch_info }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200/60">
                        <div class="mb-8">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Lease Rate</span>
                            <div class="flex items-baseline gap-1 mt-1">
                                <h3 class="text-3xl font-black text-gray-900 tracking-tighter">
                                    ₱{{ number_format($property->monthly_rate, 2) }}
                                </h3>
                                <span class="text-gray-400 font-bold text-sm">/mo</span>
                            </div>
                        </div>
                        
                        <div class="space-y-5 mb-8">
                            <div class="flex justify-between items-center py-3 border-b border-slate-50">
                                <span class="text-gray-400 text-xs font-black uppercase tracking-wider">Type</span>
                                <span class="text-[#853953] font-black bg-[#853953]/5 px-3 py-1 rounded-lg text-[10px] uppercase tracking-wider border border-[#853953]/10">
                                    {{ $property->property_type }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-slate-50">
                                <span class="text-gray-400 text-xs font-black uppercase tracking-wider">Rooms</span>
                                <span class="text-gray-900 font-black text-xs uppercase">{{ $property->no_of_rooms }} Bedrooms</span>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 space-y-4">
                            <div>
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Staff In-Charge</p>
                                <p class="text-gray-900 font-black text-sm">{{ $property->staff_name }}</p>
                            </div>
                            <div class="pt-4 border-t border-slate-200/60">
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Registered Owner</p>
                                <p class="text-gray-700 text-sm font-bold">{{ $property->owner_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>