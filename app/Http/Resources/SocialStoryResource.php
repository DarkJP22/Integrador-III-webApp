<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class SocialStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'time_ago' => $this->resource->created_at->diffForHumans(),
            $this->merge(Arr::except(parent::toArray($request), [
                'updated_at'
            ]))
        ];
    }
}
