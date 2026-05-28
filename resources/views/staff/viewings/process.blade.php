{{-- resources/views/staff/viewings/process.blade.php --}}
<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-pink-50">
                
                <div class="mb-8">
                    <span class="text-[10px] font-black text-[#853953] uppercase tracking-[0.2em] bg-pink-50 px-3 py-1 rounded-lg">Action Required</span>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tighter mt-3">Finalize Viewing</h2>
                    <p class="text-sm text-gray-500 font-medium">Assign a staff guide to confirm the request for <b>{{ $requestData->street }}</b>.</p>
                </div>

                <form action="{{ route('staff.viewings.store') }}" method="POST" class="space-y-8">
                    @csrf
                    {{-- Hidden fields to preserve the requested data --}}
                    <input type="hidden" name="viewingid" value="{{ trim($requestData->viewingid) }}">
                    <input type="hidden" name="propertyno" value="{{ trim($requestData->propertyno) }}">
                    <input type="hidden" name="renterno" value="{{ trim($requestData->renterno) }}">

                    {{-- READ-ONLY INFO CARDS --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Client</p>
                            <p class="text-sm font-bold text-gray-800">{{ $requestData->firstname }} {{ $requestData->lastname }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Requested Date</p>
                            <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($requestData->view_date)->format('F d, Y') }}</p>
                        </div>
                    </div>

                    {{-- ACTIVE ASSIGNMENT FIELD --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Assign Staff Guide</label>
                            <select name="staffno" class="w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold focus:border-[#853953] focus:ring-0 transition-all" required>
                                <option value="" disabled selected>Select an available staff...</option>
                                @foreach($staffList as $staff)
                                    <option value="{{ trim($staff->staffno) }}">{{ $staff->firstname }} {{ $staff->lastname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Adjust Date (Optional)</label>
                            <input type="date" name="view_date" value="{{ \Carbon\Carbon::parse($requestData->view_date)->format('Y-m-d') }}" 
                                class="w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold focus:border-[#853953] focus:ring-0 transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Staff Instructions</label>
                            <textarea name="comment" rows="3" class="w-full bg-white border-2 border-gray-100 rounded-2xl py-4 px-5 text-sm font-bold focus:border-[#853953] focus:ring-0 transition-all" placeholder="Any special notes for the guide?">{{ $requestData->comment }}</textarea>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('staff.viewings') }}" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-[10px] uppercase tracking-widest text-center hover:bg-gray-200 transition-all">Cancel</a>
                        <button type="submit" class="flex-[2] py-4 bg-[#853953] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-pink-100 hover:bg-pink-900 transition-all">
                            Confirm Assignment
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>