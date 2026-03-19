<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental PS Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @livewireStyles
</head>
<body class="bg-gray-50 pb-24">
    
    {{ $slot }} 
    
    @auth
    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-40 rounded-t-3xl transition-all">
        <div class="flex justify-around items-center h-20 max-w-md mx-auto">
            <a href="{{ route('kasir') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('kasir') ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                <span class="text-2xl mb-1">{{ request()->routeIs('kasir') ? '🎮' : '🕹️' }}</span>
                <span class="text-[10px] font-bold tracking-wider">KASIR</span>
            </a>
            
            <a href="{{ route('laporan') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('laporan') ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                <span class="text-2xl mb-1">{{ request()->routeIs('laporan') ? '📊' : '📉' }}</span>
                <span class="text-[10px] font-bold tracking-wider">LAPORAN</span>
            </a>
            
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('pengaturan') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('pengaturan') ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                <span class="text-2xl mb-1">{{ request()->routeIs('pengaturan') ? '⚙️' : '🔧' }}</span>
                <span class="text-[10px] font-bold tracking-wider">SETTING</span>
            </a>
            @endif

            <a href="{{ route('profil') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('profil') ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                <span class="text-2xl mb-1">{{ request()->routeIs('profil') ? '👤' : '🧑‍💼' }}</span>
                <span class="text-[10px] font-bold tracking-wider">PROFIL</span>
            </a>
        </div>
    </div>
    @endauth

    @livewireScripts
</body>
</html>