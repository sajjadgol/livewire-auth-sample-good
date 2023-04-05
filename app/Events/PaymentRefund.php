<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentRefund
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order_id, $varable;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order_id, $varable)
    {
        $this->order_id = $order_id;
        $this->varable = $varable;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('payment-refund');
    }
}
