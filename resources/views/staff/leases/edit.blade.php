<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-pink-50">
                <div class="mb-10 flex justify-between items-end">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tighter mb-1">Edit Lease</h2>
                        <p class="text-sm text-gray-400 font-medium">Update agreement terms for Lease {{ $lease->leaseno }}.</p>
                    </div>
                </div>

                <form action="{{ route('staff.leases.update', $lease->leaseno) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Monthly Rent</label>
                            <input type="number" name="monthly_rent" value="{{ $lease->monthly_rent }}" step="0.01" required class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#853953] text-sm h-12">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Payment Method</label>
                            <select name="paymentmethod" class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#853953] text-sm h-12">
                                <option value="Cash" {{ $lease->paymentmethod == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ $lease->paymentmethod == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Cheque" {{ $lease->paymentmethod == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Deposit Amount</label>
                            <input type="number" name="deposit" value="{{ $lease->deposit }}" step="0.01" required class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#853953] text-sm h-12">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Deposit Received?</label>
                            <select name="isdepositpaid" class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#853953] text-sm h-12">
                                <option value="Yes" {{ $lease->isdepositpaid == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ $lease->isdepositpaid == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Lease Start</label>
                            <input type="date" name="startdate" value="{{ $lease->startdate }}" required class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#853953] text-sm h-12">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#853953] uppercase mb-2 tracking-widest">Lease End</label>
                            <input type="date" name="enddate" value="{{ $lease->enddate }}" required class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-[#853953] text-sm h-12">
                        </div>
                    </div>

                    <div class="pt-8 flex gap-4">
                        <a href="{{ route('staff.leases.index') }}" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest text-center">Cancel</a>
                        <button type="submit" class="flex-[2] bg-[#853953] text-white py-4 rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest shadow-xl shadow-pink-100 hover:bg-pink-900 transition-all">Update Agreement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>