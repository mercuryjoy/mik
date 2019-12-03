@extends('admin.layouts.app')

@section('htmlheader_title')
    新建经销商
@endsection

@section('contentheader_title')
    新建经销商
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/distributors') }}"><i class="fa fa-sitemap"></i> 经销商管理</a></li>
    <li class="active">新建经销商</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Distributor, ['route' => ['admin.distributors.store']]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">经销商信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('level', '级别') !!}
                        {!! Form::select('level', ["1" => "一级经销商", "2"=> "二级经销商"], old('level'), ['class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group{{ $errors->has('parent_distributor_id') ? ' has-error' : '' }}">
                        {!! Form::label('parent_distributor_id', '上级经销商ID') !!}
                        {!! Form::text('parent_distributor_id', old('parent_distributor_id'), ['class' => 'form-control']) !!}
                        {!! $errors->first('parent_distributor_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('area_id') ? ' has-error' : '' }}">
                        {!! Form::label('area_id', '地区') !!}
                        {!! Form::text('area_id', old('area_id'), ['class' => 'areapicker form-control hidden']) !!}
                        {!! $errors->first('area_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('address', '地址') !!}
                        {!! Form::text('address', old('address'), ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('contact', '联系人') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </div>
                            {!! Form::text('contact', old('contact'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('telephone', '联系电话') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </div>
                            {!! Form::text('telephone', old('telephone'), ['class' => 'form-control']) !!}
                        </div>
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
