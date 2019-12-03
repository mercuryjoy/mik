<meta charset="UTF-8">

<table class="table table-hover dataTable" role="grid">
    <thead>
    <tr role="row">
        <th>ID</th>
        <th>二维码</th>
        <th>服务员ID</th>
        <th>服务员</th>
        <th>终端</th>
        <th>经销商</th>
        <th>LuckId</th>
        <th>红包金额</th>
        <th>领取时间</th>
        <th>营销员姓名</th>
        <th>营销员手机号</th>
        <th>经销商</th>
        <th>餐饮类型</th>
    </thead>
    <tbody>
    @foreach($scan_logs as $log)
        <tr role="row">
            <td>{{ $log->id }}</td>
            <td>{{ $log->code->code or '[已删除二维码]' }}</td>
            <td>{{ $log->user_id }}</td>
            <td>
                @if($log->user != null)
                    {{ $log->user->name }}
                @else
                    [已删除用户]
                @endif
            </td>
            <td>
                @if($log->shop != null)
                    {{  $log->shop->name }}
                @else
                    [已删除终端]
                @endif
            </td>
            <td>
                @if($log->shop != null && $log->shop->distributor != null)
                    {{  $log->shop->distributor->name }}
                @else
                    [已删除终端或经销商]
                @endif
            </td>
            <td>{{ $log->luck_id }}</td>
            <td>¥{{ $log->money / 100 }}</td>
            <td>{{ $log->created_at }}</td>
            <td>
			@if ($log->salesman != null && $log->salesman->name != null)
				{{ $log->salesman->name }}
			@elseif ($log->shop != null && $log->shop->salesman != null)
				{{ $log->shop->salesman->name }}
			@endif
			</td>
			<td>
			@if ($log->salesman != null && $log->salesman->name != null)
				{{ $log->salesman->phone }}
			@elseif ($log->shop != null && $log->shop->salesman != null)
				{{ $log->shop->salesman->phone }}
			@endif
			</td>
            <td>
                @if ($log->distributor != null && $log->distributor->name != null)
                    {{ $log->distributor->name }}
                @endif
            </td>
            <td>
                @if ($log->shop != null && $log->shop->category != null)
                    {{ $log->shop->category->name }}
                @endif
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
