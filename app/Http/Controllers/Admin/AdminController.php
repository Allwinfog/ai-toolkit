<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use App\Models\Plan;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ── Dashboard Stats ──

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'new_today' => User::whereDate('created_at', today())->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            ],
            'generations' => [
                'total' => Generation::count(),
                'today' => Generation::whereDate('created_at', today())->count(),
                'by_type' => Generation::select('type', DB::raw('count(*) as count'))
                    ->groupBy('type')->pluck('count', 'type'),
                'total_cost' => Generation::sum('cost'),
            ],
            'popular_templates' => Template::orderByDesc('usage_count')->limit(5)
                ->get(['id', 'name', 'usage_count']),
            'revenue' => [
                'mrr' => Plan::join('users', 'plans.id', '=', 'users.plan_id')
                    ->sum('plans.price'),
            ],
        ]);
    }

    // ── User Management ──

    public function users(Request $request): JsonResponse
    {
        $users = User::with('plan:id,name')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'role' => 'sometimes|in:user,admin',
            'plan_id' => 'sometimes|nullable|exists:plans,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->update($validated);
        return response()->json($user->fresh()->load('plan'));
    }

    // ── Plan Management ──

    public function plans(): JsonResponse
    {
        return response()->json(Plan::withCount('users')->orderBy('sort_order')->get());
    }

    public function storePlan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:plans',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'stripe_price_id' => 'nullable|string',
            'text_generations' => 'required|integer|min:-1',
            'image_generations' => 'required|integer|min:-1',
            'code_generations' => 'required|integer|min:-1',
            'words_per_generation' => 'required|integer|min:100',
            'features' => 'nullable|array',
        ]);

        $plan = Plan::create($validated);
        return response()->json($plan, 201);
    }

    public function updatePlan(Request $request, Plan $plan): JsonResponse
    {
        $plan->update($request->validated());
        return response()->json($plan);
    }

    // ── Template Management ──

    public function templates(): JsonResponse
    {
        return response()->json(Template::orderBy('sort_order')->get());
    }

    public function storeTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:templates',
            'description' => 'required|string',
            'icon' => 'nullable|string',
            'category' => 'required|string',
            'type' => 'required|in:text,image,code',
            'system_prompt' => 'required|string',
            'user_prompt_template' => 'required|string',
            'fields' => 'required|array',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
        ]);

        $template = Template::create($validated);
        return response()->json($template, 201);
    }

    public function updateTemplate(Request $request, Template $template): JsonResponse
    {
        $template->update($request->all());
        return response()->json($template);
    }

    public function deleteTemplate(Template $template): JsonResponse
    {
        $template->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
