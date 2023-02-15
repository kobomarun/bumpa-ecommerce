<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $achievementName;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  string  $achievementName
     * @param  User  $user
     * @return void
     */
    public function __construct($achievementName, User $user)
    {
        $this->achievementName = $achievementName;
        $this->user = $user;
    }
}
