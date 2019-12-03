@extends('admin.layouts.app')

@section('htmlheader_title')
    资金池记录
@endsection

@section('contentheader_title')
    资金池记录
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">资金池记录</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>类型</th>
                                        <th>金额</th>
                                        <th>余额</th>
                                        <th>操作管理员</th>
                                        <th>用户ID</th>
                                    </thead>
                                    <tbody>
                                    @foreach($fundingPoolLogs as $log)
                                        <tr role="row">
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->typeDisplay }}</td>
                                            <td><span{!! $log->amount < 0 ? ' style="color:red;"' : '' !!}>￥{{ $log->amount / 100 }}</span></td>
                                            <td>￥{{ $log->balance / 100 }}</td>
                                            <td>{{ $log->admin['name'] }}</td>
                                            <td>{{ $log->user_id }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$fundingPoolLogs->firstItem()}} - {{$fundingPoolLogs->lastItem()}} (共{{$fundingPoolLogs->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $fundingPoolLogs->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection