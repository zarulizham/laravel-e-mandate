<?php

namespace ZarulIzham\EMandate\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use ZarulIzham\EMandate\Models\EMandateTransaction;

class EMandateTransactionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $eMandateTransaction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EMandateTransaction $eMandateTransaction)
    {
        $this->eMandateTransaction = $eMandateTransaction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
