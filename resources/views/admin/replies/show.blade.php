@extends('admin.layouts.app')

@section('htmlheader_title')
    经销商 - 
@endsection

@section('contentheader_title')
经销商 - 
@endsection

@section('breadcrumb')
<li><a href="{{ url('admin/replies') }}"><i class="fa fa-sitemap"></i> 经销商管理</a></li>
<li class="active"></li>
@endsection

@section('main-content')

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">查看详情</h3>
                </div>
                <div class="box-body">
                    <div class="form-group {{ $errors->has('id') ? ' has-error' : '' }}">
                      
                        <label class="control-label">用户:</label>
                        <label class="control-label">
                            @if ($feedback['0']->name != null)
                            {{ $feedback['0']->name }}
                            @else
                            无
                            @endif
                         </label>
                    </div>
                    <div class="form-group">
                        <label class="control-label">创建时间:</label>
                        <label class="control-label">
                            @if ($feedback['0']->created_at != null)
                            {{ $feedback['0']->created_at }}
                            @else
                            无
                            @endif
                         </label>
                    </div>
                    <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                        <div class="form-group">
                            <textarea class="form-control" rows="3" placeholder="{!! $feedback['0']->content !!}" name="content" readonly="" style="background-color: white" ></textarea>
                        </div>
                    </div>                 
                    <hr>
                    {!! Form::model(new App\Reply, ['route' => ['admin.replies.store'], 'method' => 'POST']) !!}

                        <input type="hidden" name="feedback_id" value="{{ $feedback['0']->id }}">
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
