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
            'super_admins' => User::byRole('super_admin')->count(),
            'admins' => User::byRole('admin')->count(),
            'salespersons' => User::byRole('salesperson')->count(),
            'inventory_clerks' => User::byRole('inventory_clerk')->count(),
            'users' => User::byRole('user')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Role and status options for filters
        $roles = ['super admin', 'admin', 'salesperson', 'inventory clerk', 'user'];
        $statuses = ['active', 'inactive', 'suspended'];

        return view('user-management.index', compact('users', 'stats', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $currentUser = Auth::user();
        
        // Super admin can create any role
        // Admin can create admin, salesperson, inventory_clerk, and user roles (not super_admin)
        if ($currentUser->hasRole('super_admin')) {
            $roles = ['super_admin', 'admin', 'salesperson', 'inventory_clerk', 'user'];
        } elseif ($currentUser->hasRole('admin')) {
            $roles = ['admin', 'salesperson', 'inventory_clerk', 'user'];
        } else {
            $roles = ['user'];
        }
        
        $statuses = ['active', 'inactive', 'suspended'];
        return view('user-management.create', compact('roles', 'statuses'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentUser = Auth::user();
        
        // Determine allowed roles based on current user
        if ($currentUser->hasRole('super_admin')) {
            $allowedRoles = ['super_admin', 'admin', 'salesperson', 'inventory_clerk', 'user'];
        } elseif ($currentUser->hasRole('admin')) {
            $allowedRoles = ['admin', 'salesperson', 'inventory_clerk', 'user'];
        } else {
            $allowedRoles = ['user'];
        }
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
            'role' => ['required', Rule::in($allowedRoles)],
            'status' => ['required', 'in:active,inactive,suspended'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'password.regex' => 'The password must contain at least one special character (!@#$%^&*(),.?":{}|<>).',
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

        // Log activity
        if (class_exists(Activity::class)) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->withProperties(['role' => $request->role, 'status' => $request->status])
                ->log('created');
        }

        return redirect()->route('user-management.index')
            ->with('success', 'User created successfully and email verified.');
    }

    /**
     * Display the specified user.
     */
    public function show($userManagement): View
    {
        $user = User::findOrFail($userManagement);

        // Get user activities from activity log
        if (class_exists(Activity::class)) {
            // Get activities caused by this user (their actions)
            $activities = Activity::where('causer_id', $user->id)
                ->orWhere(function($query) use ($user) {
                    // Also get activities performed ON this user
                    $query->where('subject_type', User::class)
                          ->where('subject_id', $user->id);
                })
                ->with('causer')
                ->latest()
                ->take(20)
                ->get()
                ->map(function($activity) use ($user) {
                    // Format activity for display
                    $action = $activity->description ?? $activity->event ?? 'unknown';
                    $description = '';
                    
                    // Build description based on activity
                    if ($activity->causer_id == $user->id) {
                        // User performed this action
                        $description = $this->formatUserActivity($activity);
                    } else {
                        // Action was performed ON this user
                        $causerName = $activity->causer ? $activity->causer->name : 'System';
                        $description = "{$causerName} " . $this->formatActivityOnUser($activity);
                    }
                    
                    return (object)[
                        'action' => $action,
                        'description' => $description,
                        'created_at' => $activity->created_at,
                    ];
                });
        } else {
            // Fallback if activity log package is not available
            $activities = collect([]);
        }
        
        return view('user-management.show', compact('user', 'activities'));
    }
    
    /**
     * Format activity description for user actions
     */
    private function formatUserActivity($activity)
    {
        $subject = $activity->subject_type ? class_basename($activity->subject_type) : 'item';
        $event = $activity->event ?? $activity->description;
        
        $descriptions = [
            'created' => "Created a new {$subject}",
            'updated' => "Updated a {$subject}",
            'deleted' => "Deleted a {$subject}",
            'login' => 'Logged into the system',
            'logout' => 'Logged out of the system',
            'password_changed' => 'Changed their password',
            'profile_updated' => 'Updated their profile',
        ];
        
        return $descriptions[$event] ?? ucfirst(str_replace('_', ' ', $event));
    }
    
    /**
     * Format activity description for actions performed on user
     */
    private function formatActivityOnUser($activity)
    {
        $event = $activity->event ?? $activity->description;
        
        $descriptions = [
            'created' => 'created this user account',
            'updated' => 'updated this user\'s information',
            'deleted' => 'deleted this user account',
            'status_changed' => 'changed this user\'s status',
            'role_changed' => 'changed this user\'s role',
            'suspended' => 'suspended this user account',
            'activated' => 'activated this user account',
        ];
        
        return $descriptions[$event] ?? str_replace('_', ' ', $event);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($userManagement): View|RedirectResponse
    {
        $user = User::findOrFail($userManagement);
        $currentUser = Auth::user();
        
        // Super admin cannot be edited by anyone
        if ($user->hasRole('super_admin') && !$currentUser->hasRole('super_admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'Super admin users cannot be edited.');
        }
        
        // Admin users can only be edited by super admin
        if ($user->hasRole('admin') && !$currentUser->hasRole('super_admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'Admin users can only be edited by super admin.');
        }
        
        // Determine available roles based on current user
        if ($currentUser->hasRole('super_admin')) {
            $roles = ['super_admin', 'admin', 'salesperson', 'inventory_clerk', 'user'];
        } elseif ($currentUser->hasRole('admin')) {
            $roles = ['admin', 'salesperson', 'inventory_clerk', 'user'];
        } else {
            $roles = ['user'];
        }
        
        $statuses = ['active', 'inactive', 'suspended'];
        return view('user-management.edit', compact('user', 'roles', 'statuses'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $userManagement): RedirectResponse
    {
        $user = User::findOrFail($userManagement);
        $currentUser = Auth::user();
        
        // Super admin cannot be edited by anyone except themselves
        if ($user->hasRole('super_admin') && !$currentUser->hasRole('super_admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'Super admin users cannot be edited.');
        }
        
        // Admin users can only be edited by super admin
        if ($user->hasRole('admin') && !$currentUser->hasRole('super_admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'Admin users can only be edited by super admin.');
        }
        
        // Determine allowed roles based on current user (match creation permissions)
        if ($currentUser->hasRole('super_admin')) {
            $allowedRoles = ['super_admin', 'admin', 'salesperson', 'inventory_clerk', 'user'];
        } elseif ($currentUser->hasRole('admin')) {
            $allowedRoles = ['admin', 'salesperson', 'inventory_clerk', 'user'];
        } else {
            $allowedRoles = ['user'];
        }
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
            'role' => ['required', Rule::in($allowedRoles)],
            'status' => ['required', 'in:active,inactive,suspended'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'password.regex' => 'The password must contain at least one special character (!@#$%^&*(),.?":{}|<>).',
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

        $oldData = $user->only(['status', 'department', 'position']);
        $oldRole = $user->roles->pluck('name')->first();
        
        $user->update($data);
        
        // Update role using Spatie Permission
        $user->syncRoles([$request->role]);

        // Log activity
        if (class_exists(Activity::class)) {
            $changes = [];
            if ($oldRole !== $request->role) $changes['role'] = "from {$oldRole} to {$request->role}";
            if ($oldData['status'] !== $request->status) $changes['status'] = "from {$oldData['status']} to {$request->status}";
            if ($request->filled('password')) $changes['password'] = 'changed';
            
            activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->withProperties(['changes' => $changes])
                ->log('updated');
        }

        return redirect()->route('user-management.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($userManagement): RedirectResponse
    {
        $user = User::findOrFail($userManagement);
        $currentUser = Auth::user();
        
        // Prevent deleting the currently authenticated user
        if ($user->id === Auth::id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Log activity before deletion
        if (class_exists(Activity::class)) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->withProperties(['email' => $user->email, 'role' => $user->roles->pluck('name')->first()])
                ->log('deleted');
        }
        
        // Prevent deleting super admin users
        if ($user->hasRole('super_admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot delete super admin users.');
        }
        
        // Only super admin can delete admin users
        if ($user->hasRole('admin') && !$currentUser->hasRole('super_admin')) {
            return redirect()->route('user-management.index')
                ->with('error', 'Only super admin can delete admin users.');
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
