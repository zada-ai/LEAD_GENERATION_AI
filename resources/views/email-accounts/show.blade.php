@extends('layouts.app')
<x-app-layout :title="$emailAccount->email">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $emailAccount->email }}</h1>
                    <p class="text-gray-600 mt-1">Account Details</p>
                </div>
                <a href="{{ route('email-accounts.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    ← Back to Accounts
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Account Info Card -->
            <x-card>
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Email Address</p>
                            <p class="text-gray-900 font-semibold">{{ $emailAccount->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Provider</p>
                            <p class="text-gray-900 font-semibold">{{ ucfirst($emailAccount->provider) }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <div class="mt-1">
                                @if ($emailAccount->status === 'connected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                                        Connected
                                    </span>
                                @elseif ($emailAccount->status === 'disconnected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <span class="w-2 h-2 bg-gray-600 rounded-full mr-2"></span>
                                        Disconnected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-600 rounded-full mr-2"></span>
                                        Error
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Connected Since</p>
                            <p class="text-gray-900 font-semibold">{{ $emailAccount->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Sync Stats Card -->
            <x-card>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sync Statistics</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Total Emails Synced</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $emailCount }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Unread Emails</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $unreadCount }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Conversations</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $conversationCount }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Last Sync</p>
                            <p class="text-gray-900 font-semibold">
                                @if ($emailAccount->last_synced_at)
                                    {{ $emailAccount->last_synced_at->format('M d, Y \a\t g:i A') }}
                                @else
                                    Never synced
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Actions -->
        <div class="mt-6">
            <x-card>
                <div class="flex flex-wrap gap-3">
                    @if ($emailAccount->status === 'connected')
                        <form action="{{ route('email-accounts.sync', $emailAccount) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Sync Now
                            </button>
                        </form>

                        <a href="{{ route('inbox.index', ['account_id' => $emailAccount->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg bg-white hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Open Inbox
                        </a>
                    @endif

                    <form action="{{ route('email-accounts.disconnect', $emailAccount) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? Conversations will be preserved.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 font-semibold rounded-lg bg-red-50 hover:bg-red-100 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Disconnect Account
                        </button>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
