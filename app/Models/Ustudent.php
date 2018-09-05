<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Ustudent extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'ustudents';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'group_id',
        'verification_code',
        'verified',
        'national_code',
        'password',
        'first_name',
        'last_name',
        'phone_number',
        'token',
        'fire_base_token'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getDate(){
        $v = new Verta($this->created_at);
        $year = $v->year;
        $month = ($v->month < 10) ? '0' . $v->month : $v->month;
        $day = ($v->day < 10) ? '0' . $v->day : $v->day;
        return $year . '-' . $month . '-' . $day;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
