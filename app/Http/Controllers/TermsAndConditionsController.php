<?php

namespace App\Http\Controllers;

use App\Models\TermsAndConditions;
use App\Models\UserTermsAcceptance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TermsAndConditionsController extends Controller
{
    /**
     * Display a listing of terms and conditions.
     */
    public function index(Request $request)
    {
        $query = TermsAndConditions::with(['creator', 'updater'])
            ->withCount('acceptances');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('version', 'like', '%' . $request->search . '%');
            });
        }

        $terms = $query->latest()->paginate(15);

        $types = TermsAndConditions::select('type')->distinct()->pluck('type');

        return view('settings.terms.index', compact('terms', 'types'));
    }

    /**
     * Show the form for creating new terms.
     */
    public function create()
    {
        return view('settings.terms.create');
    }

    /**
     * Store newly created terms.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:terms_of_service,privacy_policy,data_privacy,refund_policy,shipping_policy,cookie_policy',
            'content' => 'required|string',
            'version' => 'required|string|max:20',
            'is_active' => 'boolean',
            'requires_acceptance' => 'boolean',
            'effective_date' => 'nullable|date',
            'change_summary' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        $validated['requires_acceptance'] = $request->has('requires_acceptance');

        $terms = TermsAndConditions::create($validated);

        return redirect()->route('settings.terms.index')
            ->with('success', 'Terms and conditions created successfully.');
    }

    /**
     * Display the specified terms.
     */
    public function show(TermsAndConditions $term)
    {
        $term->load(['creator', 'updater', 'acceptances.user']);
        
        $acceptanceStats = [
            'total' => $term->acceptances()->count(),
            'today' => $term->acceptances()->whereDate('accepted_at', today())->count(),
            'this_week' => $term->acceptances()->where('accepted_at', '>=', now()->startOfWeek())->count(),
            'this_month' => $term->acceptances()->whereMonth('accepted_at', now()->month)->count(),
        ];

        $recentAcceptances = $term->acceptances()
            ->with('user')
            ->latest('accepted_at')
            ->limit(10)
            ->get();

        return view('settings.terms.show', compact('term', 'acceptanceStats', 'recentAcceptances'));
    }

    /**
     * Show the form for editing terms.
     */
    public function edit(TermsAndConditions $term)
    {
        return view('settings.terms.edit', compact('term'));
    }

    /**
     * Update the specified terms.
     */
    public function update(Request $request, TermsAndConditions $term)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:terms_of_service,privacy_policy,data_privacy,refund_policy,shipping_policy,cookie_policy',
            'content' => 'required|string',
            'version' => 'required|string|max:20',
            'is_active' => 'boolean',
            'requires_acceptance' => 'boolean',
            'effective_date' => 'nullable|date',
            'change_summary' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        $validated['requires_acceptance'] = $request->has('requires_acceptance');

        $term->update($validated);

        return redirect()->route('settings.terms.index')
            ->with('success', 'Terms and conditions updated successfully.');
    }

    /**
     * Remove the specified terms.
     */
    public function destroy(TermsAndConditions $term)
    {
        $term->delete();

        return redirect()->route('settings.terms.index')
            ->with('success', 'Terms and conditions deleted successfully.');
    }

    /**
     * Show user acceptance form.
     */
    public function showAcceptanceForm()
    {
        $pendingTerms = TermsAndConditions::active()
            ->where('requires_acceptance', true)
            ->get()
            ->filter(function($terms) {
                return !$terms->isAcceptedByUser(Auth::id());
            });

        if ($pendingTerms->isEmpty()) {
            return redirect()->route('dashboard');
        }

        return view('settings.terms.accept', compact('pendingTerms'));
    }

    /**
     * Process user acceptance.
     */
    public function acceptTerms(Request $request)
    {
        $validated = $request->validate([
            'terms_ids' => 'required|array',
            'terms_ids.*' => 'exists:terms_and_conditions,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['terms_ids'] as $termsId) {
                $terms = TermsAndConditions::findOrFail($termsId);
                
                UserTermsAcceptance::create([
                    'user_id' => Auth::id(),
                    'terms_id' => $termsId,
                    'version_accepted' => $terms->version,
                    'accepted_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'acceptance_metadata' => [
                        'accepted_from' => 'web',
                        'session_id' => session()->getId(),
                    ],
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Thank you for accepting our terms and conditions.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process acceptance. Please try again.');
        }
    }

    /**
     * Show public terms page.
     */
    public function showPublic($slug)
    {
        $terms = TermsAndConditions::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('settings.terms.public', compact('terms'));
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(TermsAndConditions $term)
    {
        $term->update([
            'is_active' => !$term->is_active,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Status updated successfully.');
    }
}
