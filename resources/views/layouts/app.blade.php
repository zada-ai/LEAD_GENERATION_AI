<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Lead Automation CRM' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <x-navbar />
            
            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <div class="container mx-auto px-4 py-8 md:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if (session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif
</body>
</html>
