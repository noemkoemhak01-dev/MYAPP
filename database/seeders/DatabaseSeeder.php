<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'kimhak029@gmail.com',
        ], [
            'name' => 'Admin',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $user = User::firstOrCreate([
            'email' => 'hakk96676@gmail.com',
        ], [
            'name' => 'User',
            'role' => 'user',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            FeedTopicSeeder::class,
            FeedPostSeeder::class,
            BookmarkSeeder::class,
            DashboardSeeder::class,
            NotificationSeeder::class,
        ]);

        // Guarantee demo ownership alignment
        $admin->feedPosts()->update(['user_id' => $admin->id]);
        $user->bookmarks()->update(['user_id' => $user->id]);
    }
}
