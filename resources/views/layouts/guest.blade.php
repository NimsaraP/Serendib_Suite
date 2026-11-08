<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Authentication' }} – SerendibSuite</title>

    {{-- Fonts: Inter (UI) & Platypi / Playfair Display (Editorial Display) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Platypi:ital,wght@0,500;0,600;0,700;1,500&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">

    {{-- Styles --}}
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    {{-- Tailwind CSS CDN Fallback with Design System Theme Tokens --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Platypi', 'Playfair Display', 'Georgia', 'serif'],
                    },
                    colors: {
                        'warm-canvas': '#F5F1E8',
                        'warm-card': '#FFFDF8',
                        'warm-header': '#FFFDFA',
                        'warm-border': '#E7E1D7',
                        'input-border': '#E6DED2',
                        'brand-dark': '#262626',
                        'brand-muted': '#737373',
                        'client-orange': '#F5864E',
                        'client-tint': '#FFE3CF',
                        'photographer-purple': '#6656FF',
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js for lightweight UI interactivity (e.g. password visibility toggle) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .font-display { font-family: 'Platypi', 'Playfair Display', Georgia, serif; }
    </style>
</head>
<body class="min-h-full bg-[#F5F1E8] text-[#262626] flex flex-col antialiased">

    {{-- Application Top Header Bar (Figma: 1440x72, #FFFDFA, border #E7E1D7) --}}
    <header class="w-full h-[72px] bg-[#FFFDFA] border-b border-[#E7E1D7] px-6 sm:px-12 flex items-center justify-between shrink-0 z-20">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-3.5 group">
            <div class="w-9 h-9 rounded-lg border border-[#D9C8B3] overflow-hidden bg-white shadow-xs flex items-center justify-center shrink-0">
                <img src="{{ asset('images/auth/logo.png') }}" alt="SerendibSuite Logo" class="w-full h-full object-cover">
            </div>
            <span class="text-base font-semibold tracking-tight text-[#262626] group-hover:text-[#4A3D32] transition-colors">
                SerendibSuite
            </span>
        </a>

        <div class="text-xs sm:text-sm text-[#737373] font-normal">
            {{ $portalSubtitle ?? 'Client & Photographer Portal' }}
        </div>
    </header>

    {{-- Main Workspace Container --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-8 lg:p-14">
        {{ $slot }}
    </main>

</body>
</html>

