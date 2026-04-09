<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Loja Virtual')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(session('success'))
        <meta name="flash-success" content="{{ session('success') }}">
    @endif
    @if(session('error'))
        <meta name="flash-error" content="{{ session('error') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ===== BASE ===== */
        html, body {
            background-color: #02040a !important;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Video background */
        #bg-video {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            object-fit: cover !important;
            z-index: -10 !important;
            opacity: 0.7 !important;
            pointer-events: none !important;
        }

        /* Overlay effects */
        .bg-overlay {
            position: fixed !important;
            inset: 0 !important;
            z-index: -5 !important;
            background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.2) 40%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.5) 100%) !important;
            pointer-events: none !important;
        }

        /* Efeito bokeh suave - tons cinematográficos */
        .bg-bokeh {
            position: fixed !important;
            inset: 0 !important;
            z-index: -4 !important;
            background:
                radial-gradient(circle at 25% 25%, rgba(70, 130, 180, 0.08) 0%, transparent 30%),
                radial-gradient(circle at 75% 75%, rgba(138, 43, 226, 0.06) 0%, transparent 25%),
                radial-gradient(circle at 40% 60%, rgba(255, 140, 0, 0.05) 0%, transparent 35%),
                radial-gradient(circle at 60% 40%, rgba(220, 20, 60, 0.04) 0%, transparent 40%) !important;
            pointer-events: none !important;
            animation: bokeh-float 18s ease-in-out infinite !important;
        }

        @keyframes bokeh-float {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.03); }
        }

        /* Grão cinematográfico sutil */
        .bg-grain {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: -3 !important;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48ZmlsdGVyIGlkPSJmIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iMC44IiBudW1PY3RhdmVzPSIzIi8+PC9maWx0ZXI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsdGVyPSJ1cmwoI2YpIiBvcGFjaXR5PSIwLjAyIi8+PC9zdmc+') !important;
            opacity: 0.2 !important;
            pointer-events: none !important;
        }

        /* Conteúdo principal */
        #app {
            position: relative;
            z-index: 10;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-slate-300 antialiased">
    <!-- Background layers: vídeo fica atras de tudo -->
    <video
        autoplay
        muted
        loop
        playsinline
        id="bg-video"
        preload="auto"
    >
        <source src="/videos/12421669_3840_2160_30fps.mp4" type="video/mp4">
    </video>

    <div class="bg-overlay"></div>
    <div class="bg-bokeh"></div>
    <div class="bg-grain"></div>

    <div id="app">
        <App />
    </div>

    <div id="loading" class="fixed inset-0 flex items-center justify-center z-[1000] pointer-events-none" style="background: transparent;">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500 mx-auto mb-4"></div>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Initializing Nexus Core...</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            const loading = document.getElementById('loading');
            if (loading) loading.style.display = 'none';
            
            // Force video play (some browsers block autoplay)
            const video = document.getElementById('bg-video');
            if (video) {
                video.play().catch(e => console.log('Video autoplay blocked:', e));
            }
        });
    </script>
</body>
</html>