<?php

namespace Database\Seeders;

use App\Models\DashboardBreakdown;
use App\Models\DashboardSnapshot;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        $snapshot = DashboardSnapshot::updateOrCreate(
            ['label' => 'Last 7 Days'],
            [
                'range_type' => 'last_7_days',
                'range_start' => now()->subDays(6),
                'range_end' => now(),
                'total_views' => 1_200_000,
                'visits' => 890_000,
                'new_users' => 5600,
                'active_users' => 125_000,
                'views_change' => 12.5,
                'visits_change' => 9.8,
                'new_users_change' => -2.1,
                'active_users_change' => 5.3,
            ]
        );

        $snapshot->breakdowns()->delete();

        $audiences = [
            ['metric_type' => 'audience', 'label' => 'Age 25-34', 'value' => 45, 'position' => 1],
            ['metric_type' => 'audience', 'label' => 'Age 18-24', 'value' => 35, 'position' => 2],
            ['metric_type' => 'audience', 'label' => 'Age 35-44', 'value' => 12, 'position' => 3],
        ];

        foreach ($audiences as $data) {
            DashboardBreakdown::create([
                'dashboard_snapshot_id' => $snapshot->id,
                'metric_type' => $data['metric_type'],
                'label' => $data['label'],
                'value' => $data['value'],
                'position' => $data['position'],
                'meta' => null,
            ]);
        }
    }
}
