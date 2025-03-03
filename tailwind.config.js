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
                test: ["Test", "sans-serif"],
            },
            colors: {
                primary: {
                    DEFAULT: "#588157", // Warna utama
                    light: "#3B82F6",
                    dark: "#1E3A8A",
                },
                secondary: {
                    DEFAULT: "#588157",
                    light: "#FBBF24",
                    dark: "#B45309",
                },
                neutral: {
                    DEFAULT: "#588157",
                    light: "#6B7280",
                    dark: "#1F2937",
                },
            },
        },
    },
    plugins: [],
};
