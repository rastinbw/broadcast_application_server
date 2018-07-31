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
            $titles = [];
            $i = 1;

            foreach($rows as $row)
            {
                if ($i == 1){
                    $titles = array_values($row->toArray());
                    unset($titles[0], $titles[1], $titles[2]);}else{
                    $grades = array_values($row->toArray());
                    $student = Student::where([
                        ['user_id', '=', $user_id],
                        ['national_code', '=', $grades[0]],
                    ])->first();
                    $year = $grades[1];
                    $month = $grades[2];
                    unset($grades[0], $grades[1], $grades[2]);
                    if ($student)
                        Workbook::create([
                            'user_id' => $user_id,
                            'student_id' => $student->id,
                            'year' => $year,
                            'month' => $month,
                            'grades' => implode("|",$grades),
                            'lessons' => implode("|",$titles)
                        ]);}$i++;}
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