@extends('admin.layouts.app')

@section('htmlheader_title')
    商城商品管理
@endsection

@section('contentheader_title')
    商城商品管理 <a href="{{ route('admin.store.items.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> 新建商品</a>
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-12'>
            {!! Form::open(['route' => ['admin.store.items.index'], 'method' => 'get']) !!}
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
                                    {!! Form::label('status', '状态') !!}
                                {!! Form::select('status', ["" => "全部状态", "in_stock" => "正常", "out_of_stock"=> "下架"], old('status'), ['class' => 'form-control select2']) !!}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> 应用过滤条件</button>
                    <a class="btn btn-warning pull-right" href="{{ route("admin.store.items.index") }}"><i class="fa fa-trash"></i> 清除过滤条件</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">商品列表</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>名称</th>
                                        <th>需要积分</th>
                                        <th>需要金额</th>
                                        <th>是否虚拟商品</th>
                                        <th>库存</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($items as $item)
                                        <tr role="row">
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->price_point }}</td>
                                            <td>￥{{ $item->price_money / 100 }}</td>
                                            <td>{{ $item->is_virtual ? "是" : "否" }}</td>
                                            <td>{{ $item->stock }}</td>
                                            <td>{{ $item->statusDisplay }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.store.items.edit', ['id' => $item['id']]) }}">详情/修改</a>
                                                <a class="btn btn-xs btn-primary" href="{{ route('admin.store.orders.index', ['item_id' => $item['id']]) }}">相关订单</a>
                                                {{-- <a class="btn btn-xs btn-warning" href="{{ route('admin.store.items.destroy', ['id' => $item['id']]) }}">下架</a> --}}
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="dataTables_info" role="status" aria-live="polite">{{$items->firstItem()}} - {{$items->lastItem()}} (共{{$items->total()}}条记录)</div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $items->appends(['status' => old('status')])->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    删除商品
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认删除该商品(<span class="item-name"></span>), 此操作无法恢复, 请慎用!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'delete', 'id' => 'deleteDistributorForm')) }}
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">删除</button>
                    {{ Form::close() }}

                    </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-delete').on('show.bs.modal', function(e) {
    $(this).find('.distributor-name').text($(e.relatedTarget).data('distributor-name'));
    $(this).find('#deleteDistributorForm').attr('action', $(e.relatedTarget).data('href'));
    });
@endsection
