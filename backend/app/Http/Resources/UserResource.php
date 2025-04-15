<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\DocType;

class UserResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'data';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $employee = $this->employee; // langsung ambil relasi
        $employmentStatus = $employee ? $employee->employment_status : null;
        $doctypes = DocType::select('id', 'type_name')->where('status', $employmentStatus)
        ->orderBy('id')->get();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'employee' => $employee ? [
                'id' => $employee->id,
                'nip' => $employee->nip,
                'full_name' => $employee->full_name,
                'date_of_birth' => $employee->date_of_birth,
                'gender' => $employee->gender,
                'phone_number' => $employee->phone_number,
                'email' => $employee->email,
                'job_title' => $employee->job_title,
                'work_unit_id' => $employee->id_work_unit,
                'employment_status' => $employee->employment_status,
                'tmt_pangkat' => $employee->tmt_pangkat,
                'tmt_jabatan' => $employee->tmt_jabatan,
            ] : null,
            'doc_types' => $employee ? $doctypes : null
        ];
    }
}
