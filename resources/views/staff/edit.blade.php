<x-app-layout>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                       <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Staff Modification</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Updating details for authorized personnel at DreamHome CDO.</p>
                </div>
                <a href="{{ route('staff.staff') }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-[#853953] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    Back to Staff
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                    <p class="font-bold">Please correct the following errors:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Staff Information</h3>
                    <p class="text-sm text-gray-500">Editing details for Staff ID: <span class="font-mono text-[#853953] font-bold">{{ $staff->staffno }}</span></p>
                </div>

                <form action="{{ route('staff.update', $staff->staffno) }}" method="POST" class="p-8" onsubmit="confirmAction(event, this, 'Update Staff Details')">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">First Name</label>
                            <input type="text" name="firstname" value="{{ old('firstname', $staff->firstname) }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Last Name</label>
                            <input type="text" name="lastname" value="{{ old('lastname', $staff->lastname) }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $staff->email) }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Telephone</label>
                            <input type="text" name="telephoneno" value="{{ old('telephoneno', $staff->telephoneno) }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Position</label>
                            <select name="position" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                                <option value="Regular" {{ $staff->position == 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="Supervisor" {{ $staff->position == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="Secretary" {{ $staff->position == 'Secretary' ? 'selected' : '' }}>Secretary</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Branch</label>
                            <select name="branchno" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branchno }}" {{ $staff->branchno == $branch->branchno ? 'selected' : '' }}>
                                        {{ $branch->city }} ({{ $branch->branchno }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Sex</label>
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="sex" value="M" {{ $staff->sex == 'M' ? 'checked' : '' }} class="text-[#853953] focus:ring-[#853953]">
                                    <span class="ml-2 text-sm text-gray-700">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="sex" value="F" {{ $staff->sex == 'F' ? 'checked' : '' }} class="text-[#853953] focus:ring-[#853953]">
                                    <span class="ml-2 text-sm text-gray-700">Female</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $staff->date_of_birth) }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Address</label>
                            <textarea name="address" rows="3" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">{{ old('address', $staff->address) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">NIN</label>
                            <input type="text" name="nin" value="{{ old('nin', $staff->nin) }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Salary</label>
                            <input type="number" name="salary" value="{{ old('salary', $staff->salary) }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#853953] focus:border-[#853953] p-2.5">
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end space-x-3">
                        <a href="{{ route('staff.staff') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button  type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#853953] rounded-lg hover:bg-pink-900 focus:ring-4 focus:ring-pink-300 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function confirmAction(event, form, actionName) {
            // Stop the form from submitting immediately
            event.preventDefault(); 
            
            Swal.fire({
                title: 'Confirm Changes',
                text: 'Are you sure you want to update this staff member\'s details?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#853953', // Matches your theme color
                cancelButtonColor: '#94a3b8', // slate-400
                confirmButtonText: 'Yes, save changes',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-3xl' // Matches your existing UI styling
                }
            }).then((result) => {
                // If the user clicks "Yes", submit the form programmatically
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>