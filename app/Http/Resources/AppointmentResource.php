<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'show_authorized' => auth()->user()->can('showAuthorized', $this->resource),
            $this->merge(Arr::except(parent::toArray($request), [
                'updated_at', 'updated_at'
            ]))
        ];
    }
}
