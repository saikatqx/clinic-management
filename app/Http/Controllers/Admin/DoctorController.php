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
        // 🔹 Validation
        $request->validate([
            'name'          => 'required|string|max:255',
            'specialty_id'  => 'required|exists:specialties,id',
            'email'         => 'required|email|unique:doctors,email',
            'phone'         => 'nullable|string|max:15|unique:doctors,phone',
            'qualification' => 'nullable|string|max:255',
            'bio'           => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:300', // 300 KB
            'is_active'     => 'required|boolean',
        ]);

        // 🔹 Handle file upload
        $imageName = null;
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/doctors'), $imageName);
        }

        // 🔹 Save Doctor
        Doctor::create([
            'name'          => $request->name,
            'specialty_id'  => $request->specialty_id,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'qualification' => $request->qualification,
            'bio'           => $request->bio,
            'profile_image' => $imageName,
            'is_active'     => $request->is_active,
        ]);

        // 🔹 Redirect with success
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        $specialties = Specialty::select('id', 'name')->get();
        return view('admin.doctors.create', compact('doctor', 'specialties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 🔹 Find doctor
        $doctor = Doctor::findOrFail($id);

        // 🔹 Validation
        $request->validate([
            'name'          => 'required|string|max:255',
            'specialty_id'  => 'required|exists:specialties,id',
            'email'         => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone'         => 'nullable|string|max:15',
            'qualification' => 'nullable|string|max:255',
            'bio'           => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:300', // 300 KB
            'is_active'     => 'required|boolean',
        ]);

        // 🔹 Handle file upload (replace old image if new one is uploaded)
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($doctor->profile_image && file_exists(public_path('images/doctors/' . $doctor->profile_image))) {
                unlink(public_path('images/doctors/' . $doctor->profile_image));
            }

            $image = $request->file('profile_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/doctors'), $imageName);

            $doctor->profile_image = $imageName;
        }

        // 🔹 Update other fields
        $doctor->update([
            'name'          => $request->name,
            'specialty_id'  => $request->specialty_id,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'qualification' => $request->qualification,
            'bio'           => $request->bio,
            'is_active'     => $request->is_active,
            'profile_image' => $doctor->profile_image, // keep existing if no new file
        ]);

        // 🔹 Redirect with success
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        // Find doctor by ID
        $doctor = Doctor::findOrFail($id);

        // If profile image exists, delete it from storage
        if ($doctor->profile_image && file_exists(public_path('images/doctors/' . $doctor->profile_image))) {
            unlink(public_path('images/doctors/' . $doctor->profile_image));
        }

        // Delete doctor
        $doctor->delete();

        // Redirect back with success message
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor deleted successfully!');
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
