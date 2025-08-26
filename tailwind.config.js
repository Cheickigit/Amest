import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,ts,vue}',
  ],
  theme: {
    extend: {
      colors: {
        bk: { night: '#151e27', gold: '#dcc176', off: '#fdfdfe' },
      },
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
        display: ['Teko', 'Inter', ...defaultTheme.fontFamily.sans],
      },
      boxShadow: {
        gold: '0 4px 24px rgba(220,193,118,.25)',
        goldy: '0 16px 40px -16px rgba(220,193,118,.45)',
      },
      borderRadius: { xl2: '1rem', xl3: '1.5rem' },
    },
  },
  plugins: [forms, typography],
}
