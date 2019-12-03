<?php

/**
 * @brief: 请求接口返回数据
 * @Author: WT
 * @Date: 2017-12-19 11:54
 * @param string $url 接口地址
 * @param string $requestType 请求类型[get, post]
 * @return array
 */
if (!function_exists('get_api_data')) {
    function get_api_data($url, $request_type = 'get', $data_type = 'json') {
        $client = new \GuzzleHttp\Client();
        $res = $client->request($request_type, $url);
        $body = $res->getBody();

        $data = null;
        if ($data_type == 'xml') {
            $body = simplexml_load_string($body);
            $data = json_decode($body,TRUE);
        } elseif ($data_type == 'json') {
            $data = json_decode($body,TRUE);
        }
        return $data;
    }
}

/**
 * @brief: 支付宝加密
 * @Author: WT
 * @Date: 2018-01-3 20:45
 * @return array
 */
if (!function_exists('get_alipay_param')) {
    function get_alipay_param($subject, $body, $out_trade_no, $total_amount) {
        $alipay = new \App\Helpers\Alipay();
        $alipay_param = $alipay->getAlipayParam($subject, $body, $out_trade_no, $total_amount);
        return $alipay_param;
    }
}

/**
 * @brief: net订单通知
 * @Author: WT
 * @Date: 2018-01-3 20:45
 * @return array
 */
if (!function_exists('order_notification')) {
    function order_notification($paramArr) {
        $param = json_encode($paramArr);
        $url = config('custom.db_url').config('custom.order_notification_api_url').'?data='.$param;
        $data = get_api_data($url, 'get', 'xml');
        return $data;
    }
}

/**
 * 数组分页函数  核心函数  array_slice
 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
 * $count   每页多少条数据
 * $page    当前第几页
 * $array   查询出来的所有数组
 * order 0 - 不变     1- 反序
 */

function page_array($count, $page, $array, $order = ''){
    global $count_page;
    $page = (empty($page)) ? '1' : $page;
    $start = ($page - 1) * $count;
    if($order == 'desc'){
        $array = array_reverse($array);
    } elseif ($order == 'asc') {

    }
    $totals = count($array);
    $count_page = ceil($totals / $count);
    return array_slice($array, $start, $count);
}

/**
 * 格式化活动积分和金额数据
 * @param json $rule_json json活动数据
 */

if (! function_exists('get_rule_json')) {
    function get_rule_json($rule_json)
    {
        $rule_json = json_decode($rule_json, true);
        $key = array_keys($rule_json[0])[0];
        return $rule_json[0][$key];
    }
}

/**
 * 获取指定日期段内每一天的日期
 * @param Date $startdate 开始日期
 * @param Date $enddate  结束日期
 * @return Array
 */
if (! function_exists('get_date_from_range')) {
    function get_date_from_range($start_date, $end_date) {
        $stimestamp = strtotime($start_date);
        $etimestamp = strtotime($end_date);
        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400 + 1;
        $date = [];
        for($i=0; $i<$days; $i++){
            $date[] = date('Y-m-d', $stimestamp+(86400*$i));
        }
        return $date;
    }
}