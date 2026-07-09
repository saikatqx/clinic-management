<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthPackage;
use App\Models\Diagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class HealthPackageController extends Controller
{
    public function index()
    {
        return view('admin.health_packages.index');
    }

    public function create()
    {
        $diagnostics = Diagnostic::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.health_packages.create', compact('diagnostics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'actual_price' => 'required|numeric|min:0',
            'package_price' => 'required|numeric|min:0',
            'gender' => 'required|in:MALE,FEMALE,BOTH',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'diagnostic_ids' => 'required|array|min:1',
            'diagnostic_ids.*' => 'exists:diagnostics,id'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            // ensure directory exists
            $destinationPath = public_path('images/packages');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $imagePath = $filename;
        }

        $package = HealthPackage::create([
            'name' => $request->name,
            'description' => $request->description,
            'actual_price' => $request->actual_price,
            'package_price' => $request->package_price,
            'gender' => $request->gender,
            'status' => $request->has('status') ? (bool)$request->status : true,
            'image' => $imagePath
        ]);

        // Sync pivot table
        $package->diagnostics()->sync($request->diagnostic_ids);

        return redirect()->route('admin.health-packages.index')
            ->with('success', 'Health Package created successfully!');
    }

    public function edit(string $id)
    {
        $package = HealthPackage::with('diagnostics')->findOrFail($id);
        $diagnostics = Diagnostic::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.health_packages.create', compact('package', 'diagnostics'));
    }

    public function update(Request $request, string $id)
    {
        $package = HealthPackage::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'actual_price' => 'required|numeric|min:0',
            'package_price' => 'required|numeric|min:0',
            'gender' => 'required|in:MALE,FEMALE,BOTH',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'diagnostic_ids' => 'required|array|min:1',
            'diagnostic_ids.*' => 'exists:diagnostics,id'
        ]);

        $imagePath = $package->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($package->image && file_exists(public_path('images/packages/' . $package->image))) {
                unlink(public_path('images/packages/' . $package->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('images/packages');
            $file->move($destinationPath, $filename);
            $imagePath = $filename;
        }

        $package->update([
            'name' => $request->name,
            'description' => $request->description,
            'actual_price' => $request->actual_price,
            'package_price' => $request->package_price,
            'gender' => $request->gender,
            'status' => $request->has('status') ? (bool)$request->status : true,
            'image' => $imagePath
        ]);

        $package->diagnostics()->sync($request->diagnostic_ids);

        return redirect()->route('admin.health-packages.index')
            ->with('success', 'Health Package updated successfully!');
    }

    public function destroy(string $id)
    {
        $package = HealthPackage::findOrFail($id);
        
        // Delete image
        if ($package->image && file_exists(public_path('images/packages/' . $package->image))) {
            unlink(public_path('images/packages/' . $package->image));
        }

        $package->diagnostics()->detach();
        $package->delete();

        return redirect()->route('admin.health-packages.index')
            ->with('success', 'Health Package deleted successfully!');
    }

    public function data(Request $request)
    {
        $columns = ['name', 'gender', 'actual_price', 'package_price', 'status', 'id'];

        $query = HealthPackage::select('id', 'name', 'gender', 'actual_price', 'package_price', 'status');

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
            $status = '
            <label class="switch">
                <input type="checkbox" data-id="' . $value->id . '" class="toggle-status" ' . ($value->status ? 'checked' : '') . '>
                <span class="slider round"></span>
            </label>';

            $editUrl = route('admin.health-packages.edit', $value->id);
            $deleteUrl = route('admin.health-packages.destroy', $value->id);

            $imgUrl = $value->image ? asset('images/packages/' . $value->image) : asset('images/default.png');
            $imgHtml = '<img src="' . $imgUrl . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';

            $action = '
            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i>
            </a>
            <form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this package?\')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>';

            $row = [];
            $row[] = $imgHtml;
            $row[] = $value->name;
            $row[] = ucfirst(strtolower($value->gender));
            $row[] = '₹' . number_format($value->actual_price, 2);
            $row[] = '₹' . number_format($value->package_price, 2);
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
        $package = HealthPackage::findOrFail($request->id);
        $package->status = $request->status;
        $package->save();

        return response()->json(['message' => 'Status updated successfully']);
    }
}
