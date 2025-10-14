<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index()
    {
        return view('admin.banners.index');
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:512', // 512 KB max
        ]);

        // 🔹 Handle Image Upload
        $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
        $request->image->move(public_path('images/banners'), $imageName);

        // 🔹 Create Banner
        Banner::create([
            'title'        => $request->title,
            'subtitle'     => $request->subtitle,
            'button_text'  => $request->button_text,
            'button_link'  => $request->button_link,
            'order'        => $request->order ?? 0,
            'is_active'    => $request->is_active ?? 1,
            'image'        => $imageName,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner added successfully!');
    }

    /**
     * Show the form for editing an existing banner.
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.create', compact('banner'));
    }

    /**
     * Update an existing banner.
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:512',
        ]);

        $data = $request->only([
            'title',
            'subtitle',
            'button_text',
            'button_link',
            'order',
            'is_active'
        ]);

        // 🔹 Replace old image if a new one is uploaded
        if ($request->hasFile('image')) {
            if ($banner->image && file_exists(public_path('images/banners/' . $banner->image))) {
                unlink(public_path('images/banners/' . $banner->image));
            }

            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('images/banners'), $imageName);
            $data['image'] = $imageName;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    /**
     * Delete a banner and its image.
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image && file_exists(public_path('images/banners/' . $banner->image))) {
            unlink(public_path('images/banners/' . $banner->image));
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner deleted successfully!');
    }

    /**
     * Fetch banner data for DataTables.
     */
    public function data(Request $request)
    {
        $columns = ['image', 'title', 'subtitle', 'button_text', 'is_active', 'created_at', 'id'];

        $query = Banner::select('id', 'image', 'title', 'subtitle', 'button_text', 'is_active', 'created_at');

        // 🔎 Search Filter
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('button_text', 'like', "%{$search}%");
            });
        }

        $totalRecords = $query->count();

        // 🏗 Order & Paginate
        $orderBy = $columns[$request->input('order.0.column', 0)];
        $orderDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderBy, $orderDir)
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10));

        $banners = $query->get();

        // 🔹 Format for DataTables
        $data = [];
        foreach ($banners as $banner) {
            $status = '
                <label class="switch">
                    <input type="checkbox" data-id="' . $banner->id . '" class="toggle-status" ' . ($banner->is_active ? 'checked' : '') . '>
                    <span class="slider round"></span>
                </label>';

            $imageTag = $banner->image
                ? '<img src="' . asset('images/banners/' . $banner->image) . '" class="img-thumbnail" width="100">'
                : '-';

            $editUrl = route('admin.banners.edit', $banner->id);
            $deleteUrl = route('admin.banners.destroy', $banner->id);

            $action = '
                <a href="' . $editUrl . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                <form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this banner?\')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';

            $data[] = [
                $imageTag,
                $banner->title ?? '-',
                $banner->subtitle ?? '-',
                $banner->button_text ?? '-',
                $status,
                $banner->created_at ? $banner->created_at->format('d M Y') : '-',
                $action
            ];
        }

        return Response::json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data
        ]);
    }

    /**
     * Toggle active/inactive status (AJAX).
     */
    public function toggleStatus(Request $request)
    {
        $banner = Banner::findOrFail($request->id);
        $banner->is_active = $request->is_active;
        $banner->save();

        return response()->json(['message' => 'Banner status updated successfully!']);
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.show', compact('banner'));
    }
}
