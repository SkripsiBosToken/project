/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                poppins: ["Poppins", "sans-serif"],
            },
            colors: {
                // Skala warna merek. Kunci non-numerik (DEFAULT, secondary,
                // gray, gray_light, danger, dark, light) dipertahankan agar
                // kelas lama seperti `text-primary-gray` tetap berfungsi.
                primary: {
                    50: "#FDF3F3",
                    100: "#FBE5E5",
                    200: "#F7CFCF",
                    300: "#F0ADAD",
                    400: "#E57C7C",
                    500: "#D65252",
                    600: "#C03535",
                    700: "#A12828",
                    800: "#860000",
                    900: "#6E0A0A",
                    950: "#3C0303",
                    DEFAULT: "#860000",
                    secondary: "#550A10",
                    gray: "#6F6F6F",
                    gray_light: "#F0F0F0",
                    light: "#3B82F6",
                    dark: "#344E41",
                    danger: "#B00000",
                },
                secondary: {
                    DEFAULT: "#860000",
                    secondary: "#550A10",
                    gray: "#6F6F6F",
                    gray_light: "#F0F0F0",
                    light: "#FBBF24",
                    dark: "#344E41",
                    danger: "#B00000",
                },
                neutral: {
                    DEFAULT: "#860000",
                    secondary: "#550A10",
                    gray: "#6F6F6F",
                    gray_light: "#F0F0F0",
                    light: "#6B7280",
                    dark: "#344E41",
                    danger: "#B00000",
                },
                // Warna semantik untuk status pesanan & notifikasi.
                success: {
                    50: "#F0FDF4",
                    100: "#DCFCE7",
                    600: "#16A34A",
                    700: "#15803D",
                },
                warning: {
                    50: "#FFFBEB",
                    100: "#FEF3C7",
                    600: "#D97706",
                    700: "#B45309",
                },
                info: {
                    50: "#EFF6FF",
                    100: "#DBEAFE",
                    600: "#2563EB",
                    700: "#1D4ED8",
                },
            },
            boxShadow: {
                card: "0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)",
                "card-hover": "0 10px 25px -5px rgba(0,0,0,0.10), 0 8px 10px -6px rgba(0,0,0,0.05)",
            },
            borderRadius: {
                xl2: "1rem",
            },
            keyframes: {
                "fade-in-up": {
                    "0%": { opacity: "0", transform: "translateY(8px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
                shimmer: {
                    "100%": { transform: "translateX(100%)" },
                },
            },
            animation: {
                "fade-in-up": "fade-in-up .3s ease-out both",
                shimmer: "shimmer 1.6s infinite",
            },
        },
    },
    plugins: [
        require('tailwind-bootstrap-grid')({
            containerMaxWidths: { sm: '540px', md: '720px', lg: '960px', xl: '1140px' }
        })
    ],
};
