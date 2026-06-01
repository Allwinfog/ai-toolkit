<?php

namespace App\Services;

use App\Models\Generation;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('ai-toolkit.openai.api_key');

        if (empty($this->apiKey) || str_starts_with($this->apiKey, 'sk-your')) {
            throw new \RuntimeException('OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file.');
        }
    }

    // ── Text Generation ──

    public function generateText(User $user, array $params): Generation
    {
        $model = $params['model'] ?? config('ai-toolkit.openai.defaults.text_model');
        $maxTokens = $params['max_tokens'] ?? config('ai-toolkit.openai.defaults.max_tokens');
        $temperature = $params['temperature'] ?? config('ai-toolkit.openai.defaults.temperature');

        $messages = [];

        if (!empty($params['system_prompt'])) {
            $messages[] = ['role' => 'system', 'content' => $params['system_prompt']];
        }

        $messages[] = ['role' => 'user', 'content' => $params['prompt']];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(120)->post("{$this->baseUrl}/chat/completions", [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);

        $data = $response->json();

        if (!$response->successful() || isset($data['error'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'OpenAI API request failed (HTTP ' . $response->status() . ')');
        }

        $result = $data['choices'][0]['message']['content'] ?? '';
        $tokensUsed = $data['usage']['total_tokens'] ?? 0;
        $wordCount = str_word_count($result);
        $cost = $this->calculateCost($model, $tokensUsed);

        $generation = Generation::create([
            'user_id' => $user->id,
            'template_id' => $params['template_id'] ?? null,
            'type' => 'text',
            'model_used' => $model,
            'prompt' => $params['prompt'],
            'result' => $result,
            'tokens_used' => $tokensUsed,
            'word_count' => $wordCount,
            'cost' => $cost,
            'metadata' => [
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
                'system_prompt' => $params['system_prompt'] ?? null,
            ],
        ]);

        $user->incrementUsage('text', $wordCount);

        return $generation;
    }

    // ── Image Generation ──

    public function generateImage(User $user, array $params): Generation
    {
        $model = $params['model'] ?? config('ai-toolkit.openai.defaults.image_model');
        $size = $params['size'] ?? '1024x1024';
        $quality = $params['quality'] ?? 'standard';
        $style = $params['style'] ?? 'vivid';

        $requestBody = [
            'model' => $model,
            'prompt' => $params['prompt'],
            'n' => 1,
            'size' => $size,
        ];

        if ($model === 'dall-e-3') {
            $requestBody['quality'] = $quality;
            $requestBody['style'] = $style;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(120)->post("{$this->baseUrl}/images/generations", $requestBody);

        $data = $response->json();

        if (!$response->successful() || isset($data['error'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'OpenAI API request failed (HTTP ' . $response->status() . ')');
        }

        $imageUrl = $data['data'][0]['url'] ?? '';
        $revisedPrompt = $data['data'][0]['revised_prompt'] ?? $params['prompt'];

        // Download and store image locally
        $imagePath = null;
        if ($imageUrl) {
            $imageContent = Http::get($imageUrl)->body();
            $filename = 'generations/' . Str::uuid() . '.png';
            Storage::disk('local')->put($filename, $imageContent);
            $imagePath = $filename;
        }

        $cost = $this->calculateImageCost($model, $size, $quality);

        $generation = Generation::create([
            'user_id' => $user->id,
            'template_id' => $params['template_id'] ?? null,
            'type' => 'image',
            'model_used' => $model,
            'prompt' => $params['prompt'],
            'result' => $revisedPrompt,
            'image_url' => $imageUrl,
            'image_path' => $imagePath,
            'cost' => $cost,
            'metadata' => [
                'size' => $size,
                'quality' => $quality,
                'style' => $style,
            ],
        ]);

        $user->incrementUsage('image');

        return $generation;
    }

    // ── Code Generation ──

    public function generateCode(User $user, array $params): Generation
    {
        $model = $params['model'] ?? config('ai-toolkit.openai.defaults.code_model');
        $language = $params['language'] ?? 'php';

        $systemPrompt = "You are an expert programmer. Generate clean, well-commented, production-ready {$language} code. Only output the code with comments, no additional explanation unless specifically asked.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $params['prompt']],
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(120)->post("{$this->baseUrl}/chat/completions", [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $params['max_tokens'] ?? 4096,
            'temperature' => 0.3,
        ]);

        $data = $response->json();

        if (!$response->successful() || isset($data['error'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'OpenAI API request failed (HTTP ' . $response->status() . ')');
        }

        $result = $data['choices'][0]['message']['content'] ?? '';
        $tokensUsed = $data['usage']['total_tokens'] ?? 0;
        $cost = $this->calculateCost($model, $tokensUsed);

        $generation = Generation::create([
            'user_id' => $user->id,
            'template_id' => $params['template_id'] ?? null,
            'type' => 'code',
            'model_used' => $model,
            'prompt' => $params['prompt'],
            'result' => $result,
            'tokens_used' => $tokensUsed,
            'word_count' => str_word_count($result),
            'cost' => $cost,
            'metadata' => [
                'language' => $language,
            ],
        ]);

        $user->incrementUsage('code', str_word_count($result));

        return $generation;
    }

    // ── Template-Based Generation ──

    public function generateFromTemplate(User $user, Template $template, array $inputs): Generation
    {
        $prompt = $template->buildPrompt($inputs);

        $params = [
            'prompt' => $prompt,
            'system_prompt' => $template->system_prompt,
            'template_id' => $template->id,
            'model' => $template->model,
            'max_tokens' => $template->max_tokens,
        ];

        $template->increment('usage_count');

        return match ($template->type) {
            'image' => $this->generateImage($user, $params),
            'code' => $this->generateCode($user, $params),
            default => $this->generateText($user, $params),
        };
    }

    // ── Cost Calculation ──

    protected function calculateCost(string $model, int $tokens): float
    {
        $rates = [
            'gpt-4o' => 0.005,
            'gpt-4o-mini' => 0.00015,
            'gpt-3.5-turbo' => 0.0005,
        ];

        $rate = $rates[$model] ?? 0.001;
        return round(($tokens / 1000) * $rate, 6);
    }

    protected function calculateImageCost(string $model, string $size, string $quality): float
    {
        if ($model === 'dall-e-3') {
            return $quality === 'hd' ? 0.08 : 0.04;
        }
        return $size === '1024x1024' ? 0.02 : 0.018;
    }
}
