<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Models\DiagnosticCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DiagnosticController extends Controller
{
    public function indexDiag()
    {
        return view('admin.diagnostics.index', ['type' => 'diag']);
    }

    public function indexPath()
    {
        return view('admin.diagnostics.index', ['type' => 'path']);
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'diag');
        $categories = DiagnosticCategory::where('status', 1)->where('type', $type)->orderBy('name', 'asc')->get();
        return view('admin.diagnostics.create', compact('categories', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate(Diagnostic::$rules);

        $diagnostic = Diagnostic::create([
            'diagnostic_category_id' => $request->diagnostic_category_id,
            'name' => $request->name,
            'price' => $request->price,
            'status' => $request->has('status') ? (bool)$request->status : true,
        ]);

        if ($request->hasFile('image')) {
            $diagnostic->addMediaFromRequest('image')->toMediaCollection(Diagnostic::IMAGE);
        }

        $category = DiagnosticCategory::find($request->diagnostic_category_id);
        $redirectRoute = ($category && $category->type === 'path') ? 'admin.diagnostics.indexPath' : 'admin.diagnostics.indexDiag';

        return redirect()->route($redirectRoute)
            ->with('success', (($category && $category->type === 'path') ? 'Pathology' : 'Diagnostic') . ' Test created successfully!');
    }

    public function edit(string $id)
    {
        $diagnostic = Diagnostic::findOrFail($id);
        $type = optional($diagnostic->category)->type ?? 'diag';
        $categories = DiagnosticCategory::where('status', 1)->where('type', $type)->orderBy('name', 'asc')->get();
        return view('admin.diagnostics.create', compact('diagnostic', 'categories', 'type'));
    }

    public function update(Request $request, string $id)
    {
        $diagnostic = Diagnostic::findOrFail($id);
        
        $rules = Diagnostic::$rules;
        $rules['name'] = 'required|string|max:255|unique:diagnostics,name,' . $diagnostic->id;

        $request->validate($rules);

        $diagnostic->update([
            'diagnostic_category_id' => $request->diagnostic_category_id,
            'name' => $request->name,
            'price' => $request->price,
            'status' => $request->has('status') ? (bool)$request->status : true,
        ]);

        if ($request->hasFile('image')) {
            $diagnostic->clearMediaCollection(Diagnostic::IMAGE);
            $diagnostic->addMediaFromRequest('image')->toMediaCollection(Diagnostic::IMAGE);
        }

        $category = DiagnosticCategory::find($request->diagnostic_category_id);
        $redirectRoute = ($category && $category->type === 'path') ? 'admin.diagnostics.indexPath' : 'admin.diagnostics.indexDiag';

        return redirect()->route($redirectRoute)
            ->with('success', (($category && $category->type === 'path') ? 'Pathology' : 'Diagnostic') . ' Test updated successfully!');
    }

    public function destroy(string $id)
    {
        $diagnostic = Diagnostic::findOrFail($id);
        $category = $diagnostic->category;
        $diagnostic->delete();

        $redirectRoute = ($category && $category->type === 'path') ? 'admin.diagnostics.indexPath' : 'admin.diagnostics.indexDiag';

        return redirect()->route($redirectRoute)
            ->with('success', (($category && $category->type === 'path') ? 'Pathology' : 'Diagnostic') . ' Test deleted successfully!');
    }

    public function data(Request $request, $type = 'diag')
    {
        $columns = ['name', 'category', 'price', 'status', 'created_at', 'id'];

        $query = Diagnostic::whereHas('category', function($q) use ($type) {
            $q->where('type', $type);
        })->with('category')->select('id', 'diagnostic_category_id', 'name', 'price', 'status', 'created_at');

        if ($search = strtoupper($request->input('search.value'))) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(name) LIKE ?', ["%$search%"])
                    ->orWhereHas('category', function ($qc) use ($search) {
                        $qc->whereRaw('UPPER(name) LIKE ?', ["%$search%"]);
                    });
            });
        }

        $totalRecords = $query->count();

        $orderByColumn = $columns[$request->input('order.0.column', 0)];
        $orderByDir = $request->input('order.0.dir', 'asc');
        
        if ($orderByColumn === 'category') {
            $query->join('diagnostic_categories', 'diagnostics.diagnostic_category_id', '=', 'diagnostic_categories.id')
                ->orderBy('diagnostic_categories.name', $orderByDir)
                ->select('diagnostics.*');
        } else {
            $query->orderBy($orderByColumn, $orderByDir);
        }

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

            $editUrl = route('admin.diagnostics.edit', $value->id);
            $deleteUrl = route('admin.diagnostics.destroy', $value->id);

            $imgUrl = $value->image_url ? $value->image_url : asset('images/default.png');
            $imgHtml = '<img src="' . $imgUrl . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';

            $action = '
            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i>
            </a>
            <form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this test?\')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>';

            $row = [];
            $row[] = $imgHtml;
            $row[] = $value->name;
            $row[] = $value->category->name ?? '-';
            $row[] = '₹' . number_format($value->price, 2);
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
        $diagnostic = Diagnostic::findOrFail($request->id);
        $diagnostic->status = $request->status;
        $diagnostic->save();

        return response()->json(['message' => 'Status updated successfully']);
    }
}
