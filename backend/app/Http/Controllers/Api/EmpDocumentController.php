<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\EmpDocument;
use App\Models\Employee;
use App\Models\DocType;

class EmpDocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_doc_type' => 'required|exists:doc_types,id',
            'doc_number' => 'required|string|max:50',
            'doc_date' => 'required|date',
            'file' => 'required|mimes:pdf|max:2048'
        ]);

        $employee = Auth::user()->employee;
        $employeeId = $employee->id;

        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $docType = DocType::find($request->id_doc_type);
        $fileName = $docType->label . $request->parameter . '_' .$employee->nip . '.'. $extension;
        $filePath = $file->storeAs(
            'documents/'.$employee->nip,
            $fileName,
            'public'
        );


        $document = EmpDocument::create([
            'id_doc_type' => $request->id_doc_type,
            'doc_number' => $request->doc_number,
            'doc_date' => $request->doc_date,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'id_employee' => $request->id_employee,
            'parameter' => $request->parameter
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil disimpan',
            'data' => $document
        ]);
    }

    public function getByEmployee($id)
    {
        try {
            $documents = \App\Models\EmpDocument::where('id_employee', $id)->get();

            return response()->json([
                'success' => true,
                'data' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data dokumen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
