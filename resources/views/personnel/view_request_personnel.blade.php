@extends('layouts.apps')



@section('content')

<style>

@media print {

    @page { size: A4 portrait; margin: 12mm; }

    html, body { margin:0!important; padding:0!important; background:#fff!important; color:#111827!important; font-family:Arial,Helvetica,sans-serif!important; }

    aside, header, .no-print { display:none!important; }

    main { overflow:visible!important; height:auto!important; }

    main > div { max-width:none!important; margin:0!important; padding:0!important; }

    .print-card { border:1px solid #d1d5db!important; border-radius:0!important; box-shadow:none!important; break-inside:avoid; }

}

@media print {
    /* Hide the shared layout and everything outside the record */
    body * {
        visibility: hidden !important;
    }

    /* Show only the maintenance request record */
    .print-page,
    .print-page * {
        visibility: visible !important;
    }

    /* Start the record at the top of the printed page */
    .print-page {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
        overflow: visible !important;
    }

    /* Never print the work update form or navigation controls */
    .no-print {
        display: none !important;
    }

    /* Keep the record readable on paper */
    .print-card {
        background: #fff !important;
        color: #111827 !important;
        box-shadow: none !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0 !important;
        overflow: visible !important;
        break-inside: auto !important;
        page-break-inside: auto !important;
    }

    /* Do not print a photo viewer overlay */
    #imageFullscreen {
        display: none !important;
    }

    img {
        max-width: 100% !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    a {
        color: #111827 !important;
        text-decoration: none !important;
    }
}

</style>



<div class="py-5 print-page">
    <div class="flex items-center justify-between gap-3 mb-5 no-print">

        <a href="{{ route('maintenanceRequest.personnel') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-width="2" d="M15 19l-7-7 7-7"/>

            </svg>

            Back to Assigned Requests

        </a>

        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#607844] hover:bg-[#3b551e] text-white text-sm font-semibold">

            Print Request

        </button>

    </div>



    @if(session('success'))

        <div class="no-print mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>

    @endif

    @if(session('error'))

        <div class="no-print mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>

    @endif

    @if($errors->any())

        <div class="no-print mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">

            <p class="font-semibold">Please review the work update:</p>

            <ul class="mt-1 list-disc pl-5 space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif



    <div class="hidden print:block mb-6">

        <div class="border-b-2 border-gray-900 pb-4">

            <div class="text-xs font-bold tracking-wider text-gray-500">CDSO MAINTENANCE</div>

            <div class="text-2xl font-bold">MAINTENANCE REQUEST RECORD</div>

            <div class="text-sm mt-1">{{ $requestData->ticket_number }}</div>

        </div>

    </div>



    <section class="print-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

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



    <section class="no-print mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

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

                    <select id="work-status" name="status" class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500">

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

                    <textarea name="remarks" rows="2" placeholder="Describe the work performed, findings, instructions, or any other update..."

                              class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>

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



    <section class="print-card mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

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







    <div class="hidden print:flex mt-8 pt-4 border-t border-gray-300 justify-between text-[10px] text-gray-500">

        <span>CDSO Maintenance Management System</span>

        <span>Printed {{ now()->format('M d, Y h:i A') }}</span>

    </div>

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

        row.className = 'grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_110px_auto] gap-2 items-center';



        const select = document.createElement('select');

        select.name = 'item_id[]';

        select.required = true;

        select.className = 'rounded-xl border-gray-200 bg-gray-50 text-sm';

        const placeholder = document.createElement('option');

        placeholder.value = '';

        placeholder.textContent = 'Select material';

        select.appendChild(placeholder);



        items.forEach(item => {

            const option = document.createElement('option');

            option.value = item.id;

            option.disabled = item.quantity <= 0;

            option.textContent = `${item.name} (${item.quantity} ${item.unit} available)`;

            select.appendChild(option);

        });



        const quantity = document.createElement('input');

        quantity.type = 'number';

        quantity.name = 'quantity_used[]';

        quantity.min = '1';

        quantity.step = '1';

        quantity.value = '1';

        quantity.required = true;

        quantity.className = 'rounded-xl border-gray-200 bg-gray-50 text-sm';



        const remove = document.createElement('button');

        remove.type = 'button';

        remove.textContent = 'Remove';

        remove.className = 'px-3 py-2 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50';

        remove.addEventListener('click', () => row.remove());



        row.append(select, quantity, remove);

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
