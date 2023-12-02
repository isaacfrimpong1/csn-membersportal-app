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
    color: #fff;
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
{{ __("You're logged in!") }} {{ $businessName }} <br/> <br/>


<div style="margin-bottom: 40px;"> <!-- Add margin for spacing -->
<form style="display: inline;">
    Start Date: <input type="date" id="start-date" name="start-date" placeholder="Start Date" style="color: black;" value="{{ $request->date('start-date')?->format('Y-m-d') }}" />
    End Date: <input type="date" id="end-date" name="end-date" placeholder="End Date" style="color: black;" value="{{ $request->date('end-date')?->format('Y-m-d') }}"  />
    <input type="submit" value="Search" id="styled-button" style="background-color: #00FF00; color: black; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; transition: background-color 0.3s;" />
</form>
<form style="display: inline;">
    <input type="submit" value="Reset" id="styled-button" style="background-color: #DDDDDD; color: black; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; transition: background-color 0.3s;" />
</form>
</div>

<!-- <h2>Items in Stock</h2> --> 
@if (!empty($allOrders))
<div class="table-wrapper">
<table class="table">
<thead>
<tr>
    <th class="text-left">Date</th>
    <th class="text-left">Item Name</th>
    <th class="text-left">Quantity</th>
    <th class="text-left">Base Price</th>
    <th class="text-left">Gross Sales</th>
    <th class="text-left">Discount</th>
    <th class="text-left">Net Sales</th>
</tr>
</thead>
<tbody>
    {{-- List best selling products first --}}
    <tr><td colspan="3"></td></tr>
    <tr class="green-row">
        <td><strong>Best Sellers</strong></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

@foreach ($bestSellers as $order)
    <tr>
        <td></td>
        <td>{{ $order['item_name'] }}</td>
        <td>{{ $order['quantity'] }}</td>
        <td>£{{ number_format($order['total_base_price'], 2) }}</td>
        <td>£{{ number_format($order['total_gross_amount'], 2) }}</td>
        <td>£{{ number_format($order['total_discount'], 2) }}</td>
        <td><strong>£{{ number_format($order['total_money'], 2) }}</td>
    </tr>
@endforeach
    
    {{-- Totals for all sales --}}
    <tr><td colspan="3"></td></tr>
    <tr class="green-row">
        <td><strong>All Sales</strong></td>
        <td></td>
        <td></td>
        <td><strong>£{{ number_format($orderTotals['total_base_price'], 2) }}</strong></td>
        <td><strong>£{{ number_format($orderTotals['total_gross_amount'], 2) }}</strong></td>
        <td><strong>£{{ number_format($orderTotals['total_discount'], 2) }}</strong></td>
        <td><strong>£{{ number_format($orderTotals['total_money'], 2) }}</strong></td>
    </tr>
    
    {{-- List all sales --}}
    <tr><td colspan="3"></td></tr>
@php $counter = 0; @endphp
@foreach ($allOrders as $order)
    <tr>
        <td>{{ \Carbon\Carbon::parse($order->order_date)->format('M d,Y') }}</td>
        <td>{{ $order->item_name }}</td>
        <td class="text-left">{{ $order->quantity }}</td>
        <td>£{{ number_format($order->total_base_price, 2) }}</td>
        <td>£{{ number_format($order->total_gross_amount, 2) }}</td>
        <td>£{{ number_format($order->total_discount, 2) }}</td>
        <td>£{{ number_format($order->total_money, 2) }}</td>
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
