<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Post;
use App\Models\Student;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Includes\Constant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Psy\Util\Str;

class WebserviceController extends Controller
{
    function save_ticket($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($student) {
            $data = [
                'email' => $req->input('email'),
                'title' => $req->input('title'),
                'message' => $req->input('message'),
                'user_id' => $user_id,
                'student_id' => $student->id,
            ];
            $ticket = new Ticket($data);
            $ticket->save();

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_student_info($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($student) {
            $data = [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'phone_number' => $student->phone_number,
                'national_code' => $student->national_code,
            ];
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($data)->toJson());
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_student_workbook($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($student) {
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $student->workbooks()->get()->toJson());
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

        if ($student) {
            $student->phone_number = $req->input('phone_number');
            $student->password = strtolower(str_random(6));
            $student->token = bin2hex(random_bytes(16));
            $student->save();

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_NATIONAL_CODE);
    }

    function send_password($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['national_code', '=', $req->input('national_code')],
        ])->first();

        //sending password via sms
        $password = $student->password;

        $url = 'https://api.kavenegar.com/v1/' .
            env('SMS_API_KEY') .
            '/sms/send.json?receptor=' .
            $student->phone_number .
            '&sender=' .
            env('SENDER_NUMBER') .
            '&message=' .
            $password;

        $student->password = Hash::make($password);
        $student->save();

        return Redirect::away($url);
    }


    function change_password($user_id, Request $req){
        $student = Student::where([
            ['user_id', '=', $user_id],
            ['token', '=', $req->input('token')],
        ])->first();

        if ($student) {
            //Student Exists
            if (Hash::check($req->input('password'), $student->password)) {
                $student->password = Hash::make($req->input('new_password'));
                $student->save();
                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }
            else
                return sprintf('{"result_code": %u}', Constant::$INVALID_PASSWORD);
        }else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
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


    function get_last_notifications($user_id, $date){
        $arr = explode('-', $date);
        $new_date = new Carbon($arr[0].'-'.$arr[1].'-'.$arr[2].' '.$arr[3].':'.$arr[4].':'.$arr[5]);

        $notifications = Notification::where([
            ['user_id', '=', $user_id],
            ['created_at' , '>' , $new_date]
        ])->get();

        return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($notifications)->toJson());
    }


    function get_posts($user_id, $type, $chunk_count, $page_count, $search_phrase)
    {
        $table = "posts";
        if ($type == Constant::$TYPE_MEDIA)
            $table = "media";

        $query = [['user_id', '=', $user_id]];
        if ($search_phrase != 'null')
            array_push($query, ['title', 'LIKE', '%'.$search_phrase.'%']);

        try{
            $items = DB::table($table)->where($query)->get();
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


    public function show_post($id)
    {
        $table = "posts";
        $query = [['id', '=', $id]];

        $item = DB::table($table)->where($query)->get()->first();
        if ($item) {
            return view('post_detail', ['post'=>$item]);
        }else
            abort(404);
    }



}
