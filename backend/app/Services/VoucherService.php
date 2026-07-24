<?php

namespace App\Services;

use App\Exceptions\VoucherAlreadyExistsException;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Orchestrates the voucher lifecycle: checking for existing vouchers and
 * generating new ones. Delegates seat-map logic to SeatGeneratorService
 * and persistence to the Voucher Eloquent model.
 */
class VoucherService
{
    public function __construct(
        private readonly SeatGeneratorService $seatGenerator,
    ) {}

    /**
     * Determine whether vouchers already exist for the given flight
     * number + flight date combination.
     */
    public function exists(string $flightNumber, string $flightDate): bool
    {
        return Voucher::query()
            ->where('flight_number', $flightNumber)
            ->whereDate('flight_date', $flightDate)
            ->exists();
    }

    /**
     * Generate exactly three unique random voucher seats for a flight and
     * persist them. Throws if vouchers already exist for that flight/date.
     *
     * @param  array{name: string, id: string, flightNumber: string, date: string, aircraft: string}  $data
     *
     * @throws VoucherAlreadyExistsException
     * @throws InvalidArgumentException
     */
    public function generate(array $data): Voucher
    {
        return DB::transaction(function () use ($data) {
            // Lock against race conditions: re-check for existence inside the transaction.
            $exists = Voucher::query()
                ->where('flight_number', $data['flightNumber'])
                ->whereDate('flight_date', $data['date'])
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw new VoucherAlreadyExistsException($data['flightNumber'], $data['date']);
            }

            $seats = $this->seatGenerator->generateUniqueSeats($data['aircraft'], 3);

            $voucher = Voucher::query()->create([
                'crew_name' => $data['name'],
                'crew_id' => $data['id'],
                'flight_number' => $data['flightNumber'],
                'flight_date' => $data['date'],
                'aircraft_type' => $data['aircraft'],
                'seat1' => $seats[0],
                'seat2' => $seats[1],
                'seat3' => $seats[2],
            ]);

            Log::channel('voucher')->info('Voucher seats generated', [
                'flight_number' => $voucher->flight_number,
                'flight_date' => $voucher->flight_date->toDateString(),
                'aircraft_type' => $voucher->aircraft_type,
                'crew_id' => $voucher->crew_id,
                'seats' => $seats,
            ]);

            return $voucher;
        });
    }
}
