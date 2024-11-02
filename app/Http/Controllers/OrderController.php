<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Jobs\ProcessOrderJob;
use App\Models\Hmo;
use App\Models\Order;
use App\Models\OrderItem;
use App\Notifications\OrderProcessedNotification;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'hmos' => Hmo::get()
        ]);
    }


    /**
     * @param Request $request
     * @param Hmo $hmo
     *
     * @return Response
     */
    public function orders(Request $request, Hmo $hmo): Response
    {
        $currentDate = new DateTime();

        $batchBy = $request->batch_by ?? 'encounter_date';
        $startDate = $request->start_date ?? (new DateTime($currentDate->format('Y-m-01')))->format('Y-m-d');
        $endDate = $request->end_date ?? (new DateTime($currentDate->format('Y-m-d')))->format('Y-m-d');

        $orders = Order::where('hmo_id', $hmo->id)
            ->when($batchBy == 'encounter_date', function ($query) use ($startDate, $endDate) {
                $query->whereDate('encounter_date', '>=',  $startDate)
                    ->whereDate('encounter_date', '<=', $endDate);
            })
            ->when($batchBy == 'date_created', function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=',  $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->with(['items', 'hmo'])
            ->withCount('items')
            ->latest()
            ->paginate(20);

        return Inertia::render('Orders', [
            'orders' => $orders,
            'hmo' => $hmo,
            'filter' => (object)[
                'batch_by' => $batchBy,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }


    /**
     * @param Request $request
     * @param Hmo $hmo
     *
     * @return RedirectResponse
     */
    public function processOrderBatch(Request $request, Hmo $hmo): RedirectResponse
    {
        $currentDate = new DateTime();

        $batchBy = $request->batch_by ?? 'encounter_date';
        $startDate = $request->start_date ?? (new DateTime($currentDate->format('Y-m-01')))->format('Y-m-d');
        $endDate = $request->end_date ?? (new DateTime($currentDate->format('Y-m-d')))->format('Y-m-d');

        $orders = Order::where('hmo_id', $hmo->id)
            ->when($batchBy == 'encounter_date', function ($query) use ($startDate, $endDate) {
                $query->whereDate('encounter_date', '>=',  $startDate)
                    ->whereDate('encounter_date', '<=', $endDate);
            })
            ->when($batchBy == 'date_created', function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=',  $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->with(['items', 'hmo']);

        $orders->chunk(10, function ($batchOrders) {
            dispatch(new ProcessOrderJob($batchOrders));
        });

        Notification::route('mail', $hmo->email)
            ->notify(new OrderProcessedNotification());

        return redirect()->back()->with('message', 'Order created successfully!');
    }


    /**
     * @param StoreOrderRequest $request
     *
     * @return RedirectResponse
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $input = $request->validated();

        $hmo = Hmo::where('code', $input['hmo_code'])->first();

        $orderData = collect($input)->except(['items', 'hmo_code']);
        $orderData['hmo_id'] = $hmo->id;
        $orderData['status'] = 'pending';

        $order = Order::create($orderData->toArray());

        if (!$order) {
            redirect()->back()->withErrors('message', 'Unable to create order!');
        }

        $orderItems = array_map(function ($item) use ($order) {
            $item['order_id'] = $order->id;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            return $item;
        }, $input['items']);


        OrderItem::insert($orderItems);

        return redirect()->back()->with('message', 'Order created successfully!');
    }
}
