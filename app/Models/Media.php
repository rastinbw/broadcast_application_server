<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'media';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['title', 'media', 'description'];
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

    public function setMediaAttribute($value)
    {
        $attribute_name = "media";

        //         if (!request()->hasFile($attribute_name) && !request()->file($attribute_name)->isValid()) {
//   throw new \Exception('NOT WORK'); }

        $disk = "public";
        $destination_path = "media";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            Storage::disk('public')->delete($obj->media);
        });
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
