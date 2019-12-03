@extends('admin.layouts.app')

@section('htmlheader_title')
    新建服务员
@endsection

@section('contentheader_title')
    新建服务员
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/users') }}"><i class="fa fa-female"></i> 服务员管理</a></li>
    <li class="active">新建服务员</li>
@endsection

@section('main-content')
    {!! Form::model(new App\User, ['route' => ['admin.users.store']]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">服务员信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '姓名') !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('gender', '性别') !!}
                        {!! Form::select('gender', ["female" => "女", "male"=> "男"], old('level'), ['class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group{{ $errors->has('shop_id') ? ' has-error' : '' }}">
                        {!! Form::label('shop_id', '终端ID') !!}
                        {!! Form::text('shop_id', old('shop_id'), ['class' => 'form-control']) !!}
                        {!! $errors->first('shop_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('telephone') ? ' has-error' : '' }}">
                        {!! Form::label('telephone', '手机号码') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </div>
                            {!! Form::text('telephone', old('telephone'), ['class' => 'form-control']) !!}
                        </div>
                        {!! $errors->first('telephone', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('status', '审核状态') !!}
                        {!! Form::select('status', ["pending" => "待审核", "normal"=> "正常"], old('status'), ['class' => 'form-control select2']) !!}
                    </div>
                </div>
                <div class="box-footer">
                    {!! Form::submit('提交', ['class' => 'btn btn-primary pull-right']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
