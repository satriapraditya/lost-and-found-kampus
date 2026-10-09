// Warna token dari variabel CSS, contoh token("primary") → rgb(var(--rgb-primary) / <alpha-value>)
const token = (name) => `rgb(var(--rgb-${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
  // Varian dark: aktif saat <html data-theme="dark"> (lihat layout admin)
  darkMode: ["selector", '[data-theme="dark"]'],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      // Nilai warnanya ada di resources/css/app.css (:root = terang,
      // [data-theme="dark"] = gelap). Di sini hanya merujuk ke variabelnya.
      colors: {
        primary: {
          DEFAULT: token("primary"),
          hover: token("primary-hover"),
        },
        text: {
          DEFAULT: token("text"),
          secondary: token("text-secondary"),
          muted: token("text-muted"),
        },
        surface: {
          DEFAULT: token("surface"),
          white: token("surface-white"),
          muted: token("surface-muted"),
        },
        border: {
          DEFAULT: token("border"),
        },
        success: {
          bg: token("success-bg"),
          text: token("success-text"),
        },
        danger: {
          bg: token("danger-bg"),
          text: token("danger-text"),
        },
        warning: {
          bg: token("warning-bg"),
          text: token("warning-text"),
        },
        info: {
          bg: token("info-bg"),
          text: token("info-text"),
        },
      },
      fontSize: {
        hero: "36px",
        h2: "20px",
        h3: "18px",
        "card-title": "16px",
        body: "15px",
        label: "14px",
        small: "13px",
        caption: "12px",
      },
      fontFamily: {
        sans: ["Inter", "ui-sans-serif", "system-ui", "sans-serif"],
      },
      borderRadius: {
        sm: "8px",
        md: "12px",
        lg: "16px",
        full: "999px",
      },
      boxShadow: {
        card: "var(--shadow-card)",
      },
      spacing: {
        4.5: "18px",
      },
    },
  },
  plugins: [],
};
