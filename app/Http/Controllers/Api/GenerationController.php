<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use App\Models\Template;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
    public function __construct(protected AIService $aiService) {}

    // ── Text Generation ──

    public function generateText(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:10000',
            'system_prompt' => 'nullable|string|max:5000',
            'model' => 'nullable|string',
            'max_tokens' => 'nullable|integer|min:100|max:8000',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'template_id' => 'nullable|exists:templates,id',
        ]);

        $user = $request->user();

        if (!$user->canGenerate('text')) {
            return response()->json([
                'message' => 'Text generation limit reached. Please upgrade your plan.',
                'remaining' => 0,
            ], 429);
        }

        try {
            $generation = $this->aiService->generateText($user, $validated);
            return response()->json([
                'generation' => $generation,
                'remaining' => $user->fresh()->getRemainingGenerations('text'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Generation failed: ' . $e->getMessage()], 500);
        }
    }

    // ── Image Generation ──

    public function generateImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:4000',
            'model' => 'nullable|string',
            'size' => 'nullable|in:256x256,512x512,1024x1024,1792x1024,1024x1792',
            'quality' => 'nullable|in:standard,hd',
            'style' => 'nullable|in:vivid,natural',
        ]);

        $user = $request->user();

        if (!$user->canGenerate('image')) {
            return response()->json([
                'message' => 'Image generation limit reached. Please upgrade your plan.',
                'remaining' => 0,
            ], 429);
        }

        try {
            $generation = $this->aiService->generateImage($user, $validated);
            return response()->json([
                'generation' => $generation,
                'remaining' => $user->fresh()->getRemainingGenerations('image'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Generation failed: ' . $e->getMessage()], 500);
        }
    }

    // ── Code Generation ──

    public function generateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:10000',
            'language' => 'nullable|string|max:50',
            'model' => 'nullable|string',
            'max_tokens' => 'nullable|integer|min:100|max:8000',
        ]);

        $user = $request->user();

        if (!$user->canGenerate('code')) {
            return response()->json([
                'message' => 'Code generation limit reached. Please upgrade your plan.',
                'remaining' => 0,
            ], 429);
        }

        try {
            $generation = $this->aiService->generateCode($user, $validated);
            return response()->json([
                'generation' => $generation,
                'remaining' => $user->fresh()->getRemainingGenerations('code'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Generation failed: ' . $e->getMessage()], 500);
        }
    }

    // ── Template-Based Generation ──

    public function generateFromTemplate(Request $request, Template $template): JsonResponse
    {
        $user = $request->user();

        if (!$user->canGenerate($template->type)) {
            return response()->json([
                'message' => ucfirst($template->type) . ' generation limit reached.',
            ], 429);
        }

        $validated = $request->validate([
            'inputs' => 'required|array',
        ]);

        try {
            $generation = $this->aiService->generateFromTemplate($user, $template, $validated['inputs']);
            return response()->json([
                'generation' => $generation,
                'remaining' => $user->fresh()->getRemainingGenerations($template->type),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Generation failed: ' . $e->getMessage()], 500);
        }
    }

    // ── History ──

    public function history(Request $request): JsonResponse
    {
        $generations = $request->user()->generations()
            ->with('template:id,name,icon')
            ->when($request->type, fn ($q, $type) => $q->ofType($type))
            ->when($request->favorite, fn ($q) => $q->favorites())
            ->when($request->search, fn ($q, $s) => $q->where('prompt', 'like', "%{$s}%"))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($generations);
    }

    public function show(Generation $generation): JsonResponse
    {
        $this->authorize('view', $generation);
        return response()->json($generation->load('template'));
    }

    public function toggleFavorite(Generation $generation): JsonResponse
    {
        $this->authorize('update', $generation);
        $generation->update(['is_favorite' => !$generation->is_favorite]);
        return response()->json(['is_favorite' => $generation->is_favorite]);
    }

    public function destroy(Generation $generation): JsonResponse
    {
        $this->authorize('delete', $generation);
        $generation->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
