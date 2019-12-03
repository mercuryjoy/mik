<meta charset="UTF-8">

<table class="table table-hover dataTable" role="grid">
    <thead>
    <tr role="row">
        <th>ID</th>
        <th>商品名称</th>
        <th>数量</th>
        <th>积分</th>
        <th>订货人</th>
        <th>手机号</th>
        <th>终端名称</th>
        <th>终端区域</th>
        <th>收货人</th>
        <th>收货/充值电话</th>
        <th>收货地址</th>
        <th>发货状态</th>
        <th>备注</th>
        <th>下单日期</th>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr role="row">
            <td>{{ $order->id }}</td>
            <td><a href="{{ route('admin.store.items.edit', $order->item_id)}}">{{ $order->item->name }}</a></td>
            <td>{{ $order->amount }}</td>
            <td>{{ ($order->UserPointLog && $order->UserPointLog->amount) ? abs($order->UserPointLog->amount) : '' }}</td>
            <td>
                @if ($order->user != null)
                    <a href="{{ route('admin.users.edit', $order->user_id)}}">({{ $order->user_id }}) {{ $order->user->name }}</a>
                @else
                    [已删除服务员]
                @endif
            </td>
            <td>
                @if ($order->user != null)
                    {{ $order->user->telephone }}
                @endif
            </td>
            <td>
                @if ($order->user != null && $order->user->shop != null)
                    {{ $order->user->shop->name }}
                @endif
            </td>
            <td>
                @if ($order->user != null && $order->user->shop != null)
                    {{ $order->user->shop->area["display"] }}
                @endif
            </td>
            <td>{{ $order->contact_name }}</td>
            <td>{{ $order->contact_phone }}</td>
            <td>{{ $order->shipping_address }}</td>
            <td>{{ $order->statusDisplay }}</td>
            <td>{{ $order->remarks }}</td>
            <td>{{ $order->created_at }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
