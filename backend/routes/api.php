<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WorkUnitController;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocTypeController;
use App\Http\Controllers\Api\EmpDocumentController;


Route::get('/emp-documents/by-employee/{id}', [EmpDocumentController::class, 'getByEmployee']);

Route::get('/doc-types/{employment_status}', [DocTypeController::class, 'index']);
Route::post('/emp-documents', [EmpDocumentController::class, 'store']);

Route::get('/user', function (Request $request) {
    return new UserResource($request->user());
})->middleware('auth:sanctum');

Route::get('/test-user', function(Request $request) {
    $user =  User::find(1);
    $user->load('employee');

    $employee = Employee::all();
    return [
        'user' => $user,
        'employee' => $employee
    ];
});

Route::patch('/profile', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email'],
        'avatar' => [
            // ['required'],
            'nullable',
            'image',
            'mimes:jpg,png',
        ],
    ]);

    $path = null;
    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
    }

    $request->user()->update([...$request->only('name', 'email'), 'avatar' => $path]);

    return $request->user()->refresh();
})->middleware(['auth:sanctum']);


Route::get('/nip-to-bod/{nip}', function ($nip) {
    $dob = substr($nip, 0, 4) . '-' . substr($nip, 4, 2) . '-' . substr($nip, 6, 2) ;
    return $dob;
});

Route::get('/nip-to-gender/{nip}', function ($nip) {
    $gender = (substr($nip, 14, 1) == 1) ? 'M' : 'F';
    return $gender;
});

Route::get('/employee-by-nip/{nip}', function ($nip) {
    $employee = \App\Models\Employee::where('nip', $nip)->first();
    $employee->load('work_unit');
    return $employee;
});

Route::get('/all-employees', function () {
    $employees = \App\Models\Employee::all();
    return $employees;
});

Route::resource('work_unit',WorkUnitController::class);
Route::resource('employee',EmployeeController::class);