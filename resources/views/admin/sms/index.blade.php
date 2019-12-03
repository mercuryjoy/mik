@extends('admin.layouts.app')

@section('htmlheader_title')
    短信日志
@endsection

@section('contentheader_title')
    短信日志
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::model(new App\SMSLog, ['route' => ['admin.sms.store'], 'class' => 'form-horizontal']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">测试</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group {{ $errors->has('telephone') ? ' has-error' : '' }}">
                            {!! Form::label('telephone', '手机号', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                {!! Form::text('telephone', old('telephone'), ['class' => 'form-control']) !!}
                                </div>
                                {!! $errors->first('telephone', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                            </div>
                            <div class="col-sm-4">
                                <button class="btn btn-primary" type="submit">发 送</button>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">短信日志</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>手机号</th>
                                        <th>内容</th>
                                        <th>类型</th>
                                        <th>验证码</th>
                                        <th>状态</th>
                                        <th>发送时间</th>
                                    </thead>
                                    <tbody>
                                    @foreach($sms_logs as $sms)
                                        <tr role="row">
                                            <td>{{ $sms['id'] }}</td>
                                            <td>{{ $sms['telephone'] }}</td>
                                            <td>{{ $sms['content'] }}</td>
                                            <td>{{ $sms->typeDisplay }}</td>
                                            <td>{{ $sms['code'] }}</td>
                                            @if ($sms->status == 'error')
                                                <td>
                                                    <span class="text-red" data-toggle="tooltip" data-original-title="{{$sms->comment}}">{{ $sms->statusDisplay }}</span>
                                                </td>
                                            @else
                                                <td>{{ $sms->statusDisplay }}</td>
                                            @endif
                                            <td>{{ $sms['created_at'] }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$sms_logs->firstItem()}} - {{$sms_logs->lastItem()}} (共{{$sms_logs->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $sms_logs->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection