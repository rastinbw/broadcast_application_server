<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Post;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Includes\Constant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WebserviceController extends Controller
{
    function get_student_workbook($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($student) {
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $student->workbook()->get()->toJson());
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function authorize_student($user_id, Request $req){
        //Validation
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        if ($student) {
            $data = ['first_name' => $student->first_name, 'last_name' => $student->last_name];
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($data)->toJson());
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_NATIONAL_CODE);

    }

    function confirm_student($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        if ($student){
            $student->phone_number = $req->input('phone_number');

            $password = str_random(8);
            //TODO send password via sms panel to student phone number
            $student->password = Hash::make($password);
            $student->token = bin2hex(random_bytes(16));
            $student->save();

            return sprintf('{"result_code": %u }', Constant::$SUCCESS);
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_NATIONAL_CODE);
    }

    function login_student($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        if ($student) {
            //Student Exists
            if (Hash::check($req->input('password'), $student->password))
                return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $student->token);
            else
                return sprintf('{"result_code": %u}', Constant::$INVALID_PASSWORD);
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_NATIONAL_CODE);
    }

    function get_last_notifications($user_id, Request $req){
        //Token authentication
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();
        if(!$student)
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);

        $notifications = Notification::where([
            ['user_id', '=', $user_id],
//            ['created_at' , '>' , $req->input('date')]
        ])->get();

        return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $notifications->toJson());
    }

    function get_posts($user_id, $type, $chunk_count, $page_count)
    {
        $table = "posts";
        if ($type == Constant::$TYPE_MEDIA)
            $table = "media";

        try{
            $items = DB::table($table)->where([
                ['user_id', '=', $user_id],
            ])->get();
            $last_items = (collect($items)->sortByDesc('id')->chunk($chunk_count))[$page_count];

            //putting the selected chunk in a new array to make it start from index zero
            $temp = [];
            $i = 0;
            foreach ($last_items as $item){
                $temp[$i] = $item;
                $i++;
            }
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($temp)->toJson());
        }catch(\Exception $e){
            return sprintf('{"result_code": %u}', Constant::$NO_MORE_POSTS);
        }
    }



}
