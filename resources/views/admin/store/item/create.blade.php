@extends('admin.layouts.app')

@section('htmlheader_title')
    新建商品
@endsection

@section('contentheader_title')
    新建商品
@endsection

@section('breadcrumb')
<li><a href="{{ route('admin.store.items.index') }}"><i class="fa fa-shopping-bag"></i> 商城商品管理</a></li>
<li class="active">新建商品</li>
@endsection

@section('main-content')
    {!! Form::model(new App\StoreItem, ['route' => 'admin.store.items.store', 'files' => true]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">新建商品</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('price_point') ? ' has-error' : '' }}">
                        {!! Form::label('price_point', '需要积分') !!}
                        {!! Form::text('price_point', old('price_point'), ['class' => 'form-control']) !!}
                        {!! $errors->first('price_point', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('price_money') ? ' has-error' : '' }}">
                        {!! Form::label('price_money', '需要红包金额') !!}
                        {!! Form::text('price_money', old('price_money'), ['class' => 'form-control']) !!}
                        {!! $errors->first('price_money', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        {!! Form::label('description', '描述') !!}
                        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
                        {!! $errors->first('description', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('is_virtual', '是否虚拟商品') !!}
                        {!! Form::select('is_virtual', ["1" => "是虚拟商品", "0"=> "不是虚拟商品"], old('is_virtual'), ['class' => 'form-control select2']) !!}
                    </div>
                    <div class="form-group{{ $errors->has('stock') ? ' has-error' : '' }}">
                        {!! Form::label('stock', '库存') !!}
                        {!! Form::text('stock', old('description'), ['class' => 'form-control']) !!}
                        {!! $errors->first('stock', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                        {!! Form::label('photo', '图片 (400 x 400)') !!}
                        @if(old('photo_url'))
                            {!! Form::hidden('photo_url', old('photo_url')) !!}
                            <p><img src="{{old('photo_url')}}" /></p>
                        @endif
                        {!! Form::file('photo') !!}
                        {!! $errors->first('photo', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('status', '状态') !!}
                        {!! Form::select('status', ["in_stock" => "正常", "out_of_stock"=> "下架"], old('status'), ['class' => 'form-control select2']) !!}
                    </div>
                </div>
                <div class="box-footer">
                    <a href="javascript:window.history.back();" class="btn btn-default">取消</a>
                    <button type="submit" class="btn btn-primary pull-right">提交</button>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
