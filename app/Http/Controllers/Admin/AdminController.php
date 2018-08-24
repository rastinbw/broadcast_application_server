<?php
/**
 * Created by PhpStorm.
 * User: nitsarof
 * Date: 8/13/18
 * Time: 7:16 PM
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Ustudent;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Input;
use App\Models\Workbook;
use DB;
use Session;
use Excel;

class AdminController extends Controller
{
    public function import_student(){
        return view('vendor/backpack/crud/import_student')
            ->with('title', 'وارد کردن لیست دانش آموزان')
            ->with('user_id', \Auth::user()->id);
    }

    public function import_workbook(){
        return view('vendor/backpack/crud/import_workbook')
            ->with('title', 'وارد کردن لیست کارنامه دانش آموزان')
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

                        Workbook::create([
                            'user_id' => $user_id,
                            'student_id' => $student->id,
                            'year' => $year,
                            'month' => $month,
                            'scale' => $scale,
                            'grades' => implode("|",$grades),
                            'lessons' => implode("|",$titles)
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

            foreach($rows as $row) {
                $student = array_values($row->toArray());
                Student::create([
                    'user_id' => $user_id,
                    'first_name' => $student[0],
                    'last_name' => $student[1],
                    'national_code' => $student[2],
                    'grade' => $student[3],
                ]);
            }
        } catch (Exception $e) {
            return back()->withErrors(['error'=>'.فرمت فایل انتخابی با فرمت ارائه شده مطابقت ندارد']);
        }

        return redirect(URL::to('/admin/student'));
    }


}