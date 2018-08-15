<?php namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\WorkbookCrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\WorkbookRequest as StoreRequest;
use App\Http\Requests\WorkbookRequest as UpdateRequest;

class StudentWorkbookCrudController extends WorkbookCrudController {

    public function setup() {
        parent::setup();

        // get the user_id parameter
        $student_id = \Route::current()->parameter('student_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/student/search/".$student_id."/workbook");

        // show only that user's posts
        $this->crud->addClause('where', 'student_id', $student_id);
//        $this->crud->addClause('where', 'user_id', '=', \Auth::user()->id);

    }



    public function store(StoreRequest $request)
    {
        //print("<pre>".print_r($request->input('records'), true)."</pre>");

        $redirect_location = parent::storeCrud();

        $records = json_decode($request->input('records'),true);
        $lessons = array();
        $grades = array();

        foreach ($records as $record) {
            if ($record != null) {
                array_push($lessons, $record['lesson']);
                array_push($grades, $record['grade']);
            }
        }

        $workbook = $this->data['entry'];

        $workbook->user_id = \Auth::user()->id;
        $workbook->student_id = \Route::current()->parameter('student_id');
        $workbook->lessons = implode("|",$lessons);
        $workbook->grades = implode("|",$grades);

        $workbook->save();

        return $redirect_location;

    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        $records = json_decode($request->input('records'),true);
        $lessons = array();
        $grades = array();

        foreach ($records as $record) {
            if ($record != null) {
                array_push($lessons, $record['lesson']);
                array_push($grades, $record['grade']);
            }
        }


        $workbook = $this->data['entry'];
        $workbook->lessons = implode("|",$lessons);
        $workbook->grades = implode("|",$grades);

        $workbook->save();

        return $redirect_location;

    }
}
