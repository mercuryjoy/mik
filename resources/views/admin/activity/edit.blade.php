@extends('admin.layouts.app')

@section('htmlheader_title')
    编辑{{ $activity->actionZoneDisplay }}活动 - {{\App\Activity::$typeDisplays[$activity->type]}}活动
@endsection

@section('contentheader_title')
    编辑{{ $activity->actionZoneDisplay }}活动 - {{\App\Activity::$typeDisplays[$activity->type]}}活动
@endsection

@section('breadcrumb')
    <li><a href="{{ route('admin.activities.index') }}"><i class="fa fa-money"></i> 活动设置</a></li>
    <li class="active">编辑{{ $activity->actionZoneDisplay }}活动 - {{\App\Activity::$typeDisplays[$activity->type]}}活动</li>
@endsection

@section('main-content')
    {!! Form::model(new App\Activity, ['route' => ['admin.activities.update', $activity->id], 'method' => 'PUT']) !!}

    <div class='row'>
        <div class='col-md-6'>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">活动信息</h3>
                </div>
                <div class="box-body">

                    {!! Form::text('type', old('type', $activity->type), ['class' => 'hidden']) !!}
                    {!! Form::text('action_zone', old('action_zone', $activity->action_zone), ['class' => 'hidden']) !!}

                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        {!! Form::label('title', '标题') !!}
                        {!! Form::text('title', old('title', $activity->title), ['class' => 'form-control']) !!}
                        {!! $errors->first('title', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                    </div>

                    @if($activity->action_zone == 'part')
                        <div class="form-group{{ $errors->has('shop_ids') ? ' has-error' : '' }}">
                            {!! Form::label('shop_ids', '适用终端') !!}
                            <button type="button" data-toggle="modal" data-target="#shopsModal" class="btn btn-block btn-primary btn-sm"><i class="fa fa-plus-square"></i> 选择终端</button>
                            {!! Form::text('shop_ids', old('shop_ids', $activity->shop_ids), ['class' => 'form-control hidden', 'id' => 'shop_ids']) !!}
                            {!! Form::textarea('shop_names', old('shops_names',  $activity->shop_names), ['class' => 'form-control', 'id' => 'shop_names', 'readonly' => 'readonly', 'placeholder' => '请选择终端']) !!}
                            {!! $errors->first('shop_ids', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                    @endif

                    @if($activity->type == 'point')
                        <div class="form-group{{ $errors->has('point') ? ' has-error' : '' }}">
                            {!! Form::label('point', '积分') !!}
                            {!! Form::number('point', old('point', $activity->point), ['class' => 'form-control', 'min' => 0, 'max' => 100, 'step' => 1]) !!}
                            {!! $errors->first('point', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                    @elseif($activity->type == 'shop_owner')
                        <div class="form-group{{ $errors->has('money') ? ' has-error' : '' }}">
                            {!! Form::label('money', '店长发红包金额') !!}
                            {!! Form::number('money', old('money', $activity->money), ['class' => 'form-control', 'min' => 0, 'max' => 100, 'step' => 0.01]) !!}
                            {!! $errors->first('money', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                    @elseif ($activity->type == 'red_envelope')
                        <div class="form-group{{ $errors->has('rule_json') ? ' has-error' : '' }}">
                            {!! Form::label('rule_json', '活动占比') !!}
                            {!! Form::text('rule_json', old('rule_json', $activity->rule_json), ['class' => 'form-control hidden']) !!}
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
                            <button type="button" data-toggle="modal" data-target="#newRuleModal" class="btn btn-block btn-primary btn-sm"><i class="fa fa-plus-square"></i> 编辑一条</button>
                            {!! $errors->first('rule_json', '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>   :message</label>') !!}
                        </div>
                    @endif

                    @if($activity->action_zone == 'part')

                        <div class="form-group">
                            {!! Form::label('daterange', '时间段') !!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                {!! Form::text('daterange', old('daterange', $activity->daterange), ['class' => 'daterange form-control']) !!}
                            </div>
                        </div>
                    @endif

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
                    <h4 class="modal-title">编辑活动</h4>
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

    <div class="modal modal-danger fade" id="overPercent" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    温馨提示
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    占比已经达到100%了!
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline btn-ok" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shopsModal" tabindex="-1" role="dialog" aria-labelledby="selectShopModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    选择终端
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">终端列表</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="shopTable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" class="checkAll" /></th>
                                    <th>ID</th>
                                    <th>终端名称</th>
                                    <th>地址</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($shops as $shop)
                                    <tr>
                                        <td><input type="checkbox" class="checkChild" value="{{  $shop['id']  }}" /></td>
                                        <td>{{ $shop['id'] }}</td>
                                        <td>{{ $shop['name'] }}</td>
                                        <td>{{ $shop['area']['name'] or '' }} {{ $shop['address'] or '' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
                    <button type="button" id="selectShopBtn" class="btn btn-primary btn-ok" data-dismiss="modal">确认</button>

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
    $(function () {
        $.views.settings.delimiters("<%", "%>");

        var readRuleData = function () {
            return JSON.parse($('input[name="rule_json"]').val());
        };

        var writeRuleData = function (data) {
            return $('input[name="rule_json"]').val(JSON.stringify(data));
        };

        var maxPercentAvailable = function () {
            var data = readRuleData();
            var sumPercent = data.map( (rule) => rule.percentage ).reduce( (prev, curr) => prev + curr, 0 );
            return (100 - sumPercent);
        };

        var buildRuleUIFromData = function () {
            $("#ruleTable tbody tr").remove();
            var data = readRuleData();
            for (var i = 0; i < data.length; i++) {
                $("#ruleTable tbody").append($.templates("#ruleTemplate").render(data[i]));
            }
        };

        buildRuleUIFromData();

        $('#ruleTable').on('click', '.remove-rule', function () {
            var index = $(this).parents('tr').index();
            var data = readRuleData();
            data.splice(index, 1);
            writeRuleData(data);
            buildRuleUIFromData();
        });


        $('#newRuleModal').on('show.bs.modal', function (event) {
            $(".percent-range").text("1 - " + maxPercentAvailable());
            if (maxPercentAvailable() == 0) {
                $('#overPercent').modal('show');
                return false;
            }
        }).on('hidden.bs.modal	', function () {
            $('#newRuleModal input').val("").parents('.form-group').removeClass('has-error');
        });

        $('.add-rule-submit').on('click', function () {
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

    // 初始化选中
    var shop_ids = '{{ $activity->shop_ids }}';
    var select_shop_ids = shop_ids.split(',');
    $.each(select_shop_ids,function (index, item) {
        var checkboxs = $("input[type='checkbox'][class='checkChild'][value="+item+"]");
        $.each(checkboxs,function (){
            this.checked="checked";
        });
    });

    $(".checkAll").click(function () {
        var check = $(this).prop("checked");
        $(".checkChild").prop("checked", check);
    });

    $("#selectShopBtn").click(function () {
        var tbl = $("#shopTable").dataTable();
        var trList = tbl.fnGetNodes();
        var shopIdArr = new Array();
        var shopNameArr = new Array();
        for(i=0; i < trList.length; i++){
            var trObj = trList[i];
            var boolean = trObj.firstElementChild.firstElementChild.checked
            if (boolean === true) {
                var id = trList[i].firstElementChild.nextElementSibling.innerHTML.trim();
                var name = trList[i].childNodes[5].innerHTML.trim();
                shopIdArr.push(parseInt(id));
                shopNameArr.push(name);
            }
        }

        var shopIdAll = '';
        var shopNameAll = '';
        if (shopIdArr.length > 0) {
            shopIdAll = shopIdArr.join(',');
            shopNameAll = shopNameArr.join('，');
        }

        $('#shop_ids').val(shopIdAll);
        $('#shop_names').val(shopNameAll);
    });

    //Initialize Select2 Elements
    $("input.areapicker").areapicker();

    $("input.select2, select.select2").select2({
        language: "zh-CN"
    });

    $("input.daterange").daterangepicker({
        language : 'zh-CN',
        format : 'YYYY-MM-DD HH:mm:ss',
        endDate : moment().format('YYYY-MM-DD HH:mm:ss'),
        maxDate : moment().format('YYYY-MM-DD HH:mm:ss'),
        locale: {
            customRangeLabel : '自定义',
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月' ]
        },
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: false,
        startDate: "{{ $activity->start_at }}",
        endDate: "{{ $activity->end_at }}",
        minDate: '1977-01-01 00:00:00',
        maxDate: '2999-12-31 23:59:59',
    });


    $('#shopTable').DataTable({
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        }],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        order: [[ 1, 'asc' ]],
        'bProcessing' : true,
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : true,
        'language'    : {
            'sSearch': '搜索终端名称',
            'sInfo': '显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项',
            'sLengthMenu': '显示 _MENU_ 条',
            'sInfoFiltered': '(由 _MAX_ 项结果过滤)',
            'sEmptyTable': '表中数据为空',
            'sInfoEmpty': '没有数据',
            'sZeroRecords': '没有相关数据',
            'sProcessing': '加载中...',
            'oPaginate': {
                'sFirst': '首页',
                'sLast': '末页',
                'sPrevious': '上一页',
                'sNext': '下一页',
            }
        }
    })
@endsection