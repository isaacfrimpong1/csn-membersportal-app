<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
{{ __('Sales for all Businesses') }}
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


<!-- Include Pikaday Stylesheet -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">

<!-- Include Pikaday JavaScript -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/js/pikaday.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>

<!-- Include Pikaday Range JavaScript -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/plugins/pikaday.jquery.min.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>



<script type="text/javascript">
    // Initialize Pikaday Range
    var startDatePicker = new Pikaday({
        field: document.getElementById('start-date'),
        format: 'MM/DD/YYYY', // Customize the date format as needed
        // Add any other configuration options here
    });

    var endDatePicker = new Pikaday({
        field: document.getElementById('end-date'),
        format: 'MM/DD/YYYY', // Customize the date format as needed
        // Add any other configuration options here
    });

    // Script to handle dynamic search

    $(document).ready(function() {
        $('#search').on('keyup',function(){
            //$value=$(this).val();
            var query = $(this).val().toLowerCase();
            console.log(query);
            if (query === '') {
                $('#search-results').html(''); // Clear the search results if the query is empty
                return; // Exit the function early
            }
            $.ajax({
                method : 'GET',
                url : '/search',
                //data:{'searchBusiness':$value},
                data: {query: query},
                success:function(data){
                    displaySearchResults(data);
            },
                    error: function(error) {
                        console.log(error);
                    }
            });
    });

    // Function to display search results in dropdown
    function displaySearchResults(results) {
        var dropdown = '<ul class="dropdown-menu" aria-labelledby="dropdownMenu">';
        results.forEach(function(result) {
            dropdown += '<li class="dropdown-item">' + result + '</li>';
        });
        dropdown += '</ul>';
        $('#search-results').html(dropdown);
        
        // Handle click on search result
        $('.dropdown-item').click(function() {
            var selectedResult = $(this).text();
            $('#search').val(selectedResult);
            $('#search-results').html('');
        });
    }

});



/**
    $(document).ready(function() {
        $('#search').on('input', function() {
            var query = $(this).val();

            if (query.length >= 3) { // Adjust the minimum length for triggering the search

                $.ajax({
                    url: '/search-business',
                    method: 'GET',
                    data: {query: query},
                    success: function(data) {
                        displaySearchResults(data);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            } else {
                $('#search-results').empty(); // Clear the results if the input is less than the minimum length
            }
        });

        function displaySearchResults(results) {
            var resultsContainer = $('#search-results');
            resultsContainer.empty();

            if (results.length > 0) {
                resultsContainer.append('<select id="businessSelect" name="selectedBusiness" style="color: black;">');

                results.forEach(function(result) {
                    resultsContainer.append('<option id="business_id" name="business_id" value="'+ result.business_id +'" style="color: black;">' + result.business_name + '</option>'); // Adjust the column_name based on your model
                });

                resultsContainer.append('</select>');
            } else {
                resultsContainer.append('<p>No results found.</p>');
            }
        }
    });
*/


</script>
<script type="text/javascript">
    $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>


<div class="py-12">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
<div class="p-6 text-gray-900 dark:text-gray-100">



<div style="margin-bottom: 40px;"> <!-- Add margin for spacing -->
<form style="display: inline;">
    <label for="selectBox">Search for Business </label>
    
<select id="businessSelect" name="selectedBusiness" style="color: black;">
    @foreach ($businesses as $business)
        <option id="business_{{ $business->business_id }}" name="business_id" value="{{ $business->business_id }}" {{ $business->business_id == request()->input('selectedBusiness') ? 'selected' : '' }} style="color: black;">{{ $business->business_name }}</option>
    @endforeach
</select>



<!--
    <select id="businessSelect" name="selectedBusiness" style="color: black;">
        @foreach ($businesses as $business)
            <option id="business_id" name="business_id" value="{{ $business->business_id }}" {{ $business->business_id == $request->input('business_id') ? 'selected' : '' }} style="color: black;">{{ $business->business_name }}</option>
        @endforeach
    </select>
-->

    <!--<input style="color: black;" type="text" class="form-control" id="search" name="search" placeholder="Type to search...">
    <div id="search-results">
        <ul id="businessSelect" name="selectedBusiness" style="color: black;">
        </ul>
    </div>
    -->
    Start Date: <input type="date" id="start-date" name="start-date" placeholder="Start Date" style="color: black;" value="{{ $request->date('start-date')?->format('Y-m-d') }}" />
    End Date: <input type="date" id="end-date" name="end-date" placeholder="End Date" style="color: black;" value="{{ $request->date('end-date')?->format('Y-m-d') }}"  />
    <input type="submit" value="Search" id="styled-button" style="background-color: #00FF00; color: black; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; transition: background-color 0.3s;" />
</form>
<form style="display: inline;">
    <input type="submit" value="Reset" id="styled-button" style="background-color: #DDDDDD; color: black; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; transition: background-color 0.3s;" />
</form>
</div>

<!-- <h2>Items in Stock</h2> --> 
@if (!empty($orders))
<div class="table-wrapper">
<table class="table">
<thead>
<tr>
    <th class="text-left">Business Name</th>
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

    <!-- Display the sums at the end of the table -->
            <tr><td colspan="3"></td></tr>
            <tr class="green-row">
                <td><strong>Total Amount </strong></td>
                <td></td>
                <td></td>
                <td>{{ $sumQuantity }}</td>
                <td><strong>£{{ $sumBasePrice }}</strong></td>
                <td><strong>£{{ $sumGrossAmount }}</strong></td>
                <td><strong>£{{ $sumDiscount }}</strong></td>
                <td><strong>£{{ $sumTotalMoney }}</strong></td>
            </tr>
            <tr><td colspan="3"></td></tr>

@php $counter = 0; @endphp
@foreach ($orders as $order)                                        
            <tr>
                <td>{{ $order->business_name }}</td>
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
