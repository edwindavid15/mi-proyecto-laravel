<!DOCTYPE html>
<html class="dark">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f97316", // Bright orange
                        "accent": "#ffffff", // Clean white accent
                        "background-dark": "#0a0c10",
                    },
                    fontFamily: {
                        "display": ["Poppins", "sans-serif"],
                        "serif": ["Poppins", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
    .bg-barber-hero {
      background-image: linear-gradient(to right, rgba(10, 12, 16, 0.4), rgba(10, 12, 16, 0.7)), url('https://images.unsplash.com/photo-1599351431202-1e0f0137899a?auto=format&fit=crop&q=80&w=2074');
      background-size: cover;
      background-position: center;
    }
    .glass-effect {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(16px);
      border: 2px solid rgba(255, 255, 255, 0.1);
    }
  }
</style>
</head>

<body class="bg-background-dark font-display text-slate-100 antialiased">
    <div class="relative flex h-screen w-full flex-col md:flex-row overflow-hidden">
        <div class="relative hidden h-full w-1/2 md:block">
            <div class="absolute inset-0 bg-barber-hero"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-background-dark/50 to-background-dark"></div>
            <div class="absolute bottom-16 left-16 max-w-lg">
                <div class="flex items-center gap-3 mb-6">
                    
                    <span class="text-primary text-base font-black tracking-[0.3em] uppercase" style=""><br></span>
                </div>
                <h2 class="text-6xl font-serif font-black leading-tight text-white mb-6 drop-shadow-2xl" style=""><br></h2>
            </div>
        </div>
        <div class="relative flex h-full w-full flex-col items-center justify-center px-8 md:w-1/2 lg:px-24 bg-background-dark z-10 shadow-[-20px_0_50px_rgba(0,0,0,0.5)]">
            <div class="absolute inset-0 block md:hidden bg-barber-hero opacity-20"></div>
            <div class="absolute inset-0 block md:hidden bg-gradient-to-b from-background-dark via-background-dark/90 to-background-dark"></div>
            <div class="relative z-10 flex w-full max-w-[480px] flex-col gap-14">
                <div class="flex flex-col items-center md:items-start gap-6">
                    <div class="flex items-center gap-5">
                        <div class="flex size-16 items-center justify-center rounded-3xl bg-primary shadow-[0_0_30px_rgba(249,115,22,0.4)]">
                            <span class="material-symbols-outlined text-5xl text-white" style="font-variation-settings: &quot;FILL&quot; 1;">content_cut</span>
                        </div>
                        <div>
                            <h1 class="text-5xl font-black tracking-tighter text-white" style="">StyleRadar</h1>
                            <div class="h-1.5 w-full bg-primary mt-2 rounded-full shadow-[0_0_15px_rgba(249,115,22,0.6)]"></div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-8">
                        <p class="text-center md:text-left text-3xl font-black text-white" style="">El futuro de la barbería</p>
                        <p class="text-center md:text-left text-slate-300 font-semibold text-lg leading-relaxed" style="">
                            El ecosistema de reservas y gestión más sofisticado del mundo para barberos de élite y su clientela.
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-10">
                   @guest
    <a href="{{ route('login') }}" 
       class="group relative flex h-20 w-full items-center justify-center overflow-hidden rounded-2xl bg-primary text-xl font-black text-white transition-all hover:scale-[1.03] active:scale-[0.97] shadow-[0_0_40px_rgba(249,115,22,0.3)] hover:shadow-[0_0_50px_rgba(249,115,22,0.5)] border-2 border-primary">
        <span class="relative z-10 flex items-center gap-3 tracking-wide">
            ingresar
            <span class="material-symbols-outlined text-3xl transition-transform group-hover:translate-x-2">
                arrow_forward
            </span>
        </span>
    </a>

    <a href="{{ route('register') }}" 
       class="glass-effect flex h-20 w-full items-center justify-center rounded-2xl text-xl font-black text-white transition-all hover:bg-white/10 hover:border-white/30 active:scale-[0.97] tracking-wide">
        registrarse
    </a>
@endguest
@auth
    <a href="{{ url('/dashboard') }}" 
       class="group relative flex h-20 w-full items-center justify-center overflow-hidden rounded-2xl bg-primary text-xl font-black text-white transition-all hover:scale-[1.03] active:scale-[0.97]">
        Ir al Dashboard
    </a>
@endauth
                
                    <div class="flex w-full flex-col md:flex-row items-center justify-between gap-6 border-t-2 border-white/5 pt-8">
                        <p class="text-sm font-bold text-slate-500" style="">© 2024 StyleRadar Technologies Inc.</p>
                        <div class="flex items-center gap-4 px-4 py-2 rounded-full bg-white/5 border border-white/10">
                            <span class="size-2.5 rounded-full bg-primary shadow-[0_0_10px_rgba(249,115,22,0.8)] animate-pulse"></span>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pointer-events-none absolute inset-0 overflow-hidden opacity-50 z-0">
            <div class="absolute -right-32 -top-32 size-[600px] rounded-full bg-primary/20 blur-[150px]"></div>
            <div class="absolute -bottom-32 left-1/4 size-[500px] rounded-full bg-white/5 blur-[120px]"></div>
        </div>
    </div>

</body>

</html>