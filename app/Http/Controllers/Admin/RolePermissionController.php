<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    /* =========================================================================
       1. ROLES CRUD
       ========================================================================= */
    
    public function rolesIndex()
    {
        $roles = Role::all();
        return view('admin.roles.roles_index', compact('roles'));
    }

    public function rolesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        Role::create(['name' => strtolower($request->name)]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function rolesUpdate(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id . '|max:255',
        ]);

        $role->update(['name' => strtolower($request->name)]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function rolesDestroy($id)
    {
        $role = Role::findOrFail($id);

        if (in_array(strtolower($role->name), ['admin', 'super admin'])) {
            return redirect()->back()->with('error', 'Administrative roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    /* =========================================================================
       2. MANAGE ROLE PERMISSIONS
       ========================================================================= */

    public function manageRolePermissions($id)
    {
        $role = Role::findOrFail($id);
        
        // Fetch permissions grouped by their 'group_name' field
        $groupedPermissions = Permission::all()->groupBy('group_name');

        return view('admin.roles.manage_permissions', compact('role', 'groupedPermissions'));
    }

    public function updateRolePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Permissions updated for role: ' . strtoupper($role->name));
    }

    /* =========================================================================
       3. ASSIGN ROLES TO USERS
       ========================================================================= */

    public function assignRoleIndex()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();

        return view('admin.roles.assign_role_index', compact('users', 'roles'));
    }

    public function assignRoleUpdate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'nullable|array',
        ]);

        $user = User::findOrFail($request->user_id);
        
        $user->syncRoles($request->roles ?? []);

        // Sync fallback database column 'role'
        $firstRole = $request->roles[0] ?? 'patient';
        $user->update(['role' => $firstRole]);

        return redirect()->route('admin.assign-role.index')->with('success', 'Roles updated for user: ' . $user->name);
    }

    /* =========================================================================
       4. PERMISSIONS CRUD
       ========================================================================= */

    public function permissionsIndex()
    {
        $permissions = Permission::all();
        return view('admin.roles.permissions_index', compact('permissions'));
    }

    public function permissionsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
            'group_name' => 'required|string|max:255',
        ]);

        Permission::create([
            'name' => strtolower($request->name),
            'group_name' => $request->group_name,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function permissionsUpdate(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id . '|max:255',
            'group_name' => 'required|string|max:255',
        ]);

        $permission->update([
            'name' => strtolower($request->name),
            'group_name' => $request->group_name,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function permissionsDestroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
