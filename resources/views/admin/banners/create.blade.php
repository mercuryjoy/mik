@extends('admin.layouts.app')

@section('htmlheader_title')
    新建图片
@endsection

@section('contentheader_title')
    新建图片
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.banners.index') }}"><i class="fa fa-bannerspaper-o"></i> 图片管理</a></li>
    <li class="active">新建图片</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Banners, ['route' => 'admin.banners.store', 'files' => true]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">新建图片</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        {!! Form::label('title', '标题') !!}
                        {!! Form::text('title', old('title'), ['class' => 'form-control']) !!}
                        {!! $errors->first('title', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('thumbnail') ? ' has-error' : '' }}">
                        {!! Form::label('thumbnail', '题图 ') !!}
                        @if(old('thumbnail_url'))
                            {!! Form::hidden('thumbnail_url', old('thumbnail_url')) !!}
                            <p><img src="{{old('thumbnail_url')}}"/></p>
                        @endif
                        {!! Form::file('thumbnail') !!}
                        {!! $errors->first('thumbnail', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                    <div class="form-group{{ $errors->has('content_url') ? ' has-error' : '' }}">
                        {!! Form::label('content_url', '图片链接') !!}
                        {!! Form::text('content_url', old('content_url'), ['class' => 'form-control']) !!}
                        {!! $errors->first('content_url', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                    <div class="form-group{{ $errors->has('order_id') ? ' has-error' : '' }}">
                        {!! Form::label('order_id', '序号(轮播图排序序号)') !!}
                        {!! Form::text('order_id', old('order_id'), ['class' => 'form-control']) !!}
                        {!! $errors->first('order_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                </div>

                <div class="box-footer">
                    <a href="javascript:window.history.back();" class="btn btn-default">取消</a>
                    <button type="submit" class="btn btn-primary pull-right">提交</button>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
