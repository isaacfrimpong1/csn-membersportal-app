<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Square\SquareClient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


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

            $sku = $item->getItemData()->getVariations()[0]->getItemVariationData()->getSku();
            $item_name = $item->getItemData()->getName();


            $price = 0; // Default value in case price retrieval fails

            $variations = $item->getItemData()->getVariations();

            if (!empty($variations) && isset($variations[0])) {
                $variationData = $variations[0]->getItemVariationData();
                
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
            } else {
                Log::info('SKU NOT INSERTED: ' . $sku);
            }




            /*$price = $item->getItemData()->getVariations()[0]->getItemVariationData()->getpricemoney()->getamount() / 100;*/
            $catalog_object_id = $item->getItemData()->getVariations()[0]->getID();

            /* Assign the correct business id vale to each business product */
            if (strpos($sku, 'BSG') !== false) {
                $business_id = 658910;
            }else if(strpos($sku, 'BK') !== false){
                $business_id = 109785;
            }else if(strpos($sku, 'LS') !== false){
                $business_id = 76309;
            }else {
                $business_id = 1;
            }

            if ($sku) {

                // Insert the stock information into the stock table
                DB::table('stock')->insert([
                    'sku' => $sku,
                    'item_name' => $item_name,
                    'price' => $price,
                    'business_id' => $business_id,
                    'date_updated' => $today,
                    'catalog_object_id' => $catalog_object_id,

                ]);
            }
        }  

    }
}
