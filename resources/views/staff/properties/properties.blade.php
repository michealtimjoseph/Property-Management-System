<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Property Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
                <form action="{{ route('staff.properties.properties') }}" method="GET" class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">
                    
                    <div class="flex items-center space-x-4 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Property..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] block w-full pl-10 p-2.5">
                        </div>

                        <select name="branchno" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] block p-2.5">
                            <option value="">All Properties</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branchno }}" {{ request('branchno') == $branch->branchno ? 'selected' : '' }}>
                                    {{ $branch->city }} ({{ $branch->branchno }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="hidden">Search</button>

                    <a href="{{ route('staff.properties.create') }}" class="flex items-center justify-center text-white bg-[#853953] hover:bg-pink-900 focus:ring-4 focus:ring-pink-300 font-medium rounded-lg text-sm px-5 py-2.5 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Property
                    </a>
                </form>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Property No</th>
                            <th scope="col" class="px-6 py-4 font-bold">Address</th>
                            <th scope="col" class="px-6 py-4 font-bold">Type</th>
                            <th scope="col" class="px-6 py-4 font-bold">Rooms</th>
                            <th scope="col" class="px-6 py-4 font-bold">Monthly Rate</th>
                            <th scope="col" class="px-6 py-4 font-bold">Managed By</th>
                            <th scope="col" class="px-6 py-4 font-bold text-right">Options</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($properties as $property)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-[#853953]">{{ $property->propertyno }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-900 font-semibold">{{ $property->street }}</span>
                                    <span class="text-xs text-gray-400">{{ $property->city }}, {{ $property->area }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                    <span class="bg-pink-50 text-[#853953] text-xs font-medium px-2.5 py-0.5 rounded-full border border-pink-100">
                                        {{ $property->property_type }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $property->no_of_rooms }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">₱{{ number_format($property->monthly_rate, 2) }}</td>
                            <td class="px-6 py-4 text-xs">{{ $property->staffno }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button id="dropdownMenuButton-{{ $property->propertyno }}" data-dropdown-toggle="dropdownAction-{{ $property->propertyno }}" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                    </button>
                                    
                                    <div id="dropdownAction-{{ $property->propertyno }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 border border-gray-200">
                                        <ul class="py-2 text-sm text-gray-700 text-left">
                                            <li><a href="{{ route('staff.properties.show', $property->propertyno) }}" class="block px-4 py-2 hover:bg-gray-100">View Details</a></li>
                                            <li><a href="{{ route('staff.properties.edit', $property->propertyno) }}" class="block px-4 py-2 hover:bg-gray-100">Edit Property</a></li>
                                        </ul>
                                    </div>
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>