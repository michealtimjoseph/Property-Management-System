<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
                      <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Property Registration</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Adding a new real estate asset to the DreamHome CDO inventory.</p>
                </div>
                <a href="{{ route('staff.properties.properties') }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Back to Properties
                </a>
            </div>

            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-white">
                <form method="POST" action="{{ route('staff.properties.store') }}" enctype="multipart/form-data" class="space-y-10" onsubmit="confirmAction(event, this, 'Add Property')">
                    @csrf

                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Management Assignment
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Property No.</label>
                                <input type="text" name="propertyno" value="{{ $autoPropertyNo }}" readonly
                                    class="w-full bg-gray-100 border-none rounded-2xl py-4 px-5 text-sm font-black text-[#853953] cursor-not-allowed shadow-inner">                            </div>
                            
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Assign Branch</label>
                                <select name="branchno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 focus:ring-2 focus:ring-[#853953] text-sm font-bold appearance-none">
                                    <option value="" disabled selected>Select branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branchno }}">{{ $branch->city }} ({{ $branch->branchno }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Managing Staff</label>
                                <select name="staffno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 focus:ring-2 focus:ring-[#853953] text-sm font-bold appearance-none">
                                    <option value="" disabled selected>Select staff</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->staffno }}">{{ $staff->firstname }} {{ $staff->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Visual Assets
                        </h3>
                        <div class="w-full">
                            <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Main Property Image</label>
                            <input type="file" name="main_image" accept="image/*" required
                                class="w-full bg-gray-50 border-dashed border-2 border-gray-200 rounded-2xl py-8 px-5 text-sm font-bold text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-[#853953] file:text-white hover:file:bg-gray-900 cursor-pointer">
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Specifications
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Street Address</label>
                                <input type="text" name="street" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Area</label>
                                <input type="text" name="area" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">City</label>
                                <input type="text" name="city" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Property Type</label>
                                <select name="property_type" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 focus:ring-2 focus:ring-[#853953] text-sm font-bold">
                                    <option value="House">House</option>
                                    <option value="Flat">Flat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">No. of Rooms</label>
                                <input type="number" name="no_of_rooms" required min="1" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Monthly Rate (PHP)</label>
                                <input type="number" name="monthly_rate" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Postcode</label>
                                <input type="text" name="postcode" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-50">
                        <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Property Owner</label>
                        <select name="ownerno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 focus:ring-2 focus:ring-[#853953] text-sm font-bold">
                            <option value="" disabled selected>Select owner</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->ownerid }}">{{ $owner->firstname }} {{ $owner->lastname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-[#853953] transition-all">
                            Add Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{-- SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmAction(event, form, actionName) {
            // Stop the form from submitting immediately
            event.preventDefault(); 
            
            Swal.fire({
                title: 'Confirm Registration',
                text: 'Are you sure you want to ' + actionName.toLowerCase() + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#853953', // Matches your theme color
                cancelButtonColor: '#94a3b8', // slate-400
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-3xl' // Matches your existing UI styling
                }
            }).then((result) => {
                // If the user clicks "Yes", submit the form programmatically
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>