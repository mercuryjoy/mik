@extends('admin.layouts.app')

@section('htmlheader_title')
    管理员 - {{ $admin['name'] }}
@endsection

@section('contentheader_title')
    管理员 - {{ $admin['name'] }}
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/admins') }}"><i class="fa fa-users"></i> 管理员管理</a></li>
    <li class="active">{{ $admin['name'] }}</li>
@endsection

@section('main-content')

    <div class='row'>
        @if (Auth::user()->isSeniorAdmin())
            {!! Form::model(new App\Admin, ['route' => ['admin.admins.update', $admin->id], 'method' => 'PUT']) !!}
            {!! Form::hidden('action', (Auth::user()->id == $admin->id) ? "change_self" : "change_other") !!}

            <div class='col-md-6'>
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">修改管理员信息</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            {!! Form::label('name', '名称') !!}
                            {!! Form::text('name', old('name', $admin->name), ['class' => 'form-control']) !!}
                            {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            {!! Form::label('email', 'Email') !!}
                            {!! Form::text('email', old('email', $admin->email), ['class' => 'form-control']) !!}
                            {!! $errors->first('email', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                        @if (Auth::user()->id != $admin->id)
                            <div class="form-group">
                                {!! Form::label('password', '重置密码') !!}
                                {!! Form::password('password', ['class' => 'form-control']) !!}
                                {!! $errors->first('password', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('level', '级别') !!}
                                {!! Form::select('level', $levels, old('level', $admin->level), ['class' => 'form-control select2']) !!}
                            </div>
                        @endif
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">提交</button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        @endif

        @if (Auth::user()->id == $admin->id)
            {!! Form::model(new App\Admin, ['route' => ['admin.admins.update', $admin->id], 'method' => 'PUT']) !!}
            {!! Form::hidden('action', 'change_password') !!}

            <div class='col-md-6'>
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">修改密码</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            {!! Form::label('password', '当前密码') !!}
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                            {!! $errors->first('password', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                        <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                            {!! Form::label('new_password', '新密码') !!}
                            {!! Form::password('new_password', ['class' => 'form-control']) !!}
                            {!! $errors->first('new_password', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                        <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                            {!! Form::label('new_password_confirmation', '确认新密码') !!}
                            {!! Form::password('new_password_confirmation', ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">提交</button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        @endif
    </div>

@endsection
