<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\UstudentRequest as StoreRequest;
use App\Http\Requests\UstudentRequest as UpdateRequest;

use App\Models\Ustudent;
use App\User;

class PlanUstudentCrudController extends UstudentCrudController {

    public function setup() {
        parent::setup();

        // get the user_id parameter
        $plan_id = \Route::current()->parameter('plan_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/plan/search/".$plan_id."/ustudent");

        // show only that user's posts
        $this->crud->addClause('whereHas', 'plans', function($query) use ($plan_id){
                $query->where( 'plans.id', $plan_id );
        });

        // $this->crud->addClause('where', 'user_id', '=', \Auth::user()->id);

        $this->crud->removeButton('delete');

    }


    public function store(StoreRequest $request)
    {
        $redirect_location = parent::storeCrud();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        return $redirect_location;
    }
}
