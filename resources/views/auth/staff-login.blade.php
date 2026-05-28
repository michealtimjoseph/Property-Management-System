<x-guest-layout>
    <div class="rounded min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-transparent rounded-[2rem] border border-white overflow-hidden">
            
            <div class="flex justify-center mb-8">
                <img src="{{ asset('storage/images/dreamhome-logo-colored.png') }}" 
                     alt="DreamHome Logo" 
                     class="h-20 w-auto object-contain">
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Staff Portal</h2>
                <p class="text-sm text-gray-500 font-medium mt-1">Authorized access only for DreamHome personnel.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 font-bold text-sm text-green-600 bg-green-50 p-3 rounded-xl border border-green-100">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('staff.login') }}">
                @csrf

                <div>
                    <label for="email" class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1">Staff Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                        class="block w-full border-gray-100 bg-gray-50 rounded-2xl shadow-sm focus:ring-2 focus:ring-[#853953] focus:border-transparent transition-all">
                    @error('email')
                        <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tighter">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label for="password" class="block font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full border-gray-100 bg-gray-50 rounded-2xl shadow-sm focus:ring-2 focus:ring-[#853953] focus:border-transparent transition-all">
                    @error('password')
                        <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tighter">{{ $message }}</p>
                    @enderror
                </div>

                <div class="block mt-6">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded-lg border-gray-200 text-[#853953] shadow-sm focus:ring-[#853953] transition-all">
                        <span class="ml-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Remember Session</span>
                    </label>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-[#853953] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-gray-200 hover:bg-[#853953] hover:shadow-pink-100 transition-all transform active:scale-[0.98]">
                        Login to Dashboard
                    </button>
                    
                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors">
                            Switch to Renter Portal
                        </a>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</x-guest-layout>