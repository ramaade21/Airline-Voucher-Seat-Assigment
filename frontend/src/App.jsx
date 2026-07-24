import React, { useState, useCallback } from 'react'
import VoucherForm from './components/VoucherForm.jsx'
import SeatResult from './components/SeatResult.jsx'
import Notification from './components/Notification.jsx'
import { checkVoucherExists, generateVoucherSeats } from './api/axios.js'

const INITIAL_FORM = {
  name: '',
  id: '',
  flightNumber: '',
  date: '',
  aircraft: '',
}

function extractErrorMessage(error, fallback) {
  const data = error?.response?.data
  if (data?.errors) {
    const firstField = Object.values(data.errors)[0]
    if (Array.isArray(firstField) && firstField.length > 0) return firstField[0]
  }
  if (data?.message) return data.message
  if (error?.message) return error.message
  return fallback
}

export default function App() {
  const [form, setForm] = useState(INITIAL_FORM)
  const [loading, setLoading] = useState(false)
  const [seats, setSeats] = useState(null)
  const [generatedFor, setGeneratedFor] = useState(null)
  const [notification, setNotification] = useState({ type: null, message: '' })

  const handleChange = useCallback((field, value) => {
    setForm((prev) => ({ ...prev, [field]: value }))
  }, [])

  const clearNotification = useCallback(() => {
    setNotification({ type: null, message: '' })
  }, [])

  const handleSubmit = useCallback(
    async (e) => {
      e.preventDefault()
      clearNotification()
      setSeats(null)
      setLoading(true)

      try {
        // Step 1: check whether vouchers already exist for this flight/date.
        const { exists } = await checkVoucherExists({
          flightNumber: form.flightNumber,
          date: form.date,
        })

        if (exists) {
          setNotification({
            type: 'error',
            message: `Vouchers have already been generated for flight ${form.flightNumber} on ${form.date}.`,
          })
          setLoading(false)
          return
        }

        // Step 2: generate exactly three unique random voucher seats.
        const result = await generateVoucherSeats({
          name: form.name,
          id: form.id,
          flightNumber: form.flightNumber,
          date: form.date,
          aircraft: form.aircraft,
        })

        setSeats(result.seats)
        setGeneratedFor({ flightNumber: form.flightNumber, date: form.date })
        setNotification({
          type: 'success',
          message: `Successfully generated 3 voucher seats for flight ${form.flightNumber}.`,
        })
      } catch (error) {
        const status = error?.response?.status
        const fallback =
          status === 409
            ? 'Vouchers already exist for this flight and date.'
            : 'Something went wrong. Please try again.'
        setNotification({ type: 'error', message: extractErrorMessage(error, fallback) })
      } finally {
        setLoading(false)
      }
    },
    [form, clearNotification]
  )

  return (
    <div className="min-h-screen flex flex-col items-center px-4 py-10 sm:py-16">
      <Notification
        type={notification.type}
        message={notification.message}
        onClose={clearNotification}
      />

      <div className="w-full max-w-md">
        <header className="mb-6 text-center">
          <h1 className="text-2xl font-bold text-slate-900">
            ✈️ Airline Voucher Seat Assignment
          </h1>
          <p className="mt-1 text-sm text-slate-500">
            Generate three unique random voucher seats for a flight.
          </p>
        </header>

        <div className="rounded-2xl bg-white p-6 shadow-md border border-slate-100">
          <VoucherForm
            form={form}
            onChange={handleChange}
            onSubmit={handleSubmit}
            loading={loading}
          />

          {seats && (
            <SeatResult
              seats={seats}
              flightNumber={generatedFor?.flightNumber}
              date={generatedFor?.date}
            />
          )}
        </div>

        <footer className="mt-6 text-center text-xs text-slate-400">
          Airline Voucher Seat Assignment · Laravel 11 + React 18
        </footer>
      </div>
    </div>
  )
}
