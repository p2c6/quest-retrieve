/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js}'],
  theme: {
    extend: {
      colors: {
        'primary': '#1E3A8A',
        'secondary': '#FFA726',
        'tertiary': '#A4A4A4'
      },
    },
  },
  plugins: [],
}

