<?php

namespace App\Http\Controllers;

use App\Models\Sos;
use Illuminate\Http\Request;

class SosController extends Controller
{
    public function index()
    {
        $sos = Sos::orderBy('created_at', 'desc')->get();
        return response()->json(['message' => 'All sos requests retrieved', 'data' => $sos]);
    }

    public function show($id)
    {
        $sos = Sos::find($id);
        return response()->json(['message' => 'Sos request retrieved ', 'data' => $sos]);
    }

    public function store(Request $request)
    {
        if ($request->input('status') === 'attended') {
            $request->merge(['status' => 'attending']);
        }

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'nullable|in:pending,attending,solved',
            'attended_by' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request
                ->file('image')
                ->store('sos', 'public');
        }

        $sos = Sos::create($data);

        return response()->json(['message' => 'Sos request created successflyy', 'data' => $sos]);
    }

    public function update(Request $request, Sos $sos)
    {
        if ($request->input('status') === 'attended') {
            $request->merge(['status' => 'attending']);
        }

        $data = $request->validate([
            'description' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'sometimes|in:pending,attending,solved',
            'attended_by' => 'sometimes|nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request
                ->file('image')
                ->store('sos', 'public');
        }

        $sos->update($data);

        return response()->json(['message' => 'Sos request updated successfully', 'data' => $sos->fresh()]);
    }

    public function destroy($id)
    {
        $data = Sos::find($id);
        $data->delete();

        return response()->json(['message' => 'Sos request deleted']);
    }
}
