<?php

namespace App\Http\Requests;

use App\Services\SeatGeneratorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        /** @var SeatGeneratorService $seatGenerator */
        $seatGenerator = app(SeatGeneratorService::class);

        return [
            'name' => ['required', 'string', 'max:100'],
            'id' => ['required', 'string', 'max:50'],
            'flightNumber' => ['required', 'string', 'max:10'],
            'date' => ['required', 'date_format:Y-m-d'],
            'aircraft' => ['required', 'string', Rule::in($seatGenerator->supportedAircraftTypes())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Crew name is required.',
            'id.required' => 'Crew ID is required.',
            'flightNumber.required' => 'Flight number is required.',
            'date.required' => 'Flight date is required.',
            'date.date_format' => 'Flight date must be in YYYY-MM-DD format.',
            'aircraft.required' => 'Aircraft type is required.',
            'aircraft.in' => 'Aircraft type must be one of: ATR, Airbus 320, Boeing 737 Max.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'crew name',
            'id' => 'crew ID',
            'flightNumber' => 'flight number',
            'date' => 'flight date',
            'aircraft' => 'aircraft type',
        ];
    }
}
