<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSnapshotResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'label' => $this->label,
            'range' => [
                'type' => $this->range_type,
                'start' => optional($this->range_start)->toDateString(),
                'end' => optional($this->range_end)->toDateString(),
            ],
            'overview' => [
                'totalViews' => (int) $this->total_views,
                'viewsChange' => (float) $this->views_change,
                'visits' => (int) $this->visits,
                'visitsChange' => (float) $this->visits_change,
                'newUsers' => (int) $this->new_users,
                'newUsersChange' => (float) $this->new_users_change,
                'activeUsers' => (int) $this->active_users,
                'activeUsersChange' => (float) $this->active_users_change,
            ],
            'breakdowns' => DashboardBreakdownResource::collection($this->whenLoaded('breakdowns')),
        ];
    }
}
