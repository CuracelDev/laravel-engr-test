<?php

namespace App\Actions;

use App\Enums\BatchingCriteriaEnum;
use App\Enums\BatchStatusEnum;
use App\Events\CreateOrder;
use App\Models\Batch;
use App\Models\Order;
use App\Notifications\OrderCreated;
use App\Traits\HasResponse;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SubmitOrder
{
    use AsAction;
    use HasResponse;

    public function handle(array $data)
    {
        DB::beginTransaction();
        try {
            $data['total_price'] = collect($data['items'])->sum(function ($item) {
                return $item['unit_price'] * $item['quantity'];
            });

            $order = Order::query()->create($data);

            $this->assignOrderToBatch($order);
            DB::commit();


            return $order->load('items');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'hmo_code' => ['required', 'string', 'exists:hmos,code'],
            'provider_name' => ['required', 'string'],
            'encounter_date' => ['required', 'date', 'before_or_equal:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->validated());
    }

    public function asListener(CreateOrder $event): void
    {
        $event->hmo->notify(new OrderCreated($event->order));
    }

    private function getBatchDate(Order $order)
    {
        return $order->hmo->batching_criteria === BatchingCriteriaEnum::SUBMISSION_DATE->value ? $order->created_at : $order->encounter_date;
    }

    private function assignOrderToBatch(Order $order)
    {
        $batchDate = $this->getBatchDate($order);
        $batch = Batch::firstOrCreate([
            'hmo_id' => $order->hmo->id,
            'name' => $order->provider_name . ' ' . $batchDate->format('M Y'),
            'month' => $batchDate->format('m'),
            'year' => $batchDate->format('Y'),
            'status' => BatchStatusEnum::PENDING->value,
        ]);

        $order->batch()->associate($batch);
        $order->save();
    }

    public function jsonResponse(Order $order, ActionRequest $request): JsonResponse
    {
        return $this->responseJson(
            data: $order,
            message: 'Order submitted successfully',
            responseCode: 201,
            status: true
        );
    }
}
