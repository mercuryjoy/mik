@extends('admin.layouts.app')

@section('htmlheader_title')
    经销商 - {{ $feedback['name'] }}
@endsection

@section('contentheader_title')
经销商 - {{ $feedback['name'] }}
@endsection

@section('breadcrumb')
<li><a href="{{ url('admin/feedbacks') }}"><i class="fa fa-sitemap"></i> 经销商管理</a></li>
<li class="active">{{ $feedback['name'] }}</li>
@endsection

@section('main-content')
    {!! Form::model(new App\feedback, ['route' => ['admin.feedbacks.update', $feedback->id], 'method' => 'PUT']) !!}

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
                        <label class="control-label">商户名称:</label>
                        <label class="control-label">
                        @if ($feedbacks != null)
                        {{ $feedbacks->sname }}
                        @else
                        无
                        @endif
                        </label>
                    </div>  
                    <div class="form-group">
                        <label class="control-label">销售员:</label>
                        <label class="control-label">
                        @if ($feedbacks != null)
                        {{ $feedbacks->smname }}
                        @else
                        无
                        @endif
                        </label>
                    </div> 
                    <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                        <div class="form-group">
                        <label class="control-label">评论:</label>
                            <textarea class="form-control" rows="3" placeholder="{!! $feedback->content !!}" name="content" readonly="" ></textarea>
                        </div>
                    </div>                  
                    <div class="form-group">
                        <label class="control-label">创建时间:</label>
                        <label class="control-label">
                            {{ $feedback->created_at }}
                         </label>
                    </div>
                    <hr>                    
                    @foreach ($reply as $reply)
                    <div class="form-group">
                        <label class="control-label">回复内容:</label>
                            @if ($reply->content != null)
                            <textarea class="form-control" rows="2" placeholder="{!! $reply->content !!}" name="content" readonly="" style="background-color: white" ></textarea>
                            <label class="control-label">{{ $reply->created_at }}</label>
                            @else
                            无
                            @endif
                    </div> 

                    @endforeach                   
                    
                </div>
    
                <div class="box-footer">
                    <a href="javascript:window.history.back();" class="btn btn-default">退出</a>
                     <button type="submit" class="btn btn-primary pull-right">提交</button>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection

