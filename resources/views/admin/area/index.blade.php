@extends('admin.layouts.app')

@section('htmlheader_title')
    地区管理
@endsection

@section('contentheader_title')
    地区管理
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>地区编号</th>
                                        <th>地区名称</th>
                                        <th>地区类别</th>
                                        <th>上级地区</th>
                                        <th>上上级地区</th>
                                    </thead>
                                    <tbody>
                                    @foreach($areas as $area)
                                        <tr role="row" class="odd">
                                            <td>{{ $area->id }}</td>
                                            <td>{{ $area->name }}</td>
                                            <td>{{ $area->type }}</td>
                                            <td>{{ $area->parent_area["name"] }} {{ $area->parent_area["id"]}}</td>
                                            <td>{{ $area->grandparent_area["name"] }} {{ $area->grandparent_area["id"]}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">共{{$areas->count()}}条记录</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">{!! $areas->links() !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection