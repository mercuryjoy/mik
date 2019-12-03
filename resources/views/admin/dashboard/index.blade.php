@extends('admin.layouts.app')

@section('htmlheader_title')
    首页
@endsection

@section('contentheader_title')
    首页
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-6'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-barcode"></i> 扫码汇总</h3>
                    {{-- <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div> --}}
                </div>
                <div class="box-body no-padding">
                    <table class="table table-hover dataTable" style="margin:0 !important;">
                        <tr><td>当前已领红包总数<span class="pull-right">{{ $scan_count }}</span></td></tr>
                        <tr><td>当前消耗金额总数<span class="pull-right">￥{{ number_format($scan_money_total / 100, 2, '.', '') }}</span></td></tr>
                        <tr><td>当前红包均值<span class="pull-right">￥{{ number_format($scan_count > 0 ? $scan_money_total / 100 / $scan_count : 0, 2, '.', '') }}</span></td></tr>
                        <tr><td>当前资金池(余额)<span class="pull-right">￥{{ number_format($pool_balance / 100, 2, '.', '') }}</span></td></tr>
                        <tr><td>当前应领未领金额(用户钱包余额总数)<span class="pull-right">￥{{ number_format($user_money_balance_total / 100, 2, '.', '') }}</span></td></tr>
                    </table>
                </div>
                <div class="box-footer ">
                    <a href="{{ route('admin.scans.index') }}" class="btn btn-primary pull-right">打开扫码记录</a>
                </div>
            </div>
        </div>
    </div><!-- /.row -->
@endsection
