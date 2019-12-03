@extends('admin.layouts.app')

@section('htmlheader_title')
    新建支付方式
@endsection

@section('contentheader_title')
    新建支付方式
@endsection

@section('breadcrumb')
<li><a href="{{ route('admin.store.items.index') }}"><i class="fa fa-shopping-bag"></i> 支付方式管理</a></li>
<li class="active">新建支付方式</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Pay(), ['route' => 'admin.pays.store', 'files' => true]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">新建支付方式</h3>
                </div>
                <div class="box-body">

                    <div class="form-group {{ $errors->has('pay_way') ? 'has-error' : '' }}">
                        {!! Form::label('pay_way', '支付方式') !!}
                        {!! Form::select('pay_way', ["balance" => "余额", "alipay" => "支付宝", "wechat"=> "微信", 'line'=>'线下'], old('pay_way'), ['class' => 'form-control select2', 'placeholder' => '请选择支付方式']) !!}
                        {!! $errors->first('pay_way', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        {!! Form::label('description', '描述') !!}
                        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
                        {!! $errors->first('description', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
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
