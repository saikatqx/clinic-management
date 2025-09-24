<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Doctor;
use App\Models\Specialty;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.doctors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialties = Specialty::select('id', 'name')->get();
        return view('admin.doctors.create', compact('specialties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function data(Request $request)
    {
        // Define columns for ordering (must match table columns order in DataTable)
        $columns = ['name', 'specialty_id', 'email', 'phone', 'is_active', 'created_at', 'id'];

        // Base query (eager load specialty to avoid N+1 problem)
        $query = Doctor::with('specialty')->select('id', 'specialty_id', 'name', 'email', 'phone', 'is_active', 'created_at');

        // 🔎 Search filter
        if ($search = strtoupper($request->input('search.value'))) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(name) LIKE ?', ["%$search%"])
                    ->orWhereRaw('UPPER(email) LIKE ?', ["%$search%"])
                    ->orWhereRaw('UPPER(phone) LIKE ?', ["%$search%"]);
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
            // ✅ Status toggle based on DB
            $status = '
        <label class="switch">
            <input type="checkbox" data-id="' . $value->id . '" class="toggle-status" ' . ($value->is_active ? 'checked' : '') . '>
            <span class="slider round"></span>
        </label>';

            // ✅ Routes for doctor actions
            $viewUrl = route('admin.doctors.show', $value->id);
            $editUrl = route('admin.doctors.edit', $value->id);
            $deleteUrl = route('admin.doctors.destroy', $value->id);

            $action = '
        <a href="' . $viewUrl . '" class="btn btn-sm btn-primary me-1">
            <i class="fas fa-eye"></i>
        </a>
        <a href="' . $editUrl . '" class="btn btn-sm btn-warning me-1">
            <i class="fas fa-edit"></i>
        </a>
        <form action="' . $deleteUrl . '" method="POST" style="display:inline;">
            ' . csrf_field() . method_field('DELETE') . '
            <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this Doctor?\')">
                <i class="fas fa-trash"></i>
            </button>
        </form>';

            // ✅ Build row
            $row = [];
            $row[] = $value->name;
            $row[] = $value->specialty ? $value->specialty->name : '-';
            $row[] = $value->email;
            $row[] = $value->phone ?? '-';
            $row[] = $status;
            $row[] = $value->created_at->format('d M Y');
            $row[] = $action;

            $data[] = $row;
        }

        // Return JSON for DataTables
        return Response::json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $specialty = Doctor::findOrFail($request->id);
        $specialty->is_active = $request->is_active;
        $specialty->save();

        return response()->json(['message' => 'Status updated successfully']);
    }
}
