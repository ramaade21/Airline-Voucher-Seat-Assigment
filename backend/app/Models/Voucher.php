<?php

namespace App\Models;

use Database\Factories\VoucherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $crew_name
 * @property string $crew_id
 * @property string $flight_number
 * @property \Illuminate\Support\Carbon $flight_date
 * @property string $aircraft_type
 * @property string $seat1
 * @property string $seat2
 * @property string $seat3
 */
class Voucher extends Model
{
    /** @use HasFactory<VoucherFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'crew_name',
        'crew_id',
        'flight_number',
        'flight_date',
        'aircraft_type',
        'seat1',
        'seat2',
        'seat3',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'flight_date' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the three generated seats as a plain array, in generation order.
     *
     * @return array<int, string>
     */
    public function getSeatsArrayAttribute(): array
    {
        return [$this->seat1, $this->seat2, $this->seat3];
    }
}
