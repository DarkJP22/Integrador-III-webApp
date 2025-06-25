<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class PatientResource extends JsonResource
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
            'authorized_medic' => $this->isPatientAuthorizedOf(auth()->user()),
            $this->merge(Arr::except(parent::toArray($request), [
                'updated_at', 'updated_at'
            ]))
        ];
    }
}
