<aside class="w-64 bg-gray-900 text-white min-h-screen p-4 hidden md:block">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-blue-400">Lead CRM</h1>
        <p class="text-xs text-gray-400 mt-1">AI-Powered Lead Automation</p>
    </div>

    <nav class="space-y-1">
        <!-- Main Navigation -->
        <a 
            href="{{ route('dashboard') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">📊</span>
            <span class="text-sm">Dashboard</span>
        </a>

        <a 
            href="{{ route('leads.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('leads.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">👥</span>
            <span class="text-sm">Leads</span>
        </a>

        <a 
            href="{{ route('sources.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('sources.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">🔍</span>
            <span class="text-sm">Sources & Discovery</span>
        </a>

        <a 
            href="{{ route('campaigns.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('campaigns.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">🚀</span>
            <span class="text-sm">Campaigns</span>
        </a>

        <a 
            href="{{ route('inbox.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('inbox.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">📥</span>
            <span class="text-sm">Inbox</span>
        </a>

        <a 
            href="{{ route('email-accounts.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('email-accounts.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">✉️</span>
            <span class="text-sm">Email Accounts</span>
        </a>

        <a 
            href="{{ route('follow-ups.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('follow-ups.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">⏰</span>
            <span class="text-sm">Follow-ups</span>
        </a>

        <a 
            href="{{ route('ai-automation.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('ai-automation.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">🤖</span>
            <span class="text-sm">AI Automation</span>
        </a>

        <a 
            href="{{ route('analytics.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('analytics.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">📈</span>
            <span class="text-sm">Analytics</span>
        </a>

        <div class="my-4 border-t border-gray-800"></div>

        <a 
            href="{{ route('settings.index') }}" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white font-medium' : 'text-gray-300 hover:bg-gray-800' }}"
        >
            <span class="text-lg">⚙️</span>
            <span class="text-sm">Settings</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="pt-4">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm text-red-300 hover:bg-red-900/30 rounded-lg transition-colors">
                <span class="text-lg">🚪</span>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</aside>

<!-- Mobile Navigation Header -->
<div class="md:hidden bg-gray-900 text-white p-4 flex justify-between items-center sticky top-0 z-50">
    <div class="flex items-center gap-2">
        <span class="text-xl font-bold text-blue-400">Lead CRM</span>
    </div>
    <button type="button" class="mobile-menu-btn text-gray-300 hover:text-white focus:outline-none text-2xl">
        ☰
    </button>
</div>

<!-- Mobile Navigation Drawer -->
<div class="mobile-menu hidden md:hidden bg-gray-900 text-white p-4 border-b border-gray-800">
    <nav class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>📊</span><span>Dashboard</span>
        </a>
        <a href="{{ route('leads.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>👥</span><span>Leads</span>
        </a>
        <a href="{{ route('sources.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>🔍</span><span>Sources & Discovery</span>
        </a>
        <a href="{{ route('campaigns.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>🚀</span><span>Campaigns</span>
        </a>
        <a href="{{ route('inbox.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>📥</span><span>Inbox</span>
        </a>
        <a href="{{ route('email-accounts.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>✉️</span><span>Email Accounts</span>
        </a>
        <a href="{{ route('follow-ups.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>⏰</span><span>Follow-ups</span>
        </a>
        <a href="{{ route('ai-automation.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>🤖</span><span>AI Automation</span>
        </a>
        <a href="{{ route('analytics.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>📈</span><span>Analytics</span>
        </a>
        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800">
            <span>⚙️</span><span>Settings</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button type="submit" class="w-full px-4 py-2 text-left text-sm bg-red-600 hover:bg-red-700 rounded-lg">
                🚪 Logout
            </button>
        </form>
    </nav>
</div>

<script>
    document.querySelector('.mobile-menu-btn')?.addEventListener('click', function() {
        document.querySelector('.mobile-menu')?.classList.toggle('hidden');
    });
</script>
