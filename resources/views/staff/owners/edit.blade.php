<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Modify Owner</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Update details for Owner: <span class="text-[#853953] font-bold">{{ $owner->ownerid }}</span></p>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                <form action="{{ route('staff.owners.update', $owner->ownerid) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">First Name</label>
                            <input type="text" name="firstname" value="{{ $owner->firstname }}" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Last Name</label>
                            <input type="text" name="lastname" value="{{ $owner->lastname }}" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Telephone</label>
                            <input type="text" name="contact" value="{{ $owner->contact }}" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Address</label>
                            <textarea name="address" required rows="3" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]">{{ $owner->address }}</textarea>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('staff.renters.index', ['type' => 'owners']) }}" class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-xl font-black text-xs uppercase tracking-widest text-center hover:bg-gray-200 transition-all">Cancel</a>
                        <button type="submit" class="flex-1 py-4 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-pink-900 transition-all shadow-lg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>