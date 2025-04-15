<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocType;

class DocTypeController extends Controller
{
    public function index($employment_status)
    {
        return response()->json([
            'data' => DocType::where('status', $employment_status)
                                ->orderBy('id')->get()
        ]);
    }
}
