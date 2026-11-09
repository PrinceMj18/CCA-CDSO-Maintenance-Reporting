<div id="inventoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-sm">

        <div class="flex justify-center min-h-screen p-4">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg my-8">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 p-5">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Add Maintenance Material
                        </h2>

                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                            Register a new inventory item for maintenance operations.
                        </p>
                    </div>

                    <button type="button" onclick="closeInventoryModal()"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-2xl">
                        &times;
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('save_inventory') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-4 sm:p-6">

                        <!-- Material Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Material Name
                            </label>

                            <input type="text" name="name"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                placeholder="Ex. LED Bulb 12W">
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Category
                            </label>

                            <select name="category"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <option>Equipments</option>
                                <option>Tools</option>
                                <option>Furniture</option>
                                <option>Electrical Supplies</option>
                                <option>Plumbing Supplies</option>
                                <option>School Supplies</option>
                            </select>
                        </div>

                        <!-- Unit -->
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Unit
                            </label>

                            <select name="unit"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <option>Pieces</option>
                                <option>Boxes</option>
                                <option>Rolls</option>
                                <option>Cans</option>
                                <option>Pairs</option>
                                <option>Sets</option>
                                <option>Bags</option>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Quantity
                            </label>

                            <input type="number" name="quantity"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                placeholder="0">
                        </div>

                        <!-- Minimum Stock -->
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Minimum Stock
                            </label>

                            <input type="number" name="minimum_stock"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                placeholder="10">
                        </div>

                        <!-- Cost -->
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Unit Cost (₱)
                            </label>

                            <input type="number" step="0.01" name="unit_cost"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                placeholder="0.00">
                        </div>

                        <!-- Storage -->
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Storage Location
                            </label>

                            <input type="text" name="location"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                placeholder="Stock Room A">
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                                Description (Optional)
                            </label>

                            <textarea rows="3" name="description"
                                class="w-full rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                placeholder="Additional notes..."></textarea>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-4">

                        <button type="button" onclick="closeInventoryModal()"
                            class="w-full sm:w-auto px-5 py-3 sm:py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Cancel
                        </button>

                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-3 sm:py-2 rounded-xl bg-[#607844] text-xs font-semibold text-white
           transition hover:bg-[#52693a]">
                            Save Material
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
    </div>