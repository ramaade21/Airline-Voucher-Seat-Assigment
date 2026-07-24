<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckVoucherRequest extends FormRequest
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
        return [
            'flightNumber' => ['required', 'string', 'max:10'],
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'flightNumber.required' => 'Flight number is required.',
            'date.required' => 'Flight date is required.',
            'date.date_format' => 'Flight date must be in YYYY-MM-DD format.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'flightNumber' => 'flight number',
            'date' => 'flight date',
        ];
    }
}
