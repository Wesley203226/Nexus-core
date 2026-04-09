<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Core - Loja Virtual</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Flash Messages -->
    @if(session('success'))
        <meta name="flash-success" content="{{ session('success') }}">
    @endif
    @if(session('error'))
        <meta name="flash-error" content="{{ session('error') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background-color: #02040a; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased overflow-x-hidden bg-[#02040a]">
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

    <div id="app"></div>

    <!-- Minimal Loading fallback -->
    <div id="loading" class="fixed inset-0 bg-[#02040a] flex items-center justify-center z-[1000] transition-opacity duration-500">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin mx-auto mb-6"></div>
            <p class="text-slate-500 font-black uppercase tracking-[0.4em] text-[10px] animate-pulse">Initializing Core...</p>
        </div>
    </div>

    <script>
        // Smoothly hide loading after app mounts
        window.addEventListener('load', () => {
            const loading = document.getElementById('loading');
            if (loading) {
                loading.style.opacity = '0';
                setTimeout(() => {
                    loading.style.display = 'none';
                }, 500);
            }
            
            // Force video play (some browsers block autoplay)
            const video = document.getElementById('bg-video');
            if (video) {
                video.play().catch(e => console.log('Video autoplay blocked:', e));
            }
        });
    </script>
</body>
</html>