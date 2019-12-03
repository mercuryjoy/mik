@extends('admin.layouts.app')

@section('htmlheader_title')
    资金池管理
@endsection

@section('contentheader_title')
    资金池管理
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-6'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">资金池状态</h3>
                </div>
                <div class="box-body">
                    <div>
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-bank"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">资金池余额</span>
                                <span class="info-box-number" style="font-size: 30px; margin-top: 10px;"><small>￥</small>{{ number_format($pool_balance / 100, 2, '.', '') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='col-md-6'>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">充值</h3>
                </div>
                {!! Form::open(['route' => 'admin.fundingpool.deposit', 'method' => 'PUT']) !!}
                <div class="box-body">
                    <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                        {!! Form::label('amount', '金额') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-rmb"></i>
                            </div>
                            {!! Form::number('amount', '', ['class' => 'form-control']) !!}
                        </div>
                        {!! $errors->first('amount', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right">确定</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection