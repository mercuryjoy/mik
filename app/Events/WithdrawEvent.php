<?php

namespace App\Events;

use App\Helpers\Contracts\SMSContract;
use Illuminate\Queue\SerializesModels;

class WithdrawEvent extends Event
{
    use SerializesModels;

    public $smsSender;

    /**
     * Create a new event instance.
     *
     * @param SMSContract $smsSender
     */
    public function __construct(SMSContract $smsSender)
    {
        $this->smsSender = $smsSender;
    }
}
