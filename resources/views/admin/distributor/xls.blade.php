<meta charset="UTF-8">

<div class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-hover dataTable" role="grid">
                <thead>
                <tr role="row">
                    <th>终端ID</th>
                    <th>终端名称</th>
                    <th>终端地址</th>
                    <th>终端服务员</th>
                    <th>终端服务员扫码总数量</th>
                    <th>终端服务员扫码总金额</th>
                </thead>
                <tbody>
                @foreach($shops as $shop)
                    <tr role="row">
                        <td>{{ $shop->id }}</td>
                        <td>{{ $shop->name }}</td>
                        <td>{{ $shop->address }}</td>
                        <td>{{ count($shop->users) }}</td>
                        <td>{{ $shop->users->sum(function ($user) { return count($user->scanLog);}) }}</td>
                        <td>￥{{ $shop->users->sum(function ($user) { return $user->scanLog->sum('money');}) / 100 }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
