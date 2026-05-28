<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
                      <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Renter Registration</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Adding a new renter to the DreamHome CDO database.</p>
                </div>
                <a href="{{ route('staff.renters.index') }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Back to Renters
                </a>
            </div>

            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-white">
                <form method="POST" action="{{ route('staff.renters.store') }}" class="space-y-10">
                    @csrf

                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Client Identification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Renter No. (Auto)</label>
                                <input type="text" name="renterno" value="{{ $autoRenterNo }}" readonly
                                    class="w-full bg-gray-100 border-none rounded-2xl py-4 px-5 text-sm font-black text-[#853953] cursor-not-allowed shadow-inner">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">First Name</label>
                                <input type="text" name="firstname" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Last Name</label>
                                <input type="text" name="lastname" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Contact & Preferences
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Current Address</label>
                                <input type="text" name="address" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Phone Number</label>
                                <input type="text" name="phone" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Preferred Property Type</label>
                                <select name="preferred_property_type" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                                    <option value="House">House</option>
                                    <option value="Flat">Flat</option>
                                    <option value="Condo">Condo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Maximum Rent (PHP)</label>
                                <input type="number" name="max_rent" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Comment/Notes</label>
                                <input type="text" name="comment" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Witnessing Staff</label>
                                <select name="witness_staffno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold">
                                    <option value="" disabled selected>Select staff</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->staffno }}">{{ $staff->firstname }} {{ $staff->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Assigned Branch</label>
                                <select name="branchno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold">
                                    <option value="" disabled selected>Select branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branchno }}">{{ $branch->city }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-[#853953] transition-all">
                        Register Renter
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>