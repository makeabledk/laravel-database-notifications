@include('notifications::email')

{{-- Test eloquent serialization --}}
{{ $order->created_at }}