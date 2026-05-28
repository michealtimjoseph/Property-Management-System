<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Register Owner</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Add a new property owner to the DreamHome system.</p>
                </div>
                <a href="{{ route('staff.renters.index', ['type' => 'owners']) }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Back to List
                </a>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                <form action="{{ route('staff.owners.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Owner ID</label>
                            <input type="text" name="ownerid" value="{{ $autoOwnerId }}" readonly class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm font-black text-[#853953] shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Telephone</label>
                            {{-- Ensure name="contact" --}}
                            <input type="text" name="contact" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">First Name</label>
                            <input type="text" name="firstname" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Last Name</label>
                            <input type="text" name="lastname" required class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Address</label>
                            <textarea name="address" required rows="3" class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-[#853953]"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-[#853953] text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-pink-900 transition-all shadow-lg">Register Owner</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>