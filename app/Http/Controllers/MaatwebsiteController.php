<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Student;
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

class MaatwebsiteController extends Controller
{
    public function import_workbook()
    {
        return view('import_workbook');
    }

    public function import_student()
    {
        return view('import_student');
    }

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