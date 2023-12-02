<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
{{ __('Sales') }}
</h2>
</x-slot>

<style>
    .table-wrapper {
    overflow-x: auto;
}

.table {
    width: 100%;
}

.table th,
.table td {
    padding: 4px;
}

.table thead th {
    background-color: #000;
    font-weight: bold;
}


.table tbody td {
    white-space: nowrap;
}

.styled-button:hover {
  background-color: #00FF00 !important;

}

.green-row {
    background-color: #00FF00; /* Light green color */
}

.green-row td {
    font-weight: bold; /* Make text bold */
    color: #000; /* Black text color */
}

</style>


<div class="py-12">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
<div class="p-6 text-gray-900 dark:text-gray-100">
{{ __("Viewing") }} {{ $businessName }} {{ __("Stock Items") }}<br/> <br/>


<!-- <h2>Items in Stock</h2> --> 
@if (!empty($stock))
<div class="table-wrapper">
<table class="table">
<thead>
<tr>
    <th class="text-left">Item Name</th>
    <th class="text-left">Sku</th>
    <th class="text-left">Quantity</th>
    <th class="text-left">Price</th>
</tr>
</thead>
<tbody>

    <!-- Display the sums at the end of the table -->
            <tr><td colspan="3"></td></tr>
            <tr class="green-row">
                <td ><strong>Total Quantity </strong></td>
                <td></td>
                <td><strong>{{ $sumQuantity }}</strong></td>
                <td></td>
            </tr>
            <tr><td colspan="3"></td></tr>

@php $counter = 0; @endphp
        @foreach ($stock as $items)                                        
            <tr>
                <td>{{ $items->item_name }}</td>
                <td>{{ $items->sku }}</td>
                <td class="text-left">{{ $items->quantity }}</td>
                <td>£{{ number_format($items->price, 2) }}</td>

            </tr>
        @php $counter++; @endphp
@endforeach
</tbody>
</table>
</div>
@endif
</div>
</div>
</div>
</div>
</x-app-layout>
