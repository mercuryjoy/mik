<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\UserPointLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sendPoint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send point to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('point_balance', '>', 0)->get();
        Log::useFiles(storage_path('logs/point.log'), 'debug');

        foreach($users as $user) {
            DB::transaction(function () use ($user) {
                $times = 10;
                $before_point = $user->point_balance;
                $after_point = $before_point * $times;
                $user->point_balance = $after_point;
                if ($user->save()) {

                    UserPointLog::create([
                        'type' => 'adjustment',
                        'amount' => -$before_point,
                        'user_id' => $user->id,
                        'comment' => 'decrease origin point'
                    ]);

                    $logs = UserPointLog::create([
                        'type' => 'adjustment',
                        'amount' => $after_point,
                        'user_id' => $user->id,
                        'comment' => 'increase ' . $times . ' times point'
                    ]);

                    if ($logs) {
                        $all_success_ids[] = $user->id;
                        Log::info('SEND_POINT_LOG:', ['send point to ' . $user->id .' success !']);
                        $this->info('send point to ' . $user->id .' success !');
                    } else {
                        $point_log_failed_ids[] = $user->id;
                        Log::info('SEND_POINT_LOG:', ['send point to ' . $user->id .'log failed !']);
                        $this->info('send point to ' . $user->id .'log failed !');
                    }
                } else {
                    $all_failed_ids[] = $user->id;
                    Log::info('SEND_POINT_LOG:', ['send point to ' . $user->id .'failed !']);
                    $this->info('send point to ' . $user->id .'failed !');
                }
            });
        }

        $this->info('All users send point over !');
    }
}
