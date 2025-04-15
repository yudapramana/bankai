<?php

namespace App\Http\Controllers;

use App\Models\WorkUnit;
use Illuminate\Http\Request;

class WorkUnitController extends Controller
{
    /**
     * Display a listing of the work unit.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->search_query;
        $workUnits = WorkUnit::query()
                ->when(request('search_query'), function ($q) use ($searchQuery) {
                    return $q->where('name', 'like', "%{$searchQuery}%");
                })
                ->latest()
                ->paginate(10);

        return response()->json($workUnits);
    }

    /**
     * Store a newly created work unit in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_name' => 'required|unique:work_units',
            'unit_code' => 'required|unique:work_units|max:14',
        ]);

        return WorkUnit::create([
            'unit_name' => $data['unit_name'],
            'unit_code' => $data['unit_code']
        ]);
    }

    /**
     * Display the specified work unit.
     */
    public function show(string $id)
    {
        return WorkUnit::find($id);
    }

    /**
     * Update the specified work unit in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'unit_name' => 'required|unique:work_units',
            'unit_code' => 'required|unique:work_units|max:14',
        ]);

        return WorkUnit::update([
            'unit_name' => $data['unit_name'],
            'unit_code' => $data['unit_code']
        ]);
    }

    /**
     * Remove the specified work unit from storage.
     */
    public function destroy(string $id)
    {
        $wu = WorkUnit::find($id);
        $wu->delete();
        return response()->noContent();
    }
}
