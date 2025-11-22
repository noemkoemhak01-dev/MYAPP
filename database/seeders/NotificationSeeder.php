<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = User::factory()->count(3)->create();
        }

        foreach ($users as $user) {
            Notification::updateOrCreate(
                ['user_id' => $user->id, 'title' => 'New followers joined!'],
                [
                    'message' => 'You have 250 new followers viewing your latest posts.',
                    'type' => 'insight',
                    'cta_label' => 'View dashboard',
                    'cta_url' => '/dashboard',
                ]
            );

            Notification::updateOrCreate(
                ['user_id' => $user->id, 'title' => 'Bookmark milestone'],
                [
                    'message' => 'Your video report reached 1.2K bookmarks in the last week.',
                    'type' => 'activity',
                    'cta_label' => 'Open report',
                    'cta_url' => '/post/featured',
                ]
            );
        }
    }
}
