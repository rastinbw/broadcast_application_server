<?php
/**
 * Created by PhpStorm.
 * User: nitsarof
 * Date: 3/19/19
 * Time: 9:19 PM
 */

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Student;
use App\Models\Ustudent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function get_filtered_students(Request $request) {
        $group_id = $request->input('group_id');
        $field_id = $request->input('field_id');
        $gender_id = $request->input('gender_id');
        $search_phrase = $request->input('search_phrase');
        $user_id = $request->input('user_id');

        $filter_list = array();

        array_push($filter_list, ['user_id', '=', $user_id]);

        if ($group_id != "")
            array_push($filter_list, ['group_id', '=', $group_id]);

        if ($field_id != "")
            array_push($filter_list, ['field_id', '=', $field_id]);

        if ($gender_id != "")
            array_push($filter_list, ['gender', '=', $gender_id]);

        if ($search_phrase != ""){
            $students = Ustudent::where($filter_list)
                ->where(function ($query) use ($search_phrase){
                    $query->where(DB::raw('concat(first_name," ",last_name)'), 'like', '%' . $search_phrase . '%')
                        ->orWhere('national_code', '=',  $search_phrase);
                })->get();
        }else
            $students = Ustudent::where($filter_list)->get();

        return response()->json(array('students'=> $students), 200);
    }


    public function get_some_students(Request $request) {
        $id_list = $request->input('id_list');
        $students = [];

        if ($id_list) {
            foreach ($id_list as $id)
                array_push($students, Ustudent::find($id));
        }

        return response()->json(array('students'=> $students), 200);
    }

    public function add_students_to_course(Request $request){
        $id_list = $request->input('id_list');
        $course =  Course::find($request->input('course_id'));

        if ($id_list) {
            foreach ($id_list as $id) {
                $check = DB::table('course_ustudent')
                        ->where([
                                ['ustudent_id', $id],
                                ['course_id', $course->id]]
                        )->count() > 0;

                if (!$check) {
                    $course->ustudents()->save(
                        Ustudent::find($id)
                    );
                }
            }
        }

        return response()->json(array('result'=> 'success'), 200);
    }


    public function remove_students_from_course(Request $request){
        $id_list = $request->input('id_list');
        $course =  Course::find($request->input('course_id'));

        if ($id_list) {
            foreach ($id_list as $id) {
                $course->ustudents()->detach(
                    Ustudent::find($id)
                );
            }
        }
        return response()->json(array('result'=> 'success'), 200);
    }

}