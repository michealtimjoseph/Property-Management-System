<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <a href="{{ route('staff.renters.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Cancel and Back
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('staff.renters.update', $renter->renterno) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-[#853953] flex items-center justify-center text-white text-xl font-bold">
                                {{ substr($renter->firstname, 0, 1) }}{{ substr($renter->lastname, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Edit Renter Profile</h3>
                                <p class="text-[#853953] font-medium">ID: {{ $renter->renterno }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name Fields -->
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">First Name</label>
                            <input type="text" name="firstname" value="{{ old('firstname', $renter->firstname) }}" class="w-full rounded-lg border-gray-300 focus:border-[#853953] focus:ring-[#853953]">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">Last Name</label>
                            <input type="text" name="lastname" value="{{ old('lastname', $renter->lastname) }}" class="w-full rounded-lg border-gray-300 focus:border-[#853953] focus:ring-[#853953]">
                        </div>

                        <!-- Contact -->
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $renter->phone) }}" class="w-full rounded-lg border-gray-300 focus:border-[#853953] focus:ring-[#853953]">
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-bold text-gray-700">Address</label>
                            <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 focus:border-[#853953] focus:ring-[#853953]">{{ old('address', $renter->address) }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">Sex</label>
                            <select name="sex" class="w-full rounded-lg border-gray-300 focus:border-[#853953] focus:ring-[#853953]">
                                <option value="M" {{ old('sex', $renter->sex) == 'M' ? 'selected' : '' }}>Male</option>
                                <option value="F" {{ old('sex', $renter->sex) == 'F' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">Preferred Property Type</label>
                            <select name="preferred_property_type" class="w-full rounded-lg border-gray-300 focus:border-[#853953] focus:ring-[#853953]">
                                <option value="Flat" {{ $renter->preferred_property_type == 'Flat' ? 'selected' : '' }}>Flat</option>
                                <option value="House" {{ $renter->preferred_property_type == 'House' ? 'selected' : '' }}>House</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">Max Rent (₱)</label>
                            <input type="number" name="max_rent" value="{{ old('max_rent', $renter->max_rent) }}" class="w-full rounded-lg border-gray-300 focus:border-[#853953] focus:ring-[#853953]">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">Branch</label>
                            <select name="branchno" class="w-full rounded-lg border-gray-300">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branchno }}" {{ $renter->branchno == $branch->branchno ? 'selected' : '' }}>
                                        {{ $branch->city }} ({{ $branch->branchno }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">Witness Staff</label>
                            <select name="witness_staffno" class="w-full rounded-lg border-gray-300">
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->staffno }}" {{ $renter->witness_staffno == $staff->staffno ? 'selected' : '' }}>
                                        {{ $staff->firstname }} {{ $staff->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-gray-700">Comments</label>
                            <textarea name="comment" rows="3" class="w-full rounded-lg border-gray-300">{{ old('comment', $renter->comment) }}</textarea>
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-[#853953] hover:bg-[#6d2e44] text-white px-6 py-2 rounded-lg font-bold transition">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>