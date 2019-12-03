@extends('admin.layouts.app')

@section('htmlheader_title')
    经销商报表 - {{ $distributor['name'] }}
@endsection

@section('contentheader_title')
    经销商报表 - {{ $distributor['name'] }}
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/distributors') }}"><i class="fa fa-sitemap"></i> 经销商管理</a></li>
    <li class="active">{{ $distributor['name'] }}</li>
@endsection

@section('main-content')
    <div class='row'>
        {!! Form::open(['route' => ['admin.distributors.show', $distributor->id], 'method' => 'get']) !!}

        <div class='col-md-12'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">报表设定</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('daterange', '时间段') !!}
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('daterange', old('daterange'), ['class' => 'daterange form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_keyword', '终端名称(关键字)') !!}
                                {!! Form::text('filter_keyword', old('filter_keyword'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_shop_id', '终端ID') !!}
                                {!! Form::text('filter_shop_id', old('filter_shop_id'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit">应用报表设定</button>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">报表</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('admin.distributors.show', [$distributor->id, 'export' => 'xls', 'daterange' => old('daterange')]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>终端ID</th>
                                        <th>终端名称</th>
                                        <th>地址</th>
                                        <th>服务员人数</th>
                                        <th>扫码总数</th>
                                        <th>扫码总金额</th>
                                    </thead>
                                    <tbody>
                                    @foreach($shops as $shop)
                                        <tr role="row">
                                            <td>{{ $shop->id }}</td>
                                            <td>{{ $shop->name }}</td>
                                            <td>{{ $shop->address }}</td>
                                            <td>{{ count($shop->users) }}</td>
                                            <td>{{ $shop->users->sum(function ($user) { return count($user->scanLog);}) }}</td>
                                            <td>￥{{ $shop->users->sum(function ($user) { return $user->scanLog->sum('money');}) / 100 }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{--<div class="row">--}}
                            {{--<div class="col-sm-3">--}}
                                {{--<div class="dataTables_info" role="status" aria-live="polite">{{$distributors->firstItem()}} - {{$distributors->lastItem()}} (共{{$distributors->total()}}条记录)</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-9">--}}
                                {{--<div class="dataTables_paginate paging_simple_numbers">--}}
                                    {{--{!! $distributors->appends(['keyword' => old('keyword'), 'level' => old('level'), 'area_id' => old('area_id')])->links() !!}--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
