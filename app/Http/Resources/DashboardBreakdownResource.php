<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardBreakdownResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'type' => $this->metric_type,
            'label' => $this->label,
            'value' => $this->value,
            'position' => $this->position,
            'meta' => $this->meta,
        ];
    }
}
