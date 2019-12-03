<?php

namespace App\Helpers\Contracts;

interface SMSContract {

    public function sendTestMessage($phone);
    public function sendVerifyCode($phone, $code, $type);
    public function sendPassAuditMessage($phone);

    public function sendMoneyCostNotificationMessage($phone, $money);
    public function sendUserScanCountNotificationMessage($phone, $username, $count);
    public function sendFundingPoolNotificationMessage($phone, $money);

}
