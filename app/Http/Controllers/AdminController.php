<?php

namespace App\Http\Controllers;

/*require 'vendor/autoload.php'; // Include the Square SDK*/


use App\Models\BusinessLookup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Add this at the top of your controller


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


        // Fetch all businesses for the dynamic search
        $allBusinesses = BusinessLookup::all();

        $searchResults = [];
        $searchQuery = $request->input('searchBusiness');

        // Check if there's a search query
        if ($searchQuery) {
            // Filter businesses based on the search query
            $searchResults = $allBusinesses->filter(function ($business) use ($searchQuery) {
                return stripos($business->business_name, $searchQuery) !== false;
            });
        }
        
$BusinessLookup = DB::select('SELECT business_name, business_id FROM public."business_lookup" WHERE "active" = ? ORDER BY "business_name" ASC', ['Yes']);


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

public function searchBusiness(Request $request)
{
    $query = $request->input('query');

    /**dd($query);
        $results = business_lookup::where('business_name', 'like', '%' . $query . '%')->get();
        dd($results);
        return response()->json($results);
    */

    if ($request->ajax()) {
        $output = "";
        //$results = DB::table('business_lookup')->where('business_name', 'LIKE', '%' . $query . '%')->get();
        $results = DB::table('business_lookup')->where(DB::raw('LOWER(business_name)'), 'LIKE', '%' . strtolower($query) . '%')->get();
        if ($results) {
            foreach ($results as $key => $business) {
                $output .= '<li id="business_id" name="business_id" value="' . $business->business_id . '" style="color: black;">' . $business->business_name . '</li>';
            }
            return response($output);
        }
    }

}

}
