<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <a href="{{ route('staff.renters.index') }}" class="text-[#853953] hover:underline">&larr; Back</a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs border-b">
                        <tr>
                            <th class="px-6 py-4 font-bold">Lease No</th>
                            <th class="px-6 py-4 font-bold">Property</th>
                            <th class="px-6 py-4 font-bold">Rent</th>
                            <th class="px-6 py-4 font-bold">Deposit</th>
                            <th class="px-6 py-4 font-bold">Start Date</th>
                            <th class="px-6 py-4 font-bold">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($leases as $lease)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-[#853953]">{{ $lease->leaseno }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $lease->property_address }}</td>
                                <td class="px-6 py-4">₱{{ number_format($lease->monthly_rent, 2) }}</td>
                                <td class="px-6 py-4">
                                    ₱{{ number_format($lease->deposit, 2) }}
                                    @if($lease->isdepositpaid)
                                        <span class="text-green-600 text-[10px] block">Paid</span>
                                    @else
                                        <span class="text-red-600 text-[10px] block">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $lease->startdate }}</td>
                                <td class="px-6 py-4">{{ $lease->duration }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">No leases found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>