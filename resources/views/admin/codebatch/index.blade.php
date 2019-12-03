@extends('admin.layouts.app')

@section('htmlheader_title')
    二维码批次管理
@endsection

@section('contentheader_title')
    二维码批次管理
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-6'>
            {!! Form::model(new App\Code, ['route' => ['admin.codebatches.store']]) !!}
            {!! Form::hidden('generate', 'generate', ['class' => 'form-control']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">随机生成二维码</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('batch') ? ' has-error' : '' }}">
                                {!! Form::label('batch', '批次号', ['class' => 'control-label']) !!}
                                {!! Form::text('batch', old('batch'), ['class' => 'form-control']) !!}
                                {!! $errors->first('batch', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('count') ? ' has-error' : '' }}">
                                {!! Form::label('count', '数量', ['class' => 'control-label']) !!}
                                {!! Form::text('count', old('count'), ['class' => 'form-control']) !!}
                                {!! $errors->first('count', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">

                    <button class="btn btn-primary pull-right" type="submit" name="type" value="activity"
                            style="display:block;float:right;margin-right:20px">生成活动二维码
                    </button>
                    <button class="btn btn-primary pull-right" type="submit" name="type" value="normal"
                            style="display:block;float:right;margin-right:20px">生成二维码
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">全部二维码批次</h3>
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
                                        <th>二维码数量</th>
                                        <th>已扫数量</th>
                                        <th>未扫数量</th>
                                        <th>生成时间</th>
                                        <th>类型</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @foreach($batches as $batch)
                                        <tr role="row">
                                            <td>{{ $batch->id }}</td>
                                            <td>{{ $batch->name }}</td>
                                            <td>{{ $batch->count }}</td>
                                            <td>{{ $batch->code()->where('user_scan_log_id', '>', 0)->count() }}</td>
                                            <td>{{ $batch->count - $batch->code()->where('user_scan_log_id', '>', 0)->count() }}</td>
                                            <td>{{ $batch->created_at }}</td>
                                            <td>{{ $batch->typeDisplay }}</td>
                                            <td>
                                                <span class="@if ($batch->status == 'frozen') text-red @endif">{{ $batch->statusDisplay }}</span>
                                            </td>
                                            <td>
                                                <a class="btn btn-xs btn-primary"
                                                   href="{{ route('admin.codebatches.export', ['batch' => $batch->id]) }}">导出该批次</a>

                                                @if ($batch->status == 'normal')
                                                    <a class="btn btn-xs btn-danger" href="#"
                                                       data-target-status="frozen"
                                                       data-href="{{ route('admin.codebatches.update.status', ['id' => $batch->id]) }}"
                                                       data-batch-name="{{ $batch->name }}" data-toggle="modal"
                                                       data-target="#confirm-status-change">冻结</a>
                                                @elseif ($batch->status == 'frozen')
                                                    <a class="btn btn-xs btn-danger" href="#"
                                                       data-target-status="normal"
                                                       data-href="{{ route('admin.codebatches.update.status', ['id' => $batch->id]) }}"
                                                       data-batch-name="{{ $batch->name }}" data-toggle="modal"
                                                       data-target="#confirm-status-change">取消冻结</a>
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
                                <div class="dataTables_info" role="status" aria-live="polite">{{$batches->firstItem()}}
                                    - {{$batches->lastItem()}} (共{{$batches->total()}}条记录)
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $batches->links() !!}
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
                    修改二维码批次冻结状态
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    您是否确认<span class="status-change-action"></span>该批次二维码(<span class="batch-name"></span>)!
                </div>
                <div class="modal-footer">

                    {{ Form::open(array('method' => 'put', 'id' => 'statusChangeForm')) }}
                    {!! Form::hidden('status')  !!}

                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-outline btn-ok">确认</button>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    $('#confirm-status-change').on('show.bs.modal', function(e) {
    $(this).find('.batch-name').text($(e.relatedTarget).data('batch-name'));
    $(this).find('#statusChangeForm').attr('action', $(e.relatedTarget).data('href'));

    var target_status = $(e.relatedTarget).data('target-status');
    $(this).find('.status-change-action').text((target_status == 'normal') ? '取消冻结' : '冻结');
    $(this).find('input[name="status"]').val(target_status);
    });

    {{--$('#name1').on('click',function(){
    $('#flag').val(10);
    $('form').submit();
    });
    $('#name2').on('click',function(){
    $('#flag').val(12);
    $('form').submit();
    });--}}

@endsection

