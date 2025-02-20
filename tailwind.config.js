import { skeleton } from '@skeletonlabs/tw-plugin';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';
import { myCustomTheme } from './my-custom-theme'

const __dirname = dirname(fileURLToPath(import.meta.url));

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'selector',
  content: [
    "./**/*.{html,js,jsx,ts,tsx,php}",
    "./index.html",
    join(dirname(fileURLToPath(import.meta.url)), 'node_modules/@skeletonlabs/skeleton/**/*.{html,js,svelte}')
  ],
  theme: {
    extend: {},
  },
  plugins: [
    skeleton({
      themes: {
        custom: [
          myCustomTheme
        ],
        preset: [
          {
            name: 'skeleton',
            enhancements: true
          },
          {
            name: 'modern',
            enhancements: true
          },
          {
            name: 'crimson',
            enhancements: true
          },
          {
            name: 'gold-nouveau',
            enhancements: true
          },
          {
            name: 'hamlindigo',
            enhancements: true
          },
          {
            name: 'vintage',
            enhancements: true
          },
          {
            name: 'seafoam',
            enhancements: true
          },
          {
            name: 'rocket',
            enhancements: true
          },
          {
            name: 'sahara',
            enhancements: true
          }
        ]
      }
    })
  ],
}