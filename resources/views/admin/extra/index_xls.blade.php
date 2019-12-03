<meta charset="UTF-8">

<table class="table table-hover dataTable" role="grid">
    <thead>
    <tr role="row">
        <th>ID</th>
        <th>二维码</th>
        <th>类型</th>
        <th>服务员</th>
        <th>用户</th>
        <th>店长</th>
        <th>收益(元)</th>
        <th>积分收益(积分)</th>
        <th>领取时间</th>
        <th>销售员</th>
    </thead>
    <tbody>
    @foreach($scan_logs as $log)
        <tr role="row">
            <td>{{ $log->id }}</td>
            <td>{{ $log->code->code or '未知' }}</td>
            <td>
                {{ $log->typeDisplay }}
            </td>
            <td>
                @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'waiter_scan_user_no']))
                    {{ $log->user->name or '' }}
                @elseif ($log->typeDetail == 'user_scan_waiter_no')
                    [暂无服务员扫描]
                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                    {{ $log->waiter->name or '' }}
                @elseif ($log->typeDetail == 'unKnown')
                    [已删除服务员]
                @endif
            </td>
            <td>
                @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'user_scan_waiter_no']))
                    {{ $log->net_user_name or '' }}
                @elseif ($log->typeDetail == 'waiter_scan_user_no')
                    [暂无用户扫描]
                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                    <span class="text-red">[无]</span>
                @elseif ($log->typeDetail == 'unKnown')
                    [已删除用户]
                @endif
            </td>
            <td>
                @if (in_array($log->typeDetail, ['waiter_user_scan_over', 'waiter_scan_user_no', 'user_scan_waiter_no']))
                    <span class="text-red">[无]</span>
                @elseif ($log->typeDetail == 'waiter_owner_scan_over')
                    {{ $log->user->name or '' }}
                @elseif ($log->typeDetail == 'unKnown')
                    [已删除店长]
                @endif
            </td>
            <td>￥{{ $log->money / 100 }}</td>
            <td>{{ $log->point }}积分</td>
            <td>{{ $log->created_at }}</td>
            <td>
                @if ($log->shop != null && $log->shop->salesman != null)
                    {{ $log->shop->salesman->name }} ({{ $log->shop->salesman->phone }})
                @endif
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
