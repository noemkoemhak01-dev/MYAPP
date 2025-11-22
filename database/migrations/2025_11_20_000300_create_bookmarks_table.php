<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookmarks')) {
            Schema::create('bookmarks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('feed_post_id')->constrained()->cascadeOnDelete();
                $table->string('source')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'feed_post_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
