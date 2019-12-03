<?php

namespace App\Helpers;

class Alipay
{
    //应用ID
    public $alipaySdkVersion = 'alipay-sdk-php-20161101';

    //返回数据格式
    public $format = "json";

    //返回数据格式
    public $method = 'alipay.trade.app.pay';

    // 表单提交字符集编码
    public $postCharset = "UTF-8";

    private $fileCharset = "UTF-8";

    //签名类型
    public $signType = "RSA2";

    //加密密钥和类型
    public $encryptKey;

    public $encryptType = "AES";

    function getAlipayParam($subject, $body, $out_trade_no, $total_amount)
    {
        $total_amount = $total_amount/100;
        $bizcontent = "{\"body\":\"$body\","
            . "\"subject\": \"$subject\","
            . "\"out_trade_no\": \"$out_trade_no\","
            . "\"timeout_express\": \"30m\","
            . "\"total_amount\": \"$total_amount\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\""
            . "}";

        return [
            //商户外网可以访问的异步地址 (异步回掉地址，根据自己需求写)
            'alipay_sdk'  => $this->alipaySdkVersion,
            'notify_url'  => config('custom.notify_url'),
            'app_id'      => config('custom.app_id'),
            'biz_content' => htmlspecialchars_decode($bizcontent),
            'charset'     => 'utf-8',
            'format'      => $this->format,
            'method'      => $this->method,
            'timestamp'   => date("Y-m-d H:i:s", time()),
            'version'     => '1.0',
            'sign_type'   => $this->signType,
            'sign'        => config('custom.rsa_private_key'),
        ];
    }

    public function getNotifications($post)
    {
        return $this->rsaCheckV1($post, NULL, "RSA2");
    }

    /** rsaCheckV1 & rsaCheckV2
     *  验证签名
     *  在使用本方法前，必须初始化AopClient且传入公钥参数。
     *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
     **/
    public function rsaCheckV1($params, $rsaPublicKeyFilePath,$signType='RSA') {
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;
        return $this->verify($this->getSignContent($params), $sign, $rsaPublicKeyFilePath,$signType);
    }

    function verify($data, $sign, $rsaPublicKeyFilePath, $signType = 'RSA') {

        $res = null;
        $pubKey= config('custom.rsa_public_key');
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值

        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        }

        //释放资源
        openssl_free_key($res);

        return $result;
    }

    public function generateSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }

    public function getSignContent($params) {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);

        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }


        return $data;
    }

    protected function sign($data, $signType = "RSA") {
        $priKey=config('custom.rsa_public_key');
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }
}



