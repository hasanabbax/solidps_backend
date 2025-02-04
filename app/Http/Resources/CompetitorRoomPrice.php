<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompetitorRoomPrice extends JsonResource
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
            'room_id' => $this->room_id,
            'room' => $this->room,
            'price' => round($this->price, 2),
            'price_should' => round($this->price_should, 2),
            'criteria' => $this->criteria,
            'room_type' => $this->room_type,
            'check_in_date' => $this->check_in_date,
            'request_date' => $this->request_date,
//            'check_out_date' => $this->check_out_date,
            'competitors_data' => (isset($this->competitors) ? $this->competitors : null),
        ];
    }
}
