<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'email',
        'title',
        'message',
        'user_id',
        'ustudent_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function student()
    {
        return $this->belongsTo('App\Models\Ustudent');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
