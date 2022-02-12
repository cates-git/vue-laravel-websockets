<?php

namespace App\Events;

use App\Models\Message as ModelsMessage;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id_to;
    public $new_message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id_to, $new_message)
    {
        $this->user_id_to = $user_id_to;
        $this->new_message = $new_message;
    }

    public function broadcastWith()
    {
        return ['new' => $this->new_message];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('message.'.$this->user_id_to);
    }
}
