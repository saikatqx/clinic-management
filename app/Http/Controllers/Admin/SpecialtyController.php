<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Specialty;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.speciality.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.speciality.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ 1. Validate incoming request
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:specialties,name',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'required|boolean',
        ]);

        // ✅ 2. Create specialty
        $specialty = Specialty::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $validated['is_active'],
        ]);

        // ✅ 3. Redirect back with success message
        return redirect()
            ->route('admin.specialties.index')
            ->with('success', 'Specialty created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // ✅ Find the specialty or fail with 404
        $specialty = Specialty::findOrFail($id);

        // ✅ Return a view to display details
        return view('admin.speciality.show', compact('specialty'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $specialty = Specialty::find($id);
        return view('admin.speciality.create', compact('specialty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // ✅ 1. Find the record (404 if not found)
        $specialty = Specialty::findOrFail($id);

        // ✅ 2. Validate input
        $request->validate([
            'name'        => 'required|string|max:255|unique:specialties,name,' . $specialty->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'required|boolean',
        ]);

        // ✅ 3. Update the record
        $specialty->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->is_active,
        ]);

        // ✅ 4. Redirect back to index with success message
        return redirect()
            ->route('admin.specialties.index')
            ->with('success', 'Specialty updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // ✅ Find the specialty or fail with 404
        $specialty = Specialty::findOrFail($id);

        // ✅ Delete the record
        $specialty->delete();

        // ✅ Redirect back with success message
        return redirect()
            ->route('admin.specialties.index')
            ->with('success', 'Specialty deleted successfully!');
    }



    public function data(Request $request)
    {
        // Define columns for ordering (must match table column order)
        $columns = ['name', 'description', 'is_active', 'created_at', 'id'];

        // Base query
        $query = Specialty::select('id', 'name', 'description', 'is_active', 'created_at');

        // 🔎 Search filter
        if ($search = strtoupper($request->input('search.value'))) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(name) LIKE ?', ["%$search%"])
                    ->orWhereRaw('UPPER(description) LIKE ?', ["%$search%"]);
            });
        }

        // Get total records before pagination
        $totalQuery = clone $query;
        $totalRecords = $totalQuery->count();

        // 🏗 Ordering
        $orderByColumn = $columns[$request->input('order.0.column', 0)];
        $orderByDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderByColumn, $orderByDir);

        // 📄 Pagination
        $limit = $request->input('length');
        $offset = $request->input('start');
        $query->limit($limit)->offset($offset);

        $results = $query->get();

        // Format data for DataTables
        $data = [];
        foreach ($results as $value) {
            $user = auth()->user();

            // Toggle switch — guarded by 'toggle specialties'
            $status = $user->can('toggle specialties')
                ? '<label class="switch">
                    <input type="checkbox" data-id="' . $value->id . '" class="toggle-status" ' . ($value->is_active ? 'checked' : '') . '>
                    <span class="slider round"></span>
                </label>'
                : ($value->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>');

            $viewUrl   = route('admin.specialties.show', $value->id);
            $editUrl   = route('admin.specialties.edit', $value->id);
            $deleteUrl = route('admin.specialties.destroy', $value->id);

            // View button — always visible to anyone who can reach this page
            $action = '<a href="' . $viewUrl . '" class="btn btn-sm btn-primary me-1"><i class="fas fa-eye"></i></a>';

            // Edit button — guarded by 'edit specialties'
            if ($user->can('edit specialties')) {
                $action .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>';
            }

            // Delete button — guarded by 'delete specialties'
            if ($user->can('delete specialties')) {
                $action .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this specialty?\')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';
            }

            $row = [];
            $row[] = $value->name;
            $row[] = $value->description ?? '-';
            $row[] = $status;
            $row[] = $value->created_at->format('d M Y');
            $row[] = $action;

            $data[] = $row;
        }

        // Return JSON response
        return Response::json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $specialty = Specialty::findOrFail($request->id);
        $specialty->is_active = $request->is_active;
        $specialty->save();

        return response()->json(['message' => 'Status updated successfully']);
    }
}
