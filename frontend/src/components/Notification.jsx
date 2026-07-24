import React, { useEffect } from 'react'

/**
 * Simple dismissible toast notification.
 * @param {{ type: 'success' | 'error', message: string, onClose: () => void }} props
 */
export default function Notification({ type, message, onClose }) {
  useEffect(() => {
    const timer = setTimeout(onClose, 5000)
    return () => clearTimeout(timer)
  }, [onClose])

  if (!message) return null

  const isSuccess = type === 'success'

  return (
    <div
      role="alert"
      className={`fixed top-4 right-4 left-4 sm:left-auto sm:w-96 z-50 rounded-lg border p-4 shadow-lg transition-all
        ${isSuccess
          ? 'bg-emerald-50 border-emerald-300 text-emerald-800'
          : 'bg-red-50 border-red-300 text-red-800'}`}
    >
      <div className="flex items-start gap-3">
        <span className="text-lg leading-none">{isSuccess ? '✅' : '⚠️'}</span>
        <div className="flex-1 text-sm font-medium">{message}</div>
        <button
          type="button"
          onClick={onClose}
          className="text-current opacity-60 hover:opacity-100 text-sm leading-none"
          aria-label="Dismiss notification"
        >
          ✕
        </button>
      </div>
    </div>
  )
}
