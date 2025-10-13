<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.services.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:300', // 300 KB
            'is_active'   => 'required|boolean',
        ]);

        // ✅ Handle image upload
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/services'), $imageName);
        }

        // ✅ Store record
        Service::create([
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $imageName,
            'is_active'   => $request->is_active,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.create', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        // ✅ Validation
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:300',
            'is_active'   => 'required|boolean',
        ]);

        // ✅ Handle image replacement
        if ($request->hasFile('image')) {
            if ($service->image && file_exists(public_path('images/services/' . $service->image))) {
                unlink(public_path('images/services/' . $service->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/services'), $imageName);
            $service->image = $imageName;
        }

        // ✅ Update other fields
        $service->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->is_active,
            'image'       => $service->image,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);

        // Delete image file if exists
        if ($service->image && file_exists(public_path('images/services/' . $service->image))) {
            unlink(public_path('images/services/' . $service->image));
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }

    /**
     * Fetch DataTable data (AJAX).
     */
    public function data(Request $request)
    {
        $columns = ['name', 'description', 'image', 'is_active', 'created_at', 'id'];

        $query = Service::select('id', 'name', 'description', 'image', 'is_active', 'created_at');

        // 🔍 Search filter
        if ($search = strtoupper($request->input('search.value'))) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(name) LIKE ?', ["%$search%"])
                  ->orWhereRaw('UPPER(description) LIKE ?', ["%$search%"]);
            });
        }

        $totalRecords = (clone $query)->count();

        // 🏗 Ordering
        $orderByColumn = $columns[$request->input('order.0.column', 0)];
        $orderByDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderByColumn, $orderByDir);

        // 📄 Pagination
        $limit = $request->input('length');
        $offset = $request->input('start');
        $query->limit($limit)->offset($offset);

        $results = $query->get();

        // ✅ Format Data
        $data = [];
        foreach ($results as $service) {
            $imageHtml = $service->image
                ? '<img src="' . asset('images/services/' . $service->image) . '" width="50" height="50" class="rounded">'
                : '<span class="text-muted">No Image</span>';

            $statusHtml = '
            <label class="switch">
                <input type="checkbox" data-id="' . $service->id . '" class="toggle-status" ' . ($service->is_active ? 'checked' : '') . '>
                <span class="slider round"></span>
            </label>';

            $viewUrl = route('admin.services.show', $service->id);
            $editUrl = route('admin.services.edit', $service->id);
            $deleteUrl = route('admin.services.destroy', $service->id);

            $action = '
            <a href="' . $viewUrl . '" class="btn btn-sm btn-primary me-1"><i class="fas fa-eye"></i></a>
            <a href="' . $editUrl . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
            <form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this service?\')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>';

            $data[] = [
                $service->name,
                $service->description ?? '-',
                $imageHtml,
                $statusHtml,
                $service->created_at->format('d M Y'),
                $action,
            ];
        }

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    /**
     * Toggle active/inactive status (AJAX).
     */
    public function toggleStatus(Request $request)
    {
        $service = Service::findOrFail($request->id);
        $service->is_active = $request->is_active;
        $service->save();

        return response()->json(['message' => 'Status updated successfully']);
    }
}
