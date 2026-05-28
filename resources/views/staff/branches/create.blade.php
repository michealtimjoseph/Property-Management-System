<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                <h1 class="text-2xl font-black text-gray-900 mb-6">Register New Branch</h1>
                <form action="{{ route('staff.branches.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Branch No</label>
                            <input type="text" name="branchno" value="{{ $autoBranchNo }}" readonly class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 font-black text-[#853953]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">City</label>
                            <input type="text" name="city" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Street</label>
                        <input type="text" name="street" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Area</label>
                            <input type="text" name="area" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Postcode</label>
                            <input type="text" name="postcode" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Phone</label>
                            <input type="text" name="phone" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Fax</label>
                            <input type="text" name="faxno" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-[#853953] text-white py-4 rounded-xl font-black text-xs uppercase hover:bg-pink-900 transition-all">Add Branch</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>