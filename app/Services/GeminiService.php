<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadEnrichment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->model = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
    }

    /**
     * Set active model dynamically (e.g. from user settings)
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Check if Gemini API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Analyze and qualify a lead using Gemini AI
     */
    public function analyzeLead(Lead $lead, ?LeadEnrichment $enrichment = null): array
    {
        $services = 'AI Automation, AI Chatbot, Website Development, CRM Development, App Development, SEO, Other';

        $prompt = "You are an expert B2B sales development AI. Analyze the following lead and evaluate the sales potential for digital services ({$services}).\n\n"
            . "LEAD DETAILS:\n"
            . "Business Name: {$lead->business_name}\n"
            . "Contact Person: {$lead->contact_name}\n"
            . "Category: {$lead->category}\n"
            . "Website: " . ($lead->website ?? 'None') . "\n"
            . "Location: {$lead->city}, {$lead->state}, {$lead->country}\n"
            . "Current Service Interest: {$lead->service_interest}\n"
            . "Current Priority: {$lead->priority}\n"
            . "Notes: " . ($lead->notes ?? 'None') . "\n";

        if ($enrichment) {
            $prompt .= "\nENRICHMENT DATA:\n"
                . "Website Summary: " . ($enrichment->website_summary ?? 'N/A') . "\n"
                . "Opportunity Notes: " . ($enrichment->opportunity_notes ?? 'N/A') . "\n";
        }

        $prompt .= "\nRespond ONLY with a valid JSON object matching this schema exactly:\n"
            . "{\n"
            . '  "score": <integer from 0 to 100>,' . "\n"
            . '  "priority": "<low|medium|high|very_high>",' . "\n"
            . '  "best_service": "<one of: AI Automation, AI Chatbot, Website Development, CRM Development, App Development, SEO, Other>",' . "\n"
            . '  "reason": "<clear 1-2 sentence explanation of the score and service fit>",' . "\n"
            . '  "recommended_angle": "<suggested outreach pitch angle>",' . "\n"
            . '  "summary": "<2-3 sentence executive summary of the business and digital opportunity>"' . "\n"
            . "}\nDo not include markdown fences, backticks, or any extraneous text.";

        try {
            $rawContent = $this->callGemini($prompt);
            $parsed = $this->parseJson($rawContent);

            return [
                'score' => max(0, min(100, (int)($parsed['score'] ?? $this->fallbackScore($lead)))),
                'priority' => in_array($parsed['priority'] ?? '', ['low', 'medium', 'high', 'very_high'])
                    ? $parsed['priority']
                    : $this->calculatePriority((int)($parsed['score'] ?? 50)),
                'best_service' => $parsed['best_service'] ?? $lead->service_interest ?? 'AI Automation',
                'reason' => $parsed['reason'] ?? 'Lead evaluated based on business profile and category.',
                'recommended_angle' => $parsed['recommended_angle'] ?? 'Highlight digital modernization and customer automation.',
                'summary' => $parsed['summary'] ?? "{$lead->business_name} in {$lead->category} evaluated for {$lead->service_interest}.",
            ];
        } catch (Exception $e) {
            Log::warning('Gemini analyzeLead fallback triggered: ' . $e->getMessage());

            return $this->getDeterministicAnalysis($lead);
        }
    }

    /**
     * Generate a personalized outreach email draft
     */
    public function generatePersonalizedEmail(Lead $lead, string $service, string $templateSubject, string $templateBody): array
    {
        $prompt = "You are a professional B2B outreach specialist. Personalize the following email template for this specific lead.\n\n"
            . "CRITICAL RULES:\n"
            . "1. Do NOT invent fake facts or claims not supported by the lead data.\n"
            . "2. Keep the tone professional, helpful, concise, and engaging.\n"
            . "3. Address {$lead->contact_name} at {$lead->business_name}.\n"
            . "4. Focus on {$service}.\n\n"
            . "LEAD DATA:\n"
            . "Business Name: {$lead->business_name}\n"
            . "Contact Person: {$lead->contact_name}\n"
            . "Category: {$lead->category}\n"
            . "Location: " . ($lead->city ? "{$lead->city}, {$lead->country}" : $lead->country) . "\n"
            . "AI Angle: " . ($lead->ai_summary ?? $lead->notes ?? 'Modern digital growth') . "\n\n"
            . "ORIGINAL TEMPLATE:\n"
            . "Subject: {$templateSubject}\n"
            . "Body:\n{$templateBody}\n\n"
            . "Respond ONLY with a JSON object in this format:\n"
            . "{\n"
            . '  "subject": "<personalized subject line>",' . "\n"
            . '  "body": "<personalized email body in clean text/HTML formatted paragraphs>"' . "\n"
            . "}\nDo not include markdown codeblocks or extra text.";

        try {
            $rawContent = $this->callGemini($prompt);
            $parsed = $this->parseJson($rawContent);

            $subject = !empty($parsed['subject']) ? trim($parsed['subject']) : $this->replacePlaceholders($templateSubject, $lead, $service);
            $body = !empty($parsed['body']) ? trim($parsed['body']) : $this->replacePlaceholders($templateBody, $lead, $service);

            return [
                'subject' => $subject,
                'body' => $body,
            ];
        } catch (Exception $e) {
            Log::warning('Gemini generatePersonalizedEmail fallback triggered: ' . $e->getMessage());

            return [
                'subject' => $this->replacePlaceholders($templateSubject, $lead, $service),
                'body' => $this->replacePlaceholders($templateBody, $lead, $service),
            ];
        }
    }

    /**
     * Classify an incoming customer reply
     */
    public function classifyReply(string $emailBody, string $subject = ''): array
    {
        $prompt = "You are an incoming email sentiment & intent classifier for a CRM.\n"
            . "Classify this response into one of the categories:\n"
            . "- Interested\n"
            . "- Not Interested\n"
            . "- Pricing\n"
            . "- Question\n"
            . "- Follow Up\n"
            . "- Meeting Request\n"
            . "- Wrong Contact\n"
            . "- Unsubscribe\n"
            . "- Other\n\n"
            . "SUBJECT: {$subject}\n"
            . "BODY:\n{$emailBody}\n\n"
            . "Respond ONLY with JSON:\n"
            . "{\n"
            . '  "category": "<one category from list>",' . "\n"
            . '  "is_opt_out": <true or false>,' . "\n"
            . '  "confidence": <integer from 0 to 100>,' . "\n"
            . '  "summary": "<1-sentence summary of reply>"' . "\n"
            . "}";

        try {
            $rawContent = $this->callGemini($prompt);
            $parsed = $this->parseJson($rawContent);

            $validCategories = ['Interested', 'Not Interested', 'Pricing', 'Question', 'Follow Up', 'Meeting Request', 'Wrong Contact', 'Unsubscribe', 'Other'];
            $category = in_array($parsed['category'] ?? '', $validCategories) ? $parsed['category'] : 'Other';

            $isOptOut = (bool)($parsed['is_opt_out'] ?? false);
            if (preg_match('/\b(unsubscribe|stop|remove me|do not contact|don\'t email)\b/i', $emailBody)) {
                $isOptOut = true;
                $category = 'Unsubscribe';
            }

            return [
                'category' => $category,
                'is_opt_out' => $isOptOut,
                'confidence' => max(0, min(100, (int)($parsed['confidence'] ?? 80))),
                'summary' => $parsed['summary'] ?? 'Customer responded to outreach.',
            ];
        } catch (Exception $e) {
            Log::warning('Gemini classifyReply fallback triggered: ' . $e->getMessage());

            $isOptOut = (bool)preg_match('/\b(unsubscribe|stop|remove me|do not contact|don\'t email)\b/i', $emailBody);

            return [
                'category' => $isOptOut ? 'Unsubscribe' : 'Question',
                'is_opt_out' => $isOptOut,
                'confidence' => 70,
                'summary' => 'Received customer reply.',
            ];
        }
    }

    /**
     * Centralized communication with Google Gemini API
     */
    protected function callGemini(string $prompt): string
    {
        if (!$this->isConfigured()) {
            throw new Exception('GEMINI_API_KEY is not configured in environment.');
        }

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(25)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 1024,
            ]
        ]);

        if (!$response->successful()) {
            $status = $response->status();
            $error = $response->json('error.message') ?? 'Gemini API call failed';
            throw new Exception("Gemini API Error [HTTP {$status}]: {$error}");
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (empty($text)) {
            throw new Exception('Empty response from Gemini API.');
        }

        return trim($text);
    }

    /**
     * Safe JSON parsing
     */
    protected function parseJson(string $raw): array
    {
        // Strip markdown code fences if present
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $clean = preg_replace('/\s*```$/', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (!is_array($decoded)) {
            // Attempt regex extract first { ... } block
            if (preg_match('/\{[\s\S]*\}/', $clean, $matches)) {
                $decoded = json_decode($matches[0], true);
            }
        }

        if (!is_array($decoded)) {
            throw new Exception('Failed to decode JSON from Gemini response.');
        }

        return $decoded;
    }

    /**
     * Replace template placeholders
     */
    protected function replacePlaceholders(string $template, Lead $lead, string $service): string
    {
        $replacements = [
            '{{business_name}}' => $lead->business_name,
            '{{contact_name}}' => $lead->contact_name,
            '{{city}}' => $lead->city ?? $lead->country,
            '{{country}}' => $lead->country,
            '{{category}}' => $lead->category,
            '{{service}}' => $service,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Deterministic rule-based qualification fallback
     */
    public function getDeterministicAnalysis(Lead $lead): array
    {
        $score = $this->fallbackScore($lead);
        $priority = $this->calculatePriority($score);

        return [
            'score' => $score,
            'priority' => $priority,
            'best_service' => $lead->service_interest ?? 'AI Automation',
            'reason' => "Evaluated based on business profile in {$lead->category} with priority {$lead->priority}.",
            'recommended_angle' => "Introduce {$lead->service_interest} solutions to automate customer interaction.",
            'summary' => "{$lead->business_name} is an established {$lead->category} lead suitable for {$lead->service_interest}.",
        ];
    }

    protected function fallbackScore(Lead $lead): int
    {
        $score = 50;
        if ($lead->priority === 'high') $score += 20;
        if ($lead->priority === 'low') $score -= 15;
        if (!empty($lead->website)) $score += 10;
        if (!empty($lead->phone)) $score += 5;
        if (in_array($lead->status, ['interested', 'contacted'])) $score += 10;

        return max(15, min(95, $score));
    }

    protected function calculatePriority(int $score): string
    {
        return match (true) {
            $score >= 85 => 'very_high',
            $score >= 70 => 'high',
            $score >= 40 => 'medium',
            default => 'low',
        };
    }
}
