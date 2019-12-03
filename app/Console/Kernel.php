<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\AreaJSGenerator::class,
        Commands\VersionFileGenerator::class,
        Commands\AutoReceipt::class,
        Commands\Statics::class,
        Commands\SendPoint::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('inspire')
//                 ->hourly();
        // 自动完成采购订单
        $schedule->command('orders:receipt')
//            ->everyMinute();
            ->dailyAt('03:01');

        // 每天计算销售员统计数据
        $schedule->command('mik:statics')->dailyAt('00:01');
    }
}
