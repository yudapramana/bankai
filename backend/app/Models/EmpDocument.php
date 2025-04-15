<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpDocument extends Model 
{

    protected $table = 'emp_documents';
    public $timestamps = true;
    

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'id_employee', 'id');
    }

    public function doc_type()
    {
        return $this->belongsTo('App\Models\DocType', 'id_doc_type', 'id');
    }

    public function verval_log()
    {
        return $this->hasMany('App\Models\VervalLog');
    }

}