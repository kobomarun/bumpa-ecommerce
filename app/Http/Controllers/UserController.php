<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function purchase(Request $request) {
        //process user purchase


        $user = User::find(1);

        event(new UserPurchase($user));
    }

    public function achievements(User $user) {
        $unlockedAchievements = DB::table('achievements')
        ->join('user_achievements', 'achievements.id', '=', 'user_achievements.achievement_id')
        ->select('achievements.name')
        ->where('user_achievements.user_id', '=', $user->id)
        ->get()
        ->pluck('name')
        ->toArray();

        // Get next available achievements
        $nextAvailableAchievements = DB::table('achievements')
            ->whereNotIn('id', fn($query) =>
                $query->select('achievement_id')
                    ->from('user_achievements')
                    ->where('user_id', '=', $user->id)
            )
            ->select('name')
            ->distinct()
            ->get()
            ->pluck('name')
            ->toArray();

        // Get current badge
        $currentBadge = DB::table('badges')
            ->join('user_badges', 'badges.id', '=', 'user_badges.badge_id')
            ->select('badges.name')
            ->where('user_badges.user_id', '=', $user->id)
            ->first()
            ->name;

        // Get next badge and remaining achievements to unlock the next badge
        $nextBadge = DB::table('badges')
            ->whereNotIn('id', fn($query) => 
                $query->select('badge_id')
                    ->from('user_badges')
                    ->where('user_id', '=', $user->id)
            )
            ->select('name')
            ->first()
            ->name;

        $remainingToUnlockNextBadge = DB::table('achievements')
            ->whereNotIn('id', fn($query) =>
                $query->select('achievement_id')
                    ->from('user_achievements')
                    ->where('user_id', '=', $user->id)
            )
            ->count();

        // Return the results
        return [
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge,
        ];
    }

}
