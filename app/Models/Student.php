<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Student extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'students';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'national_code',
        'first_name',
        'last_name',
        'parent_code',
        'user_id',
        'is_registered'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function create(array $attributes = [])
    {

        $model = static::query()->create($attributes);

        $code = Student::generate_parent_code(100000, 999999);
        if ($code){
            $model->parent_code = $code;
            $model->save();
        }else{
            // assign 8 digit parent code if couldn't succeed in 10000 try
            $code = Student::generate_parent_code(10000000, 99999999);
            if ($code){
                $model->parent_code = $code;
                $model->save();
            }else{
                $model->parent_code = 'NA';
                $model->save();
            }
        }

        return $model;
    }

    public static function generate_parent_code($bottom, $top){
        $limitation = 10000;
        for ($i = 0; $i <= $limitation; $i++){
            //creating code
            $code = mt_rand($bottom, $top);

            //check if code not exists
            if (Student::where('parent_code', '=', $code)->exists())
                continue;

            return $code;
        }

        return null;
    }

    public function getRegistered(){
        $ustudent = Ustudent::where([
            ['user_id', '=', $this->user_id],
            ['national_code', '=', $this->national_code],
        ])->first();

        if($ustudent) {
            if ($this->is_registered == false){
                $this->is_registered = true;
                $this->save();
            }
            return "<label style='color:green'>ثبت شده</label>";
        }
        else {
            if ($this->is_registered == true){
                $this->is_registered = false;
                $this->save();
            }
            return "<label style='color:red'>ثبت نشده</label>";
        }

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

    public function workbooks()
    {
        return $this->hasMany('App\Models\Workbook');
    }

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course');
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
