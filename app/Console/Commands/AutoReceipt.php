<?php

namespace App\Console\Commands;

use App\StoreOrder;
use Illuminate\Console\Command;

class AutoReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:receipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Two Days Auto Receipt';

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
        $orders = StoreOrder::where('type', 'purchase')
            ->where('is_pay', 1)
            ->where('status', 'shipped')
            ->whereRaw('HOUR(timediff( now(), updated_at)) > 24');

        if ($orders->count() > 0) {
            $orders->update(['status' => 'finished']);
        }
    }
}
