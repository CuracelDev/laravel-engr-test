<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Hmo;
use App\Models\Provider;
use App\Models\OrderItem;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
   
    public function addOrder(array $orderData)
    {
        DB::beginTransaction(); 

        try {
           
            $hmo = Hmo::where('code', $orderData['hmo_code'])->first();

           
            $provider = Provider::findOrFail($orderData['provider_id']);
            
           
            $encounterDate = new \DateTime($orderData['encounter_date']);
            $formattedDate = $encounterDate->format('M') . $encounterDate->format('Y');

            
           
            $batchName = preg_replace('/\s+/', '', $provider->name . $formattedDate);

           
            $batch = Batch::firstOrCreate([
                    'name' => $batchName,
                    'hmo_code' => $orderData['hmo_code'],
                    'provider_id' => $orderData['provider_id'],
                    'criteria_type' => $hmo->batches_type === 'encounter' ? 'encounter' : 'actual',
            ]);

           
            $order = Order::create([
                'order_id' => uniqid('ORD-'), 
                'batch_id' => $batch->id,
                'encounter_date' => $orderData['encounter_date'],
                'total' => $orderData['total'],
            ]);

           
            $itemsToInsert = [];
            foreach ($orderData['items'] as $itemData) {
                $itemsToInsert[] = [
                    'order_id' => $order->id,
                    'item' => $itemData['item'],
                    'unit_price' => $itemData['unit_price'],
                    'qty' => $itemData['qty'],
                    'sub_total' => $itemData['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            OrderItem::insert($itemsToInsert);

            DB::commit(); 
            return [
                'order' => $order,
                'hmo_email' => $hmo->email,
                'batch_name' => $batchName
            ];
        } catch (\Exception $e) {
            DB::rollBack(); 
            throw $e;
        }
    }


    public function getOrdersByBatch(string $hmoCode, int $providerId, string $searchDate)
    {
       
        $provider = Provider::findOrFail($providerId);
      
        $data = new \DateTime($searchDate);
        $month = $data->format('m');
        $year = $data->format('Y');
                   
        $batchName = preg_replace('/\s+/', '', $provider->name . $month . $year);

       
        $batch = Batch::where('name', $batchName)->first();

        if (!$batch) {
            return []; 
        }

       
        $batchId = $batch->id;
        $criteriaType = $batch->criteria_type;

       
        $query = Order::where('batch_id', $batchId);

       
        if ($criteriaType === 'encounter') {
           
            $query->whereMonth('encounter_date', $month)
                ->whereYear('encounter_date', $year);
        } else {
           
            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
        }

       
        $orders = $query->with('items')->get();

        return $orders;
    }

}
