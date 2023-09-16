<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Square\SquareClient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Square\Models\SearchOrdersRequest;


class FetchSalesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-sales-data';

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
        // Use the square API to fetch all products in the store
    $squareClient = new SquareClient([
        'accessToken' => 'EAAAEe8HiCuRniSYz3-_1_Abk87BgB-kceii11hcPsF66QfbqobX3lBBxcUYIPhk',
        'environment' => 'production', // Use 'sandbox' for testing, 'production' for live data
    ]);

    $location_ids = ['LPJR104NJFFE6'];
    $cursor = null;
    $allOrders = [];

    do {
        $searchRequest = new SearchOrdersRequest();
        $searchRequest->setLocationIds($location_ids);

        if (!empty($cursor)) {
            $searchRequest->setCursor($cursor);
        }

        $response = $squareClient->getOrdersApi()->searchOrders($searchRequest);

        if ($response->isSuccess()) {

            $orders = $response->getResult()->getOrders();
            $allOrders = array_merge($allOrders, $orders);

            $cursor = $response->getResult()->getCursor();

        } else {
            $errors = $response->getErrors();
            Log::info('ERROR: ' . $errors);
        }
    } while ($cursor);


/* Empty the Sales table first before doing a full refresh */
    DB::table('Sales')->truncate();

    foreach ($allOrders as $order) {
        $order_id = $order->getId();
        $order_date = $order->getCreatedAt();
        if (!empty($order->getLineItems()))
        {

            $lineItemsCount = sizeof($order->getLineItems());

            for ($i = 0; $i < $lineItemsCount; $i++) {

                $item_name = $order->getLineItems()[$i]->getName();
                $uid = $order->getLineItems()[$i]->getUid();

                if(empty($item_name)){
                    $item_name = 'Custom_Sale';
                }

                $base_price = $order->getLineItems()[$i]->getBasePriceMoney()->getAmount()/100;
                $discount = $order->getLineItems()[$i]->getTotalDiscountMoney()->getAmount()/100;
                $gross_amount = $order->getLineItems()[$i]->getVariationTotalPriceMoney()->getAmount()/100;
                $total_money = $order->getLineItems()[$i]->getTotalMoney()->getAmount()/100;
                $catalog_object_id = $order->getLineItems()[$i]->getCatalogObjectId();
                $quantity = $order->getLineItems()[$i]->getQuantity();

                // Insert Sales into Sales Table
                DB::table('Sales')->insert([
                'uid' => $uid,
                'order_id' => $order_id,
                'order_date' => $order_date,
                'item_name' => $item_name,
                'base_price' => $base_price,
                'discount' => $discount,
                'gross_amount' => $gross_amount,
                'quantity' => $quantity,
                'catalog_object_id' => $catalog_object_id,
                'total_money' => $total_money,
                ]);
            }
        }

    }
}
}