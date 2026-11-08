@extends('layouts.apps')

@section('content')

<div class="py-5">
    <div class="flex items-center justify-between gap-3 mb-5">

        <a href="{{ route('maintenanceRequest.personnel') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-width="2" d="M15 19l-7-7 7-7"/>

            </svg>

            Back to Assigned Requests

        </a>
</div>

    @if(session('success'))

        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>

    @endif

    @if(session('error'))

        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>

    @endif

    @if($errors->any())

        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">

            <p class="font-semibold">Please review the work update:</p>

            <ul class="mt-1 list-disc pl-5 space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif
</div>

    <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        <div class="px-5 py-5 border-b border-gray-100 dark:border-gray-700">

            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">

                <div>

                    <span class="font-mono text-xs font-bold px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">

                        {{ $requestData->ticket_number }}

                    </span>

                    <h1 class="mt-3 text-xl font-bold text-gray-900 dark:text-white">{{ $requestData->title }}</h1>

                    <p class="mt-1 text-sm text-gray-500">{{ $requestData->category }} · {{ $requestData->location }}</p>

                </div>

                <div class="text-left md:text-right">

                    <div class="text-[11px] uppercase tracking-wider text-gray-400">Current status</div>

                    <div class="mt-1 text-sm font-bold text-indigo-700">{{ ucfirst(str_replace('_', ' ', $requestData->status)) }}</div>

                </div>

            </div>

        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>

                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Requester</div>

                <div class="mt-1 text-sm font-semibold">{{ $requestData->user?->fname }} {{ $requestData->user?->lname }}</div>

                <div class="text-xs text-gray-500">{{ $requestData->user?->email }}</div>

            </div>

            <div>

                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Assigned to</div>

                <div class="mt-1 text-sm font-semibold">{{ auth()->user()->fname }} {{ auth()->user()->lname }}</div>

            </div>

            <div>

                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Priority</div>

                <div class="mt-1 text-sm font-semibold">{{ ucfirst($requestData->priority) }}</div>

            </div>

            <div>

                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Submitted</div>

                <div class="mt-1 text-sm font-semibold">{{ $requestData->created_at?->format('M d, Y h:i A') }}</div>

            </div>

            <div class="md:col-span-2">

                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Description</div>

                <div class="mt-2 text-sm leading-6 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $requestData->description }}</div>

            </div>

            @if($requestData->images->isNotEmpty())
                <div class="md:col-span-2">
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Attached photos</div>
                    <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($requestData->images as $image)
                            <a href="{{ route('maintenance-images.show', $image->id) }}" target="_blank" rel="noopener" class="block">
                                <img src="{{ route('maintenance-images.show', $image->id) }}" alt="Photo attached to {{ $requestData->ticket_number }}" class="w-full h-36 object-cover rounded-lg border border-gray-200" loading="lazy">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($requestData->inventoryUsages->count() || $requestData->status === 'completed')
                <div class="md:col-span-2 border-t border-gray-100 dark:border-gray-700 pt-5">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between mb-3">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Materials Used</h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Inventory materials recorded for this maintenance request.</p>
                        </div>
                        @if($requestData->inventoryUsages->count())
                            <span class="inline-flex w-fit items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                {{ $requestData->inventoryUsages->count() }} {{ \Illuminate\Support\Str::plural('item', $requestData->inventoryUsages->count()) }} recorded
                            </span>
                        @endif
                    </div>

                    @if($requestData->inventoryUsages->count())
                        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700/60">
                                        <tr class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                            <th class="px-4 py-3 font-semibold">Material</th>
                                            <th class="px-4 py-3 font-semibold">Quantity used</th>
                                            <th class="px-4 py-3 font-semibold">Date recorded</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        @foreach($requestData->inventoryUsages as $usage)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $usage->item?->name ?? 'Inventory item no longer available' }}</div>
                                                    @if($usage->item?->unit)
                                                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Unit: {{ $usage->item->unit }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-200">{{ $usage->quantity_used }} {{ $usage->item?->unit }}</td>
                                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($usage->used_at ?? $usage->created_at)->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-3 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50/70 dark:bg-gray-700/30 p-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">No inventory materials recorded</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">No materials were logged for this completed request. Check Activity &amp; Remarks for the personnel’s declaration.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </section>

    <section class="mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">

            <h2 class="text-sm font-bold">Work Update</h2>

            <p class="mt-1 text-xs text-gray-500">You can add remarks even when the status does not change.</p>

        </div>

        <form method="POST" action="{{ route('updateRequest.personnel', $requestData->id) }}" class="p-5">

            @csrf

            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>

                    <select id="work-status" name="status" class="w-full min-h-12 rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-base focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">Keep current status ({{ ucfirst(str_replace('_',' ', $requestData->status)) }})</option>

                        @if($requestData->status === 'approved')

                            <option value="in_progress">In Progress</option>

                        @elseif($requestData->status === 'in_progress')

                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>

                        @endif

                    </select>

                </div>

                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Remarks / comments</label>

                    <textarea name="remarks" rows="3" placeholder="Describe the work performed, findings, instructions, or any other update..."

                              class="w-full min-h-28 rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-base leading-relaxed focus:border-indigo-500 focus:ring-indigo-500"></textarea>

                </div>

            </div>

            @if($requestData->status === 'in_progress')

                <div id="materials-section" class="mt-5 border-t border-gray-100 pt-5" style="{{ old('status') === 'completed' ? '' : 'display:none;' }}">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between mb-3">

                        <div>

                            <h3 class="text-xs font-bold text-gray-700">Materials used</h3>

                            <p class="mt-1 text-[11px] text-gray-500">Before completing the request, list every material used or explicitly confirm that no materials were needed. Inventory is deducted only after completion.</p>

                        </div>

                        <button type="button" id="add-material" class="shrink-0 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700">

                            + Add material

                        </button>

                    </div>

                    <div id="materials" class="space-y-2"></div>

                    <label class="mt-4 flex items-start gap-2 rounded-lg border border-gray-200 p-3 text-sm text-gray-700">

                        <input type="checkbox" id="no-materials-used" name="no_materials_used" value="1" {{ old('no_materials_used') ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                        <span>

                            <span class="font-semibold">No materials used</span>

                            <span class="block text-xs text-gray-500">Select this only if the work required no inventory materials.</span>

                        </span>

                    </label>

                    <p class="mt-2 text-xs text-gray-500">You must either add at least one material with its quantity or select “No materials used” before completing the request.</p>

                </div>

            @endif

            <div class="flex justify-end mt-5">

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">

                    Save Work Update

                </button>

            </div>

        </form>

    </section>

    <section class="mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">

            <h2 class="text-sm font-bold">Activity & Remarks</h2>

            <p class="mt-1 text-xs text-gray-500">Complete request history.</p>

        </div>

        <div class="p-5">

            @forelse($requestData->logs->sortByDesc('created_at') as $log)

                <div class="relative pl-8 pb-6 last:pb-0">

                    @if(!$loop->last)<span class="absolute left-[7px] top-4 bottom-0 w-px bg-gray-200"></span>@endif

                    <span class="absolute left-0 top-1 w-4 h-4 rounded-full border-2 border-white bg-indigo-500 shadow-sm"></span>

                    <div class="flex flex-col gap-1 sm:flex-row sm:justify-between">

                        <div>

                            <div class="text-sm font-semibold">{{ $log->updatedBy?->fname }} {{ $log->updatedBy?->lname }}</div>

                            <div class="mt-1 text-[10px] uppercase text-gray-400 font-semibold">

                                {{ str_replace('_',' ', $log->action ?? 'update') }}

                                @if($log->status) · {{ ucfirst(str_replace('_',' ', $log->status)) }} @endif

                            </div>

                            @if($log->remarks)

                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $log->remarks }}</p>

                            @endif

                        </div>

                        <time class="text-[11px] text-gray-400">{{ $log->created_at?->format('M d, Y h:i A') }}</time>

                    </div>

                </div>

            @empty

                <div class="py-8 text-center text-sm text-gray-400">No activity recorded yet.</div>

            @endforelse

        </div>

    </section>
</div>

@if($requestData->status === 'in_progress')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('materials');

    const button = document.getElementById('add-material');

    const statusSelect = document.getElementById('work-status');

    const materialsSection = document.getElementById('materials-section');

    const noMaterialsCheckbox = document.getElementById('no-materials-used');

    const items = {{ Illuminate\Support\Js::from(

        $inventoryItems->map(fn ($item) => [

            'id' => $item->id,

            'name' => $item->name,

            'unit' => $item->unit,

            'quantity' => $item->quantity,

        ])->values()

    ) }};

    // Custom in-page confirmation modal for removing a material.
    const removeMaterialModal = document.createElement('div');
    removeMaterialModal.className = 'fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-4 py-6';
    removeMaterialModal.innerHTML = `
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800"
             role="dialog" aria-modal="true" aria-labelledby="remove-material-title"
             aria-describedby="remove-material-description">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 6h18"></path>
                        <path d="M8 6V4h8v2"></path>
                        <path d="m19 6-1 14H6L5 6"></path>
                        <path d="M10 11v5M14 11v5"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 id="remove-material-title" class="text-lg font-bold text-gray-900 dark:text-white">
                        Remove material?
                    </h3>
                    <p id="remove-material-description" class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Are you sure you want to remove this material from the list? This action only removes it from this work update.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" data-modal-cancel
                        class="min-h-11 rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="button" data-modal-confirm
                        class="min-h-11 rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">
                    Yes, remove
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(removeMaterialModal);

    let pendingMaterialRemoval = null;

    function closeRemoveMaterialModal() {
        removeMaterialModal.classList.add('hidden');
        removeMaterialModal.classList.remove('flex');
        pendingMaterialRemoval = null;
    }

    function openRemoveMaterialModal(onConfirm) {
        pendingMaterialRemoval = onConfirm;
        removeMaterialModal.classList.remove('hidden');
        removeMaterialModal.classList.add('flex');
        removeMaterialModal.querySelector('[data-modal-cancel]')?.focus();
    }

    removeMaterialModal.querySelector('[data-modal-cancel]')?.addEventListener('click', closeRemoveMaterialModal);

    removeMaterialModal.querySelector('[data-modal-confirm]')?.addEventListener('click', () => {
        if (typeof pendingMaterialRemoval === 'function') {
            pendingMaterialRemoval();
        }
        closeRemoveMaterialModal();
    });

    removeMaterialModal.addEventListener('click', event => {
        if (event.target === removeMaterialModal) {
            closeRemoveMaterialModal();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !removeMaterialModal.classList.contains('hidden')) {
            closeRemoveMaterialModal();
        }
    });

    function updateMaterialsVisibility() {

        if (!materialsSection) return;

        const completing = statusSelect.value === 'completed';

        materialsSection.style.display = completing ? '' : 'none';

        if (!completing) {

            if (container) container.innerHTML = '';

            if (noMaterialsCheckbox) noMaterialsCheckbox.checked = false;

            syncNoMaterialsState();

        }

    }

    function syncNoMaterialsState() {

        if (!noMaterialsCheckbox || !button) return;

        button.disabled = noMaterialsCheckbox.checked;

        button.classList.toggle('opacity-50', noMaterialsCheckbox.checked);

        button.classList.toggle('cursor-not-allowed', noMaterialsCheckbox.checked);

        if (noMaterialsCheckbox.checked && container) {

            container.innerHTML = '';

        }

    }

    function addMaterial() {

        if (!container || noMaterialsCheckbox?.checked) return;

        const row = document.createElement('div');

        row.className = 'grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_140px_auto] gap-3 items-stretch';

        // Searchable material field.
        const searchWrapper = document.createElement('div');
        searchWrapper.className = 'relative min-w-0';

        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Search material by name...';
        searchInput.autocomplete = 'off';
        searchInput.required = true;
        searchInput.className = 'w-full min-h-12 rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-base focus:border-indigo-500 focus:ring-indigo-500';
        searchInput.setAttribute('aria-label', 'Search inventory materials');

        // Keep the submitted item_id[] value separate from the visible search text.
        const selectedItem = document.createElement('input');
        selectedItem.type = 'hidden';
        selectedItem.name = 'item_id[]';

        const results = document.createElement('div');
        results.className = 'absolute z-30 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg';
        results.setAttribute('role', 'listbox');

        function renderResults() {
            const query = searchInput.value.trim().toLowerCase();
            const matches = items.filter(item => {
                const searchableText = `${item.name} ${item.unit}`.toLowerCase();
                return item.quantity > 0 && searchableText.includes(query);
            });

            results.innerHTML = '';

            if (!matches.length) {
                const empty = document.createElement('div');
                empty.className = 'px-3 py-3 text-xs text-gray-500';
                empty.textContent = query
                    ? 'No available materials match your search.'
                    : 'No materials are currently available.';
                results.appendChild(empty);
            } else {
                matches.forEach(item => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'flex w-full items-center justify-between gap-3 px-3 py-2.5 text-left text-sm hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none';
                    option.setAttribute('role', 'option');

                    const name = document.createElement('span');
                    name.className = 'min-w-0 truncate font-medium text-gray-700';
                    name.textContent = item.name;

                    const stock = document.createElement('span');
                    stock.className = 'shrink-0 text-xs text-gray-500';
                    stock.textContent = `${item.quantity} ${item.unit} available`;

                    option.append(name, stock);

                    option.addEventListener('click', () => {
                        selectedItem.value = item.id;
                        searchInput.value = item.name;
                        searchInput.setCustomValidity('');
                        quantity.max = item.quantity;
                        quantity.title = `Maximum available: ${item.quantity}`;
                        results.classList.add('hidden');
                    });

                    results.appendChild(option);
                });
            }

            results.classList.remove('hidden');
        }

        searchInput.addEventListener('input', () => {
            // Typing after a selection clears it; personnel must choose a result again.
            selectedItem.value = '';
            quantity.removeAttribute('max');
            quantity.removeAttribute('title');
            searchInput.setCustomValidity('Select a material from the search results.');
            renderResults();
        });

        searchInput.addEventListener('focus', renderResults);

        searchInput.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                results.classList.add('hidden');
            }
        });

        searchInput.setCustomValidity('Select a material from the search results.');

        searchWrapper.append(searchInput, selectedItem, results);

        const quantity = document.createElement('input');
        quantity.type = 'number';
        quantity.name = 'quantity_used[]';
        quantity.min = '1';
        quantity.step = '1';
        quantity.value = '1';
        quantity.required = true;
        quantity.placeholder = 'Qty';
        quantity.className = 'w-full min-h-12 rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-base focus:border-indigo-500 focus:ring-indigo-500';

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.textContent = 'Remove';
        remove.className = 'min-h-12 justify-self-start rounded-xl border border-rose-200 px-4 py-3 text-sm font-semibold text-rose-600 hover:bg-rose-50';
        remove.addEventListener('click', () => {
            openRemoveMaterialModal(() => row.remove());
        });

        row.append(searchWrapper, quantity, remove);
        container.appendChild(row);
    }

    button?.addEventListener('click', addMaterial);

    statusSelect?.addEventListener('change', updateMaterialsVisibility);

    noMaterialsCheckbox?.addEventListener('change', syncNoMaterialsState);

    updateMaterialsVisibility();

    syncNoMaterialsState();

});

</script>

@endif

@endsection
