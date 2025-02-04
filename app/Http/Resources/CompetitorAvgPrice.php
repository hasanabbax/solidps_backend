<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompetitorAvgPrice extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'hotel_id' => $this->hotel_id,
            'hotel_name' => $this->hotel_name,
            'price' => $this->price,
            'check_in_date' => $this->check_in_date,
            'competitors_data' => (isset($this->competitorsData) ? $this->competitorsData : null),
        ];
    }
}
