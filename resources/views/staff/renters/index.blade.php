<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ $viewType == 'owners' ? __('Owner Management') : __('Renter Management') }}
                </h2>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mt-1">DreamHome Management System</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Toggle Tabs --}}
            <div class="flex gap-2 mb-8">
                <a href="{{ route('staff.renters.index', ['type' => 'renters']) }}" 
                   class="px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all 
                   {{ $viewType == 'renters' ? 'bg-[#853953] text-white shadow-lg shadow-[#853953]/20' : 'bg-white text-gray-400 hover:text-gray-600 border border-gray-200' }}">Renters</a>
                <a href="{{ route('staff.renters.index', ['type' => 'owners']) }}" 
                   class="px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all 
                   {{ $viewType == 'owners' ? 'bg-[#853953] text-white shadow-lg shadow-[#853953]/20' : 'bg-white text-gray-400 hover:text-gray-600 border border-gray-200' }}">Owners</a>
            </div>

            {{-- Search & Filter Bar --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
                <form action="{{ route('staff.renters.index') }}" method="GET" class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <input type="hidden" name="type" value="{{ $viewType }}">
                    
                    <div class="flex items-center space-x-4 w-full md:w-auto">
                        <div class="relative w-full md:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name..." class="bg-gray-50 border-none rounded-xl text-sm w-full pl-11 py-3.5 focus:ring-2 focus:ring-[#853953]">
                        </div>
                        
                        @if($viewType == 'renters')
                            <select name="branchno" onchange="this.form.submit()" class="bg-gray-50 border-none rounded-xl text-sm p-3.5 focus:ring-2 focus:ring-[#853953]">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branchno }}" {{ $selectedBranch == $branch->branchno ? 'selected' : '' }}>{{ $branch->city }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                                           
                    <a href="{{ $viewType == 'renters' ? route('staff.renters.create') : route('staff.owners.create') }}" 
                    
                        class="bg-[#853953] text-white px-6 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-pink-900 transition-all shadow-lg">
                            
                            New {{ $viewType == 'renters' ? 'Renter' : 'Owner' }}
                        </a>
                </form>
            </div>

            {{-- Data Table --}}
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">ID / No</th>
                            <th class="px-6 py-4">Full Name</th>
                            <th class="px-6 py-4">{{ $viewType == 'renters' ? 'Contact' : 'Address' }}</th>
                            <th class="px-6 py-4 text-right">Options</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($data as $item)
                            <tr class="hover:bg-[#853953]/5 transition-colors group">
                                <td class="px-6 py-4 font-black text-[#853953]">
                                    {{ $viewType == 'renters' ? $item->renterno : $item->ownerid }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900 font-bold">{{ $item->firstname }} {{ $item->lastname }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600">
                                    {{ $viewType == 'renters' ? $item->phone : $item->address }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{-- Alpine.js Dropdown --}}
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-gray-400 hover:text-[#853953] transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                        </button>
                                        
                                        {{-- Dropdown Menu --}}
                                        <div x-show="open" 
                                            @click.away="open = false" 
                                            x-cloak
                                            class="origin-top-right absolute right-0 mt-2 w-40 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 p-1">
                                            
                                            @if($viewType == 'renters')
                                                <a href="{{ route('staff.renters.show', $item->renterno) }}" class="block px-4 py-2 text-[10px] font-black uppercase text-gray-700 hover:bg-gray-50 rounded-lg">View Details</a>
                                                <a href="{{ route('staff.renters.edit', $item->renterno) }}" class="block px-4 py-2 text-[10px] font-black uppercase text-gray-700 hover:bg-gray-50 rounded-lg">Edit Renter</a>
                                                <a href="{{ route('staff.renters.leases', $item->renterno) }}" class="block px-4 py-2 text-[10px] font-black uppercase text-gray-700 hover:bg-gray-50 rounded-lg">Lease History</a>
                                            @else
                                                <a href="{{ route('staff.owners.edit', $item->ownerid) }}" class="block px-4 py-2 text-[10px] font-black uppercase text-gray-700 hover:bg-gray-50 rounded-lg">Edit</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>                
                    </table>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>