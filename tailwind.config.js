/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php', // Tambahkan baris ini
        './storage/framework/views/*.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    DEFAULT: '#7132f5', // Kraken Purple
                    dark: '#5741d8',    // Purple Dark
                    deep: '#5b1ecf',    // Purple Deep
                    subtle: 'rgba(133,91,251,0.16)',
                },
                neutral: {
                    black: '#101114',   // Near Black
                    gray: '#686b82',    // Cool Gray
                    silver: '#9497a9',  // Silver Blue
                    border: '#dedee5',  // Border Gray
                },
                semantic: {
                    green: '#149e61',
                    greenbg: 'rgba(20,158,97,0.16)',
                    greentext: '#026b3f',
                    red: '#e53935',
                }
            },
            fontFamily: {
                sans: ['"IBM Plex Sans"', 'Helvetica', 'Arial', 'sans-serif'],
                display: ['"IBM Plex Sans"', 'Helvetica', 'Arial', 'sans-serif'],
            },
            boxShadow: {
                'whisper': '0px 4px 24px rgba(0,0,0,0.03)',
                'micro': '0px 1px 4px rgba(16,24,40,0.04)',
            }
        }
    },
    plugins: [],
}