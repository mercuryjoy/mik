@extends('admin.layouts.app')

@section('htmlheader_title')
    二维码管理
@endsection

@section('contentheader_title')
    二维码管理
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.codes.index'], 'method' => 'get']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">过滤条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
<!--                         <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('keyword', '关键字(id)') !!}
                                {!! Form::text('keyword', old('keyword'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_batch_keyword', '关键字(批次号)') !!}
                                {!! Form::text('filter_batch_keyword', old('filter_batch'), ['class' => 'form-control']) !!}
                            </div>
                        </div> -->
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('code', '关键字(二维码)') !!}
                                {!! Form::text('code', old('code'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.distributors.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">全部二维码</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>批次号</th>
                                        <th>二维码</th>
                                        <th>服务员是否已扫</th>
                                        <th>用户是否已扫</th>
                                        <th>类型</th>
                                        <th>批次状态</th>
                                    </thead>
                                    <tbody>
                                    @foreach($codes as $code)
                                        <tr role="row">
                                            <td>{{ $code->id }}</td>
                                            <td>{{ $code->batch->name or '' }}</td>
                                            <td>{{ $code->code }}</td>
                                            <td>{!! $code->scan_log_id ? '<span class="text-light-blue">已扫</span>' : '<span class="text-muted">未扫</span>' !!}</td>
                                            <td>{!! $code->user_scan_log_id ? '<span class="text-light-blue">已扫</span>' : '<span class="text-muted">未扫</span>' !!}</td>
                                            <td>{{ $code->batch->typeDisplay}} </td>
                                            <td>
                                                <span class="@if ( $code->batch->status == 'frozen' ) text-red @endif">{{ $code->batch->statusDisplay }}</span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$codes->firstItem()}}
                                    - {{$codes->lastItem()}} (共{{$codes->total()}}条记录)
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $codes->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
