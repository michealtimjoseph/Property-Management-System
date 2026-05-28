<x-app-layout>
    <style>[x-cloak] { display: none !important; }</style>

        @php
        // Identify if the staff member is "Regular" based on your Controller logic
        $staff = Auth::guard('staff')->user();
        $isRegular = $staff && strtolower($staff->position) === 'regular';
        
        // Set the back route dynamically
        $backRoute = $isRegular ? route('staff.dashboard') : route('staff.leases.index');
    @endphp

    {{-- Added Alpine pagination states: currentPage, itemsPerPage, and totalItems --}}
    <div class="py-10 bg-[#F3F4F6] min-h-screen" 
         x-data="{ 
            showPaymentModal: false,
            currentPage: 1,
            itemsPerPage: 5,
            totalItems: {{ count($schedule) }}
         }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Navigation & Title --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Lease Information</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Detailed information about the selected lease.</p>
                </div>
                <a href="{{ $backRoute }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    {{ $isRegular ? __('Back to Dashboard') : __('Back to Leases') }}
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Renter & Property Summary Card --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="bg-[#853953] p-6 text-white relative">
                            <div class="absolute top-0 right-0 p-4 opacity-10">
                                <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path></svg>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Primary Renter</p>
                            <h2 class="text-xl font-black mt-1">{{ $lease->r_fname }} {{ $lease->r_lname }}</h2>
                            <p class="text-xs font-medium opacity-80 mt-1">{{ $lease->r_phone ?? 'No contact number' }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-[#853953]/10 rounded-lg text-[#853953]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Property Address</p>
                                    <p class="text-sm font-bold text-slate-700 mt-1">{{ $lease->street }}</p>
                                    <p class="text-xs text-slate-500">{{ $lease->area }}, {{ $lease->city }}</p>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-slate-50 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase">Monthly Rent</p>
                                    <p class="text-sm font-black text-slate-900">₱{{ number_format($lease->monthly_rent, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase">Security Deposit</p>
                                    <p class="text-sm font-black text-[#853953]">₱{{ number_format($lease->deposit, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Financial Health Card --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-2 h-2 rounded-full bg-[#853953]"></div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Financial Status</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-[10px] font-black uppercase mb-2">
                                    <span class="text-slate-400">Total Coverage</span>
                                    <span class="text-[#853953]">
                                        @if($lease->monthly_rent * $lease->duration > 0)
                                            {{ round(($lease->total_paid / ($lease->monthly_rent * $lease->duration)) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                                <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#853953] rounded-full" style="width: {{ ($lease->monthly_rent * $lease->duration > 0) ? ($lease->total_paid / ($lease->monthly_rent * $lease->duration)) * 100 : 0 }}%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Total Paid</p>
                                    <p class="text-sm font-black text-slate-900">₱{{ number_format($lease->total_paid, 2) }}</p>
                                </div>
                                <div class="bg-[#853953]/5 p-3 rounded-2xl border border-[#853953]/10">
                                    <p class="text-[9px] font-black text-[#853953]/60 uppercase">Remaining</p>
                                    <p class="text-sm font-black text-[#853953]">₱{{ number_format($lease->balance, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Payment Schedule --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex flex-col justify-between min-h-[520px]">
                        
                        <div>
                            <div class="p-6 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50/30 gap-4">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900">Master Schedule</h3>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-tighter">Month-by-Month Verification</p>
                                </div>
                                <button @click="showPaymentModal = true" 
                                        class="w-full sm:w-auto px-6 py-3 bg-[#853953] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#6e2e44] transition-all shadow-lg shadow-[#853953]/20 active:scale-95">
                                    Record Manual Payment
                                </button>
                            </div>

                            <div class="divide-y divide-slate-50">
                                @foreach($schedule as $index => $item)
                                <div class="p-6 flex items-center justify-between hover:bg-slate-50/50 transition-all"
                                     x-show="{{ $index }} >= (currentPage - 1) * itemsPerPage && {{ $index }} < currentPage * itemsPerPage"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform translate-y-1">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center {{ $item['is_paid'] ? 'bg-emerald-50 text-emerald-600' : 'bg-[#853953]/5 text-[#853953]/40' }}">
                                            <span class="text-[10px] font-black uppercase leading-none">{{ substr($item['month'], 0, 3) }}</span>
                                            <span class="text-xs font-black">{{ substr($item['month'], -2) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900">{{ $item['month'] }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Due: {{ \Carbon\Carbon::parse($item['due_date'])->format('M d, Y') }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-6">
                                        @if($item['is_paid'])
                                            <div class="text-right hidden sm:block">
                                                @if($item['payment'])
                                                    <p class="text-sm font-black text-emerald-600">₱{{ number_format($item['payment']->amount_paid, 2) }}</p>
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                                        {{ $item['payment']->payment_method }}
                                                        @if(stripos($item['payment']->notes, 'Advance') !== false)
                                                            <span class="text-[9px] text-[#853953] font-black block">(Advance Package)</span>
                                                        @endif
                                                    </p>
                                                @else
                                                    <p class="text-sm font-black text-emerald-600">₱{{ number_format($lease->monthly_rent, 2) }}</p>
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Account Balance/Credit</p>
                                                @endif
                                            </div>
                                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-100">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        @else
                                            <div class="text-right hidden sm:block">
                                                <p class="text-sm font-black text-[#853953]/40 italic">Unpaid</p>
                                            </div>
                                            <div class="w-10 h-10 rounded-full bg-white border-2 border-dashed border-[#853953]/20 flex items-center justify-center text-[#853953]/20">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- PAGINATION CONTROL PANEL FOOTER --}}
                        <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <button @click="if(currentPage > 1) currentPage--"
                                        :disabled="currentPage === 1"
                                        class="relative inline-flex items-center px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-50 transition-all">
                                    Previous
                                </button>
                                <button @click="if(currentPage * itemsPerPage < totalItems) currentPage++"
                                        :disabled="currentPage * itemsPerPage >= totalItems"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-50 transition-all">
                                    Next
                                </button>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold text-slate-500">
                                        Showing
                                        <span class="font-black text-slate-800" x-text="((currentPage - 1) * itemsPerPage) + 1"></span>
                                        to
                                        <span class="font-black text-slate-800" x-text="Math.min(currentPage * itemsPerPage, totalItems)"></span>
                                        of
                                        <span class="font-black text-slate-800" x-text="totalItems"></span>
                                        months
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-xl shadow-sm space-x-1" aria-label="Pagination">
                                        <button @click="currentPage = 1"
                                                :disabled="currentPage === 1"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-xl border border-slate-200 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 disabled:opacity-40 transition-all">
                                            <span class="sr-only">First</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M17 19l-7-7 7-7"/></svg>
                                        </button>
                                        <button @click="if(currentPage > 1) currentPage--"
                                                :disabled="currentPage === 1"
                                                class="relative inline-flex items-center px-3 py-2 border border-slate-200 bg-white text-xs font-black text-slate-600 hover:bg-slate-50 disabled:opacity-40 transition-all">
                                            Prev
                                        </button>
                                        
                                        <span class="relative inline-flex items-center px-4 py-2 border border-[#853953] bg-[#853953] text-xs font-black text-white rounded-lg" x-text="currentPage"></span>

                                        <button @click="if(currentPage * itemsPerPage < totalItems) currentPage++"
                                                :disabled="currentPage * itemsPerPage >= totalItems"
                                                class="relative inline-flex items-center px-3 py-2 border border-slate-200 bg-white text-xs font-black text-slate-600 hover:bg-slate-50 disabled:opacity-40 transition-all">
                                            Next
                                        </button>
                                        <button @click="currentPage = Math.ceil(totalItems / itemsPerPage)"
                                                :disabled="currentPage * itemsPerPage >= totalItems"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-xl border border-slate-200 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 disabled:opacity-40 transition-all">
                                            <span class="sr-only">Last</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M7 5l7 7-7 7"/></svg>
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- RECORD MANUAL PAYMENT MODAL --}}
        <div x-show="showPaymentModal" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center px-4">
            
            <div @click.away="showPaymentModal = false" 
                 class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden transition-all border border-[#853953]/10">
                
                <div class="bg-[#853953] p-8 text-white relative">
                    <button @click="showPaymentModal = false" class="absolute top-6 right-6 p-2 bg-white/20 rounded-full hover:bg-white/40 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <h3 class="text-2xl font-black tracking-tight">Manual Payment Record</h3>
                    <p class="text-[#853953]/20 text-xs font-bold uppercase tracking-widest mt-1">DreamHome Ledger Update</p>
                </div>

                <form action="{{ route('staff.leases.process_payment') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="leaseno" value="{{ $lease->leaseno }}">

                    {{-- Amount --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Amount Received (₱)</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-300">₱</span>
                            <input type="number" name="amount" step="0.01" value="{{ $lease->monthly_rent }}" required
                                class="w-full pl-10 pr-5 py-4 bg-slate-50 border-none rounded-2xl text-xl font-black text-slate-900 focus:ring-2 focus:ring-[#853953] transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Method</label>
                            <select name="payment_method" required
                                    class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-[#853953] transition-all">
                                <option value="Cash">Cash</option>
                                <option value="Check">Check</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-[#853953] transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Reference Notes</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Advance payment packages for 3 month(s)"
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-medium text-slate-900 focus:ring-2 focus:ring-[#853953] transition-all"></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showPaymentModal = false"
                                class="flex-1 py-4 bg-slate-100 text-slate-600 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-slate-200 transition-all">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-[2] py-4 bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">
                            Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>