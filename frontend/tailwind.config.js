/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{js,jsx}',
  ],
  theme: {
    extend: {
      colors: {
        airline: {
          50: '#eef5ff',
          100: '#d9e8ff',
          500: '#1d4ed8',
          600: '#1741b8',
          700: '#123491',
          900: '#0b2158',
        },
      },
    },
  },
  plugins: [],
}
