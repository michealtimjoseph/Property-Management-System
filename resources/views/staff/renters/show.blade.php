<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <a href="{{ route('staff.renters.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Back to Renter Directory
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center space-x-4">
                        <div class="h-20 w-20 rounded-full bg-[#853953] flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($renter->firstname, 0, 1) }}{{ substr($renter->lastname, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $renter->firstname }} {{ $renter->lastname }}</h3>
                            <p class="text-[#853953] font-medium">Renter • {{ $renter->renterno }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold">Personal & Contact Details</h4>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Full Name</p>
                            <p class="text-gray-900 font-medium">{{ $renter->firstname }} {{ $renter->lastname }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Phone Number</p>
                            <p class="text-gray-900 font-medium">{{ $renter->phone }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Home Address</p>
                            <p class="text-gray-900 font-medium">{{ $renter->address }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold">Rental Preferences</h4>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Preferred Property Type</p>
                            <p class="text-gray-900 font-medium">{{ $renter->preferred_property_type }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Maximum Budget</p>
                            <p class="text-gray-900 font-medium text-green-600">₱{{ number_format($renter->max_rent, 2) }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Assigned Branch</p>
                            <p class="text-gray-900 font-medium">{{ $renter->branchno }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-gray-100 md:col-span-2">
                        <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold">Additional Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Witnessing Staff Number</p>
                                <p class="text-gray-900 font-medium">{{ $renter->witness_staffno }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Staff Comments</p>
                                <p class="text-gray-900 font-medium italic">
                                    {{ $renter->comment ?: 'No additional comments provided.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>