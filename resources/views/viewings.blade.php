<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-black text-gray-900 mb-6">My Scheduled Viewings</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($viewings as $viewing)
                    <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-white flex flex-col gap-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase">Viewing ID: {{ $viewing->viewingid }}</p>
                                <h3 class="text-lg font-black text-gray-900 tracking-tight">{{ $viewing->street }}</h3>
                            </div>
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase 
                                {{ $viewing->status == 'Completed' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                {{ $viewing->status ?? 'Pending' }}
                            </span>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 font-bold uppercase">Date:</span>
                                <span class="font-black text-gray-800">{{ \Carbon\Carbon::parse($viewing->view_date)->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 font-bold uppercase">Assigned Staff:</span>
                                <span class="font-bold text-[#853953]">
                                    {{ $viewing->staff_fname ? $viewing->staff_fname . ' ' . $viewing->staff_lname : 'Awaiting Assignment' }}
                                </span>
                            </div>
                        </div>

                        @if($viewing->comment)
                            <div class="p-3 bg-pink-50 rounded-xl border border-pink-100">
                                <p class="text-[8px] font-black text-[#853953] uppercase mb-1">Staff Feedback</p>
                                <p class="text-xs italic text-gray-700">"{{ $viewing->comment }}"</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 text-center py-20 bg-white rounded-[2.5rem]">
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No viewings found</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-block text-[#853953] font-black text-xs uppercase underline">Book a Viewing</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>