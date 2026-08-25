<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">AI Automation Hub</h1>
                <p class="text-gray-600 mt-1">Powered by Google Gemini AI for automated lead scoring, qualification, and sales intelligence</p>
            </div>
            <form action="{{ route('ai-automation.batchQualify') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 rounded-lg text-sm font-semibold text-white hover:bg-purple-700 shadow transition">
                    <span>🤖</span> Batch Qualify Unscored Leads
                </button>
            </form>
        </div>

        <!-- AI Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Leads Analyzed</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['analyzed']) }}</h3>
                <p class="text-xs text-purple-600 font-medium mt-1">Evaluated by Gemini AI</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">High Potential (70%+)</p>
                <h3 class="text-3xl font-bold text-emerald-600 mt-1">{{ number_format($stats['high_value']) }}</h3>
                <p class="text-xs text-gray-500 mt-1">Ready for high-intent campaigns</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Pending Analysis</p>
                <h3 class="text-3xl font-bold text-amber-600 mt-1">{{ number_format($stats['pending']) }}</h3>
                <p class="text-xs text-gray-500 mt-1">Unscored in database</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Average Score</p>
                <h3 class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['average_score'] }}/100</h3>
                <p class="text-xs text-gray-500 mt-1">Overall pipeline quality</p>
            </div>
        </div>

        <!-- Highest Potential Leads Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Highest Potential -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">🌟 Highest Potential Leads</h2>
                <div class="space-y-3">
                    @foreach($topLeads as $lead)
                        <div class="p-3.5 rounded-lg border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('leads.show', $lead) }}" class="font-bold text-sm text-gray-900 hover:text-blue-600 truncate block">
                                    {{ $lead->business_name }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $lead->category }} • {{ $lead->city ?? $lead->country }}</p>
                                <p class="text-[11px] text-purple-700 font-medium mt-1">Pitch: {{ $lead->ai_service ?? $lead->service_interest }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold border {{ $lead->ai_score_badge_color }}">
                                    {{ $lead->ai_score }}/100
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pending Analysis -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">⏳ Pending AI Qualification</h2>
                @if($pendingLeads->isEmpty())
                    <p class="text-sm text-gray-500 py-6 text-center">All leads currently qualified! 🎉</p>
                @else
                    <div class="space-y-3">
                        @foreach($pendingLeads as $lead)
                            <div class="p-3.5 rounded-lg border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('leads.show', $lead) }}" class="font-bold text-sm text-gray-900 hover:text-blue-600 truncate block">
                                        {{ $lead->business_name }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $lead->category }} • {{ $lead->email }}</p>
                                </div>
                                <form action="{{ route('leads.analyzeAi', $lead) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs font-semibold shadow-sm">
                                        🤖 Qualify
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
