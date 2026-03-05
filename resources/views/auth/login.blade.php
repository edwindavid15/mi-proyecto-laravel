

<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inicio de Sesión StyleRadar</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f97316",
                        "background-dark": "#0f0f0f",
                        "neutral-dark": "#27272a",
                        "border-dark": "#4c4d52",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    },
                },
            },
        }
    </script>

    
</head>



<div class="absolute top-0 left-0 w-full px-8 py-6">


<header class="fixed top-0 left-0 w-full z-50  border-white/10 bg-[#141414]  border-b border-white/5 px-8 py-4">


    <div class="flex justify-between items-center">
        
        <!-- IZQUIERDA -->
        <div class="flex items-center gap-3">

            <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-white"
                      style="font-variation-settings:'FILL' 1;">
                    content_cut
                </span>
            </div>

            <div class="inline-block">
                <h1 class="text-2xl font-black tracking-tighter text-white ">
                    StyleRadar
                </h1>
                <div class="h-1 w-full bg-primary mt-1 rounded-full"></div>
            </div>

        </div>
        
        

        <!-- DERECHA -->
        <div class="flex items-center gap-6">
            <a href="{{ url()->previous() }}"
               class="bg-primary/20 text-primary px-5 py-2 rounded-full text-sm font-bold hover:bg-primary hover:text-white transition">
                Regresar
            </a>
        </div>
        

    </div>

    </header>


</div>





<body class="bg-background-dark text-slate-100 min-h-screen flex items-center justify-center">



<div class="w-full max-w-[480px] flex flex-col gap-8 bg-neutral-dark p-10 rounded-2xl shadow-2xl border border-border-dark">



    <div class="flex flex-col gap-2 text-center">
        <h1 class="text-white text-4xl font-black">Bienvenido de nuevo</h1>
        <p class="text-slate-400 text-base font-medium">
        
        </p>
    </div>

    {{-- MENSAJE DE ESTADO --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
        @csrf

        {{-- EMAIL --}}
        <div class="flex flex-col gap-2">
            <label class="text-slate-200 text-sm font-bold uppercase tracking-wider">
                Correo electrónico
            </label>

            <input 
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full rounded-xl border border-border-dark bg-background-dark text-white focus:ring-2 focus:ring-primary focus:border-primary h-14 px-4 text-base font-medium"
                placeholder="barbero@styleradar.com"
            />

            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- PASSWORD --}}
        <div class="flex flex-col gap-2">
            <div class="flex justify-between items-center">
                <label class="text-slate-200 text-sm font-bold uppercase tracking-wider">
                    Contraseña
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-primary text-sm font-bold hover:underline">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <input 
                name="password"
                type="password"
                required
                class="w-full rounded-xl border border-border-dark bg-background-dark text-white focus:ring-2 focus:ring-primary focus:border-primary h-14 px-4 text-base font-medium"
                placeholder="••••••••"
            />

            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- REMEMBER --}}
        <div class="flex items-center gap-3">
            <input type="checkbox" name="remember" class="rounded">
            <span class="text-sm text-slate-400">Recordarme</span>
        </div>

        {{-- BOTÓN LOGIN --}}
        <button 
            type="submit"
            class="w-full h-14 bg-primary text-white rounded-xl font-black text-lg hover:bg-orange-600 hover:scale-[1.02] shadow-lg shadow-primary/30 transition-all uppercase tracking-wide">
            Iniciar Sesión
        </button>

        {{-- REGISTER LINK --}}
        <p class="text-center text-slate-400 text-sm mt-4 font-medium">
            ¿No tienes una cuenta?
            <a href="{{ route('register') }}" 
               class="text-primary font-black hover:underline">
                Regístrate gratis
            </a>
        </p>

    </form>

</div>


</body>
</html>

