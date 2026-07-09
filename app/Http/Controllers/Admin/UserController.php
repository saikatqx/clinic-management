<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    /* =========================================================================
       INDEX — DataTable listing
       ========================================================================= */

    public function index()
    {
        return view('admin.users.index');
    }

    public function data(Request $request)
    {
        $columns = ['name', 'email', 'email_verified_at', 'created_at'];

        $query = User::with('roles');

        if ($search = $request->input('search.value')) {
            $search = strtoupper($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(name) LIKE ?', ["%$search%"])
                  ->orWhereRaw('UPPER(email) LIKE ?', ["%$search%"]);
            });
        }

        $totalRecords = $query->count();

        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'created_at';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderCol, $orderDir);

        $query->limit($request->input('length'))->offset($request->input('start'));

        $results = $query->get();

        $data = [];
        foreach ($results as $user) {
            $authUser = auth()->user();

            // Verified badge
            $verifiedBadge = $user->email_verified_at
                ? '<span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>Verified</span>'
                : '<span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i>Not Verified</span>';

            // Roles badge
            $roles = $user->roles->pluck('name');
            $rolesBadge = $roles->isNotEmpty()
                ? $roles->map(fn($r) => '<span class="badge bg-primary me-1">' . ucfirst($r) . '</span>')->implode('')
                : '<span class="badge bg-secondary">No roles</span>';

            // Actions
            $editUrl   = route('admin.users.edit', $user->id);
            $deleteUrl = route('admin.users.destroy', $user->id);

            $action = '';

            if ($authUser->can('edit users')) {
                $action .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning me-1" title="Edit User">
                    <i class="fas fa-edit"></i>
                </a>';
            }

            if ($authUser->can('delete users') && $user->id !== auth()->id()) {
                $action .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this user? This cannot be undone.\')" title="Delete User">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';
            }

            $data[] = [
                $user->name,
                $user->email,
                $rolesBadge,
                $verifiedBadge,
                $user->created_at ? $user->created_at->format('d M Y, h:i A') : '-',
                $action ?: '<span class="text-muted small">—</span>',
            ];
        }

        return Response::json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data'            => $data,
        ]);
    }

    /* =========================================================================
       CREATE / STORE
       ========================================================================= */

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email|max:255',
            'password'              => 'required|string|min:8|confirmed',
            'roles'                 => 'nullable|array',
            'email_verified'        => 'nullable|boolean',
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);

        // Assign Spatie roles
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
            $user->update(['role' => $request->roles[0]]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" created successfully.');
    }

    /* =========================================================================
       EDIT / UPDATE
       ========================================================================= */

    public function edit($id)
    {
        $user  = User::with('roles')->findOrFail($id);
        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $id . '|max:255',
            'password'       => 'nullable|string|min:8|confirmed',
            'roles'          => 'nullable|array',
            'email_verified' => 'nullable|boolean',
        ]);

        $data = [
            'name'              => $request->name,
            'email'             => $request->email,
            'email_verified_at' => $request->boolean('email_verified') ? ($user->email_verified_at ?? now()) : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Sync roles
        $user->syncRoles($request->roles ?? []);
        if (!empty($request->roles)) {
            $user->update(['role' => $request->roles[0]]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" updated successfully.');
    }

    /* =========================================================================
       DESTROY
       ========================================================================= */

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
