# User Login Tracking and Access Control Implementation

## Overview
This implementation ensures that each user account tracks login activity and enforces access control based on account status. The system tracks login count, last login time, and validates whether users can access the system based on their `is_active` flag and `status` field.

## Database Schema

### Users Table Fields
The `users` table already contains the following fields:
- **`last_login_at`** (timestamp, nullable): Records the timestamp of the user's last successful login
- **`login_count`** (integer, default: 0): Tracks the total number of successful logins
- **`is_active`** (boolean, default: true): Flag to enable/disable user login capability
- **`status`** (string, default: 'active'): User status with values: 'active', 'inactive', or 'suspended'

## Implementation Details

### 1. Login Request Validation (`app/Http/Requests/Auth/LoginRequest.php`)

**Changes Made:**
- Added `App\Models\User` import
- Enhanced the `authenticate()` method to check user status before allowing login
- Added validation checks for:
  - `is_active` = false: "Your account has been deactivated. Please contact the administrator."
  - `status` = 'inactive': "Your account is inactive. Please contact the administrator."
  - `status` = 'suspended': "Your account has been suspended. Please contact the administrator."

**Validation Flow:**
1. Check if user exists in database
2. If user exists, validate their status
3. If status is not valid, throw ValidationException with appropriate message
4. If status is valid, proceed with password authentication
5. If authentication succeeds, clear rate limiter

### 2. Authenticated Session Controller (`app/Http/Controllers/Auth/AuthenticatedSessionController.php`)

**Changes Made:**
- Added `App\Models\User` import
- Updated `store()` method to track login activity after successful authentication

**Login Tracking Logic:**
```php
// After successful authentication
$user = Auth::user();
if ($user) {
    $user->increment('login_count');  // Increment login counter
    $user->update([
        'last_login_at' => now(),     // Record login timestamp
    ]);
}
```

### 3. User Model (`app/Models/User.php`)

**New Methods Added:**

#### `canLogin(): bool`
Checks if user is allowed to log in.
Returns `true` only if both conditions are met:
- `is_active` = true
- `status` = 'active'

```php
public function canLogin(): bool
{
    return $this->is_active && $this->status === 'active';
}
```

#### `getLoginBlockReasonAttribute(): ?string`
Returns the reason why a user cannot login, or null if they can login.

```php
public function getLoginBlockReasonAttribute(): ?string
{
    if (!$this->is_active) {
        return 'Account has been deactivated';
    }
    if ($this->status === 'inactive') {
        return 'Account is inactive';
    }
    if ($this->status === 'suspended') {
        return 'Account has been suspended';
    }
    return null;
}
```

**Existing Methods:**
- `isActiveUser()`: Already exists, checks if user is active
- `recordLogin()`: Already exists, alternative method to track login

### 4. User Management Index View (`resources/views/user-management/index.blade.php`)

**Changes Made:**
Updated the "Last Login" column to display both:
- Last login time (human-readable format)
- Total login count

**Before:**
```blade
{{ $user->last_login_at->diffForHumans() }}
```

**After:**
```blade
@if ($user->last_login_at)
    <div>{{ $user->last_login_at->diffForHumans() }}</div>
    <div class="text-xs text-gray-400 dark:text-gray-500">
        {{ $user->login_count }} {{ Str::plural('login', $user->login_count) }}
    </div>
@else
    <span class="text-gray-400">Never logged in</span>
@endif
```

### 5. User Show Page (`resources/views/user-management/show.blade.php`)

**Existing Display:**
Already displays:
- Last Login date and time
- Login Count
- Can Login status (based on `is_active`)
- Status (active/inactive/suspended)

## Usage Examples

### Checking if User Can Login
```php
// In controllers or middleware
if (!$user->canLogin()) {
    $reason = $user->login_block_reason;
    return back()->with('error', $reason);
}
```

### Getting Login Statistics
```php
$user = User::find(1);

// Last login time
$lastLogin = $user->last_login_at; // Carbon instance or null

// Total logins
$totalLogins = $user->login_count; // Integer

// Can login?
$canLogin = $user->canLogin(); // Boolean

// Why can't login?
$reason = $user->login_block_reason; // String or null
```

### Manually Tracking Login (Alternative)
```php
// Using the existing recordLogin() method
$user->recordLogin();
```

## Access Control Matrix

| is_active | status      | Can Login? | Reason                            |
|-----------|-------------|------------|-----------------------------------|
| true      | active      | ✅ Yes     | -                                 |
| true      | inactive    | ❌ No      | Account is inactive               |
| true      | suspended   | ❌ No      | Account has been suspended        |
| false     | active      | ❌ No      | Account has been deactivated      |
| false     | inactive    | ❌ No      | Account has been deactivated      |
| false     | suspended   | ❌ No      | Account has been deactivated      |

## Admin Controls

Administrators can control user access through the User Management interface:

### Deactivate User
```php
$user->update(['is_active' => false]);
```
Result: User cannot login. Shows "Your account has been deactivated."

### Change Status to Inactive
```php
$user->update(['status' => 'inactive']);
```
Result: User cannot login. Shows "Your account is inactive."

### Suspend User
```php
$user->update(['status' => 'suspended']);
```
Result: User cannot login. Shows "Your account has been suspended."

### Reactivate User
```php
$user->update([
    'is_active' => true,
    'status' => 'active'
]);
```
Result: User can login again.

## Testing Scenarios

### Test Case 1: Active User Login
1. Create user with `is_active = true` and `status = 'active'`
2. Attempt login with valid credentials
3. **Expected**: Login successful, `login_count` incremented, `last_login_at` updated

### Test Case 2: Deactivated User Login
1. Set user `is_active = false`
2. Attempt login with valid credentials
3. **Expected**: Login fails with message "Your account has been deactivated. Please contact the administrator."

### Test Case 3: Inactive User Login
1. Set user `status = 'inactive'`
2. Attempt login with valid credentials
3. **Expected**: Login fails with message "Your account is inactive. Please contact the administrator."

### Test Case 4: Suspended User Login
1. Set user `status = 'suspended'`
2. Attempt login with valid credentials
3. **Expected**: Login fails with message "Your account has been suspended. Please contact the administrator."

### Test Case 5: Login Count Tracking
1. Login successfully 5 times
2. Check user record
3. **Expected**: `login_count = 5`, `last_login_at` = most recent login time

### Test Case 6: View User Management List
1. Navigate to User Management page
2. Check Last Login column
3. **Expected**: Shows time ago and login count (e.g., "2 hours ago | 15 logins")

## SQL Queries for Testing

### Check User Login Stats
```sql
SELECT 
    email, 
    is_active, 
    status, 
    login_count, 
    last_login_at 
FROM users 
WHERE email = 'user@example.com';
```

### Find Users Who Never Logged In
```sql
SELECT email, created_at 
FROM users 
WHERE last_login_at IS NULL;
```

### Find Most Active Users
```sql
SELECT email, login_count, last_login_at 
FROM users 
ORDER BY login_count DESC 
LIMIT 10;
```

### Find Inactive Users
```sql
SELECT email, is_active, status 
FROM users 
WHERE is_active = 0 OR status IN ('inactive', 'suspended');
```

## Security Notes

1. **Login attempts are rate-limited**: 5 attempts per IP address
2. **Status is checked before password verification**: Prevents timing attacks
3. **Login tracking happens after authentication**: Ensures only successful logins are counted
4. **Account status is enforced at login time**: Users cannot bypass restrictions

## Troubleshooting

### User cannot login but status appears correct
1. Check both `is_active` AND `status` fields
2. Verify email verification if using email verification feature
3. Check for any middleware that might be blocking access

### Login count not incrementing
1. Verify authentication is successful
2. Check if `AuthenticatedSessionController::store()` is being called
3. Ensure database connection is working

### Last login time not updating
1. Verify server time is correct
2. Check database timezone settings
3. Ensure `last_login_at` column is not read-only

## Integration with Email Verification

This login tracking works alongside the email verification system:
1. User must verify email (if `MustVerifyEmail` is implemented)
2. User must have `is_active = true`
3. User must have `status = 'active'`
4. Only then can they access protected routes

All conditions must be met for full system access.
