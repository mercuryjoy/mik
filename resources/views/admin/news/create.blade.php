@extends('admin.layouts.app')
@section('htmlheader_title')
    新建通知
@endsection

@section('contentheader_title')
    新建通知
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.news.index') }}"><i class="fa fa-newspaper-o"></i> 通知管理</a></li>
    <li class="active">新建通知</li>
@endsection

@section('main-content')
    {!! Form::model(new App\News, ['route' => 'admin.news.store', 'files' => true]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">新建通知</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        {!! Form::label('title', '标题') !!}
                        {!! Form::text('title', old('title'), ['class' => 'form-control']) !!}
                        {!! $errors->first('title', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                        {!! Form::label('picture', '题图 (80 x 80)') !!}
                        @if(old('picture_url'))
                            {!! Form::hidden('picture_url', old('picture_url')) !!}
                            <p><img src="{{old('picture_url')}}"/></p>
                        @endif
                        {!! Form::file('picture') !!}
                        {!! $errors->first('picture', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('thumbnail') ? ' has-error' : '' }}">
                        {!! Form::label('thumbnail', '通知内容图 (400 x 240)') !!}
                        @if(old('thumbnail_url'))
                            {!! Form::hidden('thumbnail_url', old('thumbnail_url')) !!}
                            <p><img src="{{old('thumbnail_url')}}"/></p>
                        @endif
                        {!! Form::file('thumbnail') !!}
                        {!! $errors->first('thumbnail', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                    {!! Form::label('content', '通知内容') !!}
                        <div class="form-group">
                            <textarea class="form-control" rows="3" placeholder="" name="content"  style="background-color: white" ></textarea>
                  <!--       {!! Form::text('content', old('content'), ['class' => 'form-control']) !!} -->
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('content_url') ? ' has-error' : '' }}">
                        {!! Form::label('content_url', '通知链接(非必填)') !!}
                        {!! Form::text('content_url', old('content_url'), ['class' => 'form-control']) !!}
                        {!! $errors->first('content_url', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('status', '草稿状态') !!}
                        {!! Form::select('status', ["normal"=> "关闭","caogao" => "开启"], old('status'), ['class' => 'form-control select2']) !!}
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
