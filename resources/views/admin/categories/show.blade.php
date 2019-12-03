@extends('admin.layouts.app')

@section('htmlheader_title')
    餐饮类型报表 - {{ $category['name'] }}
@endsection

@section('contentheader_title')
    餐饮类型报表 - {{ $category['name'] }}
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/categories') }}"><i class="fa fa-sitemap"></i> 餐饮类型管理</a></li>
    <li class="active">{{ $category['name'] }}</li>
@endsection

@section('main-content')
    <div class='row'>
        {!! Form::open(['route' => ['admin.categories.show', $category->id], 'method' => 'get']) !!}

        <div class='col-md-12'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">过滤条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_shop_name', '终端名称(关键字)') !!}
                                {!! Form::text('filter_shop_name', old('filter_shop_name'), ['class' => 'form-control']) !!}
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
                        <a href="{{ route('admin.categories.show', [$category->id, 'export' => 'xls', 'daterange' => old('daterange')]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>
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
                                        <th>餐饮类别</th>
                                    </thead>
                                    <tbody>
                                    @foreach($shops as $shop)
                                        <tr role="row">
                                            <td>{{ $shop->id }}</td>
                                            <td>{{ $shop->name }}</td>
                                            <td>{{ $shop->address }}</td>
                                            <td>{{ $shop->category->name }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$shops->firstItem()}} - {{$shops->lastItem()}} (共{{$shops->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $shops->appends(['keyword' => old('keyword'), 'level' => old('level'), 'area_id' => old('area_id'), 'filter_shop_name' => old('filter_shop_name'), 'filter_shop_id' => old('filter_shop_id')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
