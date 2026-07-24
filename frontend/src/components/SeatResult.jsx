import React from 'react'

/**
 * @param {{ seats: string[], flightNumber: string, date: string }} props
 */
export default function SeatResult({ seats, flightNumber, date }) {
  if (!seats || seats.length === 0) return null

  return (
    <div className="mt-6 rounded-xl border border-airline-100 bg-airline-50 p-5">
      <h3 className="text-sm font-semibold text-airline-900 uppercase tracking-wide">
        Voucher Seats Generated
      </h3>
      <p className="mt-1 text-sm text-slate-600">
        Flight <span className="font-medium">{flightNumber}</span> · {date}
      </p>

      <div className="mt-4 grid grid-cols-3 gap-3">
        {seats.map((seat) => (
          <div
            key={seat}
            className="flex flex-col items-center justify-center rounded-lg bg-white border-2 border-airline-500 py-4 shadow-sm"
          >
            <span className="text-2xl font-bold text-airline-700">{seat}</span>
            <span className="text-xs text-slate-400 mt-1">Seat</span>
          </div>
        ))}
      </div>
    </div>
  )
}
