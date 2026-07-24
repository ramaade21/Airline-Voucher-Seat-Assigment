<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_returns_false_when_no_voucher_exists(): void
    {
        $response = $this->postJson('/api/check', [
            'flightNumber' => 'GA102',
            'date' => '2025-07-12',
        ]);

        $response->assertOk()->assertJson(['exists' => false]);
    }

    public function test_check_returns_true_when_voucher_exists(): void
    {
        Voucher::factory()->create([
            'flight_number' => 'GA102',
            'flight_date' => '2025-07-12',
        ]);

        $response = $this->postJson('/api/check', [
            'flightNumber' => 'GA102',
            'date' => '2025-07-12',
        ]);

        $response->assertOk()->assertJson(['exists' => true]);
    }

    public function test_check_requires_flight_number_and_date(): void
    {
        $response = $this->postJson('/api/check', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['flightNumber', 'date']);
    }

    public function test_generate_creates_exactly_three_unique_seats(): void
    {
        $response = $this->postJson('/api/generate', [
            'name' => 'Sarah',
            'id' => '98123',
            'flightNumber' => 'GA102',
            'date' => '2025-07-12',
            'aircraft' => 'Airbus 320',
        ]);

        $response->assertCreated()
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'seats');

        $seats = $response->json('seats');
        $this->assertCount(3, array_unique($seats));

        $this->assertDatabaseHas('vouchers', [
            'flight_number' => 'GA102',
            'flight_date' => '2025-07-12',
            'crew_id' => '98123',
        ]);
    }

    public function test_generate_fails_when_vouchers_already_exist_for_flight(): void
    {
        Voucher::factory()->create([
            'flight_number' => 'GA102',
            'flight_date' => '2025-07-12',
        ]);

        $response = $this->postJson('/api/generate', [
            'name' => 'Sarah',
            'id' => '98123',
            'flightNumber' => 'GA102',
            'date' => '2025-07-12',
            'aircraft' => 'Airbus 320',
        ]);

        $response->assertStatus(409)
            ->assertJson(['success' => false, 'exists' => true]);

        $this->assertDatabaseCount('vouchers', 1);
    }

    public function test_generate_rejects_unsupported_aircraft_type(): void
    {
        $response = $this->postJson('/api/generate', [
            'name' => 'Sarah',
            'id' => '98123',
            'flightNumber' => 'GA102',
            'date' => '2025-07-12',
            'aircraft' => 'Concorde',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['aircraft']);
    }

    public function test_generate_seats_are_within_atr_layout_bounds(): void
    {
        $response = $this->postJson('/api/generate', [
            'name' => 'Sarah',
            'id' => '98123',
            'flightNumber' => 'GA300',
            'date' => '2025-09-01',
            'aircraft' => 'ATR',
        ]);

        $response->assertCreated();

        foreach ($response->json('seats') as $seat) {
            preg_match('/^(\d+)([A-Z])$/', $seat, $matches);
            $this->assertNotEmpty($matches);
            $this->assertLessThanOrEqual(18, (int) $matches[1]);
            $this->assertContains($matches[2], ['A', 'C', 'D', 'F']);
        }
    }
}
