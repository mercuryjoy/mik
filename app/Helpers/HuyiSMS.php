<?php

namespace App\Helpers;

use App\Helpers\Contracts\SMSContract;
use App\SMSLog;
use GuzzleHttp\Client;

class HuyiSMS implements SMSContract {


    public function sendTestMessage($phone) {
        $this->sendMessage($phone, '您的效验码为TEST，请妥善保管，使用后自动失效。', 'test');
    }

    public function sendVerifyCode($phone, $code, $type = 'verify_register') {
        //'verify_register':验证码登录，'verify_register_password': 用户名密码登录，'wechat_bind'：微信绑定,'wechat_unbind'：微信解绑,
        //'withdraw'：提现,'update_login_password'：更新登录密码,'update_withdraw_password'：更新提现密码
        $message = '您的效验码为' . $code .'，请妥善保管，使用后自动失效。';
        if ($type == 'verify_register') {
            $message = '您的效验码为' . $code .'，请妥善保管，使用后自动失效。';
        } elseif ($type == 'verify_register_password') {
            $message = '您的注册效验码为' . $code .'，请妥善保管，使用后自动失效。';
        } elseif ($type == 'verify_reset_password') {
            $message = '您的密码重置效验码为' . $code .'，请妥善保管，使用后自动失效。';
        } elseif ($type == 'wechat_bind') {
            $message = '您的微信绑定效验码为' . $code .'，请妥善保管，使用后自动失效。';
        } elseif ($type == 'wechat_unbind') {
            $message = '您的微信解绑效验码为' . $code .'，请妥善保管，使用后自动失效。';
        } elseif ($type == 'withdraw') {
            $message = '您的提现效验码为' . $code .'，请妥善保管，使用后自动失效。';
        } elseif ($type == 'update_login_password') {
            $message = '您的修改登录密码效验码为' . $code .'，请妥善保管，使用后自动失效。';
        } elseif ($type == 'update_withdraw_password') {
            $message = '您的修改提现密码效验码为' . $code .'，请妥善保管，使用后自动失效。';
        }

        $this->sendMessage($phone, $message, $type, $code);
    }

    public function sendVerifyRegisterPasswordCode($phone, $code) {
        $this->sendMessage($phone, '您的效验码为' . $code .'，请妥善保管，使用后自动失效。', 'verify_register_password', $code);
    }

    public function sendPassAuditMessage($phone) {
        $this->sendMessage($phone, '尊敬的用户您好，您的米客之家账户审核已经通过，谢谢您对我们的支持。', 'pass_audit');
    }

    public function sendMoneyCostNotificationMessage($phone, $money) {
        $this->sendNotificationMessage($phone, "[警示]今日奖金支出已超过警示额度，达到" . $money / 100.0 . "。", "admin_notify_daily_cost");
    }

    public function sendUserScanCountNotificationMessage($phone, $username, $count) {
        $this->sendNotificationMessage($phone, "[警示] 用户" . $username . "今日扫码数量已超过警示上限，达到" . $count . "。", "admin_notify_scan_count");
    }

    public function sendFundingPoolNotificationMessage($phone, $money) {
        $this->sendNotificationMessage($phone, "[警示] 资金池余额低于警示值，达到" . $money / 100.0 . "。", "admin_notify_funding_pool");
    }

    private function sendNotificationMessage($phone, $message, $type) {
        $this->sendMessage($phone, $message, $type);
    }

    private function sendMessage($phone, $message, $type, $code = null) {
        $url = config('sms.xingqi_url');
        $content = config('sms.xingqi_prefix').$message;
        $post_data = [
            'cust_code' => config('sms.xingqi_cust_code'),
            'destMobiles' => $phone,
            'content' => $content,
            'sign' => md5(urlencode($content.config('sms.xingqi_password'))),
            'sp_code' => config('sms.xingqi_sp_code'),
        ];

        $o = "";
        foreach ($post_data as $k=>$v)
        {
            if($k =='content') {
                $o.= "$k=".urlencode($v)."&";
            } else {
                $o.= "$k=".($v)."&";
            }
        }

        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        //为了支持cookie
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);

        $resMessage = '';
        $status = 1;
        if(strpos($result,'SUCCESS:') !== false){
            // 获取状态
            $sumLen = mb_strlen($result);
            $lastStrLen = mb_strlen(mb_substr($result, $sumLen-41, $sumLen, 'utf8' ));
            $endLen = $sumLen - $lastStrLen - 5;
            $resMessage = urldecode(mb_substr($result, 8, $endLen, 'utf8' ));
            $status = mb_substr($result, $sumLen-3, 1, 'utf8' );
        } elseif (strpos($result,'ERROR:') !== false) {
            $resMessage = urldecode(mb_substr($result, 6, mb_strlen($result), 'utf8' ));
        }

        SMSLog::create([
            'telephone' => $phone,
            'content' =>  $message,
            'type' => $type,
            'status' => ($status == 0) ? 'sent' : 'error',
            'code' => $code,
            'comment' => $resMessage
        ]);
    }

    private function sendMessage_bak($phone, $message, $type, $code = null) {
        $client = new Client();
        $response = $client->post("http://106.ihuyi.cn/webservice/sms.php?method=Submit", [
            'form_params' => [
                'account' => app('config')->get('sms.huyi_account'),
                'password' => app('config')->get('sms.huyi_password'),
                'mobile' => $phone,
                'content' => $message
            ],
            'Content-type' => 'text/html; charset=UTF-8'
        ]);
        $text = $response->getBody()->getContents();
        list($resCode, $resMessage) = $this->parseXMLResult($text);

        SMSLog::create([
            'telephone' => $phone, 'content' =>  $message,
            'type' => $type, 'status' => ($resCode == 2) ? 'sent' : 'error', 'code' => $code, 'comment' => $resMessage
        ]);
    }

    private function parseXMLResult($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
        foreach($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if(strlen($strPrefix)==0) {
                $strPrefix="res"; //Assign an arbitrary namespace prefix.
            }
            $xml->registerXPathNamespace($strPrefix,$strNamespace);
        }
        $code = (string) $xml->xpath('//res:code')[0];
        $message = (string) $xml->xpath('//res:msg')[0];
        return array($code, $message);
    }
}
