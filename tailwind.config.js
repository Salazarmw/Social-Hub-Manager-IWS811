import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                twitter: { 50: "#e8f5fe", 100: "#d0ebff" },
                facebook: { 50: "#e7f3ff", 100: "#cce5ff" },
                linkedin: { 50: "#e8f4fd", 100: "#d0e8fa" },
                instagram: { 50: "#fdf2f8", 100: "#fce7f3" },
                pinterest: { 50: "#fef2f2", 100: "#fee2e2" },
            },
        },
    },

    plugins: [forms],
};
