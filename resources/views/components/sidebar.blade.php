@auth
<aside class="hidden md:flex w-64 bg-gray-900 text-white flex-col">
    <!-- Logo -->
    <div class="px-6 py-8 border-b border-gray-800">
        <h1 class="text-2xl font-bold">Lead CRM</h1>
        <p class="text-xs text-gray-400 mt-1">Automation Platform</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <x-sidebar-link route="dashboard" icon="📊" label="Dashboard" />
        <x-sidebar-link route="leads.index" icon="👥" label="Leads" />
        
        <div class="my-4 border-t border-gray-800"></div>
        
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Coming Soon</div>
        <x-sidebar-link-disabled icon="📧" label="Campaigns" />
        <x-sidebar-link route="inbox.index" icon="📬" label="Inbox" />
        <x-sidebar-link route="email-accounts.index" icon="📧" label="Email Accounts" />
        <x-sidebar-link-disabled icon="🤖" label="AI Automation" />
        <x-sidebar-link-disabled icon="📍" label="Sources" />
        <x-sidebar-link-disabled icon="📈" label="Analytics" />
        
        <div class="my-4 border-t border-gray-800"></div>
        
        <x-sidebar-link route="profile.edit" icon="⚙️" label="Settings" />
    </nav>

    <!-- Footer -->
    <div class="px-4 py-6 border-t border-gray-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-800 rounded-lg transition-colors">
                🚪 Logout
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Menu Button -->
<div class="md:hidden fixed top-0 left-0 right-0 bg-gray-900 text-white px-4 py-4 flex items-center justify-between z-50">
    <h1 class="text-xl font-bold">Lead CRM</h1>
    <button class="mobile-menu-btn text-2xl">☰</button>
</div>

<!-- Mobile Menu -->
<div class="mobile-menu hidden fixed inset-0 bg-gray-900 text-white z-40 pt-20 overflow-y-auto md:hidden">
    <nav class="px-4 py-6 space-y-2">
        <x-sidebar-link route="dashboard" icon="📊" label="Dashboard" />
        <x-sidebar-link route="leads.index" icon="👥" label="Leads" />
        
        <div class="my-4 border-t border-gray-800"></div>
        
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Coming Soon</div>
        <x-sidebar-link-disabled icon="📧" label="Campaigns" />
        <x-sidebar-link route="inbox.index" icon="📬" label="Inbox" />
        <x-sidebar-link route="email-accounts.index" icon="📧" label="Email Accounts" />
        <x-sidebar-link-disabled icon="🤖" label="AI Automation" />
        <x-sidebar-link-disabled icon="📍" label="Sources" />
        <x-sidebar-link-disabled icon="📈" label="Analytics" />
        
        <div class="my-4 border-t border-gray-800"></div>
        
        <x-sidebar-link route="profile.edit" icon="⚙️" label="Settings" />
        
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full px-4 py-2 text-left text-sm bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                🚪 Logout
            </button>
        </form>
    </nav>
</div>

<script>
    document.querySelector('.mobile-menu-btn')?.addEventListener('click', function() {
        document.querySelector('.mobile-menu').classList.toggle('hidden');
    });
</script>
@else
<div class="hidden"></div>
@endauth
