import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        black: '#474747',
        yellow: {
          100: '#fff7d3',
        },
        primary: {
          50: '#eefbf5',
          100: '#d6f5e4',
          200: '#b0eacd',
          300: '#7cd9b0',
          default: '#44c08e',
          500: '#23a675',
          600: '#16855f',
          700: '#116b4e',
          800: '#10553f',
          900: '#0e4635',
          950: '#07271e',
        },
        red: {
          500: '#FF4747',
        },
      },
    },
  },
  plugins: [forms],
}
