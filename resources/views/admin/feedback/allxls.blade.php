<meta charset="UTF-8">

<div class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-hover dataTable" role="grid">
                <thead>
                <tr role="row">
                    <th>服务员ID</th>
                    <th>服务员</th>
                    <th>服务员手机号</th>
                    <th>内容</th>
                    <th>商户名称</th>
                    <th>对应销售员</th>
                    <th>回复状态</th>
                </thead>
                <tbody>
                @foreach($feedback as $feedback)
                    <tr role="row">
                        <td>
                        @if ($feedback->id != null)
                            {{ $feedback->id }}
                        @else

                        @endif
                        </td>
                        <td>
                        @if ($feedback->name != null)
                            {{ $feedback->name }}
                        @else
             
                        @endif
                        </td>
                        <td>
                        @if ($feedback->telephone !=null)
                            {{ $feedback->telephone }}
                        @else
                     
                        @endif
                        </td>
                        <td>{{ $feedback->content }}</td>
                        <td>
                        @if ($feedback->sname != null)
                            {{ $feedback->sname }}
                        @else
                      
                        @endif
                        </td>
                        <td>
                        @if ($feedback->smname != null)
                            {{ $feedback->smname }}
                        @else
                       
                        @endif
                        </td>
                        <td>
                        @if ($feedback->status == 'reply')
                            已回复
                        @else
                            暂未回复
                        @endif
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
