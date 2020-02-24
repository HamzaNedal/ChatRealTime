<?php

namespace App\Events;

use App\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageDelivered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $delete;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message,$delete = 0)
    {
        $this->message = $message;
        $this->delete = $delete;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
      //  return new PrivateChannel('Chat.'.$this->message->sender_id.'.'.$this->message->receiver_id);
        return new Channel('chat-real.'.$this->message->receiver_id.$this->message->sender_id);
    }
}
