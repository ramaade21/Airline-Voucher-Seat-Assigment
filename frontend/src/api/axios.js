import axios from 'axios'

const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

const apiClient = axios.create({
  baseURL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  timeout: 15000,
})

/**
 * Check whether vouchers already exist for a flight number + date.
 * @param {{ flightNumber: string, date: string }} payload
 * @returns {Promise<{ exists: boolean }>}
 */
export async function checkVoucherExists(payload) {
  const { data } = await apiClient.post('/check', payload)
  return data
}

/**
 * Generate exactly three unique random voucher seats.
 * @param {{ name: string, id: string, flightNumber: string, date: string, aircraft: string }} payload
 * @returns {Promise<{ success: boolean, seats: string[] }>}
 */
export async function generateVoucherSeats(payload) {
  const { data } = await apiClient.post('/generate', payload)
  return data
}

export default apiClient
