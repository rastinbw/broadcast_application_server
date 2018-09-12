<?php
/**
 * Created by PhpStorm.
 * User: nitsarof
 * Date: 8/13/18
 * Time: 7:16 PM
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Includes\Constant;
use App\Models\Student;
use App\Models\Ustudent;
use App\User;
use Exception;
use function GuzzleHttp\Psr7\str;
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
                    }
                }
                $i++;
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

                $student = Student::where([
                    ['user_id', '=', $user_id],
                    ['national_code', '=', $data[2]]
                ])->first();
                if ($student){
                    $student->first_name =  $data[0];
                    $student->last_name =  $data[1];
                    $student->grade =  $data[3];
                    $student->save();
                }else{
                    Student::create([
                        'user_id' => $user_id,
                        'first_name' => $data[0],
                        'last_name' => $data[1],
                        'national_code' => $data[2],
                        'grade' => $data[3],
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
        $user->save();

        return Constant::$SUCCESS;
    }

    public function download_apk($filename)
    {
        // Check if file exists in app/storage/file folder
        // use /storage/apks/ while testing on localhost
        $file_path = public_path() . "/storage/public/apks/" . $filename;
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

            // return Redirect::away($url);
            return $url;
        }
    }
}