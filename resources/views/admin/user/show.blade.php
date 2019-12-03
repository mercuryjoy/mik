@extends('admin.layouts.app')

@section('htmlheader_title')
    服务员报表 - {{ $user->name }}
@endsection

@section('contentheader_title')
    服务员报表 - {{ $user->name }}
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-map-marker"></i> 服务员管理</a></li>
    <li class="active">{{ $user->name }}</li>
@endsection

@section('main-content')
    <div class='row'>
        <div class='col-md-6'>
            <div class='row'>
                <div class='col-md-12'>
                    {!! Form::open(['route' => ['admin.users.show', $user->id], 'method' => 'get']) !!}

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
                                        {!! Form::hidden('show', old('show', $show)) !!}
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
                            <button class="btn {{ $show == 'point' ? 'btn-primary' : 'btn-default'}} pull-right" name="show" value="point" style="margin-left: 3px;" type="submit">积分变动记录</button>
                            <button class="btn {{ $show == 'money' ? 'btn-primary' : 'btn-default'}} pull-right" name="show" value="money" type="submit">钱包变动记录</button>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
            <div class='row' id="money" style="display: {{ $show == 'money' ? 'block' : 'none' }}">
                <div class='col-md-12'>
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">钱包变动记录报表 ({{old('daterange')}})</h3>
                        </div>
                        <div class="box-body">
                            <div class="dataTables_wrapper form-inline dt-bootstrap">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-hover dataTable" role="grid">
                                            <thead>
                                            <tr role="row">
                                                <th>ID</th>
                                                <th>类型</th>
                                                <th>红包金额</th>
                                                <th>变动时间</th>
                                            </thead>
                                            <tbody>
                                            @foreach($money_logs as $log)
                                                <tr role="row">
                                                    <td>{{ $log->id }}</td>
                                                    <td>
                                                        @if($log->type == "adjustment")
                                                            <span data-toggle="tooltip" data-original-title="{{$log->comment}}">{{ $log->typeDisplay }}</span>
                                                        @else
                                                            {{ $log->typeDisplay }}
                                                        @endif
                                                    </td>
                                                    <td>￥{{ $log->amount / 100 }}</td>
                                                    <td>{{ $log->created_at }}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class='row' id="point" style="display: {{ $show == 'point' ? 'block' : 'none' }}">
                <div class='col-md-12'>
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">积分变动记录报表 ({{old('daterange')}})</h3>
                        </div>
                        <div class="box-body">
                            <div class="dataTables_wrapper form-inline dt-bootstrap">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-hover dataTable" role="grid">
                                            <thead>
                                            <tr role="row">
                                                <th>ID</th>
                                                <th>类型</th>
                                                <th>变动积分</th>
                                                <th>变动时间</th>
                                            </thead>
                                            <tbody>
                                            @foreach($point_logs as $log)
                                                <tr role="row">
                                                    <td>{{ $log->id }}</td>
                                                    <td>
                                                        {{ $log->typeDisplay }}
                                                    </td>
                                                    <td>{{ $log->amount }}</td>
                                                    <td>{{ $log->created_at }}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='col-md-6'>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">服务终端记录</h3>
                </div>
                <div class="box-body table-responsive">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>终端名称</th>
                                        <th>服务时间</th>
                                    </thead>
                                    <tbody>

                                    @foreach($service_shop_logs as $log)
                                        <tr role="row">
                                            <td>
                                                @if ($log['shop'] == null)
                                                    [已删除终端]
                                                @else
                                                    {{ $log['shop']->name }}
                                                @endif
                                            </td>
                                            <td>{{ $log['start_time'] }} - {{ $log['end_time'] ?: '至今' }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection