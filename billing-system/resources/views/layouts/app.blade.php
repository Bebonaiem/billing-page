<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BillingHub') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <style>
        :root {
            --bg-primary: 15 23 42;
            --bg-secondary: 30 41 59;
            --bg-tertiary: 51 65 85;
            --text-primary: 248 250 252;
            --text-secondary: 203 213 225;
            --text-tertiary: 148 163 184;
            --accent: 59 130 246;
            --accent-hover: 37 99 235;
            --border: 71 85 105;
            --card: 30 41 59;
            --success: 34 197 94;
            --warning: 251 146 60;
            --error: 239 68 68;
        }

        [data-theme="light"] {
            --bg-primary: 255 255 255;
            --bg-secondary: 249 250 251;
            --bg-tertiary: 243 244 246;
            --text-primary: 17 24 39;
            --text-secondary: 75 85 99;
            --text-tertiary: 107 114 128;
            --accent: 59 130 246;
            --accent-hover: 37 99 235;
            --border: 229 231 235;
            --card: 255 255 255;
            --success: 34 197 94;
            --warning: 251 146 60;
            --error: 239 68 68;
        }

        body {
            background: rgb(var(--bg-primary));
            color: rgb(var(--text-primary));
            transition: all 0.3s ease;
        }

        .bg-card {
            background: rgb(var(--card));
            border-color: rgb(var(--border));
        }

        .text-secondary {
            color: rgb(var(--text-secondary));
        }

        .text-tertiary {
            color: rgb(var(--text-tertiary));
        }

        .border-custom {
            border-color: rgb(var(--border));
        }

        .accent-bg {
            background: rgb(var(--accent));
        }

        .accent-bg-hover:hover {
            background: rgb(var(--accent-hover));
        }

        .accent-text {
            color: rgb(var(--accent));
        }

        .success-bg {
            background: rgb(var(--success));
        }

        .warning-bg {
            background: rgb(var(--warning));
        }

        .error-bg {
            background: rgb(var(--error));
        }

        .glass-effect {
            background: rgba(var(--card), 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(var(--border), 0.5);
        }

        .gradient-bg {
            background: linear-gradient(135deg, rgb(var(--accent)) 0%, rgb(var(--accent-hover)) 100%);
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .shadow-glow {
            box-shadow: 0 0 20px rgba(var(--accent), 0.3);
        }

        .shadow-glow-hover:hover {
            box-shadow: 0 0 30px rgba(var(--accent), 0.5);
        }
    </style>
</head>
<body class="font-sans antialiased transition-colors duration-300" data-theme="dark">
    <div id="app">
        <!-- Header Component -->
        @include('components.header')

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer Component -->
        @include('components.footer')
    </div>

    <!-- Theme Toggle Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update theme toggle icon
            const themeIcon = document.querySelector('[onclick="toggleTheme()"] svg');
            if (themeIcon) {
                if (newTheme === 'light') {
                    themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
                } else {
                    themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>';
                }
            }
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Update theme toggle icon based on saved theme
            const themeIcon = document.querySelector('[onclick="toggleTheme()"] svg');
            if (themeIcon && savedTheme === 'light') {
                themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
            }
        });
    </script>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
