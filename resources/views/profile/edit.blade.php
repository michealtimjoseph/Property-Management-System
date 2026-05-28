<x-app-layout>

    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#853953] mb-2">Account Settings</p>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Your Profile</h1>
            <p class="text-sm text-gray-400 font-medium mt-1">Manage your personal information and account security.</p>
        </div>
    </div>

    <div class="py-10 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Profile Information --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                    <div class="w-8 h-8 bg-pink-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#853953]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Profile Information</h2>
                        <p class="text-xs text-gray-400">Update your name and email address.</p>
                    </div>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                    <div class="w-8 h-8 bg-pink-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#853953]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Update Password</h2>
                        <p class="text-xs text-gray-400">Use a long, random password to stay secure.</p>
                    </div>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-50 flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-red-600">Delete Account</h2>
                        <p class="text-xs text-gray-400">Permanently delete your account and all data.</p>
                    </div>
                </div>
                <div class="p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>

</x-app-layout>