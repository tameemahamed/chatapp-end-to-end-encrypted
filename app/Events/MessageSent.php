<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageData;

    /**
     * Create a new event instance.
     */
    public function __construct($messageData)
    {
        //
        $this->messageData = $messageData;
    }

    public function broadcastWith(){
        return [
            'id' => $this->messageData['id'] ?? null,
            'sender_id' => $this->messageData['sender_id'] ?? null,
            'receiver_id' => $this->messageData['receiver_id'] ?? null,
            'sender_en_msg' => $this->messageData['sender_en_msg'] ?? null,
            'receiver_en_msg' => $this->messageData['receiver_en_msg'] ?? null,
            'created_at' => $this->messageData['created_at'] ?? now()->toISOString(),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $receiverId = $this->messageData['receiver_id'];
        return [
            new PrivateChannel('chat.' .$receiverId)
        ];
    }
}
