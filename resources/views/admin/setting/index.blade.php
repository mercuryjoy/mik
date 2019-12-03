@extends('admin.layouts.app')

@section('htmlheader_title')
    系统设置
@endsection

@section('contentheader_title')
    系统设置
@endsection

@section('main-content')
    {!! Form::open(['route' => ['admin.settings.update'], 'method' => 'put', 'class' => 'form-horizontal']) !!}

    @foreach($settingsGroup as $settings)
        <fieldset style="margin-bottom: 20px;">
            @if(isset($settings['title']) && $settings['title'] != "")
                <legend style="margin-bottom: 5px;">{{ $settings['title'] }}</legend>
            @endif

            @foreach($settings['items'] as $settingConfig)
                <div class="row">
                    {!! Form::label($settingConfig[0], $settingConfig[2], ['class' => 'control-label col-sm-2']) !!}
                    <div class="col-sm-10">
                        @if(isset($settingConfig[6]))
                            {!! Form::select("settings[$settingConfig[0]]", $settingConfig[3], $settingConfig[1], ['class' => 'form-control select2']) !!}
                        @else
                            @if(isset($settingConfig[3]) && $settingConfig[3] == 'number')
                                {!! Form::number("settings[$settingConfig[0]]", $settingConfig[1], ['class' => 'form-control']) !!}
                            @else
                                {!! Form::text("settings[$settingConfig[0]]", $settingConfig[1], ['class' => 'form-control']) !!}
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </fieldset>
    @endforeach

    <p>
    <div class="form-actions col-sm-offset-2 col-sm-10">
        {!! Form::submit('保存设置', ['class' => 'btn btn-success']) !!}
    </div>
    </p>
    {!! Form::close() !!}
@endsection
