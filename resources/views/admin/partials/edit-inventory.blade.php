@extends('layouts.apps')

@section('content')

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-6">

        <div class="max-w-6xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('inventory') }}"
                    class="inline-flex items-center text-lg text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">

                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />

                    </svg>

                    Back to Inventory
                </a>
            </div>
            <!-- Header -->
            <div class="mb-8">
                <p class="text-sm uppercase tracking-widest text-teal-600 font-semibold">
                    Inventory Management
                </p>

                <h1 class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                    Edit Inventory Material
                </h1>

                <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                    Update the information of this inventory material. Make sure all information is accurate before saving.
                </p>
            </div>

            <form action="{{ route('update_inventory', $materials->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Material Name -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Material Name
                        </label>

                        <input type="text" name="name" value="{{ $materials->name }}"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Category
                        </label>

                        <select name="category"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg">

                            <option {{ $materials->category == 'Equipments' ? 'selected' : '' }}>Equipments</option>
                            <option {{ $materials->category == 'Tools' ? 'selected' : '' }}>Tools</option>
                            <option {{ $materials->category == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                            <option {{ $materials->category == 'Electrical Supplies' ? 'selected' : '' }}>Electrical Supplies</option>
                            <option {{ $materials->category == 'Plumbing Supplies' ? 'selected' : '' }}>Plumbing Supplies</option>
                            <option {{ $materials->category == 'School Supplies' ? 'selected' : '' }}>School Supplies</option>
                        </select>
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Unit
                        </label>

                        <select name="unit"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg">

                            <option {{ $materials->unit == 'Pieces' ? 'selected' : '' }}>Pieces</option>
                            <option {{ $materials->unit == 'Boxes' ? 'selected' : '' }}>Boxes</option>
                            <option {{ $materials->unit == 'Rolls' ? 'selected' : '' }}>Rolls</option>
                            <option {{ $materials->unit == 'Cans' ? 'selected' : '' }}>Cans</option>
                            <option {{ $materials->unit == 'Pairs' ? 'selected' : '' }}>Pairs</option>
                            <option {{ $materials->unit == 'Sets' ? 'selected' : '' }}>Sets</option>

                        </select>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Quantity
                        </label>

                        <input type="number" name="quantity" value="{{ $materials->quantity }}" min="0"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg">
                    </div>

                    <!-- Minimum Stock -->
                    <div>
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Minimum Stock
                        </label>

                        <input type="number" name="minimum_stock" value="{{ $materials->minimum_stock }}"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg">
                    </div>

                    <!-- Unit Cost -->
                    <div>
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Unit Cost (₱)
                        </label>

                        <input type="number" step="0.01" name="unit_cost" value="{{ $materials->unit_cost }}"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg">
                    </div>

                    <!-- Storage -->
                    <div>
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Storage Location
                        </label>

                        <input type="text" name="location" value="{{ $materials->location }}"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                            Description
                        </label>

                        <textarea rows="5" name="description"
                            class="text-gray-700 dark:text-gray-300 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 text-lg">{{ $materials->description }}</textarea>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4 mt-10 pt-6 border-t border-gray-300 dark:border-gray-700">

                    <a href="{{ url()->previous() }}"
                        class="px-8 py-3 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-8 py-3 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-semibold transition">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection