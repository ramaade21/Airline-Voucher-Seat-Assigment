<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $aircraft = $this->faker->randomElement(['ATR', 'Airbus 320', 'Boeing 737 Max']);
        $columns = $aircraft === 'ATR' ? ['A', 'C', 'D', 'F'] : ['A', 'B', 'C', 'D', 'E', 'F'];
        $maxRow = $aircraft === 'ATR' ? 18 : 32;

        $randomSeat = fn () => $this->faker->numberBetween(1, $maxRow).$this->faker->randomElement($columns);

        return [
            'crew_name' => $this->faker->name(),
            'crew_id' => (string) $this->faker->unique()->numberBetween(10000, 99999),
            'flight_number' => strtoupper($this->faker->bothify('??###')),
            'flight_date' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'aircraft_type' => $aircraft,
            'seat1' => $randomSeat(),
            'seat2' => $randomSeat(),
            'seat3' => $randomSeat(),
        ];
    }
}
