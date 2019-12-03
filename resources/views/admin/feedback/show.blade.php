@extends('admin.layouts.app')

@section('htmlheader_title')
    经销商 - {{ $feedback['name'] }}
@endsection

@section('contentheader_title')
经销商 - {{ $feedback['name'] }}
@endsection

@section('breadcrumb')
<li><a href="{{ url('admin/replies') }}"><i class="fa fa-sitemap"></i> 经销商管理</a></li>
<li class="active">{{ $feedback['name'] }}</li>
@endsection

@section('main-content')

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">查看详情</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('id') ? ' has-error' : '' }}">
                        <label class="control-label">id:</label>
                        <label class="control-label">
                            {{ $feedback->id }}
                        </label>
                    </div>

                    <div class="form-group {{ $errors->has('id') ? ' has-error' : '' }}">
                      
                        <label class="control-label">用户:</label>
                        <label class="control-label">
                            @if ($feedback->user != null)
                            {{ $feedback->user->name }}
                            @else
                            无
                            @endif
                         </label>
                    </div>
                    <div class="form-group">
                        <label class="control-label">联系电话:</label>
                        <label class="control-label">
                            @if ($feedback->user != null)
                            {{ $feedback->user->telephone }}
                            @else
                            无
                            @endif
                         </label>
                    </div>

                    <div class="form-group">
                        <label class="control-label">创建时间:</label>
                        <label class="control-label">
                            {{ $feedback->created_at }}
                         </label>
                    </div>
                    <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                        <div class="form-group">
                            <textarea class="form-control" rows="3" placeholder="{!! $feedback->content !!}" name="content" readonly="" style="background-color: white" ></textarea>
                        </div>
                    </div>

                    <hr>
        
                    {!! Form::model(new App\Reply, ['route' => ['admin.replies.store'], 'method' => 'POST']) !!}
                    <input type="hidden" name="feedback_id" value="{{ $feedback->id }}">
                    <input type="hidden" name="user_id" value="{{ $feedback->user_id }}">
                    <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                        {!! Form::label('content', '回复') !!}
                        {!! Form::textarea('content', old('content'), ['class' => 'form-control']) !!}
                        {!! $errors->first('content', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="box-footer">    
                        <a href="javascript:window.history.back();" class="btn btn-default">退出</a>
                        <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-share mr-1"></i> 回复</button>
                    </div>
                    {!! Form::close() !!}
                </div>


            </div>
        </div>
    </div>


@endsection
