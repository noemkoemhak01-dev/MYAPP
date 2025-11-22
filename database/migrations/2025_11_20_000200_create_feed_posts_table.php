<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('feed_posts')) {
            Schema::create('feed_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('topic')->nullable();
            $table->string('category')->nullable();
            $table->enum('type', ['news_feed', 'video', 'article'])->default('news_feed');
            $table->string('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->unsignedInteger('share_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_published']);
            $table->index(['is_trending', 'is_featured']);
            });
        }

        if (! Schema::hasTable('feed_post_media')) {
            Schema::create('feed_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_id')->constrained()->cascadeOnDelete();
            $table->enum('media_type', ['image', 'video', 'audio'])->default('image');
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            });
        }

        if (! Schema::hasTable('feed_post_comments')) {
            Schema::create('feed_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('feed_post_comments')->cascadeOnDelete();
            $table->string('author_name')->nullable();
            $table->string('author_avatar')->nullable();
            $table->text('body');
            $table->timestamps();
            });
        }

        if (! Schema::hasTable('feed_post_stats')) {
            Schema::create('feed_post_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_id')->constrained()->cascadeOnDelete();
            $table->date('recorded_for')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('saves')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_post_stats');
        Schema::dropIfExists('feed_post_comments');
        Schema::dropIfExists('feed_post_media');
        Schema::dropIfExists('feed_posts');
    }
};
