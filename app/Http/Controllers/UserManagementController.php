<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Auth\Events\Registered;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->byRole($request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::active()->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'admins' => User::byRole('admin')->count(),
            'users' => User::byRole('user')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Role and status options for filters
        $roles = ['admin', 'user'];
        $statuses = ['active', 'inactive', 'suspended'];

        return view('user-management.index', compact('users', 'stats', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = ['admin', 'user'];
        $statuses = ['active', 'inactive', 'suspended'];
        return view('user-management.create', compact('roles', 'statuses'));
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
            'username' => ['nullable', 'string', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user'],  // Only 'user' role allowed
            'status' => ['required', 'in:active,inactive,suspended'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Auto-generate full name from first_name and last_name
        $fullName = trim($request->first_name . ' ' . $request->last_name);

        $data = [
            'name' => $fullName,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
            'department' => $request->department,
            'position' => $request->position,
            'bio' => $request->bio,
            'email_verified_at' => now(), // Auto-verify users created by admin
        ];

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $image = $request->file('profile_photo');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('profile_photos', $imageName, 'public');
            $data['profile_photo'] = $imagePath;
        }

        $user = User::create($data);
        
        // Assign role using Spatie Permission
        $user->syncRoles([$request->role]);

        return redirect()->route('user-management.index')
            ->with('success', 'User created successfully and email verified.');
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
    public function edit($userManagement): View|RedirectResponse
    {
        $user = User::findOrFail($userManagement);
        
        // Prevent editing admin users
        if ($user->hasRole('admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'Admin users cannot be edited through this interface.');
        }
        
        $roles = ['admin', 'user'];
        $statuses = ['active', 'inactive', 'suspended'];
        return view('user-management.edit', compact('user', 'roles', 'statuses'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $userManagement): RedirectResponse
    {
        $user = User::findOrFail($userManagement);
        
        // Prevent editing admin users
        if ($user->hasRole('admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'Admin users cannot be edited through this interface.');
        }
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user'],  // Only 'user' role allowed
            'status' => ['required', 'in:active,inactive,suspended'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
            'department' => $request->department,
            'position' => $request->position,
            'bio' => $request->bio,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $image = $request->file('profile_photo');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('profile_photos', $imageName, 'public');
            $data['profile_photo'] = $imagePath;
        }

        $user->update($data);
        
        // Update role using Spatie Permission
        $user->syncRoles([$request->role]);

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
        if ($user->id === Auth::id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting admin users
        if ($user->hasRole('admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot delete admin users.');
        }

        // Delete profile photo if exists
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        
        // Remove current user's ID from the list
        $userIds = array_diff($userIds, [Auth::id()]);

        // Remove admin users from the list
        $adminUserIds = User::whereIn('id', $userIds)
            ->whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })
            ->pluck('id')
            ->toArray();
        
        $userIds = array_diff($userIds, $adminUserIds);

        if (empty($userIds)) {
            return redirect()->back()
                ->with('error', 'No users selected or you cannot delete your own account or admin users.');
        }

        // Delete profile photos
        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $user) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
        }

        User::whereIn('id', $userIds)->delete();

        return redirect()->back()
            ->with('success', count($userIds) . ' user(s) deleted successfully.');
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('role')) {
            $query->byRole($request->role);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Exclude admin users from export
        $query->whereDoesntHave('roles', function($q) {
            $q->where('name', 'admin');
        });

        $users = $query->get();

        $filename = 'users_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Username', 'Phone', 'Role', 'Status', 'Department', 'Position', 'Created At', 'Last Login']);

            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->username,
                    $user->phone,
                    $user->primary_role,
                    $user->status,
                    $user->department,
                    $user->position,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
