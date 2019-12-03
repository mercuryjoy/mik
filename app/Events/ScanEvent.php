<?php

namespace App\Events;

use App\Helpers\Contracts\SMSContract;
use App\ScanLog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ScanEvent extends Event
{
    use SerializesModels;

    public $scanLog;
    public $smsSender;

    /**
     * Create a new event instance.
     *
     * @param ScanLog $scanLog
     * @param SMSContract $smsSender
     */
    public function __construct(ScanLog $scanLog, SMSContract $smsSender)
    {
        $this->scanLog = $scanLog;
        $this->smsSender = $smsSender;
    }
}
