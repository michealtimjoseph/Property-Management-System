<x-app-layout>

    <div class="py-12 bg-gray-50">
        
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Staff Information</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Viewing details for authorized personnel at DreamHome CDO.</p>
                </div>
                <a href="{{ route('staff.staff') }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Back to Staff
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center space-x-4">
                        <div class="h-20 w-20 rounded-full bg-[#853953] flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($staff->firstname, 0, 1) }}{{ substr($staff->lastname, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $staff->firstname }} {{ $staff->lastname }}</h3>
                            <p class="text-[#853953] font-medium">{{ $staff->position }} • {{ $staff->staffno }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold">Contact Information</h4>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Email Address</p>
                            <p class="text-gray-900 font-medium">{{ $staff->email }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Phone Number</p>
                            <p class="text-gray-900 font-medium">{{ $staff->telephoneno }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Home Address</p>
                            <p class="text-gray-900 font-medium">{{ $staff->address }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold">Employment Details</h4>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Assigned Branch</p>
                            <p class="text-gray-900 font-medium">{{ $staff->branch_city }} ({{ $staff->branchno }})</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Monthly Salary</p>
                            <p class="text-gray-900 font-medium">₱{{ number_format($staff->salary, 2) }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Date Joined</p>
                            <p class="text-gray-900 font-medium">
                                {{ isset($staff->date_joined) ? \Carbon\Carbon::parse($staff->date_joined)->format('M d, Y') : 'N/A' }}
                            </p>                        
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-gray-100 md:col-span-2">
                        <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold">Personal Information</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">NIN</p>
                                <p class="text-gray-900 font-medium">{{ $staff->nin }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Sex</p>
                                <p class="text-gray-900 font-medium">{{ $staff->sex == 'M' ? 'Male' : 'Female' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Birth Date</p>
                                <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($staff->date_of_birth)->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>