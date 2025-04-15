<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/employee-to-user', function() {
    // ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
    $employees = Employee::doesntHave('user')->get();
        foreach ($employees as $key => $employee) {

            User::updateOrCreate(
                ['email' =>  $employee->email],
                [
                    'name' => $employee->full_name,
                    'password' => Hash::make($employee->nip),
                    'id_employee' => $employee->id
                ]
            );
        }

        return 'success';
});