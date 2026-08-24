            <a href="{{ route('email-accounts.google.redirect') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <a href="{{ route('email-accounts.google.redirect') }}" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
@extends('layouts.app')
<x-app-layout :title="'Email Accounts'">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Accounts</h1>
                <p class="text-gray-600 mt-1">Manage your connected Gmail accounts</p>
            </div>
            <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
                Connect Gmail
            </a>
        </div>

        @if ($emailAccounts->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Gmail accounts connected</h3>
                <p class="text-gray-600 mb-6">Connect your Gmail account to manage customer conversations from the CRM.</p>
                <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                    Connect Gmail Account
                </a>
            </div>
        @else
            <!-- Email Accounts Grid/Table -->
            <div class="space-y-4">
                @foreach ($emailAccounts as $account)
                    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $account->email }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Connected on {{ $account->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div>
                                    @if ($account->status === 'connected')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                                            Connected
                                        </span>
                                    @elseif ($account->status === 'disconnected')
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

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Emails Synced</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $account->emails()->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Conversations</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $account->conversations()->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Last Sync</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        @if ($account->last_synced_at)
                                            {{ $account->last_synced_at->diffForHumans() }}
                                        @else
                                            Never
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Status</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ ucfirst($account->status) }}</p>
                                </div>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                @if ($account->status === 'connected')
                                    <form action="{{ route('email-accounts.sync', $account) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Sync Now
                                        </button>
                                    </form>

                                    <a href="{{ route('inbox.index', ['account_id' => $account->id]) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        View Inbox
                                    </a>

                                    <a href="{{ route('email-accounts.show', $account) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Details
                                    </a>
                                @endif

                                <form action="{{ route('email-accounts.disconnect', $account) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? Conversations will be preserved.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Disconnect
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
