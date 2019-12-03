@extends('admin.layouts.app')

@section('htmlheader_title')
    活动设置
@endsection

@section('contentheader_title')
    活动设置
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">新建活动</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <a class="btn btn-primary margin" href="{{ route("admin.activities.create", ['type' => 'red_envelope']) }}">新建红包活动</a>
                    <a class="btn btn-primary margin" href="{{ route("admin.activities.create", ['type' => 'point']) }}">新建积分活动</a>
                    <a class="btn btn-primary margin" href="{{ route("admin.activities.create", ['type' => 'shop_owner']) }}">新建店长活动</a>

                    @can('send-red-envelope')
                    <a class="btn btn-primary margin" href="{{ route('admin.activities.create', ['type' => 'send_red_envelope']) }}">发红包 / 积分</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">活动规则</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>标题</th>
                                        <th>类型</th>
                                        <th>作用域</th>
                                        <th>积分</th>
                                        <th>金额</th>
                                        <th>开始时间</th>
                                        <th>结束时间</th>
                                        <th>添加时间</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($activities as $activity)
                                        <tr role="row">
                                            <td>{{ $activity->id or '' }}</td>
                                            <td>{{ $activity->title or '' }}</td>
                                            <td>{{ $activity->typeDisplay or '' }}</td>
                                            <td>{{ $activity->actionZoneDisplay or '' }}</td>
                                            <td>
                                                @if ($activity->type == 'point')
                                                    {{ $activity->ruleJsonDisplay or '' }}个
                                                @endif
                                            </td>
                                            <td>
                                                @if ($activity->type == 'shop_owner')
                                                    {{ $activity->ruleJsonDisplay or '' }}元
                                                @endif
                                            </td>
                                            <td>{{ $activity->start_at or '【永久有效】' }}</td>
                                            <td>{{ $activity->end_at or '【永久有效】' }}</td>
                                            <td>{{ $activity->created_at or '' }}</td>
                                            <td><span class="{{ $activity->status == 'normal' ? 'text-success' : 'text-danger' }}">{{ $activity->statusDisplay or '' }}</span></td>
                                            <td>
                                                @if ($activity->status == 'normal')
                                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.activities.edit', ['id' => $activity->id, 'type' => $activity->type, 'action_zone' => $activity->action_zone]) }}">详情/修改</a>
                                                @endif
                                                @if($activity->action_zone == 'part')
                                                    @if ($activity->status == 'normal')
                                                        <a class="btn btn-xs btn-danger" href="#"
                                                           data-target-status="stop"
                                                           data-href="{{ route('admin.activities.change', ['id' => $activity->id]) }}"
                                                           data-activity-name="{{ $activity->title }}"
                                                           data-toggle="modal"
                                                           data-target="#confirm-status-change">禁用</a>
                                                    @elseif ($activity->status == 'stop')
                                                        {{--<a class="btn btn-xs btn-success" href="#"--}}
                                                           {{--data-target-status="normal"--}}
                                                           {{--data-href="{{ route('admin.activities.change', ['id' => $activity->id]) }}"--}}
                                                           {{--data-activity-name="{{ $activity->title }}"--}}
                                                           {{--data-toggle="modal"--}}
                                                           {{--data-target="#confirm-status-change">启用</a>--}}
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-status-change" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    修改活动状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认<span class="status-change-action"></span>该活动(<span class="activity-name"></span>)!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'put', 'id' => 'statusChangeForm')) }}
                    {!! Form::hidden('status')  !!}

                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-info fade" id="send-red-envelope-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{ Form::open(array('method' => 'post', 'id' => 'sendRedEnvelopeForm', 'class' => 'form-horizontal')) }}
                <div class="modal-header">
                    发送红包
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>
                </div>
                <div class="modal-body">
                    给该服务员(<span class="user-name"></span>)发红包:

                    <div class="form-group" style="margin-top: 10px;">
                        {!! Form::label('money_amount', '金额', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="input-group">
                            {!! Form::number('money_amount', '', ['class' => 'form-control', 'min' => '0.01', 'step' => '0.01', 'max' => '200']) !!}
                            <span>(红包金额需在0.01~200之间)</span>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        {!! Form::label('point_amount', '积分', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="input-group">
                            {!! Form::number('point_amount', '', ['class' => 'form-control', 'min' => '1', 'step' => '0.01', 'max' => '1000']) !!}
                            <span>(积分数量需在1~1000之间)</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-status-change').on('show.bs.modal', function(e) {
        $(this).find('.activity-name').text($(e.relatedTarget).data('activity-name'));
        $(this).find('#statusChangeForm').attr('action', $(e.relatedTarget).data('href'));
        var target_status = $(e.relatedTarget).data('target-status');
        $(this).find('.status-change-action').text((target_status == 'normal') ? '启用' : '禁用');
        $(this).find('input[name="status"]').val(target_status);
    });
@endsection