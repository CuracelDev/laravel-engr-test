<?php

namespace App\Actions;

use App\Models\HMO;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderBatchNotification;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateOrderAction
{
    use AsAction;

    // Define the handle method to accept data
    public function handle(array $data) // Accept data as an array
    {
        // Retrieve the HMO based on the hmo_code from the provided data
        $hmo = HMO::where('code', $data['hmo_code'])->first();

        if (!$hmo) {
            // Handle the case when the HMO is not found
            throw new \Exception("HMO with the specified code does not exist.");
        }

        // Calculate batch identifier based on HMO batch type
        $batchDate = $hmo->batch_type === 'encounter_date' ? $data['encounter_date'] : now();
        $batchIdentifier = $data['provider_name'] . ' ' . Carbon::parse($batchDate)->format('M Y');

        // Create order and items
        $order = Order::create([
            'provider_name' => $data['provider_name'],
            'hmo_code' => $data['hmo_code'],
            'encounter_date' => $data['encounter_date'],
            'submitted_date' => now(),
            'batch_identifier' => $batchIdentifier,
            'total_amount' => $data['total_amount'],
        ]);

        foreach ($data['items'] as $item) {
            $subtotal = $item['unit_price'] * $item['quantity']; // Calculate subtotal here if not provided
            $order->items()->create([
                'item_name' => $item['name'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal, // Use calculated subtotal
            ]);
        }

        // Send notification email to HMO
        try {
            Mail::to($hmo->email)->send(new NewOrderBatchNotification($order));
        } catch (\Exception $e) {
            // Log the email failure but continue processing
            \Log::error("Order created, but failed to send email:.. " . $e->getMessage());

            // Optionally, you could log more details if needed.
        }

        // Return the order even if email sending fails
        return $order;
    }
}
