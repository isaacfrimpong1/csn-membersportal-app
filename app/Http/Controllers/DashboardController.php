<?php

namespace App\Http\Controllers;

/*require 'vendor/autoload.php'; // Include the Square SDK*/


use App\Models\BusinessLookup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class DashboardController extends Controller
{

public function show(Request $request)
{
    $businessName = BusinessLookup::join('users', 'business_lookup.business_id', '=', 'users.business_id')
    ->where('users.email', Auth::user()->email)
    ->value('business_lookup.business_name');

    /* Get Business ID for the logged in Business */
    $businessId = BusinessLookup::join('users', 'business_lookup.business_id', '=', 'users.business_id')
    ->where('users.email', Auth::user()->email)
    ->value('business_lookup.business_id');

    $start_date = $request->date('start-date') ?? Carbon::create(1970, 1, 1);
    $end_date = $request->date('end-date') ?? Carbon::today();

    // Execute your PostgreSQL query and fetch the results
    $ordersQuery = DB::select('
        SELECT 
            o.item_name,
            o.order_date,
            o.quantity,
            SUM(o.quantity::numeric) AS total_quantity,
            SUM(o.base_price::numeric) AS total_base_price,
            SUM(o.discount::numeric) AS total_discount,
            SUM(o.gross_amount::numeric) AS total_gross_amount,
            SUM(o.total_money::numeric) AS total_money
        FROM public."Sales" o
        LEFT JOIN public."stock" s ON o.catalog_object_id = s.catalog_object_id
        WHERE
            s.business_id = ? AND o.order_date BETWEEN ? AND ?
        GROUP BY o.item_name, o.order_date, o.quantity
        ORDER BY o.order_date ASC;
    ', [$businessId, $start_date, $end_date]);


    $allOrders = collect($ordersQuery);
    
    // Calculate the totals
    $summariseOrders = function ($orders) {
        return array(
            'item_name'=> $orders->value('item_name'),
            'quantity'=> $orders->sum('total_quantity'),
            'total_base_price'=> $orders->sum('total_base_price'),
            'total_discount'=> $orders->sum('total_discount'),
            'total_gross_amount'=> $orders->sum('total_gross_amount'),
            'total_money'=> $orders->sum('total_money'),
        );
    };
    $orderTotals = $summariseOrders($allOrders);

    // Find the best-sellers
    $salesByProduct = $allOrders->groupBy('item_name')->map($summariseOrders)->sortByDesc('total_money');
    $bestSellers = $salesByProduct->take(5);

    return view('dashboard', compact('businessName', 'businessId', 'allOrders', 'orderTotals', 'bestSellers', 'request'));
}

}
