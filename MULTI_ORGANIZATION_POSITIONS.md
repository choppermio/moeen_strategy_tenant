# Multi-Organization Employee Positions

## Overview
This document explains how the system handles employees with positions in different organizations.

## Architecture

### Database Structure
- **users** table: Contains user accounts
- **organizations** table: Contains all organizations
- **organization_user** pivot table: Links users to organizations with roles (admin/manager/member)
- **employee_positions** table: Contains employee positions with `organization_id` and `user_id`

### Key Concept
**One user can have MULTIPLE EmployeePosition records, one for each organization they belong to.**

Example:
```
User: Ahmed (ID: 1)
├── Organization 1 (جمعية 1)
│   └── Position: مدير الموارد البشرية
│   └── Role in Org: admin
├── Organization 2 (جمعية 2)
│   └── Position: موظف
│   └── Role in Org: member
```

## How It Works

### 1. Organization Access
Users with **'admin' role** in ANY organization get access to ALL organizations:
```php
// In User model
public function accessibleOrganizations()
{
    // Check if user has 'admin' role in any organization
    $hasAdminRole = $this->organizations()
        ->wherePivot('role', 'admin')
        ->exists();
    
    if ($hasAdminRole) {
        return Organization::where('is_active', true)->get();
    }
    
    // Regular users get only their assigned organizations
    return $this->organizations()->where('is_active', true)->get();
}
```

### 2. Position Selection by Organization
When a user switches organizations, the system automatically selects their position in that organization:

```php
// In helpers.php
function current_user_position()
{
    $current_user = auth()->user()->id;
    $current_org_id = current_organization_id();
    
    // Find position in the current organization
    if ($current_org_id) {
        $employee_position = EmployeePosition::withoutGlobalScopes()
            ->where('user_id', $current_user)
            ->where('organization_id', $current_org_id)
            ->first();
        
        if ($employee_position) {
            return $employee_position;
        }
    }
    
    // Fallback to any position
    return EmployeePosition::withoutGlobalScopes()
        ->where('user_id', $current_user)
        ->first();
}
```

### 3. Helper Functions

#### Get all positions for current user
```php
$positions = user_positions_by_organization();
// Returns: [
//   1 => EmployeePosition (for Org 1),
//   2 => EmployeePosition (for Org 2),
// ]
```

#### Get position in specific organization
```php
$position = user_position_in_organization($organization_id);
```

#### Get current position (in current organization)
```php
$position = current_user_position();
```

## Organization Switcher UI

The sidebar now shows:
1. **Organization name**
2. **User's position in that organization** (if exists)

```
┌─────────────────────────────┐
│ Organization Switcher       │
├─────────────────────────────┤
│ ✓ جمعية 1                   │
│   مدير الموارد البشرية      │  ← Position name
├─────────────────────────────┤
│ ⇄ جمعية 2                   │
│   موظف                      │  ← Position name
└─────────────────────────────┘
```

## Permission Handling

### System Admin (ADMIN_ID in .env)
- Has ALL permissions in ALL organizations
- Can access everything regardless of position

### Organization Admin (role = 'admin')
- Can access ALL organizations
- Permissions are based on their position in CURRENT organization
- Different positions = different permissions per organization

### Regular Users (role = 'member' or 'manager')
- Can only access their assigned organizations
- Permissions based on their position in current organization

## Usage Examples

### Example 1: User with Multiple Positions
```php
// Ahmed has:
// - Position "HR Manager" in Organization 1
// - Position "Employee" in Organization 2

// When in Organization 1:
$position = current_user_position(); // Returns: HR Manager
$permissions = has_permission('manage_employees'); // Checks HR Manager permissions

// Switch to Organization 2:
// Now: $position = Employee
// Permissions change automatically
```

### Example 2: Admin User
```php
// Sara has role='admin' in Organization 1
// She can access both Org 1 and Org 2

// In Organization 1:
$position = current_user_position(); // Her position in Org 1
$orgs = user_organizations(); // Shows ALL organizations

// Can switch to Organization 2:
// Even if she doesn't have a position there, she can still access it
```

## Best Practices

### 1. Creating Positions for New Organizations
When adding a user to a new organization, create an EmployeePosition for them:

```php
$position = EmployeePosition::create([
    'name' => 'مدير القسم',
    'user_id' => $user->id,
    'organization_id' => $organization->id,
    // ... other fields
]);
```

### 2. Checking Permissions
Always use the helper functions:
```php
// Good
if (has_permission('manage_tasks')) {
    // Do something
}

// Bad - Don't do this
if ($user->position->hasPermission('manage_tasks')) {
    // This might check wrong organization
}
```

### 3. Querying Data
Remember that models with `BelongsToOrganization` trait are automatically scoped:
```php
// Returns tasks only in current organization
$tasks = Task::all();

// To get tasks from all organizations:
$tasks = Task::withoutGlobalScope(OrganizationScope::class)->get();
```

## Migration Guide

### If you have existing users with single positions:
1. Keep their existing position records
2. When they join new organizations, create NEW position records
3. The `current_user_position()` function will handle the selection automatically

### Database Changes Required:
No changes needed! The system already supports:
- `employee_positions.organization_id` column exists
- `organization_user.role` column exists
- All necessary relationships are in place

## Troubleshooting

### Issue: User can't see organization switcher
**Solution:** Check if they have 'admin' role in at least one organization:
```sql
SELECT * FROM organization_user WHERE user_id = ? AND role = 'admin';
```

### Issue: Wrong position shown after switching
**Solution:** Ensure the user has an EmployeePosition record for that organization:
```sql
SELECT * FROM employee_positions 
WHERE user_id = ? AND organization_id = ?;
```

### Issue: User can't access data after switching
**Solution:** Check if the `organization_id` was set correctly on the records:
```sql
SELECT * FROM tasks WHERE organization_id = ?;
```

## Future Enhancements

1. **Auto-create positions**: When adding user to organization, automatically create a default position
2. **Position templates**: Pre-defined positions that can be copied to new organizations
3. **Role synchronization**: Option to sync role (admin/manager/member) with position permissions
4. **Organization hierarchy**: Support for parent/child organizations with inherited positions

## Summary

✅ **Current Implementation:**
- Users with 'admin' role see all organizations
- Each user can have different positions in different organizations
- Switching organizations switches the active position
- Permissions are organization-specific based on current position
- UI shows position name under each organization in the switcher

✅ **Key Benefits:**
- Flexible: Users can have different roles in different organizations
- Secure: Data is properly scoped by organization
- Intuitive: Position changes automatically when switching organizations
- Scalable: Works for any number of organizations and positions
