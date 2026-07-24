<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Seed a handful of realistic sample vouchers.
     */
    public function run(): void
    {
        Voucher::factory()->create([
            'crew_name' => 'Sarah Connor',
            'crew_id' => '98123',
            'flight_number' => 'GA102',
            'flight_date' => '2025-07-12',
            'aircraft_type' => 'Airbus 320',
            'seat1' => '3B',
            'seat2' => '7C',
            'seat3' => '14D',
        ]);

        Voucher::factory()->create([
            'crew_name' => 'John Reese',
            'crew_id' => '55210',
            'flight_number' => 'GA210',
            'flight_date' => '2025-08-01',
            'aircraft_type' => 'ATR',
            'seat1' => '2A',
            'seat2' => '9C',
            'seat3' => '18F',
        ]);

        Voucher::factory(3)->create();
    }
}
