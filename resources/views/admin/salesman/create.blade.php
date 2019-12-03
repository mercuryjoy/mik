@extends('admin.layouts.app')

@section('htmlheader_title')
    新建营销员
@endsection

@section('contentheader_title')
    新建营销员
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.salesmen.index') }}"><i class="fa fa-map-marker"></i> 营销员管理</a></li>
    <li class="active">新建营销员</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Salesman, ['route' => ['admin.salesmen.store']]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">营销员信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        {!! Form::label('phone', '手机号码') !!}
                        {!! Form::text('phone', old('phone'), ['class' => 'form-control']) !!}
                        {!! $errors->first('phone', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
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
