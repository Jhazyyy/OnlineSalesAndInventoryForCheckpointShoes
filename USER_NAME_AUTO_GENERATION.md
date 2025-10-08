# Auto-Generated User Name Feature

## Overview
The system now automatically generates the user's full name from their first name and last name. When users first open the system without providing these details, they see "Test User" as the default name.

## How It Works

### 1. **Name Accessor in User Model**
The `User` model includes a custom accessor that automatically generates the full name:

```php
public function getNameAttribute(): string
{
    // If both first and last names are empty, return "Test User"
    if (empty($this->attributes['first_name']) && empty($this->attributes['last_name'])) {
        return 'Test User';
    }
    
    // Otherwise, combine first and last name
    $firstName = $this->attributes['first_name'] ?? '';
    $lastName = $this->attributes['last_name'] ?? '';
    $fullName = trim($firstName . ' ' . $lastName);
    
    return $fullName ?: 'Test User';
}
```

### 2. **Automatic Sync on Profile Update**
When users update their first name or last name in the profile form, the `ProfileController` automatically updates the `name` field in the database:

```php
// Auto-update 'name' field based on first_name and last_name
if (isset($data['first_name']) || isset($data['last_name'])) {
    $firstName = $data['first_name'] ?? $user->first_name ?? '';
    $lastName = $data['last_name'] ?? $user->last_name ?? '';
    $fullName = trim($firstName . ' ' . $lastName);
    $user->name = $fullName ?: 'Test User';
}
```

### 3. **Default Behavior**
- **New users without names**: Display "Test User"
- **Users with only first name**: Display "FirstName"
- **Users with only last name**: Display "LastName"
- **Users with both names**: Display "FirstName LastName"
- **Empty strings**: Display "Test User"

## User Experience Flow

### First Time Use:
1. User logs in for the first time
2. System shows "Test User" everywhere (dashboard, profile, etc.)
3. User goes to profile page
4. User sees "Test User" in the header
5. User fills in:
   - First Name: "John"
   - Last Name: "Doe"
6. User clicks Submit
7. System automatically updates name to "John Doe"
8. All pages now show "John Doe" instead of "Test User"

### Subsequent Updates:
1. User changes first name from "John" to "Jonathan"
2. System automatically updates name to "Jonathan Doe"
3. Change reflects immediately across the entire system

## Database Structure

The `users` table has three name-related fields:
- `name` (varchar) - Stored full name, auto-generated from first_name + last_name
- `first_name` (varchar, nullable) - User's first name
- `last_name` (varchar, nullable) - User's last name

## Benefits

1. **Consistency**: Name is always synchronized across the system
2. **User-Friendly**: Users see "Test User" instead of empty or null values
3. **Flexibility**: Users can update their name by changing first/last name
4. **Backwards Compatible**: Existing users with names continue to work
5. **Database Integrity**: The name field is always populated

## Profile Form Changes

The profile form no longer includes a separate "Full Name" or "Name" input field. Users only need to fill in:
- First Name
- Last Name

The system automatically generates the full name from these two fields.

## Code Locations

### Model
- **File**: `app/Models/User.php`
- **Accessor**: `getNameAttribute()`
- **Appended**: `full_name` attribute for API responses

### Controller
- **File**: `app/Http/Controllers/ProfileController.php`
- **Method**: `update()`
- **Logic**: Auto-generates name when first_name or last_name changes

### Validation
- **File**: `app/Http/Requests/ProfileUpdateRequest.php`
- **Change**: Removed `name` from validation rules (auto-generated)

### Views
- **File**: `resources/views/profile/edit.blade.php`
- **Display**: Shows `{{ $user->name }}` which uses the accessor
- **File**: `resources/views/profile/partials/update-profile-information-form.blade.php`
- **Form**: Only includes first_name and last_name fields

### Migrations
- **File**: `database/migrations/2025_10_08_103129_update_existing_users_name_field.php`
- **Purpose**: Updates existing users' names based on their first_name and last_name

### Seeders
- **File**: `database/seeders/DatabaseSeeder.php`
- **Test User**: Creates with `first_name` and `last_name` as null to show "Test User"

## Testing

### Manual Testing Steps:
1. Create a new user or use test user (test@example.com)
2. Verify "Test User" appears in profile
3. Update first name to "William"
4. Update last name to "Castillo"
5. Submit form
6. Verify name changes to "William Castillo" everywhere
7. Upload profile photo - name should remain "William Castillo"
8. Change first name to "Will"
9. Verify name updates to "Will Castillo"

### Edge Cases Tested:
- Empty first and last name → "Test User"
- Only first name provided → Shows first name only
- Only last name provided → Shows last name only
- Whitespace handling → Properly trimmed
- Profile photo upload → Name preserved during photo-only updates

## Future Enhancements

Potential improvements:
1. Add middle name field
2. Add preferred name/nickname field
3. Add name prefixes (Mr., Mrs., Dr., etc.)
4. Add name suffixes (Jr., Sr., III, etc.)
5. Support for non-Western name formats
6. Name pronunciation guide
7. Display name customization (different from legal name)
