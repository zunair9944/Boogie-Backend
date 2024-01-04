<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class NewImageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $imageData;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @param  array  $imageData
     * @return void
     */
     
     public function __construct($userId, $imageData)
    {
        $this->userId = $userId;
        $this->imageData = $imageData;
    }
    // public function __construct($imageData)
    // {
    //     $this->imageData = $imageData;
    // }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('notifications');
    }
    
    public function broadcastWith()
    {
        return [
            'userId' => $this->userId,
            'imageData' => $this->imageData,
        ];
    }
}
