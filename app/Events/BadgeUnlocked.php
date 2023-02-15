<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $badgeName;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  string  $badgeName
     * @param  User  $user
     * @return void
     */
    public function __construct($badgeName, User $user)
    {
        $this->badgeName = $badgeName;
        $this->user = $user;
    }
}
