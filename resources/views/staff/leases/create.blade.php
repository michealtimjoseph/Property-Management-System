<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">New Lease Agreement</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Create and activate a new tenancy contract asset</p>
                </div>
                <a href="{{ route('staff.leases.index') }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Back to Leases
                </a>
            </div>

            {{-- Error Summary Block --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl">
                    <p class="text-xs font-black uppercase tracking-wider text-rose-600 mb-2">Please resolve the following system validation errors:</p>
                    <ul class="list-disc list-inside text-xs text-rose-500 font-bold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-white">
                    <form method="POST" action="{{ route('staff.leases.store') }}" class="space-y-10" onsubmit="confirmFinalize(event, this)">
                    @csrf

                    @if(request('from_app'))
                        <input type="hidden" name="from_app" value="{{ request('from_app') }}">
                    @endif

                    {{-- Section 1: Identification & Linking --}}
                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Contract Assignment
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Lease Number</label>
                                <input type="text" name="leaseno" required readonly value="{{ old('leaseno', $generatedLeaseNo ?? '') }}"
                                    class="w-full bg-gray-100 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-500 cursor-not-allowed focus:ring-0">
                                <p class="text-[10px] text-gray-400 mt-1 font-semibold ml-2">Auto-generated identifier</p>
                            </div>
                            
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Target Property</label>
                                <select name="propertyno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:ring-[#853953]">
                                    <option value="">Select Property...</option>
                                    @foreach($properties as $property)
                                        <option value="{{ trim($property->propertyno) }}" 
                                            {{ old('propertyno', strtoupper(trim(request('propertyno')))) === strtoupper(trim($property->propertyno)) ? 'selected' : '' }}>
                                            {{ $property->street }}, {{ $property->city }} ({{ trim($property->propertyno) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Prospective Renter</label>
                                <select name="renterno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:ring-[#853953]">
                                    <option value="">Select Renter...</option>
                                    @foreach($renters as $renter)
                                        <option value="{{ trim($renter->renterno) }}" 
                                            {{ old('renterno', strtoupper(trim(request('renterno')))) === strtoupper(trim($renter->renterno)) ? 'selected' : '' }}>
                                            {{ $renter->lastname }}, {{ $renter->firstname }} ({{ trim($renter->renterno) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Account Officer / Staff In-Charge</label>
                                <select name="staffno" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 focus:ring-2 focus:ring-[#853953] text-sm font-bold appearance-none">
                                    <option value="" disabled selected>Select authorizer</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->staffno }}" {{ old('staffno') == $staff->staffno ? 'selected' : '' }}>
                                            {{ $staff->firstname }} {{ $staff->lastname }} ({{ $staff->staffno }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Terms & Financial Parameters --}}
                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Financial Terms
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Monthly Rent Rate (PHP)</label>
                                <input type="number" name="monthly_rent" step="0.01" required min="0" placeholder="0.00" 
                                    value="{{ old('monthly_rent', request('monthly_rent')) }}"
                                    class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Security Deposit (PHP)</label>
                                <input type="number" name="deposit" step="0.01" required min="0" placeholder="0.00" value="{{ old('deposit') }}"
                                    class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold focus:ring-[#853953]">
                            </div>

                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Is Deposit Cleared?</label>
                                <select name="isdepositpaid" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 focus:ring-2 focus:ring-[#853953] text-sm font-bold">
                                    <option value="No" {{ old('isdepositpaid') == 'No' ? 'selected' : '' }}>No / Pending</option>
                                    <option value="Yes" {{ old('isdepositpaid') == 'Yes' ? 'selected' : '' }}>Yes / Paid</option>
                                </select>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Disbursement Method</label>
                                <select name="paymentmethod" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 focus:ring-2 focus:ring-[#853953] text-sm font-bold">
                                    <option value="Cash" {{ old('paymentmethod') == 'Cash' ? 'selected' : '' }}>Cash Transaction</option>
                                    <option value="Cheque" {{ old('paymentmethod') == 'Cheque' ? 'selected' : '' }}>Bank Cheque</option>
                                    <option value="Bank Transfer" {{ old('paymentmethod') == 'Bank Transfer' ? 'selected' : '' }}>Direct Bank Transfer / Wire</option>
                                    <option value="Credit Card" {{ old('paymentmethod') == 'Credit Card' ? 'selected' : '' }}>Credit / Debit Card Online</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Time Frame Duration --}}
                    <div>
                        <h3 class="text-xs font-black text-[#853953] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-8 h-px bg-pink-100"></span> Tenure Lifespan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Commencement Date (Start)</label>
                                <input type="date" name="startdate" required value="{{ old('startdate', request('startdate')) }}"
                                    class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:ring-[#853953]">
                            </div>
                            <div>
                                <label class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-2">Termination Date (End)</label>
                                <input type="date" name="enddate" required value="{{ old('enddate', request('enddate')) }}"
                                    class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 focus:ring-[#853953]">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-[#853953] transition-all">
                            Finalize Agreement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- SweetAlert2 Library & Custom Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmFinalize(event, form) {
            // Stop the form from submitting immediately
            event.preventDefault(); 
            
            Swal.fire({
                title: 'Finalize Agreement?',
                text: 'This will activate the new lease and officially mark the application as approved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#853953', // Matches your theme color
                cancelButtonColor: '#94a3b8', // slate-400
                confirmButtonText: 'Yes, finalize it!',
                cancelButtonText: 'Review again',
                customClass: {
                    popup: 'rounded-3xl'
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