@extends('admin.layouts.app')

@section('htmlheader_title')
    智能提醒设置
@endsection

@section('contentheader_title')
    智能提醒设置
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-6'>
            <div class="row">
                {!! Form::open(['route' => 'admin.notifications.store']) !!}
                <div class="col-sm-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">添加提醒手机号</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group {{ $errors->has('telephone') ? ' has-error' : '' }}">
                                {!! Form::label('telephone', '手机号', ['class' => 'control-label']) !!}
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    {!! Form::text('telephone', old('telephone'), ['class' => 'form-control']) !!}
                                </div>
                                {!! $errors->first('telephone', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                            </div>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-primary pull-right" type="submit">添加</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">提醒手机号列表</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-hover dataTable" role="grid">
                                <thead>
                                <tr role="row">
                                    <th>手机号</th>
                                    <th>操作</th>
                                </thead>
                                <tbody>
                                @foreach($telephones as $telephone)
                                    <tr role="row">
                                        <td>{{ $telephone }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-danger" href="#" data-href="{{ route('admin.notifications.destroy', ['telephone' => $telephone]) }}" data-toggle="modal" data-target="#confirm-delete">删除</a>
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

        <div class='col-md-6'>
            {!! Form::open(array('method' => 'put', 'route' => 'admin.notifications.update')) !!}

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">提醒触发阈值设置</h3>
                </div>
                <div class="box-body">
                    <div class="form-group {{ $errors->has('notification_daily_money_threshold') ? ' has-error' : '' }}">
                        {!! Form::label('notification_daily_money_threshold', '每日奖金支出警示额度', ['class' => 'control-label']) !!}
                        {!! Form::text('notification_daily_money_threshold', old('notification_daily_money_threshold', $notification_daily_money_threshold), ['class' => 'form-control']) !!}
                        {!! $errors->first('notification_daily_money_threshold', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('notification_daily_user_scan_threshold') ? ' has-error' : '' }}">
                        {!! Form::label('notification_daily_user_scan_threshold', '同一用户一天内扫码数量上限', ['class' => 'control-label']) !!}
                        {!! Form::text('notification_daily_user_scan_threshold', old('notification_daily_user_scan_threshold', $notification_daily_user_scan_threshold), ['class' => 'form-control']) !!}
                        {!! $errors->first('notification_daily_user_scan_threshold', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('notification_funding_pool_threshold') ? ' has-error' : '' }}">
                        {!! Form::label('notification_funding_pool_threshold', '资金池余额警示额度', ['class' => 'control-label']) !!}
                        {!! Form::text('notification_funding_pool_threshold', old('notification_funding_pool_threshold', $notification_funding_pool_threshold), ['class' => 'form-control']) !!}
                        {!! $errors->first('notification_funding_pool_threshold', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary pull-right" type="submit">修改设置</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    删除提醒手机号
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该手机号?
                </div>
                <div class="modal-footer">
                    {{ Form::open(array('method' => 'delete', 'id' => 'deleteTelephoneForm')) }}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">删除</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection


@section('javascript')
    $('#confirm-delete').on('show.bs.modal', function(e) {
    $(this).find('#deleteTelephoneForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection