<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm">
                <h2 class="text-2xl font-black text-gray-800 tracking-tighter mb-8">
                    {{ isset($prefilledRequest) ? 'Finalize Viewing' : 'Schedule New Viewing' }}
                </h2>

                <form action="{{ route('staff.viewings.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="viewingid" value="{{ $autoViewingId }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- 1. PREFILL PROPERTY --}}
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Property</label>
                            <select name="propertyno" class="w-full bg-gray-50 border-none rounded-2xl text-sm" required>
                                <option value="" disabled {{ !isset($prefilledRequest) ? 'selected' : '' }}>Choose Property...</option>
                                @foreach($properties as $property)
                                    <option value="{{ trim($property->propertyno) }}" 
                                        @if(isset($prefilledRequest) && trim($prefilledRequest->propertyno) == trim($property->propertyno)) selected @endif>
                                        {{ $property->street }} ({{ trim($property->propertyno) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. PREFILL STAFF (IF ASSIGNED) --}}
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Staff Guide</label>
                            <select name="staffno" class="w-full bg-gray-50 border-none rounded-2xl text-sm">
                                <option value="" {{ !isset($prefilledRequest->staffno) ? 'selected' : '' }}>Select staff...</option>
                                @foreach($staffList as $staff)
                                    <option value="{{ trim($staff->staffno) }}"
                                        @if(isset($prefilledRequest) && trim($prefilledRequest->staffno) == trim($staff->staffno)) selected @endif>
                                        {{ $staff->firstname }} {{ $staff->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 3. PREFILL RENTER / USER --}}
                    <div>
                        <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Client / Renter Name</label>
                        <select name="renterno" class="w-full bg-gray-50 border-none rounded-2xl text-sm" required>
                            <option value="" disabled {{ !isset($prefilledRequest) ? 'selected' : '' }}>Choose Client...</option>
                            <optgroup label="Official Renters">
                                @foreach($renters as $renter)
                                    <option value="{{ trim($renter->renterno) }}" 
                                        @if(isset($prefilledRequest) && trim($prefilledRequest->renterno) == trim($renter->renterno)) selected @endif>
                                        {{ $renter->firstname }} {{ $renter->lastname }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="System Users">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        @if(isset($prefilledRequest) && $prefilledRequest->renterno == $user->id) selected @endif>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>                
                    </div>

                    {{-- 4. PREFILL VIEWING DATE --}}
                    <div>
                        <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Viewing Date</label>
                        <input type="date" name="view_date" 
                            value="{{ isset($prefilledRequest) ? \Carbon\Carbon::parse($prefilledRequest->view_date)->format('Y-m-d') : '' }}" 
                            class="w-full bg-gray-50 border-none rounded-2xl text-sm" required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Instructions</label>
                        <textarea name="comment" rows="3" class="w-full bg-gray-50 border-none rounded-2xl text-sm">{{ isset($prefilledRequest) ? $prefilledRequest->comment : '' }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#853953] text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:bg-pink-900 transition-all">
                        {{ isset($prefilledRequest) ? 'Update Viewing' : 'Confirm Viewing' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>