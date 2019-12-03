@extends('admin.layouts.app')

@section('htmlheader_title')
    采购商品 - {{ $item['name'] }}
@endsection

@section('contentheader_title')
    采购商品 - {{ $item['name'] }}
@endsection

@section('breadcrumb')
<li><a href="{{ route('admin.goods.items.index') }}"><i class="fa fa-buysellads"></i> 采购商品管理</a></li>
<li class="active">{{ $item['name'] }}</li>
@endsection

@section('main-content')
    {!! Form::model(new App\StoreItem, ['route' => ['admin.goods.items.update', $item->id], 'method' => 'PUT', 'files' => true]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">修改商品信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name', $item->name), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('price_money') ? ' has-error' : '' }}">
                        {!! Form::label('price_money', '销售价格') !!}
                        {!! Form::text('price_money', old('name', $item->price_money / 100), ['class' => 'form-control']) !!}
                        {!! $errors->first('price_money', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        {!! Form::label('description', '描述') !!}
                        {!! Form::textarea('description', old('description', $item->description), ['class' => 'form-control']) !!}
                        {!! $errors->first('description', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('stock') ? ' has-error' : '' }}">
                        {!! Form::label('stock', '库存') !!}
                        {!! Form::text('stock', old('description', $item->stock), ['class' => 'form-control']) !!}
                        {!! $errors->first('stock', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                        {!! Form::label('photo', '图片 (400 x 400)') !!}
                        @if(old('photo_url'))
                            {!! Form::hidden('photo_url', old('photo_url')) !!}
                        @endif
                        @if(old('photo_url', $item->photo_url) )
                            <p><img src="{{old('photo_url', $item->photo_url)}}" /></p>
                        @endif
                        {!! Form::file('photo') !!}
                        {!! $errors->first('photo', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('status', '状态') !!}
                        {!! Form::select('status', ["in_stock" => "正常", "out_of_stock"=> "下架"], old('status', $item->status), ['class' => 'form-control select2']) !!}
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
