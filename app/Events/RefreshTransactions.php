<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshTransactions implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function broadcastOn()
    {
        return new Channel('transactionNotPaid');
    }

    public function broadcastWith()
    {
        return ['transactions' => $this->transactions];
    }
}
