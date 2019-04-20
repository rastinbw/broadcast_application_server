<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\WorkbookCrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CtrRequest as StoreRequest;
use App\Http\Requests\CtrRequest as UpdateRequest;
use App\Models\Ctr;
use Carbon\Carbon;

class CourseCtrCrudController extends CtrCrudController {

    public function setup() {
        parent::setup();

        // get the user_id parameter
        $course_id = \Route::current()->parameter('course_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/course/search/".$course_id."/ctr");

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

        $ctr = $this->data['entry'];

        $ctr->user_id = \Auth::user()->id;
        $ctr->course_id = \Route::current()->parameter('course_id');

        $date = $request->input('year').'/'.$request->input('month').'/'.$request->input('day');
        $ctr->date = $date;

        $ctr->save();


        return $redirect_location;

    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        $ctr = $this->data['entry'];

        $date = $request->input('year').'/'.$request->input('month').'/'.$request->input('day');
        $ctr->date = $date;
        $ctr->save();


        return $redirect_location;

    }


}
