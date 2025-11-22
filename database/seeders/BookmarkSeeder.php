<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\FeedPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookmarkSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first() ?? User::factory()->create();
        $posts = FeedPost::inRandomOrder()->limit(5)->get();

        if ($posts->isEmpty()) {
            $this->call(FeedPostSeeder::class);
            $posts = FeedPost::inRandomOrder()->limit(5)->get();
        }

        foreach ($posts as $index => $post) {
            Bookmark::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'feed_post_id' => $post->id,
                ],
                [
                    'source' => $index % 2 === 0 ? 'home_feed' : 'search',
                ]
            );
        }
    }
}
