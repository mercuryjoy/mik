@extends('admin.layouts.app')

@section('htmlheader_title')
    终端 - {{ $shop['name'] }}
@endsection

@section('contentheader_title')
    终端 - {{ $shop['name'] }}
@endsection

@section('breadcrumb')
    <li><a href="{{ url('admin/shops') }}"><i class="fa fa-map-marker"></i> 终端管理</a></li>
    <li class="active">{{ $shop['name'] }}</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Shop, ['route' => ['admin.shops.update', $shop->id], 'method' => 'post','files' => true, 'id' => 'shopEditForm' ]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">修改终端信息</h3>
                </div>
                <div class="box-body">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', '名称') !!}
                        {!! Form::text('name', old('name', $shop->name), ['class' => 'form-control']) !!}
                        {!! $errors->first('name', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('level') ? ' has-error' : '' }}">
                        {!! Form::label('level', '级别') !!}
                        {!! Form::select('level', ["A" => "A级", "B" => "B级", "C" => "C级", "D" => "D级"], old('level', $shop->level), ['class' => 'form-control select2']) !!}
                        {!! $errors->first('level', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('distributor_id') ? ' has-error' : '' }}">
                        {!! Form::label('distributor_id', '经销商ID') !!}
                        {!! Form::text('distributor_id', old('distributor_id', ($shop->distributor_id > 0) ? $shop->distributor_id : ""), ['class' => 'form-control']) !!}
                        {!! $errors->first('distributor_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('area_id') ? ' has-error' : '' }}">
                        {!! Form::label('area_id', '地区') !!}
                        {!! Form::number('area_id', old('area_id', $shop->area_id), ['class' => 'areapicker form-control hidden']) !!}
                        {!! $errors->first('area_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        {!! Form::label('address', '地址') !!}
                        {!! Form::text('address', old('address', $shop->address), ['class' => 'form-control']) !!}
                        {!! $errors->first('address', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                        {!! Form::label('category_id', '餐饮类型') !!}
                        {!! Form::select('category_id', $categories, old('category_id', $shop->category_id), ['class' => 'form-control select2', 'placeholder' => '请选择餐饮类型']) !!}
                        {!! $errors->first('category_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('salesman_id') ? ' has-error' : '' }}">
                        {!! Form::label('salesman_id', '营销员ID') !!}
                        {!! Form::text('salesman_id', old('salesman_id', ($shop->salesman_id > 0) ? $shop->salesman_id : ""), ['class' => 'form-control']) !!}
                        {!! $errors->first('salesman_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('per_consume') ? ' has-error' : '' }}">
                        {!! Form::label('per_consume', '人均消费') !!}
                        {!! Form::text('per_consume', old('per_consume', $shop->per_consume), ['class' => 'form-control']) !!}
                        {!! $errors->first('per_consume', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                        {!! Form::label('contact_person', '联系人') !!}
                        {!! Form::text('contact_person', old('contact_person', $shop->contact_person), ['class' => 'form-control']) !!}
                        {!! $errors->first('contact_person', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('contact_phone') ? ' has-error' : '' }}">
                        {!! Form::label('contact_phone', '联系号码') !!}
                        {!! Form::text('contact_phone', old('name', $shop->contact_phone), ['class' => 'form-control']) !!}
                        {!! $errors->first('contact_phone', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group{{ $errors->has('owner_id') ? ' has-error' : '' }}">
                        {!! Form::label('owner_id', '店长') !!}
                        {!! Form::select('owner_id', $waiters, old('owner_id', $shop->owner_id), ['class' => 'form-control select2', 'placeholder' => '请选择店长']) !!}
                        {!! $errors->first('owner_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('logo_url', 'LOGO (400 x 400)') !!}
                        @if(old('logo_url', $shop->logo) )
                            <p><img src="{{old('logo_url', $shop->logo)}}"/></p>
                        @endif
                        {!! Form::file('logo_url') !!}
                        {!! Form::hidden('logo', old('logo')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('created_at', '创建时间') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            {!! Form::text('', $shop->created_at, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
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

@section('javascript')
    {{--<script>--}}
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        var uploading = false;

        $("#logo_url").on("change", function(){
            if(uploading){
                alert("文件正在上传中，请稍候");
                return false;
            }
            {{--var formData = new FormData($('#shopEditForm')[0]);--}}
            {{--var logo_url = formData.get('logo_url');--}}
            {{--var json = '{"logo_url":' + logo_url + '}';--}}

            $.ajax({
                url: "/admin/shops/upload",
                type: 'POST',
                cache: false,
                data: new FormData($('#shopEditForm')[0]),
                processData: false,
                contentType: false,
                dataType:"json",
                beforeSend: function(){
                    uploading = true;
                },
                success : function(data) {
                    if (data.code == 200) {
                        $("input[name='logo']").val(data.data.logo);
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                    uploading = false;
                }
            });
        });
    });
@endsection