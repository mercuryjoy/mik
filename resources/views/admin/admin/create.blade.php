@extends('admin.layouts.app')

@section('htmlheader_title')
    新建管理员
@endsection

@section('contentheader_title')
    新建管理员
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/admins') }}"><i class="fa fa-users"></i> 管理员管理</a></li>
    <li class="active">新建管理员</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Admin, ['route' => ['admin.admins.store']]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">管理员信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        {!! Form::label('email', 'Email') !!}
                        {!! Form::text('email', old('email'), ['class' => 'form-control']) !!}
                        {!! $errors->first('email', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        {!! Form::label('password', '密码') !!}
                        {!! Form::password('password', ['class' => 'form-control select2']) !!}
                        {!! $errors->first('password', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('level', '级别') !!}
                        {!! Form::select('level', $levels, old('level'), ['class' => 'form-control select2']) !!}
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
