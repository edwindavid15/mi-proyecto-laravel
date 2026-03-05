<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registro | StyleRadar</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#f97316",
                        darkbg: "#0f0f0f",
                        card: "#1c1c1c"
                    },
                    fontFamily: {
                        display: ["Manrope", "sans-serif"]
                    }
                },
            },
        }
    </script>

    <style>
        body { font-family: 'Manrope', sans-serif; }
    </style>
</head>

<body class="bg-darkbg text-white font-display">

<!-- NAVBAR -->
<div class="w-full border-b border-white/10 bg-[#141414]">
    <div class="flex justify-between items-center px-10 py-5">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-white">content_cut</span>
            </div>
            <div class="inline-block">
                <h1 class="text-2xl font-black tracking-tighter text-white ">
                    StyleRadar
                </h1>
                <div class="h-1 w-full bg-primary mt-1 rounded-full"></div>
            </div>
            
        </div>

        <div class="flex items-center gap-6">
            <a href="{{ route('login') }}"
               class="bg-primary/20 text-primary px-5 py-2 rounded-full text-sm font-bold hover:bg-primary hover:text-white transition">
                Iniciar Sesión
            </a>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="flex justify-center py-16 px-4">
    <div class="w-full max-w-xl">

        <h1 class="text-4xl font-black mb-2">Crea tu Cuenta</h1>
        <p class="text-slate-400 mb-10">Únete a la comunidad profesional de estilismo premium.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- CAJA SOLO FORMULARIO -->
            <div class="bg-gradient-to-br from-[#1a1a1a] to-[#141414] 
                        border border-slate-800 
                        rounded-2xl 
                        p-8 
                        space-y-6 
                        shadow-xl">

                <!-- Nombre -->
                <div>
                    <label class="text-xs font-bold uppercase text-slate-400">Nombre Completo</label>
                    <input name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Ingresa tu nombre completo"
                           class="w-full h-12 mt-2 px-4 rounded-lg bg-[#0f0f0f] border border-slate-700 focus:ring-2 focus:ring-primary focus:border-primary">

                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="text-xs font-bold uppercase text-slate-400">Correo Electrónico</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="nombre@ejemplo.com"
                           class="w-full h-12 mt-2 px-4 rounded-lg bg-[#0f0f0f] border border-slate-700 focus:ring-2 focus:ring-primary focus:border-primary">

                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="text-xs font-bold uppercase text-slate-400">Contraseña</label>
                    <input type="password"
                           name="password"
                           required
                           placeholder="Crea una contraseña segura"
                           class="w-full h-12 mt-2 px-4 rounded-lg bg-[#0f0f0f] border border-slate-700 focus:ring-2 focus:ring-primary focus:border-primary">

                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="text-xs font-bold uppercase text-slate-400">Confirmar Contraseña</label>
                    <input type="password"
                           name="password_confirmation"
                           required
                           placeholder="Repite tu contraseña"
                           class="w-full h-12 mt-2 px-4 rounded-lg bg-[#0f0f0f] border border-slate-700 focus:ring-2 focus:ring-primary focus:border-primary">
                </div>

            </div>

            <!-- SECCIÓN ROL FUERA DE LA CAJA -->
            <!-- FIX: value vacío, ninguno seleccionado por defecto -->
            <input type="hidden" name="role" id="roleInput" value="">

            <div class="mt-12">
                <p class="text-xs font-bold uppercase text-slate-400 mb-6">Elige tu rol</p>

                <div class="grid grid-cols-2 gap-6">

                    <!-- PELUQUERO -->
                    <div onclick="selectRole('peluquero')" id="peluqueroCard"
                         class="role-card border-2 border-slate-700
                                bg-card
                                rounded-2xl 
                                p-6 
                                text-center 
                                cursor-pointer 
                                transition 
                                hover:border-primary
                                hover:scale-105">

                        <div class="text-4xl mb-3 text-slate-400" id="peluqueroIcon">
                            <span class="material-symbols-outlined">content_cut</span>
                        </div>
                        <h3 class="font-bold text-lg">Peluquero</h3>
                        <p class="text-sm text-slate-400 mt-2">
                            Gestiona citas, clientes y tu portafolio profesional.
                        </p>
                        <!-- FIX: check oculto por defecto -->
                        <div id="peluqueroCheck"
                             class="w-6 h-6 mx-auto mt-5 rounded-full border-2 border-primary bg-primary hidden items-center justify-center transition">
                            <span class="material-symbols-outlined text-sm text-white">check</span>
                        </div>
                        <!-- Círculo vacío cuando NO está seleccionado -->
                        <div id="peluqueroEmpty"
                             class="w-6 h-6 mx-auto mt-5 rounded-full border-2 border-slate-600 flex items-center justify-center transition">
                        </div>
                    </div>

                    <!-- CLIENTE -->
                    <div onclick="selectRole('cliente')" id="clienteCard"
                         class="role-card border-2 border-slate-700 
                                bg-card 
                                rounded-2xl 
                                p-6 
                                text-center 
                                cursor-pointer 
                                transition 
                                hover:border-primary 
                                hover:scale-105">

                        <div class="text-4xl mb-3 text-slate-400" id="clienteIcon">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <h3 class="font-bold text-lg">Cliente</h3>
                        <p class="text-sm text-slate-400 mt-2">
                            Reserva sesiones fácilmente con profesionales.
                        </p>
                        <!-- FIX: ID corregido (era "peluqueroCheck") + oculto por defecto -->
                        <div id="clienteCheck"
                             class="w-6 h-6 mx-auto mt-5 rounded-full border-2 border-primary bg-primary hidden items-center justify-center transition">
                            <span class="material-symbols-outlined text-sm text-white">check</span>
                        </div>
                        <!-- Círculo vacío cuando NO está seleccionado -->
                        <div id="clienteEmpty"
                             class="w-6 h-6 mx-auto mt-5 rounded-full border-2 border-slate-600 flex items-center justify-center transition">
                        </div>
                    </div>

                </div>
            </div>

            <!-- BOTÓN -->
            <button type="submit"
                class="w-full h-14 mt-12 rounded-xl font-extrabold text-white 
                       bg-gradient-to-r from-orange-500 to-orange-600
                       shadow-[0_0_25px_rgba(249,115,22,0.6)]
                       hover:shadow-[0_0_35px_rgba(249,115,22,0.9)]
                       hover:scale-[1.02]
                       transition-all duration-300">
                Registrarse
            </button>

        </form>
    </div>
</div>

<script>
function selectRole(role) {
    document.getElementById('roleInput').value = role;

    const peluqueroCard  = document.getElementById('peluqueroCard');
    const clienteCard    = document.getElementById('clienteCard');
    const peluqueroCheck = document.getElementById('peluqueroCheck');
    const clienteCheck   = document.getElementById('clienteCheck');
    const peluqueroEmpty = document.getElementById('peluqueroEmpty');
    const clienteEmpty   = document.getElementById('clienteEmpty');
    const peluqueroIcon  = document.getElementById('peluqueroIcon');
    const clienteIcon    = document.getElementById('clienteIcon');

    if (role === 'peluquero') {
        // Activa peluquero
        peluqueroCard.classList.add('border-primary', 'bg-primary/10');
        peluqueroCard.classList.remove('border-slate-700', 'bg-card');
        peluqueroCheck.classList.remove('hidden');
        peluqueroCheck.classList.add('flex');
        peluqueroEmpty.classList.add('hidden');
        peluqueroIcon.classList.add('text-primary');
        peluqueroIcon.classList.remove('text-slate-400');

        // Desactiva cliente
        clienteCard.classList.remove('border-primary', 'bg-primary/10');
        clienteCard.classList.add('border-slate-700', 'bg-card');
        clienteCheck.classList.add('hidden');
        clienteCheck.classList.remove('flex');
        clienteEmpty.classList.remove('hidden');
        clienteIcon.classList.remove('text-primary');
        clienteIcon.classList.add('text-slate-400');

    } else {
        // Activa cliente
        clienteCard.classList.add('border-primary', 'bg-primary/10');
        clienteCard.classList.remove('border-slate-700', 'bg-card');
        clienteCheck.classList.remove('hidden');
        clienteCheck.classList.add('flex');
        clienteEmpty.classList.add('hidden');
        clienteIcon.classList.add('text-primary');
        clienteIcon.classList.remove('text-slate-400');

        // Desactiva peluquero
        peluqueroCard.classList.remove('border-primary', 'bg-primary/10');
        peluqueroCard.classList.add('border-slate-700', 'bg-card');
        peluqueroCheck.classList.add('hidden');
        peluqueroCheck.classList.remove('flex');
        peluqueroEmpty.classList.remove('hidden');
        peluqueroIcon.classList.remove('text-primary');
        peluqueroIcon.classList.add('text-slate-400');
    }
}
</script>

</body>
</html>