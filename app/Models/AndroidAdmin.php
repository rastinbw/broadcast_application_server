<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AndroidAdmin extends Model
{
    protected $fillable = [
        'user_id',
        'token'
    ];

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
