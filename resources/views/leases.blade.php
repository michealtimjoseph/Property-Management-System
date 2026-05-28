<x-app-layout>
<style>[x-cloak] { display: none !important; }</style>

<div class="py-10 bg-[#F3F4F6] min-h-screen"
     x-data="{
        selectedSection: 'upcoming',
        showRenewal: false,
        showSupport: false,
        showPayment: false,
        currentStep: 1,

        payType: 'this_month',
        payMonths: 1,
        payMethod: '',
        payNotes: '',
        monthlyRent: {{ $lease?->monthly_rent ?? 0 }},
        leaseDuration: {{ $lease?->duration ?? 12 }},
        remainingBalance: {{ $lease?->balance ?? 0 }},
        leaseStartDate: '{{ $lease?->startdate ?? now()->tostring() }}',
        
        unpaidMonthsSequence: {{ json_encode($unpaid_months) }},
        selectedTargetMonths: [],

        get maxMonths() {
            return this.unpaidMonthsSequence.length > 0 ? this.unpaidMonthsSequence.length : 1;
        },

        get totalAmount() {
            return this.payType === 'this_month'
                ? this.monthlyRent
                : this.monthlyRent * this.payMonths;
        },

        updateTargetMonths() {
            if (this.payType === 'this_month') {
                const now = new Date();
                const label = now.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                this.selectedTargetMonths = [label];
                this.syncNotesField();
                return;
            }

            let targets = [];
            let count = 0;

            for (let i = 0; i < this.unpaidMonthsSequence.length; i++) {
                if (count >= this.payMonths) break;
                targets.push(this.unpaidMonthsSequence[i]);
                count++;
            }

            if (targets.length === 0) {
                targets.push('No open cycles found');
            }

            this.selectedTargetMonths = targets;
            this.syncNotesField();
        },

        syncNotesField() {
            if (this.payType === 'this_month') {
                this.payNotes = 'Rent statement for ' + this.selectedTargetMonths[0];
            } else {
                const unpaidListString = this.selectedTargetMonths.join(', ');
                this.payNotes = 'Advance payment packages for: ' + unpaidListString;
            }
        }
     }"
     x-init="
        $watch('payMonths', value => updateTargetMonths()); 
        $watch('payType', value => { payMonths = 1; currentStep = 1; updateTargetMonths(); });
        $watch('showPayment', value => { if(value) { currentStep = 1; payMethod = ''; } });
        updateTargetMonths();
     ">


    {{-- ===== 2-STEP SYNCHRONIZED PAYMENT MODAL ===== --}}
    <div x-show="showPayment" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click.self="showPayment = false"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center px-4">
        
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden max-h-[90vh] flex flex-col"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Header & Summary Banner --}}
            <div class="bg-gradient-to-r from-[#853953] to-[#5d273a] px-6 py-5 shrink-0">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-[10px] font-black text-pink-200 uppercase tracking-[0.2em]">Lease Payment</p>
                        <h3 class="text-white font-black text-lg tracking-tight mt-0.5"
                            x-text="payType === 'this_month' ? 'Pay Selected Cycle' : 'Pay in Advance'"></h3>
                        <p class="text-pink-200/70 text-[11px] font-bold mt-0.5">Lease No. {{ $lease?->leaseno }}</p>
                    </div>
                    <button @click="showPayment = false" class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Amount Summary (Dynamically calculates based on step choice) --}}
                <div class="bg-white/10 rounded-2xl px-5 py-4 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-pink-200 uppercase tracking-widest">Total Amount Due</p>
                            <p class="text-3xl font-black text-white mt-0.5">&#8369;<span x-text="totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-pink-200 uppercase tracking-widest">Monthly Rate</p>
                            <p class="text-sm font-black text-white mt-0.5">&#8369;{{ number_format($lease?->monthly_rent, 2) }}</p>
                            <p x-show="payType === 'advance'" class="text-[10px] text-pink-200/70 font-bold mt-0.5">× <span x-text="payMonths"></span> month(s)</p>
                        </div>
                    </div>
                </div>

                {{-- Step Progress Indicator Dots --}}
                <div class="flex items-center justify-center gap-2 mt-4">
                    <span class="h-1.5 rounded-full transition-all duration-300" :class="currentStep === 1 ? 'w-6 bg-white' : 'w-2 bg-white/40'"></span>
                    <span class="h-1.5 rounded-full transition-all duration-300" :class="currentStep === 2 ? 'w-6 bg-white' : 'w-2 bg-white/40'"></span>
                </div>
            </div>

            {{-- Multi-step Form Content --}}
            <form method="POST" action="{{ route('renter.payments.process') }}" class="p-6 space-y-4 overflow-y-auto flex-1 flex flex-col justify-between">
                @csrf
                <input type="hidden" name="payment_type" :value="payType">
                <input type="hidden" name="months" :value="payMonths">

                <div class="space-y-4 flex-1">
                    {{-- STEP 1: DURATION & COVERAGE SELECTION --}}
                    <div x-show="currentStep === 1" x-transition.opacity.duration.200ms class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">1. Structural Billing Duration</label>
                            
                            {{-- Controls for Advance Type --}}
                            <div x-show="payType === 'advance'" class="flex items-center gap-3">
                                <button type="button" @click="payMonths = Math.max(1, payMonths - 1)"
                                    class="w-10 h-10 rounded-xl bg-gray-100 text-gray-700 font-black text-lg hover:bg-gray-200 transition-all flex items-center justify-center">−</button>
                                <div class="flex-1 text-center bg-pink-50 rounded-xl py-2.5 border border-pink-100">
                                    <span class="text-2xl font-black text-[#853953]" x-text="payMonths"></span>
                                    <span class="text-sm text-gray-400 font-bold ml-1">month(s)</span>
                                </div>
                                <button type="button" @click="payMonths = Math.min(maxMonths, payMonths + 1)"
                                    class="w-10 h-10 rounded-xl bg-gray-100 text-gray-700 font-black text-lg hover:bg-gray-200 transition-all flex items-center justify-center">+</button>
                            </div>

                            {{-- Display for Single Month Type (Pulls the direct dynamic next unpaid block) --}}
                            <div x-show="payType === 'this_month'" class="bg-gray-50 border border-gray-200 text-center rounded-xl py-3 text-sm font-bold text-gray-600">
                                Paying single billing cycle: <span class="text-[#853953] font-black" x-text="selectedTargetMonths[0]"></span>
                            </div>

                            <p x-show="payType === 'advance'" class="text-[10px] text-gray-400 font-bold text-center mt-1">
                                Max Allowed: <span x-text="maxMonths"></span> month(s) based on outstanding statement history
                            </p>
                        </div>

                        {{-- Target Cycle Visualization Mapping --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Mapped Billing Cycles to be Paid:</label>
                            <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                <template x-for="(month, index) in selectedTargetMonths" :key="index">
                                    <div class="flex items-center justify-between bg-white rounded-xl px-3 py-2 border border-gray-100 shadow-sm">
                                        <span class="text-xs font-black text-gray-800" x-text="month"></span>
                                        <span class="text-[10px] font-black bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-lg border border-emerald-100">Covered</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: PAYMENT METHOD SELECTION --}}
                    <div x-show="currentStep === 2" x-transition.opacity.duration.200ms class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Select Payment Method</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach([
                                    ['value' => 'GCash',     'label' => 'GCash',     'sub' => 'E-Wallet', 'bg' => '#007DFF', 'initial' => 'G'],
                                    ['value' => 'Maya',      'label' => 'Maya',      'sub' => 'E-Wallet', 'bg' => '#0077B6', 'initial' => 'M'],
                                    ['value' => 'BPI',       'label' => 'BPI',       'sub' => 'Bank',     'bg' => '#C41E3A', 'initial' => 'BPI'],
                                    ['value' => 'BDO',       'label' => 'BDO',       'sub' => 'Bank',     'bg' => '#003087', 'initial' => 'BDO'],
                                    ['value' => 'UnionBank',  'label' => 'UnionBank', 'sub' => 'Bank',     'bg' => '#E8600A', 'initial' => 'UB'],
                                    ['value' => 'Cash',      'label' => 'Cash',      'sub' => 'Counter',  'bg' => '#059669', 'initial' => '₱'],
                                ] as $method)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $method['value'] }}" x-model="payMethod" class="sr-only" :required="currentStep === 2">
                                    <div :class="payMethod === '{{ $method['value'] }}' ? 'ring-2 ring-[#853953] border-[#853953] bg-pink-50' : 'border-gray-200 hover:border-gray-300'"
                                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition-all text-center select-none">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-black text-[9px]"
                                            style="background: {{ $method['bg'] }}">{{ $method['initial'] }}</div>
                                        <p class="text-[10px] font-black text-gray-800 leading-tight">{{ $method['label'] }}</p>
                                        <p class="text-[9px] text-gray-400 font-bold leading-none">{{ $method['sub'] }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Reference Number Field --}}
                        <div x-show="payMethod && payMethod !== 'Cash'" x-cloak>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Reference Number</label>
                            <input type="text" name="reference_no" placeholder="e.g. 1234567890" :required="payMethod && payMethod !== 'Cash' && currentStep === 2"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
                        </div>

                        {{-- Cash Counter Note --}}
                        <div x-show="payMethod === 'Cash'" x-cloak>
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[11px] font-bold text-amber-700">Please bring exact amount to the DreamHome branch office. Staff will record your payment on site.</p>
                            </div>
                        </div>

                        {{-- Dynamic Notes Field Statement Log --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Description Statement Log</label>
                            <input type="text" name="notes" x-model="payNotes" readonly
                                class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-500 font-bold focus:outline-none cursor-not-allowed">
                        </div>
                    </div>
                </div>

                {{-- Smart Footer Navigation Controls --}}
                <div class="flex gap-3 pt-4 mt-6 border-t border-gray-100 sticky bottom-0 bg-white">
                    {{-- STEP 1 FOOTER INTERFACE --}}
                    <template x-if="currentStep === 1">
                        <div class="flex gap-3 w-full">
                            <button type="button" @click="showPayment = false"
                                class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                                Cancel
                            </button>
                            <button type="button" @click="currentStep = 2"
                                class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all shadow-sm text-center">
                                Continue to Pay
                            </button>
                        </div>
                    </template>

                    {{-- STEP 2 FOOTER INTERFACE --}}
                    <template x-if="currentStep === 2">
                        <div class="flex gap-3 w-full">
                            <button type="button" @click="currentStep = 1"
                                class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                                Back
                            </button>
                            
                            {{-- Active Action Submission --}}
                            <button type="submit" x-show="payMethod" x-cloak
                                class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all shadow-sm">
                                Confirm Payment
                            </button>
                            
                            {{-- Incomplete State Warning --}}
                            <div x-show="!payMethod"
                                class="flex-1 py-3 bg-gray-200 text-gray-400 rounded-xl font-black text-xs uppercase tracking-widest text-center cursor-not-allowed">
                                Select Method
                            </div>
                        </div>
                    </template>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== RENEWAL MODAL ===== --}}
    <div x-show="showRenewal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="showRenewal = false"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-gradient-to-r from-[#853953] to-[#5d273a] px-6 py-5 flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black text-pink-200 uppercase tracking-[0.2em]">Lease Request</p>
                    <h3 class="text-white font-black text-lg tracking-tight mt-0.5">Request Renewal</h3>
                    <p class="text-pink-200/70 text-[11px] font-bold mt-0.5">Lease No. {{ $lease?->leaseno }}</p>
                </div>
                <button @click="showRenewal = false" class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('leases.renewal') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Reason for Renewal</label>
                    <div class="space-y-2">
                        @foreach([
                            'Contract Expired'       => 'My lease contract has ended and I wish to continue.',
                            'Extension Needed'       => 'I need more time before moving out.',
                            'Long-term Stay Planned' => 'I plan to stay for a longer period.',
                            'Other'                  => 'Other reason (please specify below).',
                        ] as $value => $description)
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-[#853953]/30 hover:bg-pink-50/50 transition-all has-[:checked]:border-[#853953] has-[:checked]:bg-pink-50">
                            <input type="radio" name="reason" value="{{ $value }}" class="mt-0.5 accent-[#853953] shrink-0" required>
                            <div>
                                <p class="text-xs font-black text-gray-800">{{ $value }}</p>
                                <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ $description }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Additional Message <span class="text-gray-300 normal-case font-medium">(optional)</span></label>
                    <textarea name="message" rows="3" placeholder="Add any details about your renewal request..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="showRenewal = false"
                        class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== CONTACT SUPPORT MODAL ===== --}}
    <div x-show="showSupport" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click.self="showSupport = false; supportStep = 1"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center px-4"
        x-data="{ supportStep: 1 }">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-gradient-to-r from-[#853953] to-[#5d273a] px-6 py-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black text-pink-200 uppercase tracking-[0.2em]">Help & Support</p>
                        <h3 class="text-white font-black text-lg tracking-tight mt-0.5">Contact Support</h3>
                    </div>
                    <button @click="showSupport = false; supportStep = 1" class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black transition-all"
                             :class="supportStep >= 1 ? 'bg-white text-[#853953]' : 'bg-white/20 text-white'">1</div>
                        <span class="text-[10px] font-black text-white/80 uppercase tracking-wider">Branch Info</span>
                    </div>
                    <div class="flex-1 h-px bg-white/20 mx-1"></div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black transition-all"
                             :class="supportStep >= 2 ? 'bg-white text-[#853953]' : 'bg-white/20 text-white'">2</div>
                        <span class="text-[10px] font-black text-white/80 uppercase tracking-wider">Your Issue</span>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('leases.support') }}">
                @csrf
                <div x-show="supportStep === 1" class="p-6 space-y-4">
                    @if(isset($branch))
                    <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Branch Contact Info</p>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-[#853953]/10 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-[#853953]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold">Phone</p>
                                    <p class="text-sm font-black text-gray-900">{{ $branch->phone }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-[#853953]/10 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-[#853953]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold">Address</p>
                                    <p class="text-sm font-black text-gray-900">{{ $branch->street }}, {{ $branch->area }}, {{ $branch->city }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <p class="text-xs text-gray-400 font-medium text-center">You can call us directly or submit a support ticket below.</p>
                    <div class="flex gap-3 pt-1">
                        <button type="button" @click="showSupport = false; supportStep = 1"
                            class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                        <button type="button" @click="supportStep = 2"
                            class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all flex items-center justify-center gap-2">
                            Submit Ticket
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                <div x-show="supportStep === 2" class="p-6 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">What is your issue?</label>
                        <div class="space-y-2">
                            @foreach([
                                'Billing Issue'        => 'Problem with payment records or running balance.',
                                'Property Maintenance' => 'Something in the property needs repair.',
                                'Lease Inquiry'        => 'Questions about my lease terms or contract.',
                                'Payment Problem'      => 'Having trouble making or recording a payment.',
                                'Other'                => 'Something else not listed above.',
                            ] as $value => $description)
                            <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-[#853953]/30 hover:bg-pink-50/50 transition-all has-[:checked]:border-[#853953] has-[:checked]:bg-pink-50">
                                <input type="radio" name="issue_type" value="{{ $value }}" class="mt-0.5 accent-[#853953] shrink-0" required>
                                <div>
                                    <p class="text-xs font-black text-gray-800">{{ $value }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ $description }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Describe Your Issue</label>
                        <textarea name="message" rows="3" placeholder="Please describe the issue in detail..." required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="button" @click="supportStep = 1"
                            class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all">Submit Ticket</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- PAGE HEADER --}}
        <div class="mb-8">
            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#853953] mb-1">DreamHome — CDO Branch</p>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">My Lease Agreement</h1>
            <p class="text-sm text-gray-400 font-medium mt-1">Your active rental contract with DreamHome.</p>
        </div>

        {{-- SUCCESS / ERROR TOASTS --}}
        @if(session('payment_success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold text-emerald-700">{{ session('payment_success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 rounded-2xl px-5 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold text-red-700">{{ session('error') }}</p>
        </div>
        @endif
        @if(session('renewal_success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold text-emerald-700">{{ session('renewal_success') }}</p>
        </div>
        @endif
        @if(session('support_success'))
        <div class="mb-5 bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold text-blue-700">{{ session('support_success') }}</p>
        </div>
        @endif

        @if(!$lease)
        {{-- EMPTY STATE --}}
        <div class="bg-white rounded-2xl p-16 text-center border-2 border-dashed border-gray-200">
            <div class="w-16 h-16 bg-pink-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#853953]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-800 font-black text-lg">No Active Lease</p>
            <p class="text-gray-400 font-medium text-sm mt-1">
                @if(!Auth::user()->renterno)
                    Your account is not yet linked to a renter record. Please contact DreamHome staff.
                @else
                    You don't have any lease agreements yet.
                @endif
            </p>
            <a href="{{ route('home') }}" class="inline-block mt-5 px-5 py-2.5 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] transition-all">
                Browse Properties
            </a>
        </div>

        @else
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            {{-- MAIN LEASE DOCUMENT --}}
            <div class="flex-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                    {{-- Document Header --}}
                    <div class="bg-gradient-to-r from-[#853953] to-[#5d273a] px-8 py-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-pink-200 uppercase tracking-[0.2em]">Lease Agreement</p>
                                    <h2 class="text-2xl font-black text-white tracking-tight leading-none mt-0.5">No. {{ $lease->leaseno }}</h2>
                                    <p class="text-pink-200/70 text-xs font-bold mt-1">DreamHome CDO Branch — B001</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if($lease->payment_status === 'PAID')
                                    <span class="bg-emerald-500/20 text-emerald-300 text-[10px] px-4 py-1.5 rounded-full font-black uppercase tracking-widest border border-emerald-400/30">✓ Fully Paid</span>
                                @elseif($lease->is_overdue)
                                    <span class="bg-red-500/20 text-red-300 text-[10px] px-4 py-1.5 rounded-full font-black uppercase tracking-widest border border-red-400/30">⚠ Overdue</span>
                                @else
                                    <span class="bg-white/20 text-white text-[10px] px-4 py-1.5 rounded-full font-black uppercase tracking-widest border border-white/30">● Active</span>
                                @endif
                                <span class="text-pink-200/60 text-[10px] font-bold uppercase tracking-widest">{{ $lease->duration }} {{ $lease->duration == 1 ? 'month' : 'months' }} contract</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">

                        {{-- Section 1: Property Details --}}
                        <div class="mb-7">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-1 h-4 bg-[#853953] rounded-full"></span>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Property Details</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Property No.</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->propertyno }}</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Type</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->property_type }}</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100 sm:col-span-2">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Full Address</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->street }}, {{ $lease->area }}, {{ $lease->city }} {{ $lease->postcode }}</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">No. of Rooms</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->no_of_rooms }} Rooms</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Renter Name</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->renter_name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 mb-7"></div>

                        {{-- Section 2: Lease Terms --}}
                        <div class="mb-7">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-1 h-4 bg-[#853953] rounded-full"></span>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Lease Terms</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Monthly Rent</p>
                                    <p class="text-lg font-black text-[#853953]">&#8369;{{ number_format($lease->monthly_rent, 2) }}</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Method of Payment</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->paymentmethod }}</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Lease Duration</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->duration }} {{ $lease->duration == 1 ? 'month' : 'months' }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold mt-0.5">Min 3 months · Max 1 year</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Rental Deposit</p>
                                    <p class="text-sm font-black text-gray-900">&#8369;{{ number_format($lease->deposit, 2) }}</p>
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Deposit Paid</p>
                                    @if($lease->isdepositpaid)
                                        <span class="inline-flex items-center gap-1.5 text-emerald-600 font-black text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></span >
                                            Yes — Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-red-500 font-black text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Not Paid
                                        </span>
                                    @endif
                                </div>
                                <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Arranged by Staff</p>
                                    <p class="text-sm font-black text-gray-900">{{ $lease->staff_name }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold mt-0.5">Staff No. {{ $lease->staffno }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 mb-7"></div>

                        {{-- Section 3: Contract Period --}}
                        <div class="mb-7">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-1 h-4 bg-[#853953] rounded-full"></span>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Contract Period</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-center gap-4 bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                    <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-[#853953]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Rent Start Date</p>
                                        <p class="text-sm font-black text-gray-900 mt-0.5">{{ \Carbon\Carbon::parse($lease->startdate)->format('F d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-[#853953] rounded-xl p-4">
                                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-pink-200">Rent End Date</p>
                                        <p class="text-sm font-black text-white mt-0.5">{{ \Carbon\Carbon::parse($lease->enddate)->format('F d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 mb-6"></div>

                        {{-- ACTION BUTTONS --}}
                        <div class="space-y-3">

                        {{-- PAY BUTTONS --}}
                        @if($lease->payment_status !== 'PAID' && $lease->balance > 0)
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Pay This Month Button (Isolated to match the target calendar month cycle string layout) --}}
                                @php
                                    $currentBillingCycleLabel = now()->format('F Y');
                                    
                                    $isCurrentMonthCyclePaid = $payments->contains(function($payment) use ($currentBillingCycleLabel) {
                                        return stripos($payment->notes, $currentBillingCycleLabel) !== false;
                                    });
                                @endphp

                                @if($isCurrentMonthCyclePaid)
                                    <button type="button" disabled
                                        class="flex items-center justify-center gap-2 py-3.5 bg-gray-100 text-gray-400 rounded-xl font-black text-xs uppercase tracking-widest cursor-not-allowed border border-gray-200">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Paid for {{ now()->format('F') }}
                                    </button>
                                @else
                                    <button @click="payType = 'this_month'; payMethod = ''; payNotes = 'Rent statement for ' + new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' }); showPayment = true"
                                        class="flex items-center justify-center gap-2 py-3.5 bg-[#853953] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-95 transition-all shadow-sm shadow-pink-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Pay This Month
                                    </button>
                                @endif

                                {{-- Pay in Advance Button --}}
                                <button @click="payType = 'advance'; showPayment = true"
                                    class="flex items-center justify-center gap-2 py-3.5 bg-gray-900 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#853953] active:scale-95 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Pay in Advance
                                </button>
                            </div>
                        @else
                            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-black text-emerald-700">Your lease is fully paid — no outstanding balance!</p>
                            </div>
                        @endif

                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('leases.pdf') }}"
                                    class="flex items-center gap-2 px-5 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[#853953] transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Download PDF
                                </a>
                                <button @click="showRenewal = true"
                                    class="flex items-center gap-2 px-5 py-3 bg-white text-gray-600 border border-gray-200 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-pink-50 hover:text-[#853953] hover:border-pink-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Request Renewal
                                </button>
                                <button @click="showSupport = true"
                                    class="flex items-center gap-2 px-5 py-3 bg-white text-gray-600 border border-gray-200 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-pink-50 hover:text-[#853953] hover:border-pink-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Contact Support
                                </button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <aside class="w-full lg:w-72 shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">

                    <div class="px-6 py-5 border-b border-gray-50">
                        <h2 class="text-sm font-black text-gray-900 tracking-tight">Billing Overview</h2>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Payment Schedule</p>
                    </div>

                    <div class="p-6 space-y-6">

                        {{-- Lease Progress --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Lease Progress</p>
                                <span class="text-[10px] font-black text-[#853953]">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-[#853953] to-[#c4677e] transition-all duration-700"
                                     style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1.5">
                                <span class="text-[9px] font-bold text-gray-400">{{ \Carbon\Carbon::parse($lease->startdate)->format('M d, Y') }}</span>
                                <span class="text-[9px] font-bold text-gray-400">{{ \Carbon\Carbon::parse($lease->enddate)->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-50"></div>

                        {{-- Balance Summary --}}
                        <div class="space-y-3">
                            <div class="bg-[#F3F4F6] rounded-xl p-4 border border-gray-100">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Paid</p>
                                <p class="text-base font-black text-emerald-600">&#8369;{{ number_format($lease->total_paid, 2) }}</p>
                            </div>
                            <div class="rounded-xl p-4 border {{ $lease->balance == 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-pink-50 border-pink-100' }}">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Running Balance</p>
                                <p class="text-base font-black {{ $lease->balance == 0 ? 'text-emerald-600' : 'text-[#853953]' }}">
                                    &#8369;{{ number_format($lease->balance, 2) }}
                                </p>
                                @if($lease->balance == 0)
                                <p class="text-[10px] font-black text-emerald-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Fully settled
                                </p>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-gray-50"></div>

                        {{-- OVERDUE MONTHS BREAKDOWN --}}
                        @if(isset($overdue_months) && count($overdue_months) > 0)
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <span class="text-xs font-black uppercase tracking-wider text-gray-900">Overdue Balances</span>
                            </div>
                            <div class="space-y-2.5">
                                @foreach($overdue_months as $month)
                                <div class="bg-red-50 border border-red-100 rounded-xl p-3 flex flex-col gap-2">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-[11px] font-black text-gray-900">{{ $month }}</p>
                                            <p class="text-[9px] text-red-500 font-bold uppercase">Unpaid Period</p>
                                        </div>
                                        <p class="text-[11px] font-black text-red-600">&#8369;{{ number_format($lease->monthly_rent, 2) }}</p>
                                    </div>
                                    <button type="button" 
                                            @click="payType = 'this_month'; payMethod = ''; payNotes = 'Overdue rent payment for ' + '{{ $month }}'; showPayment = true"
                                            class="w-full py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-colors duration-150 text-center shadow-sm">
                                        Pay Now
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="border-t border-gray-50"></div>
                        @endif

                        {{-- Next Payment --}}
                        <div>
                            <button @click="selectedSection = (selectedSection === 'upcoming' ? null : 'upcoming')"
                                class="flex items-center justify-between w-full outline-none mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-all"
                                         :class="selectedSection === 'upcoming' ? 'bg-[#853953] text-white' : 'bg-gray-100 text-gray-400'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-wider"
                                          :class="selectedSection === 'upcoming' ? 'text-gray-900' : 'text-gray-400'">Next Payment</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                                     :class="selectedSection === 'upcoming' ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="selectedSection === 'upcoming'" x-cloak>
                                @if($lease->payment_status === 'PAID')
                                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-emerald-700">Fully Paid!</p>
                                            <p class="text-[10px] text-emerald-600/70 font-bold mt-0.5">No outstanding balance remaining.</p>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="bg-pink-50 border border-pink-100 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 rounded-full bg-[#853953] mt-1.5 shrink-0 animate-pulse"></div>
                                        <div>
                                            <p class="text-xs font-black text-gray-900">{{ $next_due_date }}</p>
                                            <p class="text-[10px] font-black text-[#853953] mt-0.5">&#8369;{{ number_format($lease->monthly_rent, 2) }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold mt-1">Via {{ $lease->paymentmethod }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Payment History --}}
                        <div>
                            <button @click="selectedSection = (selectedSection === 'history' ? null : 'history')"
                                class="flex items-center justify-between w-full outline-none mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-all"
                                         :class="selectedSection === 'history' ? 'bg-[#853953] text-white' : 'bg-gray-100 text-gray-400'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-wider"
                                          :class="selectedSection === 'history' ? 'text-gray-900' : 'text-gray-400'">
                                        Payment History
                                        @if($payments->isNotEmpty())
                                            <span class="ml-1 text-[#853953]">({{ $payments->count() }})</span>
                                        @endif
                                    </span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                                     :class="selectedSection === 'history' ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="selectedSection === 'history'" x-cloak>
                                @if($payments->isEmpty())
                                    <p class="text-xs text-gray-400 font-medium text-center py-4">No payments recorded yet.</p>
                                @else
                                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                        @foreach($payments as $payment)
                                        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-start gap-2">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></div>
                                                    <div>
                                                        <p class="text-[10px] font-black text-gray-900">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</p>
                                                        <p class="text-[10px] font-black text-emerald-600 mt-0.5">&#8369;{{ number_format($payment->amount_paid, 2) }} ✓</p>
                                                        @if($payment->notes)
                                                            <p class="text-[9px] text-gray-400 mt-0.5 italic">{{ $payment->notes }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="text-[9px] text-gray-400 font-bold shrink-0">{{ $payment->payment_method }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </aside>

        </div>
        @endif

    </div>
</div>

</x-app-layout>