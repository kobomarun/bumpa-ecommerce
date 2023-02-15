<?php

namespace App\Listeners;

use App\Events\UserPurchase;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Support\Facades\DB;
use App\Providers\LocalPaymentProvider;

class UnlockAchievements
{
    public function handle(UserPurchase $event)
    {
        $user = $event->user;
        $purchases = DB::table('purchases')
            ->where('user_id', $user->id)
            ->count();

        // Check for unlocked achievements
        $unlockedAchievements = Achievement::where('target_count', '<=', $purchases)
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('achievement_id')
                    ->from('user_achievements')
                    ->where('user_id', $user->id);
            })
            ->get();

        foreach ($unlockedAchievements as $achievement) {
            $user->achievements()->attach($achievement->id);
            event(new AchievementUnlocked($achievement->name, $user));
        }

        // Check for unlocked badges
        $unlockedBadges = Badge::where('target_count', '<=', $user->achievements()->count())
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('badge_id')
                    ->from('user_badges')
                    ->where('user_id', $user->id);
            })
            ->get();

        foreach ($unlockedBadges as $badge) {
            $user->badges()->attach($badge->id);
            event(new BadgeUnlocked($badge->name, $user));

            //300 naira cash back
            $paymentProvider = new LocalPaymentProvider(); // this is a dummy class setup for local payment gateway

            if ($paymentProvider->sendCashbackPaymentToUser($user, 300)) {
                // Payment was successful
            } else {
                // Payment failed
            }
        }
    }

}

