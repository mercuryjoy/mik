<?php

namespace App\Listeners;

use App\Events\ScanEvent;
use App\Helpers\Contracts\SMSContract;
use App\ScanLog;
use App\SMSLog;
use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;

class ScanEventListener
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
     * @param  ScanEvent $event
     * @param SMSContract $sms
     */
    public function handle(ScanEvent $event)
    {
        $scanLog = $event->scanLog;
        $scanLog->load('user');

        // scan count notification
        $scanCountToday = ScanLog::where('user_id', $scanLog->user_id)
            ->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
            ->where('type', 'scan_prize')
            ->count();

        $dailyUserScanThreshold = Settings::get('notification.threshold.per_user_scan_count', 10000);

        if ($dailyUserScanThreshold < $scanCountToday) {
            $username = $scanLog->user->name . '(' . $scanLog->user_id . ')';
            $notiCount = SMSLog::where('type', 'admin_notify_scan_count')
                ->where('content', 'LIKE', '%' . $username . '%')
                ->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
                ->count();

            if ($notiCount == 0) {
                $phones_str = Settings::get('notification.phones', '[]');
                $phones = json_decode($phones_str);

                foreach ($phones as $phone) {
                    $event->smsSender->sendUserScanCountNotificationMessage($phone, $username, $scanCountToday);
                }
            }
        }


        // Daily Money
        $scanMoneyToday = ScanLog::whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
            ->sum('money');

        $dailyScanMoneyThreshold = Settings::get('notification.threshold.daily_money_sum', 100000000);

        if ($scanMoneyToday > $dailyScanMoneyThreshold * 100) {
            $notiCount = SMSLog::where('type', 'admin_notify_daily_cost')
                ->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
                ->count();


            if ($notiCount == 0) {
                $phones_str = Settings::get('notification.phones', '[]');
                $phones = json_decode($phones_str);

                foreach ($phones as $phone) {
                    $event->smsSender->sendMoneyCostNotificationMessage($phone, $scanMoneyToday);
                }
            }
        }
    }
}
