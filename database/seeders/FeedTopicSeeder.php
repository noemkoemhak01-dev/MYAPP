<?php

namespace Database\Seeders;

use App\Models\FeedTopic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeedTopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            ['name' => 'home', 'icon' => 'home', 'category' => 'home'],
            ['name' => 'following', 'icon' => 'users', 'category' => 'following'],
            ['name' => 'popular', 'icon' => 'trending-up', 'category' => 'popular'],
            ['name' => 'news', 'icon' => 'newspaper', 'category' => 'news'],
        ];

        foreach ($topics as $topic) {
            FeedTopic::updateOrCreate(
                ['slug' => Str::slug($topic['name'])],
                [
                    'name' => $topic['name'],
                    'icon' => $topic['icon'],
                    'category' => $topic['category'],
                    'description' => 'Trending stories in '.$topic['name'],
                    'is_featured' => in_array($topic['name'], ['home', 'popular', 'news']),
                    'trend_score' => random_int(40, 95),
                ]
            );
        }
    }
}
