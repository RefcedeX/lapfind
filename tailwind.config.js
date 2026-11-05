import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', '"DM Mono"', 'SF Mono', 'Consolas', ...defaultTheme.fontFamily.mono],
            },
            spacing: {
                '4.5': '1.125rem',
            },
            colors: {
                slate: {
                    750: '#283548',
                    850: '#151e2e',
                },
                bg: 'var(--bg)',
                surface: 'var(--surface)',
                'surface-2': 'var(--surface-2)',
                ink: 'var(--ink)',
                muted: 'var(--muted)',
                line: 'var(--line)',
                accent: {
                    DEFAULT: 'var(--accent)',
                    tint: 'var(--accent-tint)',
                    subtle: 'var(--accent-tint)',
                    hover: 'var(--accent-hover)',
                },
                danger: 'var(--danger)',

                // Backward-compatibility aliases
                canvas: 'var(--bg)',
                'surface-subtle': 'var(--surface-2)',
                'ink-muted': 'var(--muted)',
                'line-subtle': 'var(--line)',
            },
            borderRadius: {
                DEFAULT: '8px',
                sm: '4px',
                md: '8px',
                lg: '12px',
                xl: '16px',
                '2xl': '20px',
                card: '16px',
                input: '6px',
                button: '9999px',
            },
            maxWidth: {
                container: '1280px',
            },
        },
    },

    plugins: [forms],
};
