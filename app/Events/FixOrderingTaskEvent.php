<?php

namespace App\Events;

use App\Models\Tasks;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FixOrderingTaskEvent extends Event
{
    use InteractsWithSockets, SerializesModels;
    
    public $list;

    public function __construct($data)
    {  
        $this->list = H_toArrayObject($data);
        // info("Now this user {$this->data->name} is created");

        $no = 0;
        $status = $this->list->status;
        foreach ($this->list->data as $key => $tasks) {
            Tasks::where('id', $tasks->id)
            ->whereStatus($status)
            ->update([
                'ordering' => $no
            ]);
            $no++;
        }
    }

    public function broadcastOn()
    {   
        return [];
    }
}
