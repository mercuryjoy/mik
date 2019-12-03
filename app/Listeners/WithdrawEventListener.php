<?php

namespace App\Listeners;

use App\Events\WithdrawEvent;
use App\FundingPoolLog;
use App\SMSLog;
use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;

class WithdrawEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WithdrawEvent  $event
     * @return void
     */
    public function handle(WithdrawEvent $event)
    {

        $latestFundingPoolLog = FundingPoolLog::orderBy('created_at', 'desc')->first();
        $balance = $latestFundingPoolLog->balance;

        $fundingPoolThreshold = Settings::get('notification.threshold.funding_pool_balance', 10000);

        if ($fundingPoolThreshold * 100 > $balance) {
            $notiCount = SMSLog::where('type', 'admin_notify_funding_pool')
                ->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
                ->count();

            if ($notiCount == 0) {
                $phones_str = Settings::get('notification.phones', '[]');
                $phones = json_decode($phones_str);

                foreach ($phones as $phone) {
                    $event->smsSender->sendFundingPoolNotificationMessage($phone, $balance);
                }
            }
        }
    }
}
