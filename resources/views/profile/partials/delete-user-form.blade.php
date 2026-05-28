<section class="space-y-4">
    <p class="text-sm text-gray-500 leading-relaxed">
        Once your account is deleted, all of its data will be permanently removed. Please download any information you wish to keep before proceeding.
    </p>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-2.5 bg-red-500 text-white rounded-xl text-sm font-black hover:bg-red-600 active:scale-95 transition-all shadow-sm"
    >
        Delete Account
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h2 class="text-base font-black text-gray-900">Are you sure?</h2>
            </div>

            <p class="text-sm text-gray-500 mb-5">
                This action cannot be undone. Please enter your password to confirm you want to permanently delete your account.
            </p>

            <div class="mb-5">
                <label for="password" class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-1.5">Your Password</label>
                <input
                    id="password" name="password" type="password"
                    placeholder="Enter your password"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-all"
                >
                @if ($errors->userDeletion->get('password'))
                    <p class="mt-1.5 text-xs text-red-500">{{ implode(' ', $errors->userDeletion->get('password')) }}</p>
                @endif
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-black hover:bg-gray-200 transition-all">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-red-500 text-white rounded-xl text-sm font-black hover:bg-red-600 active:scale-95 transition-all">
                    Yes, Delete My Account
                </button>
            </div>
        </form>
    </x-modal>
</section>