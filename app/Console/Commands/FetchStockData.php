<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Square\SquareClient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Square\Models\RetrieveInventoryCountResponse;


class FetchStockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-stock-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locationID = 'LPJR104NJFFE6';
        //
        // Use the square API to fetch all products in the store
        $squareClient = new SquareClient([
            'accessToken' => 'EAAAEe8HiCuRniSYz3-_1_Abk87BgB-kceii11hcPsF66QfbqobX3lBBxcUYIPhk',
            'environment' => 'production', // Use 'sandbox' for testing, 'production' for live data
        ]);

        $response = $squareClient->getCatalogApi()->listCatalog(null, 'ITEM');
        $allItems = [];
        $cursor = null;

        do {
            if ($cursor) {
                $response = $squareClient->getCatalogApi()->listCatalog($cursor, 'ITEM');
            }

            $items = $response->getResult()->getObjects();
            $allItems = array_merge($allItems, $items);
            
            Log::info('Cursor INSIDE Loop: ' . $cursor);

            $cursor = $response->getResult()->getCursor();
        } while ($cursor);

        $size = count($allItems);

        // Todays Date
        $today = Carbon::today()->format('Y-m-d');

        // Retrieve the date of when stock was updated from the Stock Table
        /*$firstStockItem = DB::table('stock')->first();
        if ($firstStockItem) {
            $date = $firstStockItem->date_updated;
        }*/
    
           $DB_StockUpdate = 'New Stock Update';

           /* Empty the table first before doing a full refresh */
           DB::table('stock')->truncate();

            /* Store stock in Stock table */
            foreach ($allItems as $item) {

                // NEW LOOP TO GET ALL item variations.

                $variations = $item->getItemData()->getVariations();
                $variationIds = [];

                if (!empty($variations)) {

                    foreach ($variations as $variation) {

                        $variationData = $variation->getItemVariationData();

                        $sku = $variation->getItemVariationData()->getSku();
                        $item_name = $item->getItemData()->getName();
                        $item_variation_name = $variation->getItemVariationData()->getName();
                        $item_name = $item_name . " " . $item_variation_name;
                        $variationIds[] = $variation->getID();

                        $price = 0; // Default value in case price retrieval fails

                                if ($variationData) {
                                    $priceMoney = $variationData->getPricemoney();
                                    
                                    if ($priceMoney) {
                                        $amount = $priceMoney->getAmount();
                                        if (!is_null($amount)) {
                                            $price = $amount / 100;
                                        }
                                    }
                                }

                                if(empty($sku))
                                {
                                    //Log::info('SKU FOUND: ' . $sku);
                                    Log::info('ITEM NAME FOR EMPTY SKU: ' . $item_name);
                                }
                                else {
                                    Log::info('SKU NOT INSERTED: ' . $sku);
                                }

                            /** Check Catalog Item ID retrieval - May need changing*/
                            //$catalog_object_id = $variation->getItemVariationData()->getItemId();
                            //$item->getItemData()->getVariations()[0]->getID();



                            try {
                                $catalog_object_id = $variation->getID(); //$item->getItemData()->getVariations()->getID();
                                // Process $catalog_object_id

                            If(!empty($catalog_object_id)){

                                /* Get Qunantity */
                                $apiResponse = $squareClient->getInventoryApi()->retrieveInventoryCount($catalog_object_id, $locationID);
                                
                                // Check if the response is successful
                                if ($apiResponse->isSuccess()) {
                                    $result = $apiResponse->getResult();
                                    
                                    // Check if the result and counts are not null
                                    if ($result && $counts = $result->getCounts()) {
                                        // Check if there is at least one count
                                        if (!empty($counts)) {
                                            // Get the quantity from the first count
                                            $quantity = $counts[0]->getQuantity();

                                            // Now you can use $quantity as needed
                                             Log::info('Quantity: ' . $quantity);
                                        }
                                    }    

                                } else {
                                    // Handle errors if the request was not successful
                                    $errors = $apiResponse->getErrors();
                                    Log::info('Error retrieving inventory count: ' . implode(', ', $errors));
                                }

                            }


                            //$sku = $item->getItemData()->getVariations()[0]->getItemVariationData()->getSku();
                            //$item_name = $item->getItemData()->getName();
                            //$variations = $item->getItemData()->getVariations(); // CHANGE
                            /*$price = $item->getItemData()->getVariations()[0]->getItemVariationData()->getpricemoney()->getamount() / 100;*/


                            /* Assign the correct business id vale to each business product */
                            if(preg_match('/^BSG\d{1,2}$/', $sku)) {
                                $business_id = 658910; // B Sweet Gifts
                            }else if(preg_match('/^BK\d{1,2}$/', $sku)){
                                $business_id = 10978; // By Kala x
                            }else if(preg_match('/^LS\d{1,2}$/', $sku)){
                                $business_id = 76309; // Lione & Sheikh
                            }else if(preg_match('/^L\d{1,2}$/', $sku)){
                                $business_id = 43908; // Langis
                            }else if(preg_match('/^NL\d{1,2}$/', $sku)){
                                $business_id = 87901; // Nourish London
                            }else if(preg_match('/^BC\d{1,2}$/', $sku)){
                                $business_id = 90123; // Brûler Candles
                            }else if(preg_match('/^ES\d{1,2}$/', $sku)){
                                $business_id = 10876; // Ecozy Scents
                            }else if(preg_match('/^NAL\d{1,2}$/', $sku)){
                                $business_id = 54678; // Naila Ahmad London
                            }else if(preg_match('/^PRJ\d{1,2}$/', $sku)){
                                $business_id = 90876; // Paquita Ruby Jewellery
                            }else if(preg_match('/^DTS\d{1,2}$/', $sku)) {
                                $business_id = 34213; // Dharti The Store
                            }else if(preg_match('/^QTC\d{1,2}$/', $sku)){
                                $business_id = 29090; // QTCards
                            }else if(preg_match('/^HB\d{1,2}$/', $sku)){
                                $business_id = 87609; // Husn Beauty Ltd
                            }else if(preg_match('/^ZD\d{1,2}$/', $sku)){
                                $business_id = 24231; // Ziolla Designs
                            }else if(preg_match('/^HJ\d{1,2}$/', $sku)){
                                $business_id = 78365; // Hannah Jean Studio
                            }else if(preg_match('/^RCJ\d{1,2}$/', $sku)){
                                $business_id = 9789; // Rock Circle Jewellery
                            }else if(preg_match('/^JND\d{1,2}$/', $sku)){
                                $business_id = 90872; // Jinny Ngui Design
                            }else if(preg_match('/^SP\d{1,2}$/', $sku)){
                                $business_id = 34001; // Suzanart pottery
                            }else if(preg_match('/^SBC\d{1,2}$/', $sku)){
                                $business_id = 20120; // Serenity by Chelles
                            }else if(preg_match('/^SSC\d{1,2}$/', $sku)){
                                $business_id = 34612; // Sian Stark Ceramics
                            }else if(preg_match('/^D\d{1,2}$/', $sku)){
                                $business_id = 98076; // Dedais Ltd
                            }else {
                                $business_id = 1;
                            }

                            if ($sku) {

                                // Insert the stock information into the stock table
                                DB::table('stock')->insertOrIgnore([
                                    'sku' => $sku,
                                    'item_name' => $item_name,
                                    'price' => $price,
                                    'quantity' => $quantity,
                                    'business_id' => $business_id,
                                    'date_updated' => $today,
                                    'catalog_object_id' => $catalog_object_id,

                                ]);
                            }

                        } catch (\Square\Models\Error $error) {
                                // Handle the error
                                Log::error('Square API Error: ' . json_encode($error->getErrors()));
                    }

                    }
                }

            }  

    }
}
