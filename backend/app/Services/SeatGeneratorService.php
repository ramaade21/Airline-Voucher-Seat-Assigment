<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Responsible purely for knowing each aircraft's seat map and producing
 * a set of unique, randomly selected seats for it.
 *
 * Single Responsibility: this class does not know anything about vouchers,
 * persistence, or HTTP - it only understands seat layouts.
 */
class SeatGeneratorService
{
    /**
     * Aircraft seat layout definitions.
     *
     * @var array<string, array{rows: array{int,int}, columns: array<int,string>}>
     */
    private const AIRCRAFT_LAYOUTS = [
        'ATR' => [
            'rows' => [1, 18],
            'columns' => ['A', 'C', 'D', 'F'],
        ],
        'Airbus 320' => [
            'rows' => [1, 32],
            'columns' => ['A', 'B', 'C', 'D', 'E', 'F'],
        ],
        'Boeing 737 Max' => [
            'rows' => [1, 32],
            'columns' => ['A', 'B', 'C', 'D', 'E', 'F'],
        ],
    ];

    /**
     * Get the list of aircraft types supported by the system.
     *
     * @return array<int, string>
     */
    public function supportedAircraftTypes(): array
    {
        return array_keys(self::AIRCRAFT_LAYOUTS);
    }

    public function isSupported(string $aircraftType): bool
    {
        return array_key_exists($aircraftType, self::AIRCRAFT_LAYOUTS);
    }

    /**
     * Generate a given number of unique, randomly selected seats for the
     * given aircraft type (e.g. "3B", "18F").
     *
     * @return array<int, string>
     */
    public function generateUniqueSeats(string $aircraftType, int $count = 3): array
    {
        $layout = self::AIRCRAFT_LAYOUTS[$aircraftType] ?? null;

        if ($layout === null) {
            throw new InvalidArgumentException("Unsupported aircraft type: {$aircraftType}");
        }

        $allSeats = $this->buildAllSeats($layout['rows'], $layout['columns']);

        if ($count > count($allSeats)) {
            throw new InvalidArgumentException(
                "Cannot generate {$count} unique seats; aircraft only has ".count($allSeats).' seats.'
            );
        }

        $keys = array_rand($allSeats, $count);

        // array_rand returns a single key (not an array) when $count === 1.
        $keys = is_array($keys) ? $keys : [$keys];

        $seats = array_map(static fn (int $key) => $allSeats[$key], $keys);

        shuffle($seats);

        return array_values($seats);
    }

    /**
     * Build the full list of seat labels for a given row range and column set.
     *
     * @param  array{int,int}  $rowRange
     * @param  array<int,string>  $columns
     * @return array<int, string>
     */
    private function buildAllSeats(array $rowRange, array $columns): array
    {
        [$firstRow, $lastRow] = $rowRange;

        $seats = [];

        for ($row = $firstRow; $row <= $lastRow; $row++) {
            foreach ($columns as $column) {
                $seats[] = "{$row}{$column}";
            }
        }

        return $seats;
    }
}
