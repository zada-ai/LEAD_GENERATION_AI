@auth
<header class="hidden md:block bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h2>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>
</header>
@else
<div class="hidden"></div>
@endauth
