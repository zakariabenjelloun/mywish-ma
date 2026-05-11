/** @type {import('tailwindcss').Config} */
module.exports = {
  // Where Tailwind looks for classes to compile
  content: [
    './public/**/*.{php,html,js}',
    './src/**/*.{php,html,js}',
  ],

  theme: {
    extend: {
      // ─────────────────────────────────────
      // COLORS — Brand identity
      // ─────────────────────────────────────
      colors: {
        // Primary peach palette
        'primary': {
          DEFAULT: '#EA580C',  // peach saturated — main CTAs
          'soft': '#FB923C',   // hover state
          'deep': '#C2410C',   // pressed state
        },
        // Gold accent — premium highlights
        'gold': {
          DEFAULT: '#FCD34D',
          'soft': '#FDE68A',
          'deep': '#F59E0B',
        },
        // Dark surface scale
        'bg-deep': '#0A0A0A',     // main background
        'bg-raised': '#18181B',   // cards
        'bg-high': '#27272A',     // hover / modals
        'bg-higher': '#3F3F46',   // borders, dividers

        // Text scale
        'text-primary': '#FAFAF9',    // headings, primary
        'text-secondary': '#A1A1AA',  // body text
        'text-muted': '#71717A',      // captions, helpers
        'text-disabled': '#52525B',   // disabled states

        // Semantic
        'success': '#10B981',
        'warning': '#F59E0B',
        'danger': '#EF4444',
        'info': '#3B82F6',
      },

      // ─────────────────────────────────────
      // TYPOGRAPHY — Fonts
      // ─────────────────────────────────────
      fontFamily: {
        'display': ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
        'body': ['"Inter"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
      },

      // ─────────────────────────────────────
      // SPACING — Custom widths/heights
      // ─────────────────────────────────────
      maxWidth: {
        '8xl': '1408px',
      },

      // ─────────────────────────────────────
      // BORDER RADIUS — Rounding
      // ─────────────────────────────────────
      borderRadius: {
        'pill': '9999px',
      },

      // ─────────────────────────────────────
      // ANIMATIONS — Smooth transitions
      // ─────────────────────────────────────
      transitionTimingFunction: {
        'mywish': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },

      // ─────────────────────────────────────
      // BOX SHADOWS — Glow effects
      // ─────────────────────────────────────
      boxShadow: {
        'glow-primary': '0 0 32px rgba(234, 88, 12, 0.3)',
        'glow-gold': '0 0 24px rgba(252, 211, 77, 0.25)',
        'card': '0 4px 24px rgba(0, 0, 0, 0.4)',
      },

      // ─────────────────────────────────────
      // BACKGROUND IMAGES — Gradients
      // ─────────────────────────────────────
      backgroundImage: {
        'gradient-primary': 'linear-gradient(135deg, #EA580C, #FB923C)',
        'gradient-gold': 'linear-gradient(135deg, #FCD34D, #F59E0B)',
        'gradient-hero': 'radial-gradient(ellipse 80% 50% at 50% -10%, rgba(234, 88, 12, 0.15), transparent 70%)',
      },
    },
  },

  plugins: [],
}