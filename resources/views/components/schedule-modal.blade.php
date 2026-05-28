@props(['properties', 'staffMembers'])

<template x-teleport="body">
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[99] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showModal = false" class="bg-white rounded-[2.5rem] p-8 w-full max-w-md mx-4 shadow-2xl border border-gray-100"
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Schedule</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">New Inspection</p>
                </div>
                <button @click="showModal = false" type="button" class="bg-gray-50 p-2.5 rounded-2xl text-gray-400 hover:text-[#853953] hover:bg-pink-50 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Proper Laravel Form linking to your new route -->
            <form action="{{ route('staff.inspections.store') }}" method="POST" class="space-y-5">
                @csrf 

                <!-- Property Dropdown -->
                <div>
                    <label for="property_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2 pl-2">Property No.</label>
                    <div class="relative">
                        <select name="property_id" id="property_id" required class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm text-gray-700 font-medium focus:ring-2 focus:ring-[#853953]/20 focus:bg-white transition-all appearance-none outline-none">
    <option value="" disabled selected>Select Property...</option>
    @foreach($properties as $property)
        <option value="{{ $property->propertyno }}" @selected(old('property_id') == $property->propertyno)>
            {{ $property->street }}, {{ $property->city }} ({{ $property->propertyno }})
        </option>
    @endforeach
</select>
                    </div>
                    @error('property_id') <span class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</span> @enderror
                </div>
                
                <!-- Staff Dropdown -->
                <div>
                    <label for="staff_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2 pl-2">Assigned Staff</label>
                    <div class="relative">
                        <select name="staff_id" id="staff_id" required class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm text-gray-700 font-medium focus:ring-2 focus:ring-[#853953]/20 focus:bg-white transition-all appearance-none outline-none">
                            <option value="" disabled selected>Select Staff...</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->staffno }}" @selected(old('staff_id') == $staff->staffno)>
                                    {{ $staff->firstname }} {{ $staff->lastname }} ({{ $staff->staffno }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('staff_id') <span class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</span> @enderror
                </div>

                <!-- Date Input -->
                <div>
                    <label for="scheduled_date" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2 pl-2">Inspection Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" required value="{{ old('scheduled_date') }}" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm text-gray-700 font-medium focus:ring-2 focus:ring-[#853953]/20 focus:bg-white transition-all outline-none">
                    @error('scheduled_date') <span class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Action -->
                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#853953] text-white py-4 rounded-3xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-[#853953]/20 hover:bg-pink-900 transition-all flex items-center justify-center gap-2">
                        Confirm Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>