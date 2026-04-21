<?php

namespace App\Http\Controllers;

use App\Models\DisasterPoint;
use Illuminate\Http\Request;

class DisatserController extends Controller
{
    public function index()
    {
        $disaster = DisasterPoint::get();
        return response()->json(['message' => 'All DisasterPoint requests retrieved', 'data' => $disaster]);
    }

    public function show($id)
    {
        $disaster = DisasterPoint::find($id);
        return response()->json(['message' => 'DisasterPoint request retrieved ', 'data' => $disaster]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:flood,storm,earthquake,fire',
            'description' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'disaster_level' => 'required:low,medium,high,very_high'
        ]);

        $disaster = DisasterPoint::create($data);

        return response()->json([
            'message' => 'DisasterPoint request created successfully',
            'data' => $disaster,
        ], 201);
    }

    public function update(Request $request, DisasterPoint $disaster)
    {
        $data = $request->validate([
            'type' => 'sometimes|in:flood,storm,earthquake,fire',
            'disaster_level' => 'sometimes|in:low,medium,high,very_high',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'disasterType' => 'sometimes|in:flood,storm,earthquake,fire',
            'disasterLevel' => 'sometimes|in:low,medium,high,very_high',
            'description' => 'sometimes|string',
            'location.lat' => 'sometimes|numeric|between:-90,90',
            'location.lng' => 'sometimes|numeric|between:-180,180',
        ]);

        $mappedData = [];

        if (isset($data['type'])) {
            $mappedData['type'] = $data['type'];
        }

        if (isset($data['disasterType'])) {
            $mappedData['type'] = $data['disasterType'];
        }

        if (isset($data['disaster_level'])) {
            $mappedData['disaster_level'] = $data['disaster_level'];
        }

        if (isset($data['disasterLevel'])) {
            $mappedData['disaster_level'] = $data['disasterLevel'];
        }

        if (isset($data['description'])) {
            $mappedData['description'] = $data['description'];
        }

        if (isset($data['location']['lat'])) {
            $mappedData['latitude'] = $data['location']['lat'];
        }

        if (isset($data['latitude'])) {
            $mappedData['latitude'] = $data['latitude'];
        }

        if (isset($data['location']['lng'])) {
            $mappedData['longitude'] = $data['location']['lng'];
        }

        if (isset($data['longitude'])) {
            $mappedData['longitude'] = $data['longitude'];
        }

        $disaster->update($mappedData);

        return response()->json([
            'message' => 'DisasterPoint updated successfully',
            'data' => $disaster->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $data = DisasterPoint::find($id);
        $data->delete();

        return response()->json(['message' => 'DisasterPoint request deleted']);
    }
}
