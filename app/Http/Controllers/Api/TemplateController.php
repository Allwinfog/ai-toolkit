<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $templates = Template::active()
            ->when($request->category, fn ($q, $cat) => $q->byCategory($cat))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description', 'icon', 'category', 'type', 'fields', 'is_premium', 'usage_count']);

        return response()->json($templates);
    }

    public function show(Template $template): JsonResponse
    {
        return response()->json($template);
    }

    public function categories(): JsonResponse
    {
        return response()->json(config('ai-toolkit.template_categories'));
    }
}
