@extends('admin.layouts.app')

@section('htmlheader_title')
    终端管理
@endsection

@section('contentheader_title')
    终端管理 <a href="{{ route('admin.shops.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建终端</a>
@endsection

@section('breadcrumb')
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.shops.index'], 'method' => 'get']) !!}
            <div class='col-md-3'>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">统计设定</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i></button>
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
                        <button class="btn btn-primary" type="submit">应用统计设定</button>
                    </div>
                </div>
            </div>

            <div class='col-md-9'>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">过滤条件</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('filter_daterange', '创建时间') !!}
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        {!! Form::text('filter_daterange', old('filter_daterange'), ['class' => 'daterange form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('filter_keyword', '关键字(终端名称)') !!}
                                    {!! Form::text('filter_keyword', old('filter_keyword'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('filter_level', '级别') !!}
                                    {!! Form::select('filter_level', ["" => "全部终端", "A" => "A级", "B" => "B级", "C" => "C级", "D" => "D级"], old('filter_level'), ['class' => 'form-control select2']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('filter_salesman', '关键字(营销员姓名)') !!}
                                    {!! Form::text('filter_salesman', old('filter_salesman'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                {!! Form::label('filter_area_id', '地区') !!}
                                {!! Form::text('filter_area_id', old('filter_area_id'), ['class' => 'areapicker form-control hidden']) !!}
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('filter_status', '状态') !!}
                                    {!! Form::select('filter_status', ["" => "全部状态", "1" => "启用", "2"=> "禁用"], old('filter_status'), ['class' => 'form-control select2']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                        <a class="btn btn-warning pull-right" href="{{ route("admin.shops.index") }}"><i
                                    class="fa fa-trash"></i> 清除过滤条件</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">终端列表{{ $has_filter ? "(已过滤)" : "" }}</h3>
                    <div class="box-tools pull-right">

                        <a href="#" data-href="{{ route('admin.shops.import') }}" data-toggle="modal"
                           data-target="#import_excel"
                           class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导入Excel</a>

                        <a href="{{ route('admin.shops.index', array_merge(app('request')->all(), ['export' => 'xls']))}}"
                           class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> 导出Excel</a>

                        <a href="{{ env('EXCEL_TEMPLATE') }}" class="btn btn-primary" target="_blank"><i class="fa fa-file-excel-o"></i> Excel模板下载</a>
                    </div>

                </div>
                <div class="box-body">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>LOGO</th>
                                        <th>名称</th>
                                        <th>店长</th>
                                        <th>地址</th>
                                        <th>营销员</th>
                                        <th>扫码总数</th>
                                        <th>旗下服务员数</th>
                                        <th>扫码总金额</th>
                                        <th>创建时间</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($shops as $shop)
                                        <tr role="row">
                                            <td>{{ $shop->id }}</td>
                                            <td>
                                                @if($shop->logo)
                                                    <img width="40" src="{{ $shop->logo }}"/>
                                                @endif
                                            </td>
                                            <td>{{ $shop->name }}</td>
                                            <td>
                                                <a href="{{ route('admin.users.edit', ['id' => $shop->owner_id]) }}">{{ $shop->owner->name or '' }}</a>
                                            </td>
                                            <td>{{ $shop->area->display or '' }} {{ $shop->address }}</td>
                                            <td>
                                                {{ $shop->salesman->name or '' }}
                                            </td>
                                            <td>{{ $shop->users->sum(function ($user) { return count($user->scanLog);}) }}</td>
                                            <td>{{ $shop->users->sum(function ($user) { return count($user);}) }}</td>
                                            <td>￥{{ $shop->users->sum(function ($user) { return $user->scanLog->sum('money');}) / 100 }}</td>
                                            <td>{{ $shop->created_at }}</td>
                                            <td>{{ $shop->deletedDisplay }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary"
                                                   href="{{ route('admin.shops.edit', ['id' => $shop['id']]) }}">详情/修改</a>
                                                <a class="btn btn-xs btn-info"
                                                   href="{{ route('admin.shops.show', ['id' => $shop['id'], 'daterange' => old('daterange')]) }}">报表</a>
                                              @if (! $shop->deleted_at)
                                                  <a class="btn btn-xs btn-danger" href="#"
                                                     data-target-status="0"
                                                     data-href="{{ route('admin.shops.destroy', ['id' => $shop->id]) }}"
                                                     data-shop-name="{{ $shop->name }}"
                                                     data-toggle="modal"
                                                     data-target="#confirm-status-change">禁用</a>
                                              @else
                                                  <a class="btn btn-xs btn-success" href="#"
                                                     data-target-status="1"
                                                     data-href="{{ route('admin.shops.restore', ['id' => $shop->id]) }}"
                                                     data-shop-name="{{ $shop->name }}"
                                                     data-toggle="modal"
                                                     data-target="#confirm-status-change">启用</a>
                                              @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$shops->firstItem()}}
                                    - {{$shops->lastItem()}} (共{{$shops->total()}}条记录)
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $shops->appends(['filter_keyword' => old('filter_keyword'), 'filter_level' => old('filter_level'), 'filter_area_id' => old('filter_area_id'), 'daterange' => old('daterange'), 'filter_daterange' => old('filter_daterange'),  'filter_salesman' => old('filter_salesman'), 'filter_status' => old('filter_status')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-status-change" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    修改终端状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认<span class="status-change-action"></span>该终端(<span class="shop-name"></span>)!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('id' => 'statusChangeForm')) }}
                    {{ method_field('put') }}

                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-primary fade" id="import_excel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            {{ Form::open(array('method' => 'post', 'id' => 'importExcelForm', 'files' => true)) }}
            <div class="modal-content">
                <div class="modal-header">
                    导入Excel
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('excel', 'Excel文件') !!}
                        @if(old('excel'))
                            {!! Form::hidden('excel', old('excel')) !!}
                        @endif
                        {!! Form::file('excel') !!}
                        {!! $errors->first('excel', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">导入</button>

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.shop-name').text($(e.relatedTarget).data('shop-name'));
        $(this).find('#deleteDistributorForm').attr('action', $(e.relatedTarget).data('href'));
    });
    $('#import_excel').on('show.bs.modal', function(e) {
        $(this).find('#importExcelForm').attr('action', $(e.relatedTarget).data('href'));
    });
    
    $('#confirm-status-change').on('show.bs.modal', function(e) {
        $(this).find('.shop-name').text($(e.relatedTarget).data('shop-name'));
        $(this).find('#statusChangeForm').attr('action', $(e.relatedTarget).data('href'));
        var target_status = $(e.relatedTarget).data('target-status');
        $(this).find('.status-change-action').text((target_status == 1) ? '启用' : '禁用');
        if (target_status == 0) {
            $('input[name="_method"]').val('delete')
        }
    });
@endsection