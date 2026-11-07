
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in | City College of Angeles</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}?v=2">
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-[#f5f7f3] antialiased">

    <main class="flex min-h-screen flex-col md:flex-row">

        <!-- LEFT BRANDING PANEL -->
        <section class="relative flex min-h-[180px] w-full flex-col justify-between overflow-hidden bg-[#607844] px-7 py-6 text-white md:min-h-screen md:w-[42%] md:px-12 md:py-10">

            <!-- Decorative background elements -->
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -right-12 -top-12 h-52 w-52 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-24 h-80 w-80 rounded-full bg-white/[0.04]"></div>

            <!-- Logo -->
            <div class="relative z-10">
                <img
                    src="{{ asset('img/logo.png') }}"
                    alt="City College of Angeles"
                    class="mx-auto h-auto w-[105px] object-contain md:mx-0 md:w-[135px]"
                >
            </div>

            <!-- College identity -->
            <div class="relative z-10 my-8 hidden md:block">
                <div class="mb-5 h-1 w-12 rounded-full bg-white/80"></div>

                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-white/70">
                    Welcome to
                </p>

                <h1 class="max-w-md text-4xl font-bold leading-tight tracking-tight lg:text-[42px]">
                    City College<br>
                    of Angeles
                </h1>

                <p class="mt-5 text-base font-medium tracking-wide text-white/90">
                    Totalis Humanae
                </p>

                <p class="mt-8 max-w-sm text-sm leading-6 text-white/70">
                    CDSO Maintenance Reporting System
                </p>
            </div>

            <!-- Branding footer -->
            <div class="relative z-10 hidden border-t border-white/20 pt-5 md:block">
                <p class="text-xs tracking-wide text-white/60">
                    City College of Angeles
                </p>
                <p class="mt-1 text-xs text-white/50">
                    Campus Maintenance Management
                </p>
            </div>

            <!-- Compact mobile title -->
            <div class="relative z-10 mt-2 text-center md:hidden">
                <h1 class="text-lg font-semibold tracking-tight">
                    City College of Angeles
                </h1>
                <p class="mt-1 text-xs text-white/80">
                    CDSO Maintenance Reporting System
                </p>
            </div>

        </section>


        <!-- RIGHT LOGIN SECTION -->
        <section class="flex min-w-0 flex-1 items-center justify-center px-5 py-9 sm:px-8 md:px-12 md:py-12">

            <div class="w-full max-w-[440px]">

                <!-- Login Card -->
                <div class="rounded-2xl border border-[#e8ece4] bg-white px-6 py-8 shadow-[0_12px_40px_rgba(38,55,29,0.06)] sm:px-9 sm:py-10">

                    <!-- Heading -->
                    <div class="mb-8">
                        <div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#607844]/10 text-[#607844]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.1a7.5 7.5 0 0 1 15 0A17.9 17.9 0 0 1 12 21.75c-2.7 0-5.25-.6-7.5-1.65Z"/>
                            </svg>
                        </div>

                        <h2 class="text-2xl font-bold tracking-tight text-[#263321] sm:text-[28px]">
                            Welcome back
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Sign in to access the CDSO Maintenance Reporting System.
                        </p>
                    </div>


                    <!-- Error Message -->
                    @if ($errors->any())
                        <div role="alert"
                            class="mb-6 flex gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="mt-0.5 h-5 w-5 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" d="M12 8v4m0 4h.01"/>
                            </svg>

                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif


                    <!-- Login Form -->
                    <form id="loginForm" action="{{ url('/login') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-[#34402f]">
                                Email Address
                            </label>

                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8">
                                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/>
                                    </svg>
                                </span>

                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autocomplete="email"
                                    value="{{ old('email') }}"
                                    placeholder="Enter your email address"
                                    class="block h-[52px] w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#607844] focus:ring-4 focus:ring-[#607844]/10"
                                >
                            </div>
                        </div>


                        <!-- Password -->
                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-[#34402f]">
                                Password
                            </label>

                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8">
                                        <rect x="4" y="10" width="16" height="11" rx="2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 10V7a4 4 0 1 1 8 0v3"/>
                                    </svg>
                                </span>

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                    class="block h-[52px] w-full rounded-lg border border-gray-200 bg-white pl-11 pr-12 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#607844] focus:ring-4 focus:ring-[#607844]/10"
                                >

                                <button
                                    type="button"
                                    onclick="togglePassword()"
                                    id="passwordToggle"
                                    class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-gray-400 transition hover:text-[#607844] focus:outline-none focus:text-[#607844]"
                                    aria-label="Show password"
                                    aria-controls="password"
                                    aria-pressed="false"
                                >
                                    <svg id="eyeIcon"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>


                        <!-- Remember Me -->
                        <div class="flex items-center pt-1">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 cursor-pointer rounded border-gray-300 text-[#607844] accent-[#607844] focus:ring-[#607844]"
                            >

                            <label for="remember" class="ml-2.5 cursor-pointer text-sm text-gray-600">
                                Remember me
                            </label>
                        </div>


                        <!-- Login Button -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="inline-flex h-[52px] w-full items-center justify-center gap-2 rounded-lg bg-[#607844] px-5 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-[#50683a] hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#607844]/20 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span>Log in</span>

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                                </svg>
                            </button>
                        </div>

                    </form>

                </div>


                <!-- System Footer -->
                <footer class="mt-6 flex flex-wrap items-center justify-center gap-x-2 gap-y-2 text-center">

                    <span class="text-[11px] text-gray-400">
                        v1.75.86.88
                    </span>

                    <span class="text-gray-300">|</span>

                    <span class="text-[11px] text-[#607844]">
                        20260929
                    </span>

                    <span class="mx-1 hidden h-4 w-px bg-gray-300 sm:inline-block"></span>

                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-medium text-gray-500">
                            Powered by
                        </span>

                        <span class="relative inline-block h-5 w-5 shrink-0" aria-hidden="true">
                            <span class="absolute left-[6px] top-0 h-[8px] w-[8px] rounded-full bg-[#f58220]"></span>
                            <span class="absolute left-[7px] top-[6px] h-[14px] w-[6px] rotate-[35deg] rounded-full bg-[#f58220]"></span>
                            <span class="absolute left-[7px] top-[6px] h-[14px] w-[6px] -rotate-[35deg] rounded-full bg-[#f58220]"></span>
                        </span>

                        <span class="text-left text-[10px] font-bold leading-[11px] text-[#27304b]">
                            Hurey<br>Campus
                        </span>
                    </div>

                </footer>

                <p class="mt-5 text-center text-[11px] text-gray-400">
                    Authorized personnel only
                </p>

            </div>
        </section>

    </main>


    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const toggle = document.getElementById('passwordToggle');

            if (!password || !eyeIcon || !toggle) return;

            const isShowing = password.type === 'password';

            password.type = isShowing ? 'text' : 'password';

            toggle.setAttribute('aria-pressed', String(isShowing));
            toggle.setAttribute(
                'aria-label',
                isShowing ? 'Hide password' : 'Show password'
            );

            eyeIcon.innerHTML = isShowing
                ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.5 12s3.75 6.75 10.5 6.75c1.82 0 3.4-.45 4.73-1.14M6.23 6.23A10.45 10.45 0 0 1 12 5.25c6.75 0 10.5 6.75 10.5 6.75a18.17 18.17 0 0 1-3.09 3.89M6.23 6.23 3 3m3.23 3.23 3.18 3.18m0 0a3 3 0 0 0 4.24 4.24m-4.24-4.24L3 3m9.75 9.75L21 21"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>`;
        }


        // Prevent repeated login form submissions
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('loginForm');

            if (!loginForm) return;

            loginForm.addEventListener('submit', function (event) {

                if (loginForm.dataset.submitting === 'true') {
                    event.preventDefault();
                    return;
                }

                if (!loginForm.checkValidity()) return;

                loginForm.dataset.submitting = 'true';

                const button = loginForm.querySelector('button[type="submit"]');

                if (button) {
                    button.disabled = true;

                    button.innerHTML = `
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                        </svg>
                        <span>Logging in...</span>
                    `;
                }
            });
        });
    </script>

</body>
</html>
