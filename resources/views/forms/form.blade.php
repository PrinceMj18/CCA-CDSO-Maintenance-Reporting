@extends('layouts.apps')

@section('content')

    <div class="w-full max-w-5xl px-4 py-4 mx-auto">

        <!-- BACK TO DASHBOARD -->
        <div class="mb-4">
            <a href="{{ route('userDashboard') }}" class="inline-flex items-center text-lg text-gray-500
                               hover:text-indigo-600
                               dark:text-gray-400 dark:hover:text-indigo-400">

                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />

                </svg>

                Back to Dashboard

            </a>
        </div>

        <form method="POST" action="/request/store" enctype="multipart/form-data" class="w-full max-w-5xl space-y-6">
            @csrf
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                    <p class="font-semibold">Please correct the following errors:</p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- HEADER -->
            <div>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                    Submit Request
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    This information will be reported to CDSO.
                </p>
                <div class="mt-3 flex items-start gap-3 rounded-lg border border-amber-200
                    bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20" role="note">

                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>

                    <div>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                            Important Reminder
                        </p>
                        <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">
                            Please review your request details carefully before submitting.
                            Once submitted, your request cannot be edited. If you need to
                            correct or update any information, please use
                            the available communication channels to contact the CDSO office.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FORM CARD -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-md">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Title -->
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <label for="title" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Title <span class="text-red-500">*</span>
                            </label>

                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Examples: Leaking Faucet, Broken Light, Damaged Door
                            </p>
                        </div>

                        <input type="text" id="title" name="title" required maxlength="255" value="{{ old('title') }}"
                            placeholder="Enter request title" class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 px-3 py-2 text-sm
                       text-gray-800 dark:text-white
                       placeholder:text-gray-400 dark:placeholder:text-gray-500
                       focus:ring-2 focus:ring-[#607844] focus:border-[#607844]
                       focus:outline-none transition
                       @error('title') border-red-500 @enderror">

                        @error('title')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="space-y-1">
                        <label for="location-search" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Specific Location
                        </label>

                        <div class="relative">
                            <div class="flex gap-2">
                                <input type="text" id="location-search" required autocomplete="off"
                                    placeholder="Search or select a location" value="" class="min-w-0 flex-1 rounded-lg border border-gray-300 dark:border-gray-600
                                                   bg-white dark:bg-gray-700 px-3 py-2 text-sm
                                                   text-gray-800 dark:text-white
                                                   focus:ring-2 focus:ring-indigo-500 focus:outline-none">

                                <button type="button" id="clear-location" class="shrink-0 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium
                                                   text-gray-600 hover:bg-gray-100
                                                   dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                    Clear
                                </button>
                            </div>

                            <div id="location-options" class="absolute left-0 right-0 top-full z-50 mt-1 hidden max-h-52 overflow-y-auto
                                               rounded-lg border border-gray-200 bg-white py-1 shadow-lg
                                               dark:border-gray-600 dark:bg-gray-800"></div>

                            <select id="location" name="location" class="hidden" aria-hidden="true" tabindex="-1">
                                <option value="" disabled {{ old('location') ? '' : 'selected' }}>
                                    Select a location
                                </option>

                                <option value="Auditorium">Auditorium</option>
                                <option value="BFO">BFO</option>
                                <option value="CAO">CAO</option>
                                <option value="Canteen">Canteen</option>
                                <option value="CDSO">CDSO</option>
                                <option value="CEO">CEO</option>
                                <option value="CFGO">CFGO</option>
                                <option value="Clinic">Clinic</option>
                                <option value="Cisco Lab" @selected(old('location') === 'Cisco Lab')>Cisco Lab</option>
                                <option value="CLAB1">CLAB1</option>
                                <option value="CLAB2">CLAB2</option>
                                <option value="CLAB3">CLAB3</option>
                                <option value="CLAB4">CLAB4</option>
                                <option value="CLAB5">CLAB5</option>
                                <option value="CLAB6">CLAB6</option>
                                <option value="CLAB7">CLAB7</option>
                                <option value="CLAB8">CLAB8</option>
                                <option value="CLAB9">CLAB9</option>
                                <option value="Contact Center" @selected(old('location') === 'Contact Center')>Contact Center
                                </option>
                                <option value="Corridor">Corridor</option>
                                <option value="Gymnasium">Gymnasium</option>
                                <option value="Guidance Office">Guidance Office</option>
                                <option value="HLAB">HLAB</option>
                                <option value="HRMO">HRMO</option>
                                <option value="ICSLIS Faculty">ICSLIS Faculty</option>
                                <option value="IBM Faculty">IBM Faculty</option>
                                <option value="IEAS Faculty">IEAS Faculty</option>
                                <option value="Library">Library</option>
                                <option value="MISSO">MISSO</option>
                                <option value="MPH" @selected(old('location') === 'MPH')>MPH</option>
                                <option value="Outdoor Area">Outdoor Area</option>
                                <option value="OVPREQA">OVPREQA</option>
                                <option value="OVPSA">OVPSA</option>
                                <option value="Parking Area">Parking Area</option>
                                <option value="RMO">RMO</option>
                                <option value="SASO">SASO</option>
                                <option value="Stairway">Stairway</option>
                                <!-- Computer Laboratories -->
                                <option value="CLAB1">CLAB1</option>
                                <option value="CLAB2">CLAB2</option>
                                <option value="CLAB3">CLAB3</option>
                                <option value="CLAB4">CLAB4</option>
                                <option value="CLAB5">CLAB5</option>
                                <option value="CLAB6">CLAB6</option>
                                <option value="CLAB7">CLAB7</option>
                                <option value="CLAB8">CLAB8</option>
                                <option value="CLAB9">CLAB9</option>
                                <option value="HLAB">HLAB</option>

                                <!-- R Building - 2nd Floor -->
                                <option value="R201">R201</option>
                                <option value="R202">R202</option>
                                <option value="R203">R203</option>
                                <option value="R204">R204</option>
                                <option value="R205">R205</option>
                                <option value="R206">R206</option>
                                <option value="R207">R207</option>
                                <option value="R208">R208</option>
                                <option value="R209">R209</option>

                                <!-- R Building - 3rd Floor -->
                                <option value="R301">R301</option>
                                <option value="R302">R302</option>
                                <option value="R303">R303</option>
                                <option value="R304">R304</option>
                                <option value="R305">R305</option>
                                <option value="R306">R306</option>
                                <option value="R307">R307</option>
                                <option value="R308">R308</option>
                                <option value="R309">R309</option>

                                <!-- R Building - 4th Floor -->
                                <option value="R401">R401</option>
                                <option value="R402">R402</option>
                                <option value="R403">R403</option>
                                <option value="R404">R404</option>
                                <option value="R405">R405</option>
                                <option value="R406">R406</option>
                                <option value="R407">R407</option>
                                <option value="R408">R408</option>
                                <option value="R409">R409</option>

                                <!-- L Building - 2nd Floor -->
                                <option value="L201">L201</option>
                                <option value="L202">L202</option>
                                <option value="L203">L203</option>
                                <option value="L204">L204</option>
                                <option value="L205">L205</option>
                                <option value="L206">L206</option>
                                <option value="L207">L207</option>
                                <option value="L208">L208</option>
                                <option value="L209">L209</option>

                                <!-- L Building - 3rd Floor -->
                                <option value="L301">L301</option>
                                <option value="L302">L302</option>
                                <option value="L303">L303</option>
                                <option value="L304">L304</option>
                                <option value="L305">L305</option>
                                <option value="L306">L306</option>
                                <option value="L307">L307</option>
                                <option value="L308">L308</option>
                                <option value="L309">L309</option>

                                <!-- L Building - 4th Floor -->
                                <option value="L401">L401</option>
                                <option value="L402">L402</option>
                                <option value="L403">L403</option>
                                <option value="L404">L404</option>
                                <option value="L405">L405</option>
                                <option value="L406">L406</option>
                                <option value="L407">L407</option>
                                <option value="L408">L408</option>
                                <option value="L409">L409</option>

                                <!-- L Building - Comfort Rooms -->
                                <option value="L1-CR-Left-Mini">L1 - Left Mini CR</option>
                                <option value="L1-CR-Left-Large">L1 - Left Large CR</option>
                                <option value="L1-CR-Right-Mini">L1 - Right Mini CR</option>
                                <option value="L1-CR-Right-Large">L1 - Right Large CR</option>

                                <option value="L2-CR-Left-Mini">L2 - Left Mini CR</option>
                                <option value="L2-CR-Left-Large">L2 - Left Large CR</option>
                                <option value="L2-CR-Right-Mini">L2 - Right Mini CR</option>
                                <option value="L2-CR-Right-Large">L2 - Right Large CR</option>

                                <option value="L3-CR-Left-Mini">L3 - Left Mini CR</option>
                                <option value="L3-CR-Left-Large">L3 - Left Large CR</option>
                                <option value="L3-CR-Right-Mini">L3 - Right Mini CR</option>
                                <option value="L3-CR-Right-Large">L3 - Right Large CR</option>

                                <option value="L4-CR-Left-Mini">L4 - Left Mini CR</option>
                                <option value="L4-CR-Left-Large">L4 - Left Large CR</option>
                                <option value="L4-CR-Right-Mini">L4 - Right Mini CR</option>
                                <option value="L4-CR-Right-Large">L4 - Right Large CR</option>
                                <!-- R Building - Comfort Rooms -->
                                <option value="R1-CR-Left-Mini">R1 - Left Mini CR</option>
                                <option value="R1-CR-Left-Large">R1 - Left Large CR</option>
                                <option value="R1-CR-Right-Mini">R1 - Right Mini CR</option>
                                <option value="R1-CR-Right-Large">R1 - Right Large CR</option>

                                <option value="R2-CR-Left-Mini">R2 - Left Mini CR</option>
                                <option value="R2-CR-Left-Large">R2 - Left Large CR</option>
                                <option value="R2-CR-Right-Mini">R2 - Right Mini CR</option>
                                <option value="R2-CR-Right-Large">R2 - Right Large CR</option>

                                <option value="R3-CR-Left-Mini">R3 - Left Mini CR</option>
                                <option value="R3-CR-Left-Large">R3 - Left Large CR</option>
                                <option value="R3-CR-Right-Mini">R3 - Right Mini CR</option>
                                <option value="R3-CR-Right-Large">R3 - Right Large CR</option>

                                <option value="R4-CR-Left-Mini">R4 - Left Mini CR</option>
                                <option value="R4-CR-Left-Large">R4 - Left Large CR</option>
                                <option value="R4-CR-Right-Mini">R4 - Right Mini CR</option>
                                <option value="R4-CR-Right-Large">R4 - Right Large CR</option>

                                <!-- P Building - 1st Floor -->
                                <option value="P101">P101</option>
                                <option value="P102">P102</option>
                                <option value="P103">P103</option>
                                <option value="P104">P104</option>
                                <option value="P105">P105</option>
                                <option value="P106">P106</option>
                                <option value="P107">P107</option>
                                <option value="P108">P108</option>
                                <option value="P109">P109</option>

                                <!-- P Building - 2nd Floor -->
                                <option value="P201">P201</option>
                                <option value="P202">P202</option>
                                <option value="P203">P203</option>
                                <option value="P204">P204</option>
                                <option value="P205">P205</option>
                                <option value="P206">P206</option>
                                <option value="P207">P207</option>
                                <option value="P208">P208</option>
                                <option value="P209">P209</option>

                                <!-- P Building - 3rd Floor -->
                                <option value="P301">P301</option>
                                <option value="P302">P302</option>
                                <option value="P303">P303</option>
                                <option value="P304">P304</option>
                                <option value="P305">P305</option>
                                <option value="P306">P306</option>
                                <option value="P307">P307</option>
                                <option value="P308">P308</option>
                                <option value="P309">P309</option>

                                <!-- P Building - 4th Floor -->
                                <option value="P401">P401</option>
                                <option value="P402">P402</option>
                                <option value="P403">P403</option>
                                <option value="P404">P404</option>
                                <option value="P405">P405</option>
                                <option value="P406">P406</option>
                                <option value="P407">P407</option>
                                <option value="P408">P408</option>
                                <option value="P409">P409</option>

                                <!-- P Building - Comfort Rooms -->
                                <option value="P1-CR-Left-Mini">P1 - Left Mini CR</option>
                                <option value="P1-CR-Left-Large">P1 - Left Large CR</option>
                                <option value="P1-CR-Right-Mini">P1 - Right Mini CR</option>
                                <option value="P1-CR-Right-Large">P1 - Right Large CR</option>

                                <option value="P2-CR-Left-Mini">P2 - Left Mini CR</option>
                                <option value="P2-CR-Left-Large">P2 - Left Large CR</option>
                                <option value="P2-CR-Right-Mini">P2 - Right Mini CR</option>
                                <option value="P2-CR-Right-Large">P2 - Right Large CR</option>

                                <option value="P3-CR-Left-Mini">P3 - Left Mini CR</option>
                                <option value="P3-CR-Left-Large">P3 - Left Large CR</option>
                                <option value="P3-CR-Right-Mini">P3 - Right Mini CR</option>
                                <option value="P3-CR-Right-Large">P3 - Right Large CR</option>

                                <option value="P4-CR-Left-Mini">P4 - Left Mini CR</option>
                                <option value="P4-CR-Left-Large">P4 - Left Large CR</option>
                                <option value="P4-CR-Right-Mini">P4 - Right Mini CR</option>
                                <option value="P4-CR-Right-Large">P4 - Right Large CR</option>

                                <option value="Other">Other</option>
                            </select>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Other Location - Separate Grid Row -->
                        <div id="other-location-container" class="hidden md:col-span-2 space-y-1">

                            <label for="other_location" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Specify Location
                            </label>

                            <input type="text" id="other_location" name="other_location" value="{{ old('other_location') }}"
                                placeholder="Enter your specific location" class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 px-3 py-2 text-sm
                       text-gray-800 dark:text-white focus:ring-2
                       focus:ring-indigo-500 focus:outline-none">
                        </div>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Category
                        </label>

                        <select name="category" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                   bg-white dark:bg-gray-700 px-3 py-2 text-sm
                   text-gray-800 dark:text-white focus:ring-2
                   focus:ring-indigo-500 focus:outline-none
                   @error('category') border-red-500 @enderror">
                            <option value="" disabled {{ old('category') ? '' : 'selected' }}>
                                Select category
                            </option>

                            @foreach ([
                                    'Electrical',
                                    'Plumbing',
                                    'Furniture',
                                    'IT Equipment',
                                    'Facility',
                                    'General Services'
                                ] as $category)
                                <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>

                        @error('category')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Optional Request Images -->
                    <div class="space-y-3 md:col-span-2">
                        <label for="request-images" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Attach photos <span class="font-normal text-gray-500">(optional)</span>
                        </label>

                        <input id="request-images" type="file" name="images[]"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white" />

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Up to 5 photos, maximum 5 MB each. Accepted formats: JPG, PNG, WebP.
                            You can remove selected photos before submitting.
                        </p>

                        <div id="image-preview-container"
                            class="flex w-full max-w-full gap-2 overflow-x-auto overflow-y-hidden pb-2">
                        </div>

                        @error('images')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @error('images.*')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description <span class="font-normal text-gray-500">(optional)</span>
                        </label>

                        <textarea name="description" rows="5" placeholder="Describe the issue in detail..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                   bg-white dark:bg-gray-700 px-3 py-2 text-sm
                   text-gray-800 dark:text-white focus:ring-2
                   focus:ring-indigo-500 focus:outline-none">{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Brief description of your concern.
                        </p>
                    </div>

                </div>
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-3">

                <button type="submit" class="px-6 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700
                                   text-sm font-semibold text-white transition">
                    Submit Request
                </button>

            </div>

        </form>

    </div>
    <!-- Confirm Image Removal Modal -->
    <div id="delete-image-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/50 px-4">

        <div class="w-full max-w-xs rounded-xl bg-white p-4 shadow-xl dark:bg-gray-800">

            <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-9 0h12" />
                </svg>
            </div>

            <h3 class="text-center text-base font-bold text-gray-800 dark:text-white">
                Remove Photo?
            </h3>

            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                Are you sure you want to remove this uploaded photo?
            </p>

            <div class="mt-5 flex gap-2">
                <button type="button" id="cancel-delete-image"
                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Cancel
                </button>

                <button type="button" id="confirm-delete-image"
                    class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Remove
                </button>
            </div>
        </div>
    </div>
    <script>


        document.addEventListener('DOMContentLoaded', function () {

            // SEARCHABLE LOCATION DROPDOWN
            const locationSelect = document.getElementById('location');
            const locationSearch = document.getElementById('location-search');
            const locationOptions = document.getElementById('location-options');
            const clearLocationButton = document.getElementById('clear-location');
            const otherContainer = document.getElementById('other-location-container');
            const otherInput = document.getElementById('other_location');

            function toggleOtherLocation() {
                const isOther = locationSelect.value === 'Other';

                otherContainer.classList.toggle('hidden', !isOther);
                otherInput.required = isOther;

                if (!isOther) {
                    otherInput.value = '';
                }
            }

            function renderLocationOptions(search = '') {
                locationOptions.innerHTML = '';

                const filteredOptions = Array.from(locationSelect.options).filter(option => {
                    return option.value &&
                        option.textContent.toLowerCase().includes(search.toLowerCase());
                });

                if (filteredOptions.length === 0) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.className = 'px-3 py-2 text-sm text-gray-500 dark:text-gray-400';
                    emptyMessage.textContent = 'No locations found.';
                    locationOptions.appendChild(emptyMessage);
                } else {
                    filteredOptions.forEach(option => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.textContent = option.textContent;
                        button.className = 'block w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700';

                        button.addEventListener('click', function () {
                            locationSelect.value = option.value;
                            locationSearch.value = option.textContent;
                            locationSearch.setCustomValidity('');
                            locationOptions.classList.add('hidden');
                            toggleOtherLocation();
                        });

                        locationOptions.appendChild(button);
                    });
                }

                locationOptions.classList.remove('hidden');
            }

            clearLocationButton.addEventListener('click', function () {
                locationSelect.value = '';
                locationSearch.value = '';
                locationSearch.setCustomValidity('Please select a location from the dropdown.');
                toggleOtherLocation();
                renderLocationOptions('');
                locationSearch.focus();
            });

            locationSearch.addEventListener('focus', function () {
                renderLocationOptions(this.value);
            });

            locationSearch.addEventListener('input', function () {
                locationSelect.value = '';
                toggleOtherLocation();
                locationSearch.setCustomValidity('Please select a location from the dropdown.');
                renderLocationOptions(this.value);
            });

            document.addEventListener('click', function (event) {
                if (!locationSearch.contains(event.target) &&
                    !locationOptions.contains(event.target)) {
                    locationOptions.classList.add('hidden');
                }
            });

            // Restore the previously selected location after validation errors.
            const oldLocation = @json(old('location'));
            if (oldLocation) {
                const selectedOption = Array.from(locationSelect.options)
                    .find(option => option.value === oldLocation);

                if (selectedOption) {
                    locationSelect.value = oldLocation;
                    locationSearch.value = selectedOption.textContent;
                    locationSearch.setCustomValidity('');
                }
            }

            // Prevent submission unless a valid dropdown option was selected.
            document.querySelector('form').addEventListener('submit', function (event) {
                if (!locationSelect.value) {
                    event.preventDefault();
                    locationSearch.setCustomValidity('Please select a location from the dropdown.');
                    locationSearch.reportValidity();
                    locationSearch.focus();
                }
            });

            toggleOtherLocation();


            // IMAGE ERROR MODAL
            const imageErrorModal = document.getElementById('image-error-modal');
            const imageErrorMessage = document.getElementById('image-error-message');
            const closeImageErrorModal = document.getElementById('close-image-error-modal');

            function showImageError(message) {
                imageErrorMessage.textContent = message;
                imageErrorModal.classList.remove('hidden');
                imageErrorModal.classList.add('flex');
            }

            function closeImageError() {
                imageErrorModal.classList.add('hidden');
                imageErrorModal.classList.remove('flex');
            }

            closeImageErrorModal.addEventListener('click', closeImageError);

            imageErrorModal.addEventListener('click', function (event) {
                if (event.target === imageErrorModal) {
                    closeImageError();
                }
            });


            // IMAGE DELETE CONFIRMATION MODAL
            const deleteImageModal = document.getElementById('delete-image-modal');
            const cancelDeleteImage = document.getElementById('cancel-delete-image');
            const confirmDeleteImage = document.getElementById('confirm-delete-image');

            let imageToDelete = null;

            function closeDeleteImageModal() {
                deleteImageModal.classList.add('hidden');
                deleteImageModal.classList.remove('flex');
                imageToDelete = null;
            }

            cancelDeleteImage.addEventListener('click', closeDeleteImageModal);

            deleteImageModal.addEventListener('click', function (event) {
                if (event.target === deleteImageModal) {
                    closeDeleteImageModal();
                }
            });


            // IMAGE PREVIEW AND VALIDATION
            const imageInput = document.getElementById('request-images');
            const previewContainer = document.getElementById('image-preview-container');

            if (imageInput && previewContainer) {

                let selectedImages = new DataTransfer();

                imageInput.addEventListener('change', function () {

                    const newFiles = Array.from(this.files);
                    let hasError = false;

                    newFiles.forEach(file => {

                        if (selectedImages.files.length >= 5) {
                            if (!hasError) {
                                showImageError('You can only upload up to 5 photos.');
                            }
                            hasError = true;
                            return;
                        }

                        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                            if (!hasError) {
                                showImageError('Only JPG, PNG, and WebP images are allowed.');
                            }
                            hasError = true;
                            return;
                        }

                        if (file.size > 5 * 1024 * 1024) {
                            if (!hasError) {
                                showImageError(`${file.name} exceeds the 5 MB file size limit.`);
                            }
                            hasError = true;
                            return;
                        }

                        selectedImages.items.add(file);
                    });

                    imageInput.files = selectedImages.files;
                    renderImagePreviews();
                });


                function renderImagePreviews() {

                    previewContainer.innerHTML = '';

                    Array.from(selectedImages.files).forEach((file, index) => {

                        const imageUrl = URL.createObjectURL(file);

                        const wrapper = document.createElement('div');
                        wrapper.className = 'w-24 shrink-0 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-600 sm:w-28';

                        const image = document.createElement('img');
                        image.src = imageUrl;
                        image.alt = file.name;
                        image.className = 'aspect-square h-24 w-full object-cover sm:h-28';

                        const details = document.createElement('div');
                        details.className = 'flex items-center justify-between gap-2 bg-gray-50 px-2 py-2 dark:bg-gray-700';

                        const fileName = document.createElement('span');
                        fileName.className = 'min-w-0 truncate text-xs text-gray-600 dark:text-gray-300';
                        fileName.textContent = file.name;

                        const removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.textContent = 'Remove';
                        removeButton.className = 'shrink-0 text-xs font-semibold text-red-600 hover:text-red-800';

                        // OPEN DELETE CONFIRMATION
                        removeButton.addEventListener('click', function () {

                            imageToDelete = {
                                index: index,
                                imageUrl: imageUrl
                            };

                            deleteImageModal.classList.remove('hidden');
                            deleteImageModal.classList.add('flex');
                        });

                        details.appendChild(fileName);
                        details.appendChild(removeButton);

                        wrapper.appendChild(image);
                        wrapper.appendChild(details);

                        previewContainer.appendChild(wrapper);
                    });
                }


                // CONFIRM IMAGE DELETION
                confirmDeleteImage.addEventListener('click', function () {

                    if (imageToDelete === null) return;

                    const indexToDelete = imageToDelete.index;

                    const updatedImages = new DataTransfer();

                    Array.from(selectedImages.files).forEach((file, index) => {
                        if (index !== indexToDelete) {
                            updatedImages.items.add(file);
                        }
                    });

                    selectedImages = updatedImages;
                    imageInput.files = selectedImages.files;

                    closeDeleteImageModal();
                    renderImagePreviews();
                });
            }

        });

    </script>

    <!-- Image Upload Error Modal -->
    <div id="image-error-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4">

        <div class="w-full max-w-xs rounded-xl bg-white p-4 shadow-xl dark:bg-gray-800">

            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v4m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.7 3h16.96a2 2 0 001.7-3L13.7 3.86a2 2 0 00-3.4 0z" />
                </svg>
            </div>

            <h3 class="text-center text-lg font-bold text-gray-800 dark:text-white">
                Image Upload Error
            </h3>

            <p id="image-error-message" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
            </p>

            <button type="button" id="close-image-error-modal"
                class="mt-5 w-full rounded-lg bg-[#607844] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#4e6337]">
                Okay, Got it
            </button>
        </div>
    </div>
@endsection