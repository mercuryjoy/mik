@extends('admin.layouts.app')

@section('htmlheader_title')
    新建餐饮类型
@endsection

@section('contentheader_title')
    新建餐饮类型
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/categories') }}"><i class="fa fa-bookmark"></i> 餐饮类型管理</a></li>
    <li class="active">新建餐饮类型</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Category(), ['route' => ['admin.categories.store']]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">餐饮类型信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    {{--<div class="form-group">--}}
                        {{--{!! Form::label('level', '级别') !!}--}}
                        {{--{!! Form::select('level', ["1" => "一级餐饮类型", "2"=> "二级餐饮类型"], old('level'), ['class' => 'form-control select2']) !!}--}}
                    {{--</div>--}}
                    {{--<div class="form-group{{ $errors->has('parent_id') ? ' has-error' : '' }}">--}}
                        {{--{!! Form::label('parent_id', '上级餐饮类型ID') !!}--}}
                        {{--{!! Form::text('parent_id', old('parent_id'), ['class' => 'form-control']) !!}--}}
                        {{--{!! $errors->first('parent_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}--}}
                    {{--</div>--}}
                </div>
                <div class="box-footer">
                    {!! Form::submit('提交', ['class' => 'btn btn-primary pull-right']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
