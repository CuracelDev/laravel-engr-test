<h1>New Order Batch Created</h1>
<p>A new order batch has been created with the following details:</p>

<p>Provider: {{ $order->provider_name }}</p>
<p>Total Amount: {{ $order->total_amount }}</p>
<p>Batch Identifier: {{ $order->batch_identifier }}</p>

<p>Items:</p>
<ul>
    @foreach($order->items as $item)
        <li>{{ $item->item_name }} - ${{ $item->subtotal }}</li>
    @endforeach
</ul>
