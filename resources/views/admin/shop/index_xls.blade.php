<meta charset="UTF-8">

<table class="table table-hover dataTable" role="grid">
    <thead>
    <tr role="row">
        <th>ID</th>
        <th>名称</th>
        <th>级别</th>
        <th>经销商</th>
        <th>店长</th>
        <th>地区</th>
        <th>地址</th>
        <th>餐饮类型</th>
        <th>营销员</th>
        <th>营销员手机号</th>
        <th>人均消费</th>
        <th>联系人</th>
        <th>联系电话</th>
        <th>扫码总数</th>
        <th>旗下服务员数</th>
        <th>扫码总金额</th>
        <th>创建时间</th>
    </thead>
    <tbody>
    @foreach($shops as $shop)
        <tr role="row">
            <td>{{ $shop->id }}</td>
            <td>{{ $shop->name }}</td>
            <td>{{ $shop->level . "级" }}</td>
            <td>{{ $shop->distributor->name or '' }}</td>
            <td>{{ $shop->owner->name or '' }}</td>
            <td>{{ $shop->area->display or '' }}</td>
            <td>{{ $shop->address }}</td>
            <td>{{ $shop->category->name or '' }}</td>
            <td>{{ $shop->salesman->name or '' }}</td>
            <td>{{ $shop->salesman->phone or '' }}</td>
            <td>{{ $shop->per_consume }}</td>
            <td>{{ $shop->contact_person }}</td>
            <td>{{ $shop->contact_phone }}</td>
            <td>{{ $shop->users->sum(function ($user) { return count($user->scanLog);}) }}</td>
            <td>{{ $shop->users->sum(function ($user) { return count($user);}) }}</td>
            <td>¥{{ $shop->users->sum(function ($user) { return $user->scanLog->sum('money');}) / 100 }}</td>
            <td>{{ $shop->created_at }}</td>
        </tr>
    @endforeach

    </tbody>
</table>