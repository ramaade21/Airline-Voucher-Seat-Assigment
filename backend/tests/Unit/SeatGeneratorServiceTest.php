<?php

namespace Tests\Unit;

use App\Services\SeatGeneratorService;
use InvalidArgumentException;
use Tests\TestCase;

class SeatGeneratorServiceTest extends TestCase
{
    private SeatGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SeatGeneratorService;
    }

    public function test_generates_exactly_three_seats(): void
    {
        $seats = $this->service->generateUniqueSeats('Airbus 320', 3);

        $this->assertCount(3, $seats);
    }

    public function test_generated_seats_are_unique(): void
    {
        $seats = $this->service->generateUniqueSeats('Boeing 737 Max', 3);

        $this->assertCount(3, array_unique($seats));
    }

    public function test_atr_seats_only_use_valid_columns_and_rows(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $seats = $this->service->generateUniqueSeats('ATR', 3);

            foreach ($seats as $seat) {
                preg_match('/^(\d+)([A-Z])$/', $seat, $matches);
                $row = (int) $matches[1];
                $column = $matches[2];

                $this->assertGreaterThanOrEqual(1, $row);
                $this->assertLessThanOrEqual(18, $row);
                $this->assertContains($column, ['A', 'C', 'D', 'F']);
            }
        }
    }

    public function test_airbus_320_seats_use_all_six_columns_and_32_rows(): void
    {
        $seats = $this->service->generateUniqueSeats('Airbus 320', 3);

        foreach ($seats as $seat) {
            preg_match('/^(\d+)([A-Z])$/', $seat, $matches);
            $row = (int) $matches[1];
            $column = $matches[2];

            $this->assertLessThanOrEqual(32, $row);
            $this->assertContains($column, ['A', 'B', 'C', 'D', 'E', 'F']);
        }
    }

    public function test_throws_for_unsupported_aircraft_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->generateUniqueSeats('Concorde', 3);
    }

    public function test_supported_aircraft_types_list(): void
    {
        $this->assertEquals(
            ['ATR', 'Airbus 320', 'Boeing 737 Max'],
            $this->service->supportedAircraftTypes()
        );
    }

    public function test_is_supported_helper(): void
    {
        $this->assertTrue($this->service->isSupported('ATR'));
        $this->assertFalse($this->service->isSupported('Concorde'));
    }
}
