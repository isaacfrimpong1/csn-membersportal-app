<?php

namespace App\Http\Controllers;

/*require 'vendor/autoload.php'; // Include the Square SDK*/


use App\Models\BusinessLookup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class StockController extends Controller
{

public function show()
{
    $user = auth()->user();

    /* Get Business ID for the logged in Business */
    $businessId = BusinessLookup::join('users', 'business_lookup.business_id', '=', 'users.business_id')
    ->where('users.email', Auth::user()->email)
    ->value('business_lookup.business_id');

    /* Get Business Name for the logged in Business */
    $businessName = BusinessLookup::join('users', 'business_lookup.business_id', '=', 'users.business_id')
    ->where('users.email', Auth::user()->email)
    ->value('business_lookup.business_name');

    // Execute your PostgreSQL query and fetch the results
    $stock = DB::select('
        SELECT 
            s.item_name,
            s.sku,
            s.quantity,
            s.price,
            SUM(CAST(s.quantity AS numeric)) AS total_quantity
        FROM public."stock" s 
        WHERE
            s.business_id = ?
        GROUP BY s.item_name, s.sku, s.quantity, s.price;',
        [$businessId]);


    $stock = collect($stock);

    $sumQuantity = $stock->sum('total_quantity');
    
    return view('stock', compact('stock','businessName', 'sumQuantity'));

}

}
