<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label');
            $table->enum('range_type', ['last_7_days', 'last_30_days', 'this_month', 'custom'])->default('last_7_days');
            $table->date('range_start')->nullable();
            $table->date('range_end')->nullable();
            $table->unsignedBigInteger('total_views')->default(0);
            $table->unsignedBigInteger('visits')->default(0);
            $table->unsignedBigInteger('new_users')->default(0);
            $table->unsignedBigInteger('active_users')->default(0);
            $table->decimal('views_change', 6, 2)->default(0);
            $table->decimal('visits_change', 6, 2)->default(0);
            $table->decimal('new_users_change', 6, 2)->default(0);
            $table->decimal('active_users_change', 6, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('dashboard_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_snapshot_id')->constrained()->cascadeOnDelete();
            $table->string('metric_type');
            $table->string('label');
            $table->decimal('value', 12, 2)->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_breakdowns');
        Schema::dropIfExists('dashboard_snapshots');
    }
};
