<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employee.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->search_query;
        $employees = Employee::query()
                ->when(request('search_query'), function ($q) use ($searchQuery) {
                    return $q->where('full_name', 'like', "%{$searchQuery}%");
                })
                ->latest()
                ->paginate(10);

        return response()->json($employees);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nip' => 'required|string|size:18|unique:employees,nip',
            'full_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'phone_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:employees,email|max:100',
            'job_title' => 'required|string|max:255',
            'id_work_unit' => 'required|integer|exists:work_units,id',
            'employment_status' => 'required|in:PNS,PPPK',
            'tmt_pangkat' => 'nullable|date',
            'tmt_jabatan' => 'nullable|date'
        ]);
        
        $employee = Employee::create($data);
        return response()->json($employee, 201);
    }

    /**
     * Display the specified employee.
     */
    public function show(string $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return response()->json($employee, 200);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        
        $data = $request->validate([
            'nip' => 'sometimes|required|string|size:18|unique:employees,nip,' . $id,
            'full_name' => 'sometimes|required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'phone_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:employees,email,' . $id . '|max:100',
            'job_title' => 'sometimes|required|string|max:255',
            'id_work_unit' => 'sometimes|required|integer|exists:work_units,id',
            'employment_status' => 'sometimes|required|in:PNS,PPPK',
            'tmt_pangkat' => 'nullable|date',
            'tmt_jabatan' => 'nullable|date',
            'document' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);
        
        if ($request->hasFile('document')) {
            if ($employee->document) {
                Storage::delete($employee->document);
            }
            $data['document'] = $request->file('document')->store('documents');
        }
        
        $employee->update($data);
        return response()->json($employee, 200);
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        
        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }
}
