<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Includes\Constant;
use App\Models\Absent;
use App\Models\Course;
use App\Models\Ctr;
use App\Models\Field;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Test;
use App\Models\Student;
use App\Models\Ustudent;
use App\User;
use function Composer\Autoload\includeFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use App\Models\Workbook;
use Session;
use Excel;

class AdminController extends Controller
{
    public function get_ctr_list($course_id, $ctr_id, $user_id)
    {
        $course = Course::where([
            ['user_id', '=', $user_id],
            ['id', '=', $course_id],
        ])->first();

        $ctr = Ctr::where([
            ['user_id', '=', $user_id],
            ['id', '=', $ctr_id],
        ])->first();

        $ustudents = $course->ustudents()->get();

        $absents = Absent::where([
            ['user_id', '=', $user_id],
            ['ctr_id', '=', $ctr_id],
        ])->get(['national_code']);

        $absents_national_code_list = [];
        foreach ($absents as $absent){
            array_push($absents_national_code_list, trim($absent['national_code']));
        }

        return view('ctr_list',
            [
                'absents' => $absents_national_code_list,
                'course' => $course,
                'ctr' => $ctr,
                'students' => $ustudents,
                'user_id' => $user_id
            ]
        );
    }

    public function get_grades_list($course_id, $test_id, $user_id)
    {
        $course = Course::where([
            ['user_id', '=', $user_id],
            ['id', '=', $course_id],
        ])->first();

        $test = Test::where([
            ['user_id', '=', $user_id],
            ['id', '=', $test_id],
        ])->first();

        $ustudents = $course->ustudents()->get();

        $grades = Grade::where([
            ['user_id', '=', $user_id],
            ['test_id', '=', $test_id],
        ])->get();

        $grades_assoc = [];
        foreach ($grades as $grade){
           $grades_assoc[$grade['national_code']] = $grade->grade;
        }

        return view('grades_list',
            [
                'grades' => $grades_assoc,
                'course' => $course,
                'test' => $test,
                'students' => $ustudents,
                'user_id' => $user_id
            ]
        );
    }

    public function get_ustudents_list($course_id, $user_id)
    {
        $course = Course::where([
            ['user_id', '=', $user_id],
            ['id', '=', $course_id],
        ])->first();

        $ustudent_list = Ustudent::where([
            ['user_id', '=', $user_id],
        ])->get();

        $group_list = Group::where([
            ['user_id', '=', $user_id]
        ])->get();

        $field_list = Field::where([
            ['user_id', '=', $user_id]
        ])->get();

        $gender_list = [
            ['id' => Constant::$GENDER_MALE, 'title' => "پسر"],
            ['id' => Constant::$GENDER_FEMALE, 'title' => "دختر"]
        ];

        return view('course_ustudents',
            [
                'course' => $course,
                'student_list' => $ustudent_list,
                'group_list' => $group_list,
                'field_list' => $field_list,
                'gender_list' => $gender_list,
                'user_id' => $user_id
            ]
        );
    }

    public function update_ctr_list(Request $request, $course_id, $ctr_id)
    {
        $user_id = $request->input('user_id');
        $ustudents_fire_base_token_list = [];

        $course = Course::where([
            ['user_id', '=', $user_id],
            ['id', '=', $course_id],
        ])->first();

        $ustudents = $course->ustudents()->get();

        foreach ($ustudents as $ustudent) {
            $is_present_checked = $request->input($ustudent->national_code);

            $absent = Absent::where([
                ['user_id', '=', $user_id],
                ['ctr_id', '=', $ctr_id],
                ['national_code', '=', $ustudent->national_code],
            ])->first();

            if ($is_present_checked){
                if ($absent)
                    Absent::find($absent->id)->delete();
            }else{
               if (!$absent){
                   $ustudent = Ustudent::where([
                       ['user_id', '=', $user_id],
                       ['national_code', '=', $ustudent->national_code]
                   ])->first();
                   if ($ustudent) {
                       array_push(
                           $ustudents_fire_base_token_list,
                           $ustudent->fire_base_token
                       );
                   }

                   $new_absent = new Absent();
                   $new_absent->national_code = $ustudent->national_code;
                   $new_absent->ctr_id = $ctr_id;
                   $new_absent->user_id = $user_id;
                   $new_absent->save();

               }
            }
        }

        if (sizeof($ustudents_fire_base_token_list) > 0) {
            AdminController::notify(
                "غیبت",
                " غیبت در کلاس " . $course->title,
                User::find($user_id)->fire_base_server_key,
                $ustudents_fire_base_token_list
            );
        }

        return back();

    }

    public function update_grades_list(Request $request, $course_id, $test_id)
    {
        $user_id = $request->input('user_id');
        $ustudents_fire_base_token_list = [];

        $course = Course::where([
            ['user_id', '=', $user_id],
            ['id', '=', $course_id],
        ])->first();

        $ustudents = $course->ustudents()->get();

        foreach ($ustudents as $ustudent) {
            $grade = Grade::where([
                ['user_id', '=', $user_id],
                ['test_id', '=', $test_id],
                ['national_code', '=', $ustudent->national_code],
            ])->first();

            $value = $request->input($ustudent->national_code);

            if ($grade != null){
                if(is_numeric($value)){
                    $grade->grade =  $value;

                    $ustudent = Ustudent::where([
                        ['user_id', '=', $user_id],
                        ['national_code', '=', $ustudent->national_code]
                    ])->first();
                    if ($ustudent) {
                        array_push(
                            $ustudents_fire_base_token_list,
                            $ustudent->fire_base_token
                        );
                    }
                }else{
                    $grade->grade = "-";
                }

                $grade->save();
            }else{
                if(is_numeric($value)){
                    $grade = new Grade();
                    $grade->user_id = $user_id;
                    $grade->test_id = $test_id;
                    $grade->national_code = $ustudent->national_code;
                    $grade->grade = $value;
                    $grade->save();

                    $ustudent = Ustudent::where([
                        ['user_id', '=', $user_id],
                        ['national_code', '=', $ustudent->national_code]
                    ])->first();
                    if ($ustudent) {
                        array_push(
                            $ustudents_fire_base_token_list,
                            $ustudent->fire_base_token
                        );
                    }
                }
            }

        }

        if (sizeof($ustudents_fire_base_token_list) > 0) {
            AdminController::notify(
                "نمره آزمون",
                " نمره آزمون " . Test::find($test_id)->title . " کلاس " . $course->title,
                User::find($user_id)->fire_base_server_key,
                $ustudents_fire_base_token_list
            );
        }

        return back();

    }


    public function show_post($id)
    {
        $table = "posts";
        $query = [['id', '=', $id]];

        $item = DB::table($table)->where($query)->get()->first();
        if ($item) {
            return view('post_detail', ['post' => $item]);
        } else
            abort(404);
    }

    public function show_program($id)
    {
        $table = "programs";
        $query = [['id', '=', $id]];

        $item = DB::table($table)->where($query)->get()->first();
        if ($item) {
            return view('post_detail', ['post' => $item]);
        } else
            abort(404);
    }

    public function import_student(){
        return view('vendor/backpack/crud/import_student')
            ->with('title', 'وارد کردن لیست دانش آموزان')
            ->with('user_id', \Auth::user()->id);
    }

    public function import_workbook(){
        return view('vendor/backpack/crud/import_workbook')
            ->with('title', 'وارد کردن لیست کارنامه دانش آموزان')
            ->with('groups', \Auth::user()->groups()->get())
            ->with('fields', \Auth::user()->fields()->get())
            ->with('user_id', \Auth::user()->id);
    }

    public function reset_password_form($user_id, $token){
        $ustudent = UStudent::where([
            ['user_id', '=', $user_id],
            ['token', '=', $token],
        ])->first();

        if ($ustudent)
            return view('reset_password')->with('ustudent', $ustudent);
        else
            abort(404);
    }

    public function reset_password(Request $request){
        $password = $request->input('password');
        $password_repeat = $request->input('password_confirmation');

        if (strlen($password) < 6 || strlen($password) > 12)
            return back()->withErrors(['error'=>'.رمز عبور باید بین 6 تا 12 کاراکتر باشد']);
        else if ($password != $password_repeat)
            return back()->withErrors(['error'=>'.رمز عبور با تکرار آن مطابقت ندارد']);
        else{
            $ustudent = UStudent::where([
                ['user_id', '=', $request->input('user_id')],
                ['national_code', '=', $request->input('national_code')],
            ])->first();
            $ustudent->password = Hash::make($password);
            $ustudent->save();
            return back()->with('ok', '.رمز عبور با موفقیت تغییر یافت');
        }
    }
    // ************************operators*********************************

    public function import_workbook_excel(Request $request)
    {
        //Authenticating provider
        $user = User::where('id' , '=' , $request->input('user_id'))->first();
        $ustudents_fire_base_token_list = [];

        if ($user){
            $user_id = $user->id;
        }else
            return back()->withErrors(['error'=>'مشکل در احراز هویت']);

        $year = $request->input('year');
        $month = $request->input('month');
        $scale = $request->input('scale');

        //Importing excel
        try {
            $rows = Excel::load($request->file('file')->getRealPath(), 'UTF-8')->get();
            $titles = array();
            $i = 1;

            //return print("<pre>".print_r($rows,true)."</pre>");
            foreach($rows as $row)
            {
                if ($i == 1){
                    $titles = array_diff(array_values($row->toArray()), [""]);
                }else{
                    $grades = array_diff(array_values($row->toArray()), [""]);

                    $student = Student::where([
                        ['user_id', '=', $user_id],
                        ['national_code', '=', $grades[0]],
                    ])->first();

                    if ($student != null){
                        unset($grades[0]);
                        $lessons = $titles;

                        // clearing empty grades
                        $empty_indexes = array();
                        for ($k = 1; $k<=sizeof($grades); $k++){
                            if (!isset($grades[$k]))
                                array_push($empty_indexes, $k);
                        }

                        foreach ($empty_indexes as $index){
                            unset($grades[$index]);
                            unset($lessons[$index]);
                        }

                        Workbook::create([
                            'user_id' => $user_id,
                            'student_id' => $student->id,
                            'year' => $year,
                            'month' => $month,
                            'scale' => $scale,
                            'grades' => implode("|",$grades),
                            'lessons' => implode("|",$lessons)
                        ]);

                        $ustudent = Ustudent::where([
                            ['user_id', '=', $user_id],
                            ['national_code', '=', $student->national_code]
                        ])->first();
                        if ($ustudent) {
                            array_push(
                                $ustudents_fire_base_token_list,
                                 $ustudent->fire_base_token
                            );
                        }

                    }
                }
                $i++;
            }

            if (sizeof($ustudents_fire_base_token_list) > 0) {
                AdminController::notify(
                    "کارنامه جدید",
                    " کارنامه " . $month . " " . $year,
                    User::find(\Auth::user()->id)->fire_base_server_key,
                    $ustudents_fire_base_token_list
                );
            }

        } catch (Exception $e) {
            return back()->withErrors(['error'=>'.فرمت فایل انتخابی با فرمت ارائه شده مطابقت ندارد']);
        }

        return redirect(URL::to('/admin/student'));
    }

    public function import_student_excel(Request $request)
    {
        //Authenticating provider
        $user = User::where('id' , '=' , $request->input('user_id'))->first();
        if ($user){
            $user_id = $user->id;
        }else
            return back()->withErrors(['error'=>'مشکل در احراز هویت']);

        //Importing excel
        try {
            $rows = Excel::load($request->file('file')->getRealPath(), 'UTF-8')->get();

            //check for student limitation
            $count = Student::where([
                ['user_id', '=', $user_id],
            ])->count();
            if ($count + sizeof($rows) >= $user->student_count_limit){
                $message = '.متاسفانه نمی توانید بیشتر از ' . $user->student_count_limit . ' دانش آموز اضافه کنید';
                return back()->withErrors(['error' => $message]);
            }

            foreach($rows as $row) {
                $data = array_values($row->toArray());

                $group = Group::where([['title', trim($data[3])],['user_id', '=', $user_id]])->first();
                if (!$group && trim($data[3]) != ''){
                    $group = new Group();
                    $group->user_id = $user_id;
                    $group->title = trim($data[3]);
                    $group->save();
                }

                $field = Field::where([['title', trim($data[4])],['user_id', '=', $user_id]])->first();
                if (!$field && trim($data[4]) != ''){
                    $field = new Field();
                    $field->user_id = $user_id;
                    $field->title = trim($data[4]);
                    $field->save();
                }

                $gender = "-";
                if($data[5] == Constant::$GENDER_MALE_TITLE)
                    $gender = Constant::$GENDER_MALE;
                elseif($data[5] == Constant::$GENDER_FEMALE_TITLE)
                    $gender = Constant::$GENDER_FEMALE;


                $student = Student::where([
                    ['user_id', '=', $user_id],
                    ['national_code', '=', $data[2]]
                ])->first();

                if ($student){
                    $student->first_name =  $data[0];
                    $student->last_name =  $data[1];
                    $student->group_id = $group->id;
                    $student->field_id = $field->id;
                    $student->gender = $gender;
                    $student->save();
                }else{
                    Student::create([
                        'user_id' => $user_id,
                        'first_name' => $data[0],
                        'last_name' => $data[1],
                        'national_code' => $data[2],
                        'group_id' => $group->id,
                        'field_id' => $field->id,
                        'gender' => $gender
                    ]);
                }

            }
        } catch (Exception $e) {
            return back()->withErrors(['error'=>'.فرمت فایل انتخابی با فرمت ارائه شده مطابقت ندارد']);
        }

        return redirect(URL::to('/admin/student'));
    }


    public static function notify($title, $message, $from, $to)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $data = [
            'title' => $title,
            'message' => $message,
        ];


        if (is_array($to)){
            $fcmNotification = [
                'registration_ids' => $to, //multple token array
                'data' => $data
            ];
        }else{
            $fcmNotification = [
                'to'        => $to,
                'data' => $data
            ];
        }

        $headers = [
            'Authorization: key='.$from,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        // dd($result);
        return true;
    }

    public function upload_apk(Request $request){
        $file = $request->file('apk');
        // 2. Move the new file to the correct path
        $file_name = $file->getClientOriginalName();
        $file_path = $file->storeAs('apks', $file_name , 'public');

        $user = User::find($request->input('user_id'));
        $user->apk_name = $file_name;
        $user->last_version = $request->input('version');
        $user->must_update = $request->input('must_update');
        $user->save();

        return Constant::$SUCCESS;
    }

    public function download_apk($filename)
    {
        // Check if file exists in app/storage/file folder
        // use while testing on localhost
        if (env('APP_ON_SERVER'))
            $file_path = "/home/schoolbr/public_html" . "/storage/public/apks/" . $filename;
        else
            $file_path = public_path() . "/storage/apks/" . $filename;

        $headers = array(
            'Content-Type' => 'application/apk',
            'Content-Disposition: attachment; filename='.$filename,
        );
        if ( file_exists( $file_path ) ) {
            // Send Download
            return \Response::download( $file_path, $filename, $headers );
        } else {
            // Error
            exit( 'Requested file does not exist on our server!' );
        }
    }

    public function send_apk_link(){
        $user = User::find(Input::get('message'));

        if ($user) {
            $link = env('APP_URL')
                . '/download/'
                . $user->apk_name;

            $url = 'https://api.kavenegar.com/v1/' .
                env('SMS_API_KEY') .
                '/verify/lookup.json?receptor=' .
                Input::get('from') .
                '&template=' .
                env('TEMPLATE_APK_LINK') .
                '&token=' .
                $link;

            return Redirect::away($url);
        }
    }
}