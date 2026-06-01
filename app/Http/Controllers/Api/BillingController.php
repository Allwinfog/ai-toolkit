<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function plans(): JsonResponse
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return response()->json($plans);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|string',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $user = $request->user();

        if (!$plan->stripe_price_id) {
            // Free plan — just assign it
            $user->update(['plan_id' => $plan->id]);
            $user->resetUsage();
            return response()->json(['message' => 'Plan updated', 'plan' => $plan]);
        }

        try {
            $user->newSubscription('default', $plan->stripe_price_id)
                ->create($validated['payment_method']);

            $user->update(['plan_id' => $plan->id]);
            $user->resetUsage();

            return response()->json(['message' => 'Subscribed successfully', 'plan' => $plan]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Subscription failed: ' . $e->getMessage()], 422);
        }
    }

    public function cancel(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if ($subscription) {
            $subscription->cancel();
        }

        return response()->json(['message' => 'Subscription cancelled']);
    }

    public function resume(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
        }

        return response()->json(['message' => 'Subscription resumed']);
    }

    public function invoices(Request $request): JsonResponse
    {
        $invoices = $request->user()->invoices()->map(fn ($invoice) => [
            'id' => $invoice->id,
            'date' => $invoice->date()->toFormattedDateString(),
            'total' => $invoice->total(),
            'url' => $invoice->invoiceUrl(),
        ]);

        return response()->json($invoices);
    }

    public function setupIntent(Request $request): JsonResponse
    {
        return response()->json([
            'intent' => $request->user()->createSetupIntent(),
        ]);
    }
}
