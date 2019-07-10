<?php

namespace App;

use App\Models\About;
use App\Models\AndroidAdmin;
use App\Models\Slider;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slider_id',
        'about_id',
        'post_time_limit',
        'message_log_time_limit',
        'student_count_limit',
        'media_count_limit',
        'program_count_limit',
        'activation_date',
        'fire_base_api_key',
        'must_update',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

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

        //create about
        $about = new About();
        $about->user_id = $model->id;
        $about->save();

        // connecting android admin and slider to user
        $model->slider_id = $slider->id;
        $model->about_id = $about->id;
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

    public function fields()
    {
        return $this->hasMany('App\Models\Field');
    }

    public function plans()
    {
        return $this->hasMany('App\Models\Plan');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message');
    }

    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }

    public function ctrs()
    {
        return $this->hasMany('App\Models\Ctr');
    }

    public function absents()
    {
        return $this->hasMany('App\Models\Absent');
    }

    public function studentPasswordResets()
    {
        return $this->hasMany('App\Models\StudentPasswordReset');
    }

    public function slider()
    {
        return $this->hasOne('App\Models\Slider');
    }

    public function about()
    {
        return $this->hasOne('App\Models\About');
    }

    public function android_admin()
    {
        return $this->hasOne('App\Models\AndroidAdmin');
    }
}
