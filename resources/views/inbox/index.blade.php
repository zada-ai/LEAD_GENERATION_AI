<x-app-layout :title="'Inbox'">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Inbox</h1>
            <p class="text-gray-600 mt-1">Manage your email conversations</p>
        </div>

        @if ($emailAccounts->isEmpty())
            <!-- Empty State -->
            <x-card>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Gmail accounts connected</h3>
                    <p class="text-gray-600 mb-6">Connect your Gmail account to view conversations.</p>
                    <a href="{{ route('email-accounts.index') }}" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Go to Email Accounts
                    </a>
                </div>
            </x-card>
        @else
            <!-- Filters and Search -->
            <div class="mb-6 space-y-4">
                <form action="{{ route('inbox.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" placeholder="Search by name, email, or subject..." value="{{ $search }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                    <div>
                        <select name="account_id" class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Accounts</option>
                            @foreach ($emailAccounts as $account)
                                <option value="{{ $account->id }}" {{ $selectedAccount?->id === $account->id ? 'selected' : '' }}>
                                    {{ $account->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                        Search
                    </button>
                </form>

                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('inbox.index', array_merge(request()->query(), ['filter' => 'all'])) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All
                    </a>
                    <a href="{{ route('inbox.index', array_merge(request()->query(), ['filter' => 'unread'])) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'unread' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Unread
                    </a>
                    <a href="{{ route('inbox.index', array_merge(request()->query(), ['filter' => 'replied'])) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'replied' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Replied
                    </a>
                    <a href="{{ route('inbox.index', array_merge(request()->query(), ['filter' => 'waiting'])) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'waiting' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Waiting
                    </a>
                    <a href="{{ route('inbox.index', array_merge(request()->query(), ['filter' => 'closed'])) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'closed' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Closed
                    </a>
                </div>
            </div>

            @if ($conversations->isEmpty())
                <x-card>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No conversations found</h3>
                        <p class="text-gray-600">Start by syncing your email accounts to see conversations.</p>
                    </div>
                </x-card>
            @else
                <!-- Conversations List -->
                <div class="space-y-3">
                    @foreach ($conversations as $conversation)
                        <a href="{{ route('inbox.show', $conversation) }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-4 border-l-4 {{ $conversation->hasUnread() ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $conversation->lead?->business_name ?? 'Unknown Sender' }}
                                        </h3>
                                        @if ($conversation->hasUnread())
                                            <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium text-gray-800 truncate mb-1">{{ $conversation->subject ?? 'No Subject' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $conversation->latestEmail()?->from_email }}</p>
                                    <p class="text-sm text-gray-700 mt-1 line-clamp-2">{{ $conversation->latestEmail()?->snippet }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm text-gray-500">
                                        {{ $conversation->last_message_at?->diffForHumans() }}
                                    </p>
                                    <div class="mt-2">
                                        <x-badge :status="$conversation->status" />
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $conversations->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
