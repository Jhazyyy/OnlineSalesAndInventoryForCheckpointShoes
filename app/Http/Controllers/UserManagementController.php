<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::simplePaginate(5);
        return view('user-management.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('user-management.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Auto-generate full name from first_name and last_name
        $fullName = trim($request->first_name . ' ' . $request->last_name);

        User::create([
            'name' => $fullName,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show($userManagement): View
    {
        $user = User::findOrFail($userManagement);


        if (class_exists(Activity::class)) {
            $activities = Activity::where('causer_id', $user->id)
                ->latest()
                ->take(10)->get();
        } else {
            // Example placeholder if you don't use activitylog package
            $activities = collect([
                (object)[
                    'action' => 'login',
                    'description' => 'User logged in',
                    'created_at' => now()->subMinutes(10),
                ],
                (object)[
                    'action' => 'update_profile',
                    'description' => 'Updated profile information',
                    'created_at' => now()->subHours(1),
                ],
            ]);
        }
        return view('user-management.show', compact('user', 'activities'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($userManagement): View
    {
        $user = User::findOrFail($userManagement);
        return view('user-management.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $userManagement): RedirectResponse
    {
        $user = User::findOrFail($userManagement);
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($userManagement): RedirectResponse
    {
        $user = User::findOrFail($userManagement);
        
        // Prevent deleting the currently authenticated user
        if ($user->id === auth()->id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', 'User deleted successfully.');
    }
}
