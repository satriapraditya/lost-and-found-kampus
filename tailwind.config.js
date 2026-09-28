/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: "#7c3aed",
          hover: "#6d28d9",
        },
        text: {
          DEFAULT: "#111827",
          secondary: "#4b5563",
          muted: "#9ca3af",
        },
        surface: {
          DEFAULT: "#f9fafb",
          white: "#ffffff",
          muted: "#f3f4f6",
        },
        border: {
          DEFAULT: "#e5e7eb",
        },
        success: {
          bg: "#d1fae5",
          text: "#065f46",
        },
        danger: {
          bg: "#fee2e2",
          text: "#991b1b",
        },
        warning: {
          bg: "#fef3c7",
          text: "#92400e",
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
        card: "0px 4px 12px 0px rgba(0,0,0,0.03)",
      },
      spacing: {
        4.5: "18px",
      },
    },
  },
  plugins: [],
};
