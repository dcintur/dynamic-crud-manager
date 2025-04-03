<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Permission;
use App\Models\DynamicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(10);
        return view('user-management.index', compact('users'));
    }

    public function create()
    {
        $roles = UserRole::all();
        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_role_id' => 'nullable|exists:user_roles,id'
        ]);

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'user_role_id' => $validatedData['user_role_id']
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = UserRole::all();
        return view('user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_role_id' => 'nullable|exists:user_roles,id'
        ]);

        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'user_role_id' => $validatedData['user_role_id']
        ];

        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    // Roles Management
    public function roles()
    {
        $roles = UserRole::paginate(10);
        return view('user-management.roles.index', compact('roles'));
    }

    public function createRole()
    {
        return view('user-management.roles.create');
    }

    public function storeRole(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:user_roles',
            'description' => 'nullable|string'
        ]);

        UserRole::create($validatedData);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function editRole(UserRole $role)
    {
        return view('user-management.roles.edit', compact('role'));
    }

    public function updateRole(Request $request, UserRole $role)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:user_roles,name,' . $role->id,
            'description' => 'nullable|string'
        ]);

        $role->update($validatedData);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroyRole(UserRole $role)
    {
        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    // Permissions Management
    public function permissions(UserRole $role)
    {
        $pages = DynamicPage::all();
        $permissions = Permission::where('user_role_id', $role->id)->get()->keyBy('dynamic_page_id');
        
        return view('user-management.permissions.edit', compact('role', 'pages', 'permissions'));
    }

    public function updatePermissions(Request $request, UserRole $role)
    {
        $permissions = $request->permissions ?? [];
        
        // Remove existing permissions
        Permission::where('user_role_id', $role->id)->delete();
        
        // Create new permissions
        foreach ($permissions as $pageId => $actions) {
            Permission::create([
                'user_role_id' => $role->id,
                'dynamic_page_id' => $pageId,
                'can_view' => isset($actions['view']),
                'can_create' => isset($actions['create']),
                'can_edit' => isset($actions['edit']),
                'can_delete' => isset($actions['delete']),
                'can_export' => isset($actions['export']),
                'can_import' => isset($actions['import'])
            ]);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Permissions updated successfully.');
    }
}