<x-app-layout :title="$conversation->lead?->business_name ?? 'Conversation'">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-start">
            <div>
                <a href="{{ route('inbox.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold mb-3 inline-block">
                    ← Back to Inbox
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $conversation->subject ?? 'No Subject' }}</h1>
                @if ($conversation->lead)
                    <p class="text-gray-600 mt-2">
                        <a href="{{ route('leads.show', $conversation->lead) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                            {{ $conversation->lead->business_name }}
                        </a>
                    </p>
                @else
                    <p class="text-gray-600 mt-2">Unknown Lead</p>
                @endif
            </div>
            <div>
                <x-badge :status="$conversation->status" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Emails Thread -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <!-- Email Thread -->
                    <div class="divide-y max-h-96 overflow-y-auto">
                        @forelse ($emails as $email)
                            <div class="p-4 {{ $email->direction === 'outgoing' ? 'bg-blue-50' : 'bg-white' }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            {{ $email->direction === 'outgoing' ? 'You' : $email->from_name ?? $email->from_email }}
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $email->from_email }}</p>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        {{ ($email->received_at ?? $email->sent_at)?->format('M d, g:i A') }}
                                    </p>
                                </div>
                                <div class="text-gray-800 break-words">
                                    @if ($email->body_html)
                                        {!! nl2br(e($email->body_html)) !!}
                                    @else
                                        {{ nl2br(e($email->body_text)) }}
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">
                                No emails in this conversation yet.
                            </div>
                        @endforelse
                    </div>

                    <!-- Reply Form -->
                    <form action="{{ route('inbox.reply', $conversation) }}" method="POST" class="border-t p-4">
                        @csrf
                        <label class="block text-sm font-semibold text-gray-900 mb-3">
                            Reply
                        </label>
                        <textarea name="message" rows="6" placeholder="Type your reply..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" required></textarea>
                        @error('message')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            Send Reply
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <!-- Conversation Info -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversation Info</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <form action="{{ route('inbox.updateStatus', $conversation) }}" method="POST" class="mt-1">
                                @csrf
                                <select name="status" class="w-full px-3 py-1 border border-gray-300 rounded text-sm" onchange="this.form.submit()">
                                    <option value="open" {{ $conversation->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="waiting" {{ $conversation->status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                                    <option value="replied" {{ $conversation->status === 'replied' ? 'selected' : '' }}>Replied</option>
                                    <option value="closed" {{ $conversation->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </form>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Emails</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $emails->count() }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Started</p>
                            <p class="text-sm text-gray-900">{{ $conversation->created_at->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Last Message</p>
                            <p class="text-sm text-gray-900">{{ $conversation->last_message_at?->format('M d, g:i A') }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Lead Info -->
                @if ($conversation->lead)
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Business</p>
                                <a href="{{ route('leads.show', $conversation->lead) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                                    {{ $conversation->lead->business_name }}
                                </a>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Contact</p>
                                <p class="text-sm text-gray-900">{{ $conversation->lead->contact_name }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-sm text-gray-900">{{ $conversation->lead->email }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <x-badge :status="$conversation->lead->status" />
                            </div>
                        </div>
                    </x-card>
                @else
                    <x-card>
                        <p class="text-gray-600 text-sm mb-3">This conversation is not linked to a lead.</p>
                        <form action="{{ route('inbox.linkLead', $conversation) }}" method="POST" class="space-y-3">
                            @csrf
                            <select name="lead_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                                <option value="">Select a lead...</option>
                                @foreach (\App\Models\Lead::where('created_by', auth()->id())->get() as $lead)
                                    <option value="{{ $lead->id }}">{{ $lead->business_name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-lg hover:bg-blue-700 transition">
                                Link Lead
                            </button>
                        </form>
                    </x-card>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
