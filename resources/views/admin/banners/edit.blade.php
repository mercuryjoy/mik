@extends('admin.layouts.app')

@section('htmlheader_title')
    修改轮播图
@endsection

@section('contentheader_title')
    修改轮播图
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.banners.index') }}"><i class="fa fa-newspaper-o"></i> 轮播图管理</a></li>
    <li class="active">修改轮播图</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Banners, ['route' => ['admin.banners.update', $banners->id], 'method' => 'PUT', 'files' => true]) !!}
    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">修改轮播图</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        {!! Form::label('title', '标题') !!}
                        {!! Form::text('title', old('title', $banners->title), ['class' => 'form-control']) !!}
                        {!! $errors->first('title', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('thumbnail') ? ' has-error' : '' }}">
                        {!! Form::label('thumbnail', '题图 ') !!}
                        @if(old('thumbnail_url'))
                            {!! Form::hidden('thumbnail_url', old('thumbnail_url')) !!}
                        @endif
                        @if(old('thumbnail_url', $banners->thumbnail_url) )
                            <p><img src="{{old('thumbnail_url', $banners->thumbnail_url)}}" style="width: 100%" /></p>
                        @endif
                        {!! Form::file('thumbnail') !!}
                        {!! $errors->first('thumbnail', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('content_url') ? ' has-error' : '' }}">
                        {!! Form::label('content_url', '轮播图链接') !!}
                        {!! Form::text('content_url', old('content_url', $banners->content_url), ['class' => 'form-control']) !!}
                        {!! $errors->first('content_url', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('order_id') ? ' has-error' : '' }}">
                        {!! Form::label('order_id', '序号(轮播图排序序号)') !!}
                        {!! Form::text('order_id', old('order_id', $banners->order_id), ['class' => 'form-control']) !!}
                        {!! $errors->first('order_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                </div>
                <p></p>
                <div class="box-footer">
                    <a href="javascript:window.history.back();" class="btn btn-default">取消</a>
                    <button type="submit" class="btn btn-primary pull-right">提交</button>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
