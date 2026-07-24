import React from 'react'

const AIRCRAFT_TYPES = ['ATR', 'Airbus 320', 'Boeing 737 Max']

/**
 * @param {{
 *   form: { name: string, id: string, flightNumber: string, date: string, aircraft: string },
 *   onChange: (field: string, value: string) => void,
 *   onSubmit: (e: React.FormEvent) => void,
 *   loading: boolean,
 * }} props
 */
export default function VoucherForm({ form, onChange, onSubmit, loading }) {
  return (
    <form onSubmit={onSubmit} className="space-y-4">
      <div>
        <label htmlFor="name" className="block text-sm font-medium text-slate-700">
          Crew Name
        </label>
        <input
          id="name"
          type="text"
          required
          value={form.name}
          onChange={(e) => onChange('name', e.target.value)}
          placeholder="e.g. Sarah"
          className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-airline-500 focus:ring-airline-500"
        />
      </div>

      <div>
        <label htmlFor="crewId" className="block text-sm font-medium text-slate-700">
          Crew ID
        </label>
        <input
          id="crewId"
          type="text"
          required
          value={form.id}
          onChange={(e) => onChange('id', e.target.value)}
          placeholder="e.g. 98123"
          className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-airline-500 focus:ring-airline-500"
        />
      </div>

      <div>
        <label htmlFor="flightNumber" className="block text-sm font-medium text-slate-700">
          Flight Number
        </label>
        <input
          id="flightNumber"
          type="text"
          required
          value={form.flightNumber}
          onChange={(e) => onChange('flightNumber', e.target.value.toUpperCase())}
          placeholder="e.g. GA102"
          className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-airline-500 focus:ring-airline-500"
        />
      </div>

      <div>
        <label htmlFor="date" className="block text-sm font-medium text-slate-700">
          Flight Date
        </label>
        <input
          id="date"
          type="date"
          required
          value={form.date}
          onChange={(e) => onChange('date', e.target.value)}
          className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-airline-500 focus:ring-airline-500"
        />
      </div>

      <div>
        <label htmlFor="aircraft" className="block text-sm font-medium text-slate-700">
          Aircraft Type
        </label>
        <select
          id="aircraft"
          required
          value={form.aircraft}
          onChange={(e) => onChange('aircraft', e.target.value)}
          className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-airline-500 focus:ring-airline-500 bg-white"
        >
          <option value="" disabled>
            Select aircraft type
          </option>
          {AIRCRAFT_TYPES.map((type) => (
            <option key={type} value={type}>
              {type}
            </option>
          ))}
        </select>
      </div>

      <button
        type="submit"
        disabled={loading}
        className="w-full inline-flex items-center justify-center rounded-lg bg-airline-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-airline-600 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {loading ? (
          <>
            <svg
              className="mr-2 h-4 w-4 animate-spin"
              viewBox="0 0 24 24"
              fill="none"
            >
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
              <path
                className="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
              />
            </svg>
            Generating...
          </>
        ) : (
          'Generate Voucher Seats'
        )}
      </button>
    </form>
  )
}
