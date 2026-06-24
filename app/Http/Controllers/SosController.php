<?php

namespace App\Http\Controllers;

use App\Models\Sos;
use Illuminate\Http\Request;

class SosController extends Controller
{
    public function index()
    {
        $sos = Sos::orderBy('created_at', 'desc')->get();
        return response()->json(['message' => 'All sos requests retrieved', 'data' => $sos->map(fn (Sos $sos) => $this->formatSos($sos))]);
    }

    public function show($id)
    {
        $sos = Sos::find($id);
        return response()->json(['message' => 'Sos request retrieved ', 'data' => $sos ? $this->formatSos($sos) : null]);
    }

    public function store(Request $request)
    {
        $this->normalizeRequest($request);

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

        return response()->json([
            'message' => 'Sos request created successfully',
            'data' => $this->formatSos($sos->fresh()),
        ]);
    }

    public function update(Request $request, Sos $sos)
    {
        $this->normalizeRequest($request);

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

        return response()->json([
            'message' => 'Sos request updated successfully',
            'data' => $this->formatSos($sos->fresh()),
        ]);
    }

    public function destroy($id)
    {
        $data = Sos::find($id);
        $data->delete();

        return response()->json(['message' => 'Sos request deleted']);
    }

    private function normalizeRequest(Request $request): void
    {
        $latitude = $request->input('latitude')
            ?? $request->input('laittitude')
            ?? $request->input('latitutde')
            ?? $request->input('lat')
            ?? $request->input('location.lat');

        if ($latitude !== null) {
            $request->merge(['latitude' => $latitude]);
        }

        $longitude = $request->input('longitude')
            ?? $request->input('long')
            ?? $request->input('lng')
            ?? $request->input('location.lng');

        if ($longitude !== null) {
            $request->merge(['longitude' => $longitude]);
        }
    }

    private function formatSos(?Sos $sos): ?array
    {
        if (! $sos) {
            return null;
        }

        return [
            'id' => $sos->id,
            'description' => $sos->description,
            'latitude' => $sos->latitude,
            'longitude' => $sos->longitude,
            'status' => $sos->status,
            'attended_by' => $sos->attended_by,
            'image_path' => $sos->image_path,
            'image_url' => $sos->image_url,
            'created_at' => $sos->created_at,
            'updated_at' => $sos->updated_at,
        ];
    }
}
