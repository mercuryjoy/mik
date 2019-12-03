@extends('admin.layouts.app')

@section('htmlheader_title')
    新建{{\App\DrawRule::$typeDisplays[$type]}}规则
@endsection

@section('contentheader_title')
    新建{{\App\DrawRule::$typeDisplays[$type]}}规则
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.drawrules.index') }}"><i class="fa fa-money"></i> 中奖设置</a></li>
    <li class="active">新建{{\App\DrawRule::$typeDisplays[$type]}}规则</li>
@endsection

@section('main-content')
    {!! Form::model(new App\DrawRule, ['route' => ['admin.drawrules.store']]) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">规则信息</h3>
                </div>
                <div class="box-body">
                    @if($type == 'area')
                        <div class="form-group{{ $errors->has('area_id') ? ' has-error' : '' }}">
                            {!! Form::label('area_id', '地区') !!}
                            {!! Form::text('area_id', old('area_id'), ['class' => 'areapicker form-control hidden']) !!}
                            {!! $errors->first('area_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                    @elseif($type == 'distributor')
                        <div class="form-group{{ $errors->has('distributor_id') ? ' has-error' : '' }}">
                            {!! Form::label('distributor_id', '经销商ID') !!}
                            {!! Form::text('distributor_id', old('distributor_id'), ['class' => 'form-control']) !!}
                            {!! $errors->first('distributor_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                    @elseif($type == 'shop')
                        <div class="form-group{{ $errors->has('shop_id') ? ' has-error' : '' }}">
                            {!! Form::label('shop_id', '终端ID') !!}
                            {!! Form::text('shop_id', old('distributor_id'), ['class' => 'form-control']) !!}
                            {!! $errors->first('shop_id', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                    @endif

                    <div class="form-group{{ $errors->has('rule_json') ? ' has-error' : '' }}">
                        {!! Form::label('rule_json', '规则') !!}
                        {!! Form::text('rule_json', old('rule_json', '[]'), ['class' => 'form-control hidden']) !!}
                        <table class="table table-striped" id="ruleTable" role="grid" style="margin-bottom: 5px;">
                            <thead>
                            <tr role="row">
                                <th>占比</th>
                                <th>下限</th>
                                <th>上限</th>
                                <th width="1.5"></th>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                        <button type="button" data-toggle="modal" data-target="#newRuleModal" class="btn btn-block btn-primary btn-sm"><i class="fa fa-plus-square"></i> 新建一条</button>
                        {!! $errors->first('rule_json', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                </div>
                <div class="box-footer">
                    {!! Form::submit('提交', ['class' => 'btn btn-primary pull-right']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

    <div class="modal fade" id="newRuleModal" tabindex="-1" role="dialog" aria-labelledby="addRuleModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">新建规则</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open() !!}

                    <div class="form-group">
                        <label for="rule_percentage">占比 ( <span class="percent-range">1 - 25</span> )</label>
                        <div class="input-group">
                            {!! Form::text('rule_percentage', '', ['class' => 'form-control']) !!}
                            <div class="input-group-addon">
                                <i class="fa fa-percent"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('rule_min', '下限') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-rmb"></i>
                            </div>
                            {!! Form::text('rule_min', '', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('rule_max', '上限') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-rmb"></i>
                            </div>
                            {!! Form::text('rule_max', '', ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary add-rule-submit">添加</button>
                </div>
            </div>
        </div>
    </div>

    <script id="ruleTemplate" type="text/x-jsrender">
        <tr>
            <td><%:percentage%>%</td>
            <td>￥<%:min%></td>
            <td>￥<%:max%></td>
            <td>
                <button type="button" class="btn btn-block btn-warning btn-xs remove-rule"><i class="fa fa-remove"></i></button>
            </td>
        </tr>
    </script>
@endsection

@section('javascript')
    $(function() {
        $.views.settings.delimiters("<%", "%>");

        var readRuleData = function() {
            return JSON.parse($('input[name="rule_json"]').val());
        };

        var writeRuleData = function(data) {
            return $('input[name="rule_json"]').val(JSON.stringify(data));
        };

        var maxPercentAvailable = function() {
            var data = readRuleData();
            var sumPercent = data.map( (rule) => rule.percentage ).reduce( (prev, curr) => prev + curr, 0 );
            return (100 - sumPercent);
        };

        var buildRuleUIFromData = function() {
            $("#ruleTable tbody tr").remove();
            var data = readRuleData();
            for (var i = 0; i < data.length; i++) {
                $("#ruleTable tbody").append($.templates("#ruleTemplate").render(data[i]));
            }
        };

        buildRuleUIFromData();

        $('#ruleTable').on('click', '.remove-rule', function() {
            var index = $(this).parents('tr').index();
            var data = readRuleData();
            data.splice(index, 1);
            writeRuleData(data);
            buildRuleUIFromData();
        });


        $('#newRuleModal').on('show.bs.modal', function (event) {
            $(".percent-range").text("1 - " + maxPercentAvailable());
        }).on('hidden.bs.modal	', function(){
            $('#newRuleModal input').val("").parents('.form-group').removeClass('has-error');
        });

        $('.add-rule-submit').on('click', function() {
            $("#newRuleModal .form-group").removeClass('has-error');
            var percent = parseInt($('input[name="rule_percentage"]').val()) || 0;
            var min = parseFloat($('input[name="rule_min"]').val()) || 0;
            var max = parseFloat($('input[name="rule_max"]').val()) || 0;
            $('input[name="rule_percentage"]').val(percent)
            $('input[name="rule_min"]').val(min)
            $('input[name="rule_max"]').val(max)

            var hasError = false;
            if (percent < 1 || percent > maxPercentAvailable()) {
                hasError = true;
                $('input[name="rule_percentage"]').parents('.form-group').addClass('has-error');
            }
            if (min > max || max == 0) {
                hasError = true;
                $('input[name="rule_min"]').parents('.form-group').addClass('has-error');
                $('input[name="rule_max"]').parents('.form-group').addClass('has-error');
            }

            if (!hasError) {
                var data = readRuleData();
                data.push({
                    percentage: percent,
                    min: min,
                    max: max
                });
                writeRuleData(data);
                $('#newRuleModal').modal('hide');
                buildRuleUIFromData();
            }

        });
    });
@endsection