<?php

namespace App\Http\Controllers;

/*require 'vendor/autoload.php'; // Include the Square SDK*/


use App\Models\BusinessLookup;
use Illuminate\Support\Facades\Auth;
use Square\SquareClient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Square\Models\SearchOrdersRequest;
use Illuminate\Support\Collection; // Import the Collection class



class DashboardController extends Controller
{

public function show()
{
    $businessName = BusinessLookup::join('users', 'business_lookup.business_id', '=', 'users.business_id')
    ->where('users.email', Auth::user()->email)
    ->value('business_lookup.business_name');

    /* Get Business ID for the logged in Business */
    $businessId = BusinessLookup::join('users', 'business_lookup.business_id', '=', 'users.business_id')
    ->where('users.email', Auth::user()->email)
    ->value('business_lookup.business_id');

    $today = Carbon::today()->format('Y-m-d');

    // Execute your PostgreSQL query and fetch the results
    $orders = DB::select('
        SELECT 
            o.item_name,
            o.order_date,
            o.quantity,
            SUM(o.base_price::numeric) AS total_base_price,
            SUM(o.discount::numeric) AS total_discount,
            SUM(o.gross_amount::numeric) AS total_gross_amount,
            SUM(o.total_money::numeric) AS total_money
        FROM public."Sales" o
        LEFT JOIN public."stock" s ON o.catalog_object_id = s.catalog_object_id
        WHERE s.business_id = ?
        GROUP BY o.item_name, o.order_date, o.quantity
        ORDER BY o.order_date ASC;
    ', [$businessId]);


    $orders = collect($orders);

    // Calculate the sums for total_base_price, total_discount, and total_gross_amount
    $sumBasePrice = number_format($orders->sum('total_base_price'), 2);
    $sumDiscount = number_format($orders->sum('total_discount'), 2);
    $sumGrossAmount = number_format($orders->sum('total_gross_amount'), 2);
    $sumTotalMoney = number_format($orders->sum('total_money'), 2);
    
    return view('dashboard', compact('businessName', 'businessId', 'orders', 'sumBasePrice', 'sumDiscount', 'sumGrossAmount','sumTotalMoney'));

}

}
