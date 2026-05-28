<x-guest-layout>

    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Welcome back</h2>
        <p class="text-sm text-gray-400 font-medium mt-1">Sign in to your DreamHome account.</p>
    </div>

    @if (session('status'))
        <div class="mb-5 text-sm font-bold text-green-600 bg-green-50 p-3 rounded-xl border border-green-100">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="block w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
            @error('email')
                <p class="text-red-500 text-[10px] font-bold mt-1.5 uppercase tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[10px] font-black uppercase tracking-widest text-[#853953] hover:text-[#6e2e44] transition-colors">
                        Forgot?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="block w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
            @error('password')
                <p class="text-red-500 text-[10px] font-bold mt-1.5 uppercase tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div>
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-200 text-[#853953] shadow-sm focus:ring-[#853953] transition-all">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Keep me signed in</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="pt-2 space-y-4">
            <button type="submit"
                class="w-full bg-[#853953] text-white py-3.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-[0.98] transition-all shadow-sm shadow-pink-100">
                Login
            </button>

            <p class="text-center text-xs text-gray-400 font-medium">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-[#853953] font-black hover:underline ml-1">Register here</a>
            </p>
        </div>
    </form>

</x-guest-layout>