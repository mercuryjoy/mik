@extends('admin.layouts.app')

@section('htmlheader_title')
    终端报表 - {{ $shop->name }}
@endsection

@section('contentheader_title')
    终端报表 - {{ $shop->name }}
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/shops') }}"><i class="fa fa-map-marker"></i> 终端管理</a></li>
    <li class="active">{{ $shop->name }}</li>
@endsection

@section('main-content')
    <div class='row'>
        {!! Form::open(['route' => ['admin.shops.show', $shop->id], 'method' => 'get']) !!}

        <div class='col-md-3'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">报表设定</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
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
                        <a href="{{ route('admin.shops.show', [$shop->id, 'export' => 'xls', 'daterange' => old('daterange')]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>名字</th>
                                        <th>手机号</th>
                                        <th>扫码数量</th>
                                        <th>获得扫码总额</th>
                                        <th>获得积分总额</th>
                                        <th>绑定日期</th>
                                        <th>状态</th>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr role="row">
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->telephone }}</td>
                                            <td>{{ count($user->scanLog) }}</td>
                                            <td>￥{{ $user->scanLog->sum('money') / 100 }}</td>
                                            <td>{{ $user->scanLog->sum('point') }}</td>
                                            <td>{{ $user->updated_at }}</td>
                                            <td>{{ $user->statusDisplay }}</td>
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