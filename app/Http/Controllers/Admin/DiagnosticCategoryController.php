<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DiagnosticCategoryController extends Controller
{
    public function index()
    {
        return view('admin.diagnostic_categories.index');
    }

    public function create()
    {
        return view('admin.diagnostic_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(DiagnosticCategory::$rules);

        $category = DiagnosticCategory::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'status' => $request->has('status') ? (bool)$request->status : true,
        ]);

        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')->toMediaCollection(DiagnosticCategory::IMAGE);
        }

        return redirect()->route('admin.diagnostic-categories.index')
            ->with('success', 'Diagnostic Category created successfully!');
    }

    public function edit(string $id)
    {
        $category = DiagnosticCategory::findOrFail($id);
        return view('admin.diagnostic_categories.create', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $category = DiagnosticCategory::findOrFail($id);
        
        $rules = DiagnosticCategory::$rules;
        $rules['name'] = 'required|string|max:255|unique:diagnostic_categories,name,' . $category->id;

        $request->validate($rules);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'status' => $request->has('status') ? (bool)$request->status : true,
        ]);

        if ($request->hasFile('image')) {
            $category->clearMediaCollection(DiagnosticCategory::IMAGE);
            $category->addMediaFromRequest('image')->toMediaCollection(DiagnosticCategory::IMAGE);
        }

        return redirect()->route('admin.diagnostic-categories.index')
            ->with('success', 'Diagnostic Category updated successfully!');
    }

    public function destroy(string $id)
    {
        $category = DiagnosticCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.diagnostic-categories.index')
            ->with('success', 'Diagnostic Category deleted successfully!');
    }

    public function data(Request $request)
    {
        $columns = ['name', 'type', 'description', 'status', 'created_at', 'id'];

        $query = DiagnosticCategory::select('id', 'name', 'type', 'description', 'status', 'created_at');

        if ($search = strtoupper($request->input('search.value'))) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(name) LIKE ?', ["%$search%"])
                    ->orWhereRaw('UPPER(description) LIKE ?', ["%$search%"]);
            });
        }

        $totalRecords = $query->count();

        $orderByColumn = $columns[$request->input('order.0.column', 0)];
        $orderByDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderByColumn, $orderByDir);

        $limit = $request->input('length');
        $offset = $request->input('start');
        $query->limit($limit)->offset($offset);

        $results = $query->get();

        $data = [];
        foreach ($results as $value) {
            $user = auth()->user();

            // Toggle switch — guarded by 'toggle diagnostic categories'
            $status = $user->can('toggle diagnostic categories')
                ? '<label class="switch">
                    <input type="checkbox" data-id="' . $value->id . '" class="toggle-status" ' . ($value->status ? 'checked' : '') . '>
                    <span class="slider round"></span>
                </label>'
                : ($value->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>');

            $editUrl   = route('admin.diagnostic-categories.edit', $value->id);
            $deleteUrl = route('admin.diagnostic-categories.destroy', $value->id);

            $imgUrl  = $value->image_url ? $value->image_url : asset('images/default.png');
            $imgHtml = '<img src="' . $imgUrl . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';

            // Edit button — guarded by 'edit diagnostic categories'
            $action = '';
            if ($user->can('edit diagnostic categories')) {
                $action .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>';
            }

            // Delete button — guarded by 'delete diagnostic categories'
            if ($user->can('delete diagnostic categories')) {
                $action .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this category?\')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';
            }

            $row = [];
            $row[] = $imgHtml;
            $row[] = $value->name;
            $row[] = ($value->type === 'diag') ? 'Diagnostic' : 'Pathology';
            $row[] = $value->description ?? '-';
            $row[] = $status;
            $row[] = $action;

            $data[] = $row;
        }

        return Response::json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $category = DiagnosticCategory::findOrFail($request->id);
        $category->status = $request->status;
        $category->save();

        return response()->json(['message' => 'Status updated successfully']);
    }
}
