@extends('admin.layouts.app')

@section('htmlheader_title')
    营销员 - {{ $salesman->name }}
@endsection

@section('contentheader_title')
    营销员 - {{ $salesman->name }}
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.salesmen.index') }}"><i class="fa fa-map-marker"></i> 营销员管理</a></li>
<li class="active">{{ $salesman->name }}</li>
@endsection

<!--<form method="POST" action="http://www.mmkk.com/admin/salesmen/3" accept-charset="UTF-8">
<input name="_method" type="hidden" value="PUT">
<input name="_token" type="hidden" value="szlH8PLgpTMFVgDZSsXxOshyU2yfchJCJTN6zmwy">
    <div class='row'>
        <div class='col-md-6'>
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">名称</label>
                        <input class="form-control" name="name" type="text" value="闪酷测试" id="name">                  
                    </div>
                    <div class="form-group">
                        <label for="phone">手机号码</label>
                        <input class="form-control" name="phone" type="text" value="15921414715" id="phone">
                        
                    </div>
                </div>
                <div class="box-footer">
                    <a href="javascript:window.history.back();" class="btn btn-default">取消</a>
                    <button type="submit" class="btn btn-primary pull-right">提交</button>
                </div>
            </div>
        </div>
    </div>
</form> -->

@section('main-content')
    {!! Form::model(new App\Salesman, ['route' => ['admin.salesmen.update', $salesman->id], 'method' => 'PUT']) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">修改营销员信息</h3>
                </div>
                <div class="box-body">
                      <!--<div class="form-group">
                              <label for="name">名称</label>
                              <input  name="name"  value="闪酷测试" id="name">                  
                          </div> -->
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name', $salesman->name), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        {!! Form::label('phone', '手机号码') !!}
                        {!! Form::text('phone', old('name', $salesman->phone), ['class' => 'form-control']) !!}
                        {!! $errors->first('phone', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
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


