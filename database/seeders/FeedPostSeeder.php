<?php

namespace Database\Seeders;

use App\Models\FeedPost;
use App\Models\FeedPostComment;
use App\Models\FeedPostMedia;
use App\Models\FeedTopic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeedPostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@example.com')->first()
            ?? User::factory()->create(['role' => 'admin']);

        $topics = FeedTopic::all();
        if ($topics->isEmpty()) {
            $this->call(FeedTopicSeeder::class);
            $topics = FeedTopic::all();
        }

        $mediaSamples = [
            'https://images.unsplash.com/photo-1485827404703-89b55fcc595e',
            'https://images.unsplash.com/photo-1504384308090-c894fdcc538d',
            'https://images.unsplash.com/photo-1461749280684-dccba630e2f6',
            'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa',
        ];

        foreach ($topics as $topic) {
            for ($i = 1; $i <= 4; $i++) {
                $title = $topic->name.' headline #'.$i;
                $slug = Str::slug($title).'-'.$topic->id.'-'.$i;

                $post = FeedPost::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'user_id' => $author->id,
                        'title' => $title,
                        'topic' => $topic->slug,
                        'category' => $topic->category,
                        'type' => $i === 1 ? 'video' : ($i === 2 ? 'article' : 'news_feed'),
                        'excerpt' => 'Quick summary about '.$topic->name.' story '.$i,
                        'content' => 'Detailed content for '.$topic->name.' story '.$i.' discussing the key insights and stats users expect to see inside the app.',
                        'is_featured' => $i === 1,
                        'is_trending' => $i <= 2,
                        'is_published' => true,
                        'published_at' => now()->subHours(($topic->id * $i) % 36),
                        'view_count' => random_int(1_000, 50_000),
                        'like_count' => random_int(200, 5_000),
                        'comment_count' => random_int(20, 400),
                        'share_count' => random_int(10, 600),
                        'meta' => [
                            'duration' => $i === 1 ? random_int(45, 120).'s' : null,
                            'source' => 'E-News Studio',
                        ],
                    ]
                );

                // Reset demo relations to avoid duplication on reseed
                $post->media()->delete();
                $post->comments()->delete();

                if ($i % 2 === 0) {
                    $post->media()->create([
                        'media_type' => 'image',
                        'path' => $mediaSamples[$i % count($mediaSamples)],
                        'position' => 0,
                        'meta' => ['caption' => $title.' cover'],
                    ]);
                }

                if ($post->type === 'video') {
                    $post->media()->create([
                        'media_type' => 'video',
                        'path' => 'https://player.vimeo.com/external/449973265.sd.mp4',
                        'position' => 1,
                        'meta' => ['duration' => 120],
                    ]);
                }

                $commentAuthors = ['kim hak', 'sokun nisa', 'john carter'];
                foreach ($commentAuthors as $index => $name) {
                    FeedPostComment::create([
                        'feed_post_id' => $post->id,
                        'author_name' => Str::title($name),
                        'author_avatar' => null,
                        'body' => 'Loving this update about '.$topic->name.'! (comment '.($index + 1).')',
                    ]);
                }
            }
        }
    }
}
