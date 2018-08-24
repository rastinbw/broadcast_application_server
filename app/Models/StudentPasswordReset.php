<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPasswordReset extends Model
{
    protected $table = 'student_password_reset';

    protected $fillable = [
        'user_id',
        'national_code',
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
