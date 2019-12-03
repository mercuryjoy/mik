<meta charset="UTF-8">

<table class="table table-hover dataTable" role="grid">
    <thead>
    <tr role="row">
        <th>服务员名字</th>
        <th>服务员手机号</th>
        <th>服务员扫码数量</th>
        <th>服务员获得扫码总额</th>
        <th>服务员获得积分总额</th>
        <th>服务员绑定日期</th>
        <th>服务员状态</th>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr role="row">
            <td>{{ $user->name }}</td>
            <td>{{ $user->telephone }}</td>
            <td>{{ count($user->scanLog) }}</td>
            <td>￥{{ $user->scanLog->sum('money') / 100 }}</td>
            <td>{{ $user->scanLog->sum('point') }}</td>
            <td>{{ $user->updated_at }}</td>
            <td>{{ $user->statusDisplay }}</td>
        </tr>
    @endforeach

    </tbody>
</table>