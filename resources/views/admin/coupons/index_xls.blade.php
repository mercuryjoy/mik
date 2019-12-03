<meta charset="UTF-8">

<table class="table table-hover dataTable" role="grid">
    <thead>
    <tr role="row">
        <th>ID</th>
        <th>用户ID</th>
        <th>用户名称</th>
        <th>优惠券名称</th>
        <th>服务员名称</th>
        <th>终端名称</th>
        <th>核销时间</th>
    </thead>
    <tbody>
    @foreach($couponLogs as $couponLog)
        <tr role="row">
            <td>{{ $couponLog->id }}</td>
            <td>{{ $couponLog->net_user_id or '' }}</td>
            <td>{{ $couponLog->net_user_name }}</td>
            <td>{{ $couponLog->coupon_name }}</td>
            <td>{{ $couponLog->user->name or '' }}</td>
            <td>{{ $couponLog->shop->name or '' }}</td>
            <td>{{ $couponLog->created_at }}</td>
        </tr>
    @endforeach

    </tbody>
</table>