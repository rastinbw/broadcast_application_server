<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $year = $request->input('year');
        $month = $request->input('month');
        $user = User::where('email' , '=' , $request->input('email'))->first();

        if ($user){
            if (Hash::check($request->input('password'), $user->password))
                $user_id = $user->id;
            else
                return "Invalid password";
        }else
            return "Invalid email";

        //Importing excel
        try {
            $rows = Excel::load($request->file('import_file')->getRealPath(), 'UTF-8')->get();
            $titles = array();
            $i = 1;

            //return print("<pre>".print_r($rows,true)."</pre>");
            foreach($rows as $row)
            {
                if ($i == 1){
                    $titles =   array_diff(array_values($row->toArray()), [""]);
                }else{
                    $grades = array_diff(array_values($row->toArray()), [""]);

                    $student = Student::where([
                        ['user_id', '=', $user_id],
                        ['national_code', '=', $grades[0]],
                    ])->first();

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
                $i++;
            }
        } catch (Exception $e) {
            return $e;
        }

        return back();
    }

    public function import_student_excel(Request $request)
    {
        //Authenticating provider
        $user = User::where('email' , '=' , $request->input('email'))->first();

        if ($user){
            if (Hash::check($request->input('password'), $user->password))
                $user_id = $user->id;
            else
                return "Invalid password";
        }else
            return "Invalid email";

        //Importing excel
        try {
            $rows = Excel::load($request->file('import_file')->getRealPath(), 'UTF-8')->get();

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
            return $e;
        }

        return back();
    }

}