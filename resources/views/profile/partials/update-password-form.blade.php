<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div>
            <label for="update_password_current_password" class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-1.5">Current Password</label>
            <input
                id="update_password_current_password" name="current_password" type="password"
                autocomplete="current-password"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all"
            >
            @if ($errors->updatePassword->get('current_password'))
                <p class="mt-1.5 text-xs text-red-500">{{ implode(' ', $errors->updatePassword->get('current_password')) }}</p>
            @endif
        </div>

        {{-- New Password --}}
        <div>
            <label for="update_password_password" class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-1.5">New Password</label>
            <input
                id="update_password_password" name="password" type="password"
                autocomplete="new-password"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all"
            >
            @if ($errors->updatePassword->get('password'))
                <p class="mt-1.5 text-xs text-red-500">{{ implode(' ', $errors->updatePassword->get('password')) }}</p>
            @endif
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-1.5">Confirm New Password</label>
            <input
                id="update_password_password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#853953]/30 focus:border-[#853953] transition-all"
            >
            @if ($errors->updatePassword->get('password_confirmation'))
                <p class="mt-1.5 text-xs text-red-500">{{ implode(' ', $errors->updatePassword->get('password_confirmation')) }}</p>
            @endif
        </div>

        {{-- Save --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#853953] text-white rounded-xl text-sm font-black hover:bg-[#6e2e44] active:scale-95 transition-all shadow-sm">
                Update Password
            </button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Password updated
                </p>
            @endif
        </div>
    </form>
</section>