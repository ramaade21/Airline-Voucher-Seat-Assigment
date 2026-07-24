<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Voucher
 */
class VoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'seats' => [$this->seat1, $this->seat2, $this->seat3],
            'voucher' => [
                'id' => $this->id,
                'crewName' => $this->crew_name,
                'crewId' => $this->crew_id,
                'flightNumber' => $this->flight_number,
                'flightDate' => $this->flight_date->toDateString(),
                'aircraftType' => $this->aircraft_type,
                'generatedAt' => $this->created_at?->toIso8601String(),
            ],
        ];
    }
}
