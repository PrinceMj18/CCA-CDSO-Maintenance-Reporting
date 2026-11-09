@extends('layouts.apps')

@section('content')

<div class="mx-auto w-full max-w-5xl px-5 py-6 sm:px-8 lg:px-10">

    <!-- Page Header -->
    <div class="mb-5">
        <h1 class="text-2xl font-bold tracking-tight text-gray-800 dark:text-white">
            Create New Account
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Register an authorized user for the CCA Maintenance Reporting System.
        </p>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div role="alert"
             class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            <p class="mb-1 font-semibold">Please review the following:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Account Form -->
    <form
        method="POST"
        action="{{ route('superadmin.accounts.store') }}"
        x-data="{ showPassword: false, showConfirmation: false }"
        class="w-full rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-7 lg:p-8">

        @csrf

        <!-- Form Heading -->
        <div class="mb-5 flex items-start justify-between gap-3 border-b border-gray-100 pb-4 dark:border-gray-700">
            <div>
                <h2 class="text-base font-semibold text-gray-800 dark:text-white">
                    Account Information
                </h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Complete the required information to register a new account.
                </p>
            </div>

            <span class="shrink-0 rounded-full bg-[#607844]/10 px-3 py-1 text-xs font-semibold text-[#607844] dark:bg-[#607844]/20 dark:text-[#a9c18e]">
                Authorized Registration
            </span>
        </div>

        <div class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2">

            <!-- First Name -->
            <div>
                <label for="fname" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    First Name <span class="text-red-500">*</span>
                </label>

                <input
                    id="fname"
                    type="text"
                    name="fname"
                    value="{{ old('fname') }}"
                    required
                    maxlength="50"
                    autocomplete="given-name"
                    placeholder="Enter first name"
                    @error('fname') aria-invalid="true" @enderror
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                @error('fname')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label for="lname" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Last Name <span class="text-red-500">*</span>
                </label>

                <input
                    id="lname"
                    type="text"
                    name="lname"
                    value="{{ old('lname') }}"
                    required
                    maxlength="50"
                    autocomplete="family-name"
                    placeholder="Enter last name"
                    @error('lname') aria-invalid="true" @enderror
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                @error('lname')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="sm:col-span-2">
                <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    CCA Email Address <span class="text-red-500">*</span>
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    autocomplete="email"
                    placeholder="username@cca.edu.ph"
                    @error('email') aria-invalid="true" @enderror
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Only authorized @cca.edu.ph email addresses are accepted.
                </p>

                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role_id" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Account Role <span class="text-red-500">*</span>
                </label>

                <select
                    id="role_id"
                    name="role_id"
                    required
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                    <option value="">Select authorized role</option>

                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" data-role-name="{{ strtolower($role->name) }}" @selected(old('role_id') == $role->id)>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>

                @error('role_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Personnel Designation: shown only for Personnel role -->
            <div id="personnelDesignationWrapper" class="hidden">
                <label for="designation" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Personnel Designation <span class="text-red-500">*</span>
                </label>

                <select
                    id="designation"
                    name="designation"
                    disabled
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                    <option value="">Select designation</option>
                    <option value="CDSO Head" @selected(old('designation') === 'CDSO Head')>CDSO Head</option>
                    <option value="Utility Supervisor" @selected(old('designation') === 'Utility Supervisor')>Utility Supervisor</option>
                    <option value="Supply Officer" @selected(old('designation') === 'Supply Officer')>Supply Officer</option>
                    <option value="Office Secretary" @selected(old('designation') === 'Office Secretary')>Office Secretary</option>
                    <option value="Maintenance" @selected(old('designation') === 'Maintenance')>Maintenance</option>
                    <option value="Utility" @selected(old('designation') === 'Utility')>Utility</option>
                    <option value="Gardener" @selected(old('designation') === 'Gardener')>Gardener</option>
                    <option value="Gardener/Utility" @selected(old('designation') === 'Gardener/Utility')>Gardener/Utility</option>
                    <option value="Team Leader" @selected(old('designation') === 'Team Leader')>Team Leader</option>
                </select>

                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Used to distinguish personnel in maintenance assignments. Does not change their system permissions.
                </p>

                @error('designation')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Password <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        type="password"
                        name="password"
                        required
                        minlength="12"
                        maxlength="128"
                        autocomplete="new-password"
                        placeholder="Enter a strong password"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 pr-16 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        class="absolute inset-y-0 right-3 text-xs font-semibold text-[#607844] hover:underline dark:text-[#a9c18e]">
                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                    </button>
                </div>

                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Minimum 12 characters, including uppercase, lowercase, a number, and a symbol.
                </p>

                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="sm:col-span-2">
                <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Confirm Password <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <input
                        id="password_confirmation"
                        :type="showConfirmation ? 'text' : 'password'"
                        type="password"
                        name="password_confirmation"
                        required
                        minlength="12"
                        maxlength="128"
                        autocomplete="new-password"
                        placeholder="Re-enter the password"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 pr-16 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                    <button
                        type="button"
                        @click="showConfirmation = !showConfirmation"
                        :aria-label="showConfirmation ? 'Hide confirmation password' : 'Show confirmation password'"
                        class="absolute inset-y-0 right-3 text-xs font-semibold text-[#607844] hover:underline dark:text-[#a9c18e]">
                        <span x-text="showConfirmation ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Security Notice -->
        <div class="mt-5 flex gap-3 rounded-lg border border-[#607844]/20 bg-[#607844]/5 p-3.5 dark:bg-[#607844]/10">
            <div class="mt-0.5 shrink-0 text-[#607844] dark:text-[#a9c18e]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4.5 6v5.5c0 4.5 3.1 7.8 7.5 9.5 4.4-1.7 7.5-5 7.5-9.5V6L12 3Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>
                </svg>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    Account Security
                </p>
                <p class="mt-0.5 text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                    Account information is validated before registration. Passwords are securely hashed, and account creation is restricted to authorized administrators.
                </p>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">

            <a
                href="{{ auth()->user()->role->name === 'superadmin' ? route('acc_superadmin') : route('accountsmanagement') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#607844] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/40 focus:ring-offset-2">
                Create Account
            </button>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role_id');
    const designationWrapper = document.getElementById('personnelDesignationWrapper');
    const designationSelect = document.getElementById('designation');

    if (!roleSelect || !designationWrapper || !designationSelect) {
        return;
    }

    function toggleDesignation() {
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const selectedRole = selectedOption?.dataset.roleName || '';
        const isPersonnel = selectedRole === 'personnel';

        designationWrapper.classList.toggle('hidden', !isPersonnel);
        designationSelect.disabled = !isPersonnel;
        designationSelect.required = isPersonnel;

        if (!isPersonnel) {
            designationSelect.value = '';
        }
    }

    roleSelect.addEventListener('change', toggleDesignation);
    toggleDesignation();
});
</script>

@endsection
