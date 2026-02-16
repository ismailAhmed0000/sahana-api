<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\SafePoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SafePointController extends Controller
{
    public function index()
    {
        $safe = SafePoint::with('resources')->get();
        return response()->json(['message' => 'All SafePoint requests retrieved', 'data' => $safe]);
    }

    public function show($id)
    {
        $safe = SafePoint::with('resources')->find($id);
        return response()->json(['message' => 'SafePoint request retrieved ', 'data' => $safe]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:shelter,food_distribution,medical_camp,other',
            'capacity' => 'required|integer|min:0',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
            'resourceStatus' => 'required|in:sufficient,insufficient,critical',
            'resources' => 'required|array|min:1',
            'resources.*.type' => 'required|in:clothes,food,medical_supplies,water,others',
            'resources.*.status' => 'required|string',
        ]);

        $safePoint = DB::transaction(function () use ($data) {
            $safePoint = SafePoint::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'capacity' => $data['capacity'],
                'status' => $data['resourceStatus'],
                'latitude' => data_get($data, 'location.lat'),
                'longitude' => data_get($data, 'location.lng'),
            ]);

            foreach ($data['resources'] as $resource) {
                $safePoint->resources()->create([
                    'type' => $resource['type'],
                    'description' => $resource['status'],
                ]);
            }

            return $safePoint;
        });

        return response()->json([
            'message' => 'Safe point created successfully',
            'data' => $safePoint->load('resources'),
        ], 201);
    }

    public function update(Request $request, SafePoint $safePoint)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:shelter,food_distribution,medical_camp,other',
            'capacity' => 'sometimes|integer|min:0',
            'location.lat' => 'sometimes|numeric|between:-90,90',
            'location.lng' => 'sometimes|numeric|between:-180,180',
            'resourceStatus' => 'sometimes|in:sufficient,insufficient,critical',
            'resources' => 'sometimes|array',
            'resources.*.type' => 'required_with:resources|in:clothes,food,medical_supplies,water,others',
            'resources.*.status' => 'required_with:resources|string',
        ]);

        DB::transaction(function () use ($safePoint, $data) {
            $safePointUpdate = [];

            if (array_key_exists('name', $data)) {
                $safePointUpdate['name'] = $data['name'];
            }

            if (array_key_exists('type', $data)) {
                $safePointUpdate['type'] = $data['type'];
            }

            if (array_key_exists('capacity', $data)) {
                $safePointUpdate['capacity'] = $data['capacity'];
            }

            if (data_get($data, 'location.lat') !== null) {
                $safePointUpdate['latitude'] = data_get($data, 'location.lat');
            }

            if (data_get($data, 'location.lng') !== null) {
                $safePointUpdate['longitude'] = data_get($data, 'location.lng');
            }

            if (array_key_exists('resourceStatus', $data)) {
                $safePointUpdate['status'] = $data['resourceStatus'];
            }

            if (!empty($safePointUpdate)) {
                $safePoint->update($safePointUpdate);
            }

            if (!empty($data['resources'])) {
                foreach ($data['resources'] as $resource) {
                    $safePoint->resources()->updateOrCreate(
                        [
                            'type' => $resource['type'],
                        ],
                        [
                            'description' => $resource['status'],
                        ]
                    );
                }
            }
        });

        return response()->json([
            'message' => 'Safe point updated successfully',
            'data' => $safePoint->load('resources'),
        ]);
    }

    public function deleteResource($id)
    {
        $data = Resource::find($id);
        $data->delete();

        return response()->json(['message' => 'SafePoint request deleted']);
    }

    public function destroy($id)
    {
        $data = SafePoint::find($id);
        $data->delete();

        return response()->json(['message' => 'SafePoint request deleted']);
    }
}
