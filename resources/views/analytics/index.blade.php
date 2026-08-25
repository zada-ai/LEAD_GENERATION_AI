<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Analytics & Performance Reports</h1>
            <p class="text-gray-600 mt-1">Real-time calculations of lead conversions, outreach effectiveness, and service demand</p>
        </div>

        <!-- Metric Top Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Conversion Rate</p>
                <h3 class="text-3xl font-bold text-emerald-600 mt-1">{{ $leadMetrics['conversion_rate'] }}%</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $leadMetrics['converted'] }} converted out of {{ $leadMetrics['total'] }} leads</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Reply Rate</p>
                <h3 class="text-3xl font-bold text-purple-600 mt-1">{{ $outreachMetrics['reply_rate'] }}%</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $outreachMetrics['messages_replied'] }} replies received</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Emails Sent</p>
                <h3 class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($outreachMetrics['messages_sent']) }}</h3>
                <p class="text-xs text-gray-500 mt-1">Across {{ $outreachMetrics['total_campaigns'] }} campaigns</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Suppressed / Opt-outs</p>
                <h3 class="text-3xl font-bold text-gray-700 mt-1">{{ $leadMetrics['suppressed'] }}</h3>
                <p class="text-xs text-gray-500 mt-1">Protected from outreach</p>
            </div>
        </div>

        <!-- Breakdown Grids -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Service Performance Comparison -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Service Demand & Lead Breakdown</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase text-left">
                            <tr>
                                <th class="px-4 py-2">Service</th>
                                <th class="px-4 py-2 text-center">Total Leads</th>
                                <th class="px-4 py-2 text-center">Interested</th>
                                <th class="px-4 py-2 text-center">Converted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($serviceBreakdown as $sb)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $sb->service_interest ?? 'Other' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-700">{{ $sb->count }}</td>
                                    <td class="px-4 py-3 text-center text-green-600 font-bold">{{ $sb->interested_count }}</td>
                                    <td class="px-4 py-3 text-center text-emerald-700 font-bold">{{ $sb->converted_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- AI Score Tier Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">AI Qualification Tiers</h2>
                <div class="space-y-4 pt-2">
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                            <span>Very High Potential (85 - 100)</span>
                            <span class="text-emerald-700 font-bold">{{ $aiScoreDistribution['very_high'] }} leads</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ max(1, $leadMetrics['total']) > 0 ? ($aiScoreDistribution['very_high'] / max(1, $leadMetrics['total'])) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                            <span>High Potential (70 - 84)</span>
                            <span class="text-green-700 font-bold">{{ $aiScoreDistribution['high'] }} leads</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ max(1, $leadMetrics['total']) > 0 ? ($aiScoreDistribution['high'] / max(1, $leadMetrics['total'])) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                            <span>Medium Potential (40 - 69)</span>
                            <span class="text-yellow-700 font-bold">{{ $aiScoreDistribution['medium'] }} leads</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ max(1, $leadMetrics['total']) > 0 ? ($aiScoreDistribution['medium'] / max(1, $leadMetrics['total'])) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                            <span>Low / Unscored</span>
                            <span class="text-gray-500 font-bold">{{ $aiScoreDistribution['low'] + $aiScoreDistribution['unscored'] }} leads</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-gray-300 h-2.5 rounded-full" style="width: {{ max(1, $leadMetrics['total']) > 0 ? (($aiScoreDistribution['low'] + $aiScoreDistribution['unscored']) / max(1, $leadMetrics['total'])) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Performance Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Campaign Outreach Performance</h2>
            @if($campaignStats->isEmpty())
                <p class="text-sm text-gray-500 py-4 text-center">No campaign performance data available.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase text-left">
                            <tr>
                                <th class="px-6 py-3">Campaign</th>
                                <th class="px-6 py-3">Service</th>
                                <th class="px-6 py-3 text-center">Sent</th>
                                <th class="px-6 py-3 text-center">Replies</th>
                                <th class="px-6 py-3 text-center">Reply Rate</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($campaignStats as $c)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        <a href="{{ route('campaigns.show', $c) }}" class="hover:text-blue-600">
                                            {{ $c->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $c->service }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-900">{{ $c->sent_messages }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-purple-600">{{ $c->replied_messages }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-emerald-600">
                                        {{ $c->sent_messages > 0 ? round(($c->replied_messages / $c->sent_messages) * 100, 1) : 0 }}%
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $c->status_badge_color }}">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
