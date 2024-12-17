
<a class="action-btn" href="{{route('admin.order.edit',[$order['id']])}}">
        <i class="tio-edit"></i>
</a>

<form action="{{ route('admin.orders.delete', ['product_id' => $product['id']]) }}" method="get">
    <input type="hidden" name="order_id" id="order_id" value="{{ $order['id'] }}" />

    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">
        <i class="tio-delete-outlined"></i>
    </button>
</form>
