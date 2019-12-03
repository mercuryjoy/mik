<meta charset="UTF-8">

<table class="table table-hover dataTable" role="grid">
    <thead>
    <tr role="row">
        <th>ID</th>
        <th>姓名</th>
        <th>性别</th>
        <th>终端</th>
        <th>地区</th>
        <th>总扫码数</th>
        <th>总积分</th>
        <th>总红包额</th>
        <th>新用户扫码获总积分</th>
        <th>当前钱包金额</th>
        <th>当前积分</th>
        <th>注册日期</th>
        <th>营销员姓名</th>
        <th>营销员手机号</th>
        <th>帐号状态</th>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr role="row">
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->genderDisplay }}</td>
            <td>{{ $user->shop['name'] }}</td>
            <td>{{ $user->shop->area["display"] or '' }}</td>
            <td>{{ $user->scanLog->count() }}</td>
            <td>{{ $user->scanLog->sum('point') }}</td>
            <td>¥{{ $user->scanLog->sum('money') / 100 }}</td>
            <td>¥{{ $user->money_balance / 100 }}</td>
            <td>{{ $user->userScanGetPointLog->sum('point') }}积分</td>
            <td>{{ $user->point_balance }}</td>
            <td>{{ $user->created_at }}</td>
            @if ($user->shop != null && $user->shop->salesman != null)
                <td>{{ $user->shop->salesman->name }}</td>
                <td>{{ $user->shop->salesman->phone }}</td>
            @else
                <td /> <td />
            @endif
            <td>{{ $user->statusDisplay }}</td>
        </tr>
    @endforeach

    </tbody>
</table>