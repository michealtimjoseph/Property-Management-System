<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Staff Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
                <form action="{{ route('staff.staff') }}" method="GET" class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">
                    
                    <div class="flex items-center space-x-4 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search staff..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] block w-full pl-10 p-2.5">
                        </div>

                        <select name="branchno" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] block p-2.5">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branchno }}" {{ request('branchno') == $branch->branchno ? 'selected' : '' }}>
                                    {{ $branch->city }} ({{ $branch->branchno }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="hidden">Search</button>

                   <div class="flex items-center gap-3">
                        <a href="{{ route('staff.branches.create') }}" 
                        class="flex items-center justify-center text-[#853953] bg-pink-50 hover:bg-[#853953] hover:text-white font-black text-sm px-5 py-2.5 rounded-xl transition-all border border-[#853953]/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2-3H5m2 3H5m2 0a2 2 0 104 0m9 0a2 2 0 104 0m-8 0a2 2 0 104 0"></path>
                            </svg>
                            Add Branch
                        </a>

                        <a href="{{ route('staff.create') }}" 
                        class="flex items-center justify-center text-white bg-[#853953] hover:bg-pink-900 shadow-lg shadow-pink-200 font-black text-sm px-5 py-2.5 rounded-xl transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Add New Staff
                        </a>
                    </div>
                </form>
            </div>

            @if(request('branchno') && isset($branchDetails))
                <div class="mb-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 bg-gradient-to-r from-[#853953] to-[#5d273a] text-white flex items-center gap-6">
                        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center font-black text-2xl backdrop-blur-sm">
                            {{ substr($branchDetails->branchno, -2) }}
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-pink-200">Branch Details</span>
                            <h3 class="text-2xl font-black">{{ $branchDetails->city }} Branch</h3>
                            <p class="text-pink-100/70 text-xs font-medium">{{ $branchDetails->branchno }}</p>
                        </div>
                    </div>
                    
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block mb-1">Branch Address</span>
                            <span class="text-sm font-bold text-gray-800">{{ $branchDetails->street ?? 'N/A' }}, {{ $branchDetails->city }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block mb-1">Assigned Supervisor</span>
                            <span class="text-sm font-bold text-[#853953]">
                                {{ $branchSupervisor->firstname ?? 'No Supervisor Assigned' }} {{ $branchSupervisor->lastname ?? '' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block mb-1">Branch Postcode</span>
                            <span class="text-sm font-bold text-gray-800">{{ $branchDetails->postcode ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Staff ID</th>
                            <th scope="col" class="px-6 py-4 font-bold">Full Name</th>
                            <th scope="col" class="px-6 py-4 font-bold">Position</th>
                            <th scope="col" class="px-6 py-4 font-bold">NIN</th>
                            <th scope="col" class="px-6 py-4 font-bold">Address</th>
                            <th scope="col" class="px-6 py-4 font-bold">Joined</th>
                            <th scope="col" class="px-6 py-4 font-bold text-right">Options</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($staffs as $staff)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-[#853953]">{{ $staff->staffno }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-semibold">{{ $staff->firstname }} {{ $staff->lastname }}</span>
                                        <span class="text-xs text-gray-400">{{ $staff->email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-pink-50 text-[#853953] text-xs font-medium px-2.5 py-0.5 rounded-full border border-pink-100">
                                        {{ $staff->position }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $staff->nin }}</td>
                                <td class="px-6 py-4">{{ Str::limit($staff->address, 30) }}</td>
                                <td class="px-6 py-4 text-xs">{{$staff->date_joined}}</td>
                                <td class="px-6 py-4 text-right">
                                    <button id="dropdownMenuButton-{{ $staff->staffno }}" data-dropdown-toggle="dropdownAction-{{ $staff->staffno }}" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                    </button>
                                    
                                    <div id="dropdownAction-{{ $staff->staffno }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 border border-gray-200">
                                        <ul class="py-2 text-sm text-gray-700 text-left">
                                            <li><a href="{{ route('staff.show', $staff->staffno) }}" class="block px-4 py-2 hover:bg-gray-100">View Details</a></li>
                                            <li><a href="{{ route('staff.edit', $staff->staffno) }}" class="block px-4 py-2 hover:bg-gray-100">Edit Staff</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>               
                 </table>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $staffs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>