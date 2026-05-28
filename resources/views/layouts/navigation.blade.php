<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Logo --}}
            <div class="shrink-0 flex items-center">
                <a href="{{ Auth::check() ? route('home') : route('welcome') }}" class="flex items-center gap-2">
                    @php $logoPath = public_path('images/dreamhome-logo-colored.png'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ asset('images/dreamhome-logo-colored.png') }}" alt="DreamHome Logo" class="h-10 w-auto object-contain">
                    @else
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-[#853953] rounded-lg flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            </div>
                            <span class="font-black text-lg tracking-tight"><span class="text-[#853953]">Dream</span><span class="text-gray-800">Home</span></span>
                        </div>
                    @endif
                </a>
            </div>

            {{-- Desktop Nav Links --}}
            @auth
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ request()->routeIs('home') ? 'text-[#853953] bg-pink-50' : 'text-gray-500 hover:text-[#853953] hover:bg-pink-50' }}">Home</a>
                    <a href="{{ route('applications') }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ request()->routeIs('applications') ? 'text-[#853953] bg-pink-50' : 'text-gray-500 hover:text-[#853953] hover:bg-pink-50' }}">Applications</a>
                    <a href="{{ route('leases') }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ request()->routeIs('leases') ? 'text-[#853953] bg-pink-50' : 'text-gray-500 hover:text-[#853953] hover:bg-pink-50' }}">Leases</a>
                    <a href="{{ route('viewings') }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ request()->routeIs('viewings') ? 'text-[#853953] bg-pink-50' : 'text-gray-500 hover:text-[#853953] hover:bg-pink-50' }}">Viewings</a>
                    <a href="{{ route('listing-requests') }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ request()->routeIs('listing-requests*') ? 'text-[#853953] bg-pink-50' : 'text-gray-500 hover:text-[#853953] hover:bg-pink-50' }}">My Property</a>
                </div>
            @endauth

            {{-- Right Side --}}
            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">
                                <div class="w-7 h-7 rounded-full bg-[#853953] text-white flex items-center justify-center text-xs font-black">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    @if(!request()->routeIs('welcome'))
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-500 hover:text-[#853953] transition-colors px-3 py-2">Login</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 bg-[#853953] text-white rounded-xl text-sm font-bold hover:bg-[#6e2e44] transition-all shadow-sm">Register</a>
                    @endif
                @endauth
            </div>

            {{-- Mobile button --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-100">
        @auth
            <div class="pt-2 pb-3 space-y-1 px-4">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('applications')" :active="request()->routeIs('applications.*')">Applications</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leases')" :active="request()->routeIs('leases.*')">Leases</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('viewings')" :active="request()->routeIs('viewings.*')">Viewings</x-responsive-nav-link>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-100 px-4">
                <div class="font-semibold text-sm text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            @if(!request()->routeIs('welcome'))
                <div class="pt-2 pb-3 space-y-1 px-4">
                    <x-responsive-nav-link :href="route('login')">Login</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
                </div>
            @endif
        @endauth
    </div>
</nav>