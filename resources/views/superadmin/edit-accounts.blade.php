@extends('layouts.apps')

@section('content')

@php
$designationOptions = [
    'CDSO Head',
    'Utility Supervisor',
    'Supply Officer',
    'Office Secretary',
    'Maintenance',
    'Utility',
    'Gardener',
    'Gardener/Utility',
    'Team Leader',
];

$storedDesignation = null;
$baseLastName = $user->lname;

foreach ($designationOptions as $designation) {
    $suffix = ' (' . $designation . ')';

    if (str_ends_with($user->lname, $suffix)) {
        $storedDesignation = $designation;
        $baseLastName = substr($user->lname, 0, -strlen($suffix));
        break;
    }
}

$personnelRoleId = $roles->firstWhere('name', 'personnel')?->id;
@endphp

<div class="mx-auto w-full max-w-5xl px-5 py-6 sm:px-8 lg:px-10">

    <!-- Page Header -->
    <div class="mb-5">
        <h1 class="text-2xl font-bold tracking-tight text-gray-800 dark:text-white">
            Update User
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Manage account information, login credentials, and assigned system role.
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

    <!-- Update Form -->
    <form
        action="{{ route('update_account', $user->id) }}"
        method="POST"
x-data="{
    showPassword: false,
    showConfirmation: false,
    selectedRole: '{{ old('role_id', $user->role_id) }}'
}"        class="w-full rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-7 lg:p-8">

        @csrf
        @method('PUT')

        <!-- Form Heading -->
        <div class="mb-5 flex items-start justify-between gap-3 border-b border-gray-100 pb-4 dark:border-gray-700">

            <div>
                <h2 class="text-base font-semibold text-gray-800 dark:text-white">
                    Account Information
                </h2>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Update the user's personal information and account settings.
                </p>
            </div>

            <span class="shrink-0 rounded-full bg-[#607844]/10 px-3 py-1 text-xs font-semibold text-[#607844] dark:bg-[#607844]/20 dark:text-[#a9c18e]">
                Account Management
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
                    value="{{ old('fname', $user->fname) }}"
                    required
                    maxlength="50"
                    autocomplete="given-name"
                    placeholder="Enter first name"
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
                    value="{{ old('lname', $baseLastName) }}"
                    required
                    maxlength="50"
                    autocomplete="family-name"
                    placeholder="Enter last name"
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
                    value="{{ old('email', $user->email) }}"
                    required
                    maxlength="255"
                    autocomplete="email"
                    placeholder="username@cca.edu.ph"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Use an authorized @cca.edu.ph email address.
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
                    @change="selectedRole = $event.target.value"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

                    <option value="">Select role</option>

                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            @selected(old('role_id', $user->role_id) == $role->id)>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach

                </select>

                @error('role_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

<!-- Personnel Designation -->
<div
    x-show="selectedRole === '{{ $personnelRoleId }}'"
    x-cloak
    class="min-w-0">

    <label for="designation"
        class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
        Personnel Designation <span class="text-red-500">*</span>
    </label>

    <select
        id="designation"
        name="designation"
        :required="selectedRole === '{{ $personnelRoleId }}'"
        class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 outline-none transition focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

        <option value="">Select designation</option>

        @foreach($designationOptions as $designation)
            <option value="{{ $designation }}"
                @selected(old('designation', $storedDesignation) === $designation)>
                {{ $designation }}
            </option>
        @endforeach

    </select>

    @error('designation')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

            <!-- Password -->
            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    New Password
                </label>

                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        type="password"
                        name="password"
                        minlength="12"
                        maxlength="128"
                        autocomplete="new-password"
                        placeholder="Leave blank to keep current password"
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
                    Optional. Use at least 12 characters when changing the password.
                </p>

                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="sm:col-span-2">
                <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Confirm New Password
                </label>

                <div class="relative">
                    <input
                        id="password_confirmation"
                        :type="showConfirmation ? 'text' : 'password'"
                        type="password"
                        name="password_confirmation"
                        minlength="12"
                        maxlength="128"
                        autocomplete="new-password"
                        placeholder="Re-enter the new password"
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
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3 4.5 6v5.5c0 4.5 3.1 7.8 7.5 9.5 4.4-1.7 7.5-5 7.5-9.5V6L12 3Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m9 12 2 2 4-4"/>
                </svg>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    Account Security
                </p>

                <p class="mt-0.5 text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                    Changes are validated before being saved. Leave the password fields blank to retain the existing password. New passwords are securely hashed before storage.
                </p>
            </div>

        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">

            <a
                href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">

                Cancel

            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-[#607844] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/40 focus:ring-offset-2">

                Save Changes

            </button>

        </div>

    </form>

</div>

@endsection
