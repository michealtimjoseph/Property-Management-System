<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                      <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Property Modification</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Updating details for the selected property.</p>
                </div>
                <a href="{{ route('staff.properties.properties') }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Back to Properties
                </a>
            </div>

            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-white">
                <form method="POST" action="{{ route('staff.properties.update', $property->propertyno) }}" enctype="multipart/form-data" class="space-y-10">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="old_image" value="{{ $property->main_image }}">

                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Property Image
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <div class="relative h-48 rounded-2xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50">
                                @if($property->main_image)
                                    <img src="{{ asset('storage/' . $property->main_image) }}" class="w-full h-full object-cover" id="currentPreview">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400 text-xs font-bold uppercase">No Image</div>
                                @endif
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Replace Main Image</label>
                                <input type="file" name="main_image" accept="image/*" 
                                    class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-xs font-bold">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Specifications
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Street Address</label>
                                <input type="text" name="street" value="{{ old('street', $property->street) }}" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Area</label>
                                <input type="text" name="area" value="{{ old('area', $property->area) }}" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city', $property->city) }}" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Monthly Rate (PHP)</label>
                                <input type="number" name="monthly_rate" value="{{ old('monthly_rate', $property->monthly_rate) }}" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Postcode</label>
                                <input type="text" name="postcode" value="{{ old('postcode', $property->postcode) }}" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Property Type</label>
                                <select name="property_type" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                                    <option value="House" {{ $property->property_type == 'House' ? 'selected' : '' }}>House</option>
                                    <option value="Flat" {{ $property->property_type == 'Flat' ? 'selected' : '' }}>Flat</option>
                                    <option value="Condo" {{ $property->property_type == 'Condo' ? 'selected' : '' }}>Condo</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">No. of Rooms</label>
                                <input type="number" name="no_of_rooms" value="{{ old('no_of_rooms', $property->no_of_rooms) }}" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Assigned Staff</label>
                                <select name="staffno" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold">
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->staffno }}" {{ $property->staffno == $staff->staffno ? 'selected' : '' }}>
                                            {{ $staff->firstname }} {{ $staff->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Assign Branch</label>
                                <select name="branchno" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branchno }}" {{ $property->branchno == $branch->branchno ? 'selected' : '' }}>
                                            {{ $branch->city }} ({{ $branch->branchno }})
                                        </option>
                                    @endforeach                                
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Property Owner</label>
                                <select name="ownerno" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold">
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->ownerid }}" {{ $property->ownerno == $owner->ownerid ? 'selected' : '' }}>
                                            {{ $owner->firstname }} {{ $owner->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-[#853953] transition-all">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>