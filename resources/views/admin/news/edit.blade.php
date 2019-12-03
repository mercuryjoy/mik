@extends('admin.layouts.app')

@section('htmlheader_title')
    修改通知
@endsection

@section('contentheader_title')
    修改通知
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.news.index') }}"><i class="fa fa-newspaper-o"></i> 通知管理</a></li>
    <li class="active">修改通知</li>
@endsection

@section('main-content')
    {!! Form::model(new App\News, ['route' => ['admin.news.update', $news->id], 'method' => 'PUT', 'files' => true]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">修改通知</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        {!! Form::label('title', '标题') !!}
                        {!! Form::text('title', old('title', $news->title), ['class' => 'form-control']) !!}
                        {!! $errors->first('title', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                        {!! Form::label('picture', '题图 (80 x 80)') !!}
                        @if(old('picture_url'))
                            {!! Form::hidden('picture_url', old('picture_url')) !!}
                        @endif
                        @if(old('picture_url', $news->picture_url) )
                            <p><img src="{{old('picture_url', $news->picture_url)}}"/ width="100px"></p>
                        @endif
                        {!! Form::file('picture') !!}
                        {!! $errors->first('picture', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('thumbnail') ? ' has-error' : '' }}">
                        {!! Form::label('thumbnail', '题图 (400 x 240)') !!}
                        @if(old('thumbnail_url'))
                            {!! Form::hidden('thumbnail_url', old('thumbnail_url')) !!}
                        @endif
                        @if(old('thumbnail_url', $news->thumbnail_url) )
                            <p><img src="{{old('thumbnail_url', $news->thumbnail_url)}}"/></p>
                        @endif
                        {!! Form::file('thumbnail') !!}
                        {!! $errors->first('thumbnail', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
<!--                     <div class="form-group{{ $errors->has('audio_url') ? ' has-error' : '' }}">
                    {!! Form::label('audio', '视频') !!}
                        @if(old('audio_url'))
                            {!! Form::hidden('audio_url', old('audio_url')) !!}
                        @endif
                        @if(old('audio_url', $news->audio_url) )
                        <video src="{{old('audio_url', $news->audio_url)}}" controls="controls">
                        您的浏览器不支持 video 标签。
                        </video>
                        @endif
                        {!! Form::label('audio', '视频文件') !!}
                        {!! Form::file('audio') !!}
                        {!! $errors->first('audio', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!} -->
<!-- <video style="height:auto;" src="G:\Video_2019-07-12_165942.wmv" id="video0" controls="controls"></video>
<input class="form-control" type="file" style="height:auto;"
id="video" name="video"/> -->
                    </div>
                    <div class="form-group{{ $errors->has('content_url') ? ' has-error' : '' }}">
                        {!! Form::label('content_url', '通知链接(非必填)') !!}
                        {!! Form::text('content_url', old('content_url', $news->content_url), ['class' => 'form-control']) !!}
                        {!! $errors->first('content_url', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                    <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                        {!! Form::label('content', '通知内容') !!}
                        {!! Form::textarea('content', old('content', $news->content), ['class' => 'form-control']) !!}
                        {!! $errors->first('content', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', '草稿状态') !!}
                        {!! Form::select('status', ["normal"=> "关闭","caogao" => "开启"], old('level', $news->status), ['class' => 'form-control select2']) !!}
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