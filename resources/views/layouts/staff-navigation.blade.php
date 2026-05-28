<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('staff.dashboard') }}">
                    <?php $logoPath = public_path('images/dreamhome-logo-colored.png'); ?>
                    @if(file_exists($logoPath))
                        <img src="{{ asset('images/dreamhome-logo-colored.png') }}" alt="DreamHome Logo" class="h-10 w-auto object-contain">
                    @else
                        <img src="{{ asset('images/dreamhome-logo.png') }}" alt="DreamHome Logo" class="h-10 w-auto object-contain">
                    @endif
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-gray-700">
                    {{-- EVERYONE gets access to the Dashboard --}}
                    <x-nav-link :href="route('staff.dashboard')" :active="request()->routeIs('staff.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- SECRETARY: All CRUD Operations --}}
                    @if(in_array(strtolower(Auth::guard('staff')->user()->position), ['secretary', 'admin']))
                        <x-nav-link :href="route('staff.staff')" :active="request()->routeIs('staff.staff')">
                            {{ __('Staff') }}
                        </x-nav-link>

                        <x-nav-link :href="route('staff.properties.properties')" :active="request()->routeIs('staff.properties.properties')">
                            {{ __('Properties') }}
                        </x-nav-link>

                        <x-nav-link :href="route('staff.renters.index')" :active="request()->routeIs('staff.renters.index')">
                            {{ __('Clients') }}
                        </x-nav-link>

                        <x-nav-link :href="route('staff.applications')" :active="request()->routeIs('staff.applications')">
                            {{ __('Applications') }}
                        </x-nav-link>
                        <x-nav-link :href="route('staff.listing-requests.index')" :active="request()->routeIs('staff.listing-requests*')">
                            {{ __('Listings') }}
                        </x-nav-link>

                        <x-nav-link :href="route('staff.viewings')" :active="request()->routeIs('staff.viewings')">
                            {{ __('Viewings') }}
                        </x-nav-link>

                        <x-nav-link :href="route('staff.leases.index')" :active="request()->routeIs('staff.leases.index')">
                            {{ __('Leases') }}
                        </x-nav-link>

                        <x-nav-link :href="route('staff.inspections')" :active="request()->routeIs('staff.inspections')">
                            {{ __('Inspections') }}
                        </x-nav-link>
                    @endif

                    {{-- MANAGER: Analytics / Reports Only --}}
                    @if(in_array(strtolower(Auth::guard('staff')->user()->position), ['manager', 'admin']))
                        <x-nav-link :href="route('staff.staff')" :active="request()->routeIs('staff.staff')">
                            {{ __('Staff') }}
                        </x-nav-link>

                        <x-nav-link :href="route('staff.reports')" :active="request()->routeIs('staff.reports')">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::guard('staff')->user()->firstname }} ({{ Auth::guard('staff')->user()->position }})</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('staff.profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <hr class="border-gray-100 my-1">

                        <form method="POST" action="{{ route('staff.logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('staff.logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('staff.dashboard')" :active="request()->routeIs('staff.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- SECRETARY MOBILE LINKS --}}
            @if(in_array(strtolower(Auth::guard('staff')->user()->position), ['secretary', 'admin']))
                <x-responsive-nav-link :href="route('staff.staff')" :active="request()->routeIs('staff.staff')">
                    {{ __('Staff') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('staff.properties.properties')" :active="request()->routeIs('staff.properties.properties')">
                    {{ __('Properties') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('staff.renters.index')" :active="request()->routeIs('staff.renters.index')">
                        {{ __('Renters') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('staff.applications')" :active="request()->routeIs('staff.applications')">
                    {{ __('Applications') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('staff.viewings')" :active="request()->routeIs('staff.viewings')">
                    {{ __('Viewings') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('staff.leases.index')" :active="request()->routeIs('staff.leases.index')">
                    {{ __('Leases') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('staff.inspections')" :active="request()->routeIs('staff.inspections')">
                    {{ __('Inspections') }}
                </x-responsive-nav-link>
            @endif

            {{-- MANAGER MOBILE LINKS --}}
            @if(in_array(strtolower(Auth::guard('staff')->user()->position), ['manager', 'admin']))
                <x-responsive-nav-link :href="route('staff.reports')" :active="request()->routeIs('staff.reports')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 text-gray-800">
                <div class="font-medium text-base">{{ Auth::guard('staff')->user()->firstname }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::guard('staff')->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('staff.profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('staff.logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('staff.logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>