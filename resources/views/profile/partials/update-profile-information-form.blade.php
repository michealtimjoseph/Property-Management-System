<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        {{-- Avatar initial --}}
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-2xl bg-[#853953] text-white flex items-center justify-center text-xl font-black shadow-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-black text-gray-800">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
            </div>
        </div>

        {{-- Name --}}
        <div>
            <label for="name" class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-1.5">Full Name</label>
            <input
                id="name" name="name" type="text"
                value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all"
            >
            @if ($errors->get('name'))
                <p class="mt-1.5 text-xs text-red-500">{{ implode(' ', $errors->get('name')) }}</p>
            @endif
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-1.5">Email Address</label>
            <input
                id="email" name="email" type="email"
                value="{{ old('email', $user->email) }}"
                required autocomplete="username"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all"
            >
            @if ($errors->get('email'))
                <p class="mt-1.5 text-xs text-red-500">{{ implode(' ', $errors->get('email')) }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-xs text-amber-700">
                        Your email address is unverified.
                        <button form="send-verification" class="underline font-bold hover:text-amber-900 ml-1">
                            Resend verification email
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-xs font-bold text-green-600">Verification link sent!</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Save --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#853953] text-white rounded-xl text-sm font-black hover:bg-[#6e2e44] active:scale-95 transition-all shadow-sm">
                Save Changes
            </button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Saved successfully
                </p>
            @endif
        </div>
    </form>
</section>