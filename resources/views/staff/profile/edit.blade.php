<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-900 leading-tight tracking-tight">
            {{ __('Account Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC] min-h-screen font-sans antialiased">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Profile Card Frame --}}
            <div class="p-8 sm:p-10 bg-white shadow-sm border border-slate-200/60 rounded-3xl relative overflow-hidden">
                
                {{-- Decorative Brand Top Bar Accent --}}
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#853953]"></div>

                {{-- Interactive Initial-Based Profile Visual Header Section --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 pb-8 mb-8 border-b border-slate-100">
                    <div class="relative group self-start sm:self-auto">
                        {{-- Circular Initials Badge Element --}}
                        <div class="w-20 h-20 rounded-2xl bg-[#853953]/10 border border-[#853953]/20 flex items-center justify-center text-[#853953] text-2xl font-black uppercase tracking-wider shadow-inner select-none transition-transform duration-300 group-hover:scale-[1.03]">
                            {{ strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1)) }}
                        </div>
                        {{-- Subtle Status Indicator Tag --}}
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-lg border-4 border-white shadow-sm flex items-center justify-center"></div>
                    </div>
                    
                    <div>
                        <span class="text-[10px] font-extrabold text-[#853953] uppercase tracking-widest bg-[#853953]/5 px-2.5 py-1 rounded-md">Staff Identity Matrix</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Profile Information</h2>
                        <p class="text-sm text-slate-500 font-medium mt-0.5">Manage your account name coordinates and communication routing address.</p>
                    </div>
                </div>

                {{-- Account Mutation Form Engine --}}
                <form method="post" action="{{ route('staff.profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- First Name Field Module --}}
                        <div class="space-y-1.5">
                            <label for="firstname" class="block text-xs font-black text-slate-400 uppercase tracking-widest">First Name</label>
                            <x-text-input id="firstname" name="firstname" type="text" 
                                class="mt-1 block w-full px-4 py-3 rounded-xl bg-slate-50/50 border border-slate-200 text-sm font-bold text-slate-800 placeholder-slate-400 shadow-inner focus:bg-white focus:ring-2 focus:ring-[#853953] focus:border-[#853953] transition-all" 
                                :value="old('firstname', $user->firstname)" required autofocus />
                        </div>

                        {{-- Last Name Field Module --}}
                        <div class="space-y-1.5">
                            <label for="lastname" class="block text-xs font-black text-slate-400 uppercase tracking-widest">Last Name</label>
                            <x-text-input id="lastname" name="lastname" type="text" 
                                class="mt-1 block w-full px-4 py-3 rounded-xl bg-slate-50/50 border border-slate-200 text-sm font-bold text-slate-800 placeholder-slate-400 shadow-inner focus:bg-white focus:ring-2 focus:ring-[#853953] focus:border-[#853953] transition-all" 
                                :value="old('lastname', $user->lastname)" required />
                        </div>
                    </div>

                    {{-- Email Address Field Module --}}
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-black text-slate-400 uppercase tracking-widest">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <x-text-input id="email" name="email" type="email" 
                                class="mt-1 block w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50/50 border border-slate-200 text-sm font-bold text-slate-800 placeholder-slate-400 shadow-inner focus:bg-white focus:ring-2 focus:ring-[#853953] focus:border-[#853953] transition-all" 
                                :value="old('email', $user->email)" required />
                        </div>
                    </div>

                    {{-- Form Control Footer Action Strip --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-4 border-t border-slate-100 mt-8">
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3.5 bg-[#853953] text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-[#6e2e44] active:scale-95 transition-all shadow-md shadow-[#853953]/10 self-start sm:self-auto">
                            Save Account Configuration
                        </button>

                        @if (session('status') === 'profile-updated')
                            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                                 class="flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200/50 px-3 py-2 rounded-xl">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Changes synchronized successfully.</span>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>