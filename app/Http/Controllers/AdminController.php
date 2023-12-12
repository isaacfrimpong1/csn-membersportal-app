<?php

namespace App\Http\Controllers;

/*require 'vendor/autoload.php'; // Include the Square SDK*/


use App\Models\BusinessLookup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{

public function show(Request $request)
{
    $user = auth()->user();

    $start_date = $request->date('start-date') ?? Carbon::create(1970, 1, 1);
    $end_date = $request->date('end-date') ?? Carbon::today();
    $business_id = $request->input('selectedBusiness');

    // Execute your PostgreSQL query and fetch the results
    $orders = DB::select('
        SELECT 
            o.item_name,
            o.order_date,
            o.quantity,
            b.business_name,
            s.business_id,
            SUM(CAST(o.base_price AS numeric)) AS total_base_price,
            SUM(CAST(o.discount AS numeric)) AS total_discount,
            SUM(CAST(o.gross_amount AS numeric)) AS total_gross_amount,
            SUM(CAST(o.total_money AS numeric)) AS total_money
        FROM public."Sales" o
        LEFT JOIN public."stock" s ON o.catalog_object_id = s.catalog_object_id
        JOIN public."business_lookup" b on s.business_id::text = b.business_id::text
        WHERE
            o.order_date BETWEEN ? AND ?
            AND s.business_id = ?
        GROUP BY o.item_name, o.order_date, o.quantity, b.business_name, s.business_id
        ORDER BY o.order_date ASC;
    ', [$start_date,$end_date,$business_id]);

    $BusinessLookup = DB::select('SELECT business_name, business_id FROM public."business_lookup" ');
    $businesses = collect($BusinessLookup);


    $orders = collect($orders);

    // Calculate the sums for total_base_price, total_discount, and total_gross_amount
    $sumBasePrice = number_format($orders->sum('total_base_price'), 2);
    $sumDiscount = number_format($orders->sum('total_discount'), 2);
    $sumGrossAmount = number_format($orders->sum('total_gross_amount'), 2);
    $sumTotalMoney = number_format($orders->sum('total_money'), 2);
    $sumQuantity = $orders->sum('quantity');
    
    return view('admin', compact('orders', 'sumBasePrice', 'sumDiscount', 'sumGrossAmount','sumTotalMoney', 'sumQuantity','request','businesses'));

}

}
