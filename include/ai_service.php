<?php
/**
 * AI Service Layer
 * Handles communication with Gemini API for automated categorization
 */

class AIService {
    private static $apiKey;
    private static $apiEnabled;
    private static $lastError = '';
    private const ALLOWED_CATEGORIES = [
        'Electrical',
        'Mechanical',
        'Safety System',
        'Door System',
        'Electronic Board',
        'Hydraulic',
        'General Maintenance',
    ];

    /**
     * Initialize service settings from environment
     */
    private static function init() {
        if (self::$apiKey !== null) return;

        self::$apiKey = getenv('GEMINI_API_KEY') ?: '';
        self::$apiEnabled = (getenv('AI_ENABLED') === 'true');
        self::$lastError = '';
    }

    /**
     * Categorize a breakdown record based on its nature and description
     */
    public static function categorizeBreakdown(string $nature, string $description): string {
        self::init();

        if (!self::$apiEnabled || empty(self::$apiKey) || self::$apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            return 'Uncategorized';
        }

        $prompt = "You are a technical assistant for an elevator and escalator maintenance company. 
                   Analyze the following breakdown incident and categorize it into EXACTLY ONE of these categories: 
                   'Electrical', 'Mechanical', 'Safety System', 'Door System', 'Electronic Board', 'Hydraulic', 'General Maintenance'.
                   
                   Incident Nature: $nature
                   Work Description: $description
                   
                   Return ONLY the category name and nothing else.";

        $category = self::callGemini($prompt);
        if ($category !== null) {
            return $category;
        }

        // Fallback classification to avoid dropping all API misses into Uncategorized.
        return self::fallbackCategory($nature . ' ' . $description);
    }

    /**
     * Generate general insights from data
     */
    public static function generateInsight(string $dataSummary): string {
        self::init();

        if (!self::$apiEnabled || empty(self::$apiKey) || self::$apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            return 'API Key missing or AI disabled. Please check your configuration.';
        }

        $prompt = "As a maintenance data analyst, provide a concise 2-sentence executive summary of the following maintenance data. 
                   Identify the most critical area and suggest a focus for next month. 
                   Data: $dataSummary";

        $insight = self::callGemini($prompt);
        if ($insight === null) {
            if (!empty(self::$lastError)) {
                return 'AI service unavailable: ' . self::$lastError;
            }
            return 'AI service is temporarily unavailable. Please try again shortly.';
        }

        return $insight;
    }

    /**
     * Call the Gemini API via cURL
     */
    private static function callGemini(string $prompt): ?string {
        $models = [
            'gemini-2.0-flash',
            getenv('GEMINI_MODEL') ?: 'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
            'gemini-1.5-pro',
        ];

        $models = array_values(array_unique($models));
        $lastError = '';

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . self::$apiKey;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false || $response === null) {
                $lastError = 'network/cURL issue. ' . $curlError;
                error_log('Gemini request failed for model ' . $model . ': ' . $curlError);
                continue;
            }

            if ($httpCode !== 200) {
                if ($httpCode === 429) {
                    $lastError = 'quota exceeded or rate limited (HTTP 429).';
                } elseif ($httpCode === 401 || $httpCode === 403) {
                    $lastError = 'API key is invalid or lacks permission (HTTP ' . $httpCode . ').';
                } elseif ($httpCode === 404) {
                    $lastError = 'requested model is not available (HTTP 404).';
                } else {
                    $lastError = 'API returned HTTP ' . $httpCode . '.';
                }
                error_log('Gemini HTTP ' . $httpCode . ' for model ' . $model . ': ' . substr($response, 0, 300));
                continue;
            }

            $result = json_decode($response, true);
            if (!is_array($result)) {
                $lastError = 'invalid JSON response from AI service.';
                error_log('Gemini response JSON decode failed for model ' . $model);
                continue;
            }

            $text = trim((string)($result['candidates'][0]['content']['parts'][0]['text'] ?? ''));
            if ($text === '') {
                $lastError = 'empty response from AI service.';
                continue;
            }

            // Categorization prompt expects a category; insight prompt can return free text.
            if (stripos($prompt, 'categorize') !== false || stripos($prompt, 'category') !== false) {
                $normalized = self::normalizeCategory($text);
                if ($normalized !== null) {
                    return $normalized;
                }
                continue;
            }

            return $text;
        }

        self::$lastError = $lastError;

        return null;
    }

    private static function normalizeCategory(string $text): ?string {
        $value = trim($text);

        // Keep first line and remove common wrappers like "Category:"
        $value = preg_split('/\r\n|\r|\n/', $value)[0] ?? $value;
        $value = trim($value);
        $value = preg_replace('/^category\s*:\s*/i', '', $value);
        $value = trim($value, " \t\n\r\0\x0B`*\"'");

        foreach (self::ALLOWED_CATEGORIES as $allowed) {
            if (strcasecmp($value, $allowed) === 0) {
                return $allowed;
            }
        }

        return self::fallbackCategory($value);
    }

    private static function fallbackCategory(string $text): string {
        $value = strtolower($text);

        if (strpos($value, 'door') !== false) return 'Door System';
        if (strpos($value, 'safety') !== false || strpos($value, 'alarm') !== false) return 'Safety System';
        if (strpos($value, 'board') !== false || strpos($value, 'pcb') !== false || strpos($value, 'controller') !== false) return 'Electronic Board';
        if (strpos($value, 'hydraulic') !== false || strpos($value, 'oil') !== false || strpos($value, 'pump') !== false) return 'Hydraulic';
        if (strpos($value, 'electrical') !== false || strpos($value, 'voltage') !== false || strpos($value, 'wiring') !== false) return 'Electrical';
        if (strpos($value, 'mechanical') !== false || strpos($value, 'motor') !== false || strpos($value, 'gear') !== false) return 'Mechanical';

        return 'General Maintenance';
    }
}
