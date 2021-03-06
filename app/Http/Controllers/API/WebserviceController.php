<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Absent;
use App\Models\AndroidAdmin;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentPasswordReset;
use App\Models\Ticket;
use App\Models\Ustudent;
use App\Models\UstudentMessage;
use App\User;
use Illuminate\Http\Request;
use App\Includes\Constant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;


class WebserviceController extends Controller
{
    function check_version($user_id, $version){
        $user = User::find($user_id);

        if ($user){
            $should_update = version_compare($user->last_version, $version);
            if ($should_update){
                return sprintf('{"result_code": %u, "data": {"name": "%s", "version": "%s", "must_update": "%s"}}',
                    Constant::$SHOULD_UPDATE,
                    $user->apk_name,
                    $user->last_version,
                    $user->must_update
                );
            }

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        }
    }


    function get_messages($user_id, Request $req){
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')]
        ])->first();


        if ($ustudent){
            $messages = [];

            foreach (Message::where('user_id', '=', $user_id)->get() as $message){
                if($message->group_id != null){
                    if($message->group_id != $ustudent->group_id)
                        continue;
                }

                if($message->field_id != null){
                    if($message->field_id != $ustudent->field_id)
                        continue;
                }

                if($message->gender != null){
                    if($message->gender != $ustudent->gender)
                        continue;
                }

                if($message->plan_id != null){
                    if(!$ustudent->plans->contains($message->plan_id))
                        continue;
                }

                array_push($messages, $message);
            }

            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($messages)->toJson());


        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);

    }

    function get_staff_updated($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();

        if ($user) {
            $staffs = $user->staffs()->get();
            $list = array();
            foreach ($staffs as $staff) {
                array_push($list, ['id' => $staff->id, 'updated_at' => $staff['updated_at']->toDateTimeString()]);
            }
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, json_encode($list));
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function get_staff($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();

        if ($user) {
            $list = json_decode($req->input('required_id_list'));

            if (sizeof($list) != 0) {
                sort($list);

                $staffs = [];
                foreach ($list as $id) {
                    $item = $user->staffs()->where(
                        [
                            ['id', '=', $id],
                        ]
                    )->get();

                    if (isset($item[0]))
                        array_push($staffs, $item[0]);
                }

                $staffs = json_encode($staffs);
            } else
                $staffs = $user->staffs()->get()->toJson();


            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $staffs);
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function get_slider_updated($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();

        if ($user) {
            $slider = $user->slider()->first();
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, json_encode($slider['updated_at']->toDateTimeString()));
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function get_slider($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();

        if ($user) {
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $user->slider()->get()->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function get_about($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();

        if ($user) {
            return view('about')->with('about', $user->about()->first());
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function get_user_group_list($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();

        if ($user) {
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $user->groups()->get()->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function get_user_field_list($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();

        if ($user) {
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $user->fields()->get()->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function get_user_plan_list($user_id, Request $req)
    {
        $user = User::where([
            ['id', '=', $user_id],
        ])->first();


        if ($user) {
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $user->plans()->get()->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$USER_NOT_EXIST);
    }

    function save_ticket($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            $data = [
                'email' => $req->input('email'),
                'title' => $req->input('title'),
                'message' => $req->input('message'),
                'ustudent_id' => $ustudent->id,
                'user_id' => $user_id,
            ];
            $ticket = new Ticket($data);
            $ticket->save();

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }


    function get_posts($user_id, $type, $chunk_count, $page_count, $search_phrase, $group_id, $field_id)
    {
        $query = [['user_id', '=', $user_id]];
        $table = "posts";

        if ($type == Constant::$TYPE_MEDIA)
            $table = "media";
        elseif ($type == Constant::$TYPE_PROGRAM) {
            $table = "programs";
            if ($group_id != 'null')
                array_push($query, ['group_id', '=', $group_id]);

            if ($field_id != 'null')
                array_push($query, ['field_id', '=', $field_id]);
        }

        if ($search_phrase != 'null')
            array_push($query, ['title', 'LIKE', '%' . $search_phrase . '%']);

        try {
            $items = DB::table($table)->where($query)->get();
            $last_items = (collect($items)->sortByDesc('id')->chunk($chunk_count))[$page_count];

            //putting the selected chunk in a new array to make it start from index zero
            $temp = [];
            $i = 0;
            foreach ($last_items as $item) {
                $temp[$i] = $item;
                $i++;
            }
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($temp)->toJson());
        } catch (\Exception $e) {
            return sprintf('{"result_code": %u}', Constant::$NO_MORE_POSTS);
        }
    }

    //****************************************REGISTRATION PART*********************************************************
    function register_ustudent($user_id, Request $req)
    {
        //check user student count limitation
        $user = User::find($user_id);
        $count = Ustudent::where([
            ['user_id', '=', $user_id],
        ])->count();

        if ($count >= $user->student_count_limit)
            return sprintf('{"result_code": %u}', Constant::$SERVER_ISSUE);

        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        $new_ustudent = true;
        if ($ustudent) {
            if ($ustudent->verified == 1)
                return sprintf('{"result_code": %u}', Constant::$REPETITIVE_NATIONAL_CODE);
            else if ($ustudent->verified == 0) {
                // resetting phone number
                $new_ustudent = false;
                $ustudent->phone_number = null;
                $ustudent->save();
            }
        }

        // checking phone number
        if (Ustudent::where([
            ['user_id', '=', $user_id],
            ['phone_number', '=', $req->input('phone_number')],
        ])->first()) {
            return sprintf('{"result_code": %u}', Constant::$REPETITIVE_PHONE_NUMBER);
        }

        if ($new_ustudent) {
            // creating new student if user never exist (verified or not)
            $ustudent = new Ustudent();
        }
        $ustudent->user_id = $user_id;
        $ustudent->first_name = $req->input('first_name');
        $ustudent->last_name = $req->input('last_name');
        $ustudent->national_code = $req->input('national_code');
        $ustudent->phone_number = $req->input('phone_number');
        $ustudent->group_id = $req->input('group_id');
        $ustudent->field_id = $req->input('field_id');
        $ustudent->gender = $req->input('gender');
        $ustudent->password = Hash::make($req->input('password'));
        $ustudent->verified = 0;
        $ustudent->save();

        if ($ustudent) {
            $ustudent->plans()->attach(Plan::find(json_decode($req->input('plan_ids'))));
            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        }
    }


    function send_verification_code($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        if ($ustudent && $ustudent->verified == 0) {
            $code = mt_rand(1000, 9999);

            $url = 'https://api.kavenegar.com/v1/' .
                env('SMS_API_KEY') .
                '/verify/lookup.json?receptor=' .
                $ustudent->phone_number .
                '&template=' .
                env('TEMPLATE_VERIFY') .
                '&token=' .
                $code;

            $ustudent->verification_code = $code;
            $ustudent->save();

            return Redirect::away($url);
        }
    }


    function confirm_ustudent($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        if ($ustudent && $ustudent->verified == 0 && $ustudent->verification_code) {
            if ($ustudent->verification_code == $req->input('verification_code')) {
                $ustudent->token = bin2hex(random_bytes(16));
                $ustudent->verified = 1;
                $ustudent->save();

                $plan_ids = [];
                foreach ($ustudent->plans()->get() as $plan){
                    array_push($plan_ids, $plan->id);
                }

                return sprintf('{"result_code": %u, "data": {"token": "%s", "group_id": %g, "field_id": %g, "gender": %g, "first_name": "%s", "last_name": "%s", "plan_ids": %s}}',
                    Constant::$SUCCESS,
                    $ustudent->token,
                    $ustudent->group_id,
                    $ustudent->field_id,
                    $ustudent->gender,
                    $ustudent->first_name,
                    $ustudent->last_name,
                    json_encode($plan_ids)
                );
            } else
                return sprintf('{"result_code": %u}', Constant::$INVALID_VERIFICATION_CODE);
        }
    }


    function login_ustudent($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        if ($ustudent) {
            //Student Exists
            if ($ustudent->verified == 0)
                return sprintf('{"result_code": %u}', Constant::$INVALID_NATIONAL_CODE);

            if (Hash::check($req->input('password'), $ustudent->password)) {
                $ustudent->token = bin2hex(random_bytes(16));
                $ustudent->save();

                $plan_ids = [];
                foreach ($ustudent->plans()->get() as $plan){
                    array_push($plan_ids, $plan->id);
                }

                return sprintf('{"result_code": %u, "data": {"token": "%s", "group_id": %g, "field_id": %g, "gender": %g, "first_name": "%s", "last_name": "%s", "plan_ids": %s}}',
                    Constant::$SUCCESS,
                    $ustudent->token,
                    $ustudent->group_id,
                    $ustudent->field_id,
                    $ustudent->gender,
                    $ustudent->first_name,
                    $ustudent->last_name,
                    json_encode($plan_ids)
                );
            } else
                return sprintf('{"result_code": %u}', Constant::$INVALID_PASSWORD);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_NATIONAL_CODE);
    }


    function login_as_parent($user_id, Request $req)
    {
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['parent_code', '=', $req->input('parent_code')],
        ])->first();

        if ($student) {
            // Check for Ustudent
            $ustudent = Ustudent::where([
                ['user_id', '=', $user_id],
                ['national_code', '=', $student->national_code],
            ])->first();
            if ($ustudent) {
                $plan_ids = [];
                foreach ($ustudent->plans()->get() as $plan) {
                    array_push($plan_ids, $plan->id);
                }

                return sprintf('{"result_code": %u, "data": {"token": "%s", "group_id": %g, "field_id": %g, "gender": %g, "first_name": "%s", "last_name": "%s",  "plan_ids": %s}}',
                    Constant::$SUCCESS,
                    $ustudent->token,
                    $ustudent->group_id,
                    $ustudent->field_id,
                    $ustudent->gender,
                    $ustudent->first_name,
                    $ustudent->last_name,
                    json_encode($plan_ids)
                );
            }
            else
                return sprintf('{"result_code": %u}', Constant::$USER_NOT_REGISTERED);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_PARENT_CODE);
    }


    function send_change_password_link($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        if ($ustudent) {
            $reset = StudentPasswordReset::where([
                ['user_id', '=', $user_id],
                ['national_code', '=', $req->input('national_code')],
            ])->orderBy('created_at', 'desc')->first();

            if ($reset) {
                $created_at = $reset->created_at;
                $now = Carbon::now();
                if ($now->diffInMinutes($created_at) < 5)
                    return sprintf('{"result_code": %u}', Constant::$INVALID_REQUEST);
            }

            $reset = new StudentPasswordReset();
            $reset->user_id = $user_id;
            $reset->national_code = $req->input('national_code');
            $reset->save();

            //for test: .'/broadcast_app_server/public'
            $link = env('APP_URL')
                . '/change_password/'
                . $ustudent->user_id
                . '/'
                . $ustudent->token;

            $url = 'https://api.kavenegar.com/v1/' .
                env('SMS_API_KEY') .
                '/verify/lookup.json?receptor=' .
                $ustudent->phone_number .
                '&template=' .
                env('TEMPLATE_FORGET_PASSWORD') .
                '&token=' .
                $link;

            return Redirect::away($url);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_NATIONAL_CODE);
    }

    //****************************************REGISTRATION PART*********************************************************


    //*******************************************SECURE PART************************************************************
    function check_token($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function save_fire_base_token($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            $ustudent->fire_base_token = $req->input('fire_base_token');
            $ustudent->save();
            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_ustudent_info($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        // check weather student is defined by school
        // (to prevent student from changing his/her national code)
        $is_student = false;
        if (Student::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $ustudent->national_code],
        ])->first()) {
            $is_student = true;
        }


        if ($ustudent) {
            $plan_ids = [];
            foreach ($ustudent->plans()->get() as $plan){
                array_push($plan_ids, $plan->id);
            }

            $data = [
                'first_name' => $ustudent->first_name,
                'last_name' => $ustudent->last_name,
                'national_code' => $ustudent->national_code,
                'group_id' => $ustudent->group_id,
                'field_id' => $ustudent->field_id,
                'plan_ids' => $plan_ids,
                'gender' => $ustudent->gender,
                'is_student' => $is_student,
            ];
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($data)->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function update_ustudent_info($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            if (Ustudent::where([
                ['user_id', '=', $user_id],
                ['national_code', '=', $req->input('national_code')],
                ['token', '<>', $req->input('token')]
            ])->first()) {
                return sprintf('{"result_code": %u}', Constant::$REPETITIVE_NATIONAL_CODE);
            }

            $ustudent->first_name = $req->input('first_name');
            $ustudent->last_name = $req->input('last_name');
            $ustudent->group_id = $req->input('group_id');
            $ustudent->field_id = $req->input('field_id');
            $ustudent->national_code = $req->input('national_code');

            $ustudent->plans()->sync([]);
            $ustudent->plans()->attach(Plan::find(json_decode($req->input('plan_ids'))));

            $ustudent->save();

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_student_workbook($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            $student = Student::where([
                ['user_id', '=', $user_id],
                ['national_code', '=', $ustudent->national_code],
            ])->first();
            if ($student)
                return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $student->workbooks()->get()->toJson());
            else
                return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, json_encode([]));
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_student_courses($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $ustudent->courses()->get()->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_student_course_absents($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            $course = Course::find($req->input('course_id'));
            $ctrs = $course->ctrs()->get();

            $student_absents = [];

            foreach ($ctrs as $ctr){
                $abs = Absent::where([
                    ['user_id', '=', $user_id],
                    ['ctr_id', '=', $ctr->id],
                    ['national_code', '=', $ustudent->national_code]
                ])->first();

                if ($abs)
                    array_push($student_absents, $ctr->date);
            }

            return sprintf('{"result_code": %u, "data": %s}',
                Constant::$SUCCESS,  json_encode($student_absents));
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_student_course_grades($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            $course = Course::find($req->input('course_id'));
            $tests = $course->tests()->get();

            $student_grades = [];

            foreach ($tests as $test){
                $grade = Grade::where([
                    ['user_id', '=', $user_id],
                    ['test_id', '=', $test->id],
                    ['national_code', '=', $ustudent->national_code]
                ])->first();

                if ($grade)
                    array_push($student_grades,
                        ['title' => $test->title, 'date' => $test->date , 'grade' => $grade->grade]);
            }

            return sprintf('{"result_code": %u, "data": %s}',
                Constant::$SUCCESS,  json_encode($student_grades));     
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }


    function change_password($user_id, Request $req)
    {
        $ustudent = Ustudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($ustudent) {
            if (Hash::check($req->input('password'), $ustudent->password)) {
                $ustudent->password = Hash::make($req->input('new_password'));
                $ustudent->save();
                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            } else
                return sprintf('{"result_code": %u}', Constant::$INVALID_PASSWORD);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);

    }

    //*******************************************END SECURE PART************************************************************


}
