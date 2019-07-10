<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\WorkbookCrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TestRequest as StoreRequest;
use App\Http\Requests\TestRequest as UpdateRequest;
use App\Includes\Constant;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Test;

class CourseTestCrudController extends TestCrudController {

    public function setup() {
        parent::setup();

        // get the user_id parameter
        $course_id = \Route::current()->parameter('course_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/course/search/".$course_id."/test");

        // show only that user's posts
        $this->crud->addClause('where', 'course_id', $course_id);
//        $this->crud->addClause('where', 'user_id', '=', \Auth::user()->id);

    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($id);
        $this->data['title'] = trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['id'] = $id;

        $date = explode('/', $this->data['entry']['date']);
        $this->data['fields']['year']['value'] = $date[0];
        $this->data['fields']['month']['value'] = $date[1];
        $this->data['fields']['day']['value'] = $date[2];

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);
    }


    public function store(StoreRequest $request)
    {
        //print("<pre>".print_r($request->input('records'), true)."</pre>");

        $redirect_location = parent::storeCrud();

        $test = $this->data['entry'];

        $test->user_id = \Auth::user()->id;
        $course = Course::find(\Route::current()->parameter('course_id'));
        $test->course_id = $course->id;

        $date = $request->input('year').'/'.$request->input('month').'/'.$request->input('day');
        $test->date = $date;

        $test->save();


        return $redirect_location;

    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        $test = $this->data['entry'];

        $date = $request->input('year').'/'.$request->input('month').'/'.$request->input('day');
        $test->date = $date;
        $test->save();

        return $redirect_location;

    }
}
