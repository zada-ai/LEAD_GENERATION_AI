<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use App\Services\Discovery\LeadDiscoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class SourceController extends Controller
{
    public function __construct(
        protected LeadDiscoveryService $discoveryService
    ) {}

    public function index(): View
    {
        $sources = LeadSource::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $providers = $this->discoveryService->getProviders();

        return view('sources.index', compact('sources', 'providers'));
    }

    public function create(): View
    {
        $providers = $this->discoveryService->getProviders();
        return view('sources.create', compact('providers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:website_search,manual_import,csv_import',
            'config' => 'nullable|array',
            'config.country' => 'nullable|string|max:100',
            'config.state' => 'nullable|string|max:100',
            'config.city' => 'nullable|string|max:100',
            'config.area' => 'nullable|string|max:100',
            'config.category' => 'nullable|string|max:100',
            'config.keyword' => 'nullable|string|max:100',
            'config.max_results' => 'nullable|integer|min:1|max:100',
        ]);

        $source = LeadSource::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'config' => $validated['config'] ?? [],
            'status' => 'active',
        ]);

        return redirect()->route('sources.show', $source)->with('success', 'Lead source created successfully.');
    }

    public function show(LeadSource $source): View
    {
        $this->authorizeSource($source);
        return view('sources.show', compact('source'));
    }

    public function runSearch(Request $request, LeadSource $source): RedirectResponse
    {
        $this->authorizeSource($source);

        try {
            $params = $source->config ?? [];
            if ($request->has('max_results')) {
                $params['max_results'] = $request->input('max_results');
            }

            $result = $this->discoveryService->executeDiscovery(
                auth()->id(),
                $source->type,
                $params,
                $source
            );

            return back()->with('success', "Discovery completed: {$result['created']} new leads added, {$result['updated']} duplicates updated.");
        } catch (Exception $e) {
            return back()->with('error', 'Discovery failed: ' . $e->getMessage());
        }
    }

    public function destroy(LeadSource $source): RedirectResponse
    {
        $this->authorizeSource($source);
        $source->delete();

        return redirect()->route('sources.index')->with('success', 'Lead source removed.');
    }

    protected function authorizeSource(LeadSource $source): void
    {
        if ($source->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to lead source.');
        }
    }
}
