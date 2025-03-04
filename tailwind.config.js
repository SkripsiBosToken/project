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
                primary: {
                    DEFAULT: "#588157", // Warna utama
                    secondary: "#14532D",
                    gray: "#6F6F6F",
                    gray_light: "#F0F0F0",
                    light: "#3B82F6",
                    dark: "#1E3A8A",
                },
                secondary: {
                    DEFAULT: "#588157",
                    secondary: "#14532D",
                    gray: "#6F6F6F",
                    gray_light: "#F0F0F0",
                    light: "#FBBF24",
                    dark: "#B45309",
                },
                neutral: {
                    DEFAULT: "#588157",
                    secondary: "#14532D",
                    gray: "#6F6F6F",
                    gray_light: "#F0F0F0",
                    light: "#6B7280",
                    dark: "#1F2937",
                },
            },
        },
    },
    plugins: [],
};
