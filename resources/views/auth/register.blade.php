<x-guest-layout>

    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Create Account</h2>
        <p class="text-sm text-gray-400 font-medium mt-1">Join DreamHome to find your next home.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Full Name --}}
        <div>
            <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="block w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
            @error('name')
                <p class="text-red-500 text-[10px] font-bold mt-1.5 uppercase tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="block w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
            @error('email')
                <p class="text-red-500 text-[10px] font-bold mt-1.5 uppercase tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="block w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
            @error('password')
                <p class="text-red-500 text-[10px] font-bold mt-1.5 uppercase tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="block w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all">
            @error('password_confirmation')
                <p class="text-red-500 text-[10px] font-bold mt-1.5 uppercase tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="pt-2 space-y-4">
            <button type="submit"
                class="w-full bg-[#853953] text-white py-3.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#6e2e44] active:scale-[0.98] transition-all shadow-sm shadow-pink-100">
                Register Now
            </button>

            <p class="text-center text-xs text-gray-400 font-medium">
                Already have an account?
                <a href="{{ route('login') }}" class="text-[#853953] font-black hover:underline ml-1">Login here</a>
            </p>
        </div>
    </form>

</x-guest-layout>