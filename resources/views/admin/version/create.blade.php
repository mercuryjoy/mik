@extends('admin.layouts.app')

@section('htmlheader_title')
    新建APP版本
@endsection

@section('contentheader_title')
    新建APP版本
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/versions') }}"><i class="fa fa-sitemap"></i> APP版本管理</a></li>
    <li class="active">新建APP版本</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Category(), ['route' => 'admin.versions.store', 'files' => true]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">APP版本信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        {!! Form::label('type', '平台') !!}
                        {!! Form::select('type', ["ios" => "Ios", "android"=> "Android"], old('type'), ['class' => 'form-control select2', 'placeholder' => '请选择平台']) !!}
                        {!! $errors->first('type', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('android_file') ? ' has-error' : '' }}" id="android_file_div" style="display: @if (old('type') == 'android') display @else none @endif">
                        {!! Form::label('android_file', 'Android文件（apk格式）') !!}
                        {!! Form::file('android_file') !!}
                        {!! $errors->first('android_file', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('download_url') ? ' has-error' : '' }}" id="download_url_div" style="display: @if (old('type') == 'ios') display @else none @endif">
                        {!! Form::label('download_url', '下载地址') !!}
                        {!! Form::text('download_url', old('download_url'), ['class' => 'form-control', 'placeholder' => '请输入下载地址']) !!}
                        {!! $errors->first('download_url', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('version') ? ' has-error' : '' }}">
                        {!! Form::label('version', '版本号') !!}
                        {!! Form::text('version', old('version'), ['class' => 'form-control', 'placeholder' => '请输入版本号']) !!}
                        {!! $errors->first('version', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        {!! Form::label('description', '版本描述') !!}
                        {!! Form::textarea('description', old('description'), ['class' => 'form-control', 'placeholder' => '请输入版本描述']) !!}
                        {!! $errors->first('description', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('is_force_update') ? ' has-error' : '' }}">
                        {!! Form::label('is_force_update', '强制升级') !!}
                        {!! Form::select('is_force_update', ['no' => '否', 'yes' => '是'], old('is_force_update'), ['class' => 'form-control select2']) !!}
                        {!! $errors->first('is_force_update', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                </div>
                <div class="box-footer">
                    {!! Form::submit('提交', ['class' => 'btn btn-primary pull-right']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection

@section('javascript')
    $('#type').on('change', function() {
        var type = $('#type').val();
        if (type == 'android') {
            $("#android_file_div").css("display", "block")
            $("#download_url_div").css("display", "none")
        } else if (type == 'ios') {
            $("#download_url_div").css("display", "block")
            $("#android_file_div").css("display", "none")
        } else {
            $("#android_file_div").css("display", "none")
            $("#download_url_div").css("display", "none")
        }
    });
@endsection
