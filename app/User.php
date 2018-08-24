<?php

namespace App;

use App\Models\AndroidAdmin;
use App\Models\Slider;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','slider_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function create(array $attributes = [])
    {

        $model = static::query()->create($attributes);

        // create slider
        $slider = new Slider();
        $slider->user_id = $model->id;
        $slider->save();

        // create android admin
        $android = new AndroidAdmin();
        $android->user_id = $model->id;
        $android->save();

        // connecting android admin and slider to user
        $model->slider_id = $slider->id;
        $model->android_admin_id = $android->id;
        $model->save();


        return $model;
    }

    public function students()
    {
        return $this->hasMany('App\Models\Student');
    }

    public function ustudents()
    {
        return $this->hasMany('App\Models\Ustudent');
    }

    public function workbooks()
    {
        return $this->hasMany('App\Models\Workbook');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }

    public function medias()
    {
        return $this->hasMany('App\Models\Media');
    }

    public function staffs()
    {
        return $this->hasMany('App\Models\Staff');
    }

    public function programs()
    {
        return $this->hasMany('App\Models\Program');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Group');
    }

    public function studentPasswordResets()
    {
        return $this->hasMany('App\Models\StudentPasswordReset');
    }


    public function slider()
    {
        return $this->hasOne('App\Models\Slider');
    }

    public function android_admin()
    {
        return $this->hasOne('App\Models\AndroidAdmin');
    }
}
