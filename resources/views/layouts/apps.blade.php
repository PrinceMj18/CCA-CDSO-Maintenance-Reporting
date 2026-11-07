<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}?v=2">
    <title>CDSO Maintenance</title>
    @vite('resources/css/app.css')

    <style>
        /* Hide scrollbar but keep scrolling */
        .hide-scrollbar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }
    </style>
</head>

<body x-data="data()" :class="{ 'theme-dark': dark }">

<div class="cdso-app-shell flex min-h-screen bg-gray-50 dark:bg-gray-900" x-data="{ mobileSidebarOpen: false }" @keydown.escape.window="mobileSidebarOpen = false">

    {{-- MOBILE SIDEBAR BACKDROP --}}
    @auth
        <div x-cloak x-show="mobileSidebarOpen" x-transition.opacity
             @click="mobileSidebarOpen = false"
             class="cdso-sidebar-backdrop fixed inset-0 z-30 bg-slate-950/55 md:hidden"
             aria-hidden="true"></div>

        {{-- The same role-aware navigation is used on desktop and as a mobile drawer. --}}
        @includeIf('components.sidebar.' . auth()->user()->role->name)
    @endauth

    <div class="flex flex-col flex-1 w-full">

        {{-- HEADER --}}
        @include('components.header.header')

        {{-- PAGE CONTENT --}}
        <main class="cdso-main h-full min-w-0 flex-1 overflow-y-auto hide-scrollbar">
    <div class="cdso-page-content container px-6 mx-auto grid">
        @yield('content')
    </div>
</main>

    </div>
</div>

@vite('resources/js/app.js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const salesCtx = document.getElementById('salesChart')?.getContext('2d');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun'],
                datasets: [{
                    label: 'Monthly Sales',
                    data: [1200,1900,3000,2500,2200,3100],
                    backgroundColor: 'rgba(139, 92, 246, 0.7)',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

});
</script>

<script>
/* Convert wide data tables into labelled, full-width cards on small screens. */
document.addEventListener('DOMContentLoaded', function () {
    const mobileQuery = window.matchMedia('(max-width: 767px)');
    document.querySelectorAll('main table').forEach(function (table) {
        const headerCells = Array.from(table.querySelectorAll('thead tr:first-child th'));
        if (headerCells.length < 2) return;
        table.classList.add('cdso-mobile-cards');
        const labels = headerCells.map(function (cell) { return cell.textContent.trim().replace(/\s+/g, ' '); });
        table.querySelectorAll('tbody tr').forEach(function (row) {
            Array.from(row.children).forEach(function (cell, index) {
                if (cell.tagName !== 'TD') return;
                const label = labels[index] || '';
                if (label && !cell.hasAttribute('data-mobile-label')) cell.setAttribute('data-mobile-label', label);
            });
        });
    });
    function updateMobileTableMode() {
        document.querySelectorAll('main table.cdso-mobile-cards').forEach(function (table) {
            table.classList.toggle('cdso-mobile-cards-active', mobileQuery.matches);
        });
    }
    updateMobileTableMode();
    if (mobileQuery.addEventListener) mobileQuery.addEventListener('change', updateMobileTableMode);
    else mobileQuery.addListener(updateMobileTableMode);
});


document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (event) {

            // Prevent repeated submissions
            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            // Allow native browser validation to finish first
            if (!form.checkValidity()) {
                return;
            }

            form.dataset.submitting = 'true';

            const submitButtons = form.querySelectorAll(
                'button[type="submit"], button:not([type]), input[type="submit"]'
            );

            submitButtons.forEach(button => {
                button.disabled = true;
                button.classList.add('opacity-60', 'cursor-not-allowed');

                if (!button.dataset.originalText) {
                    button.dataset.originalText = button.tagName === 'INPUT'
                        ? button.value
                        : button.innerHTML;
                }

                if (button.tagName === 'INPUT') {
                    button.value = 'Processing...';
                } else {
                    button.innerHTML = 'Processing...';
                }
            });
        });
    });
});

</script>

</body>
</html>