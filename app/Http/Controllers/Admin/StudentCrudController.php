<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use App\Models\Student;
use App\Http\Controllers\CrudController;


// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\StudentRequest as StoreRequest;
use App\Http\Requests\StudentRequest as UpdateRequest;

class StudentCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Student');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/student');
        $this->crud->setEntityNameStrings('دانش آموز', 'دانش آموزان');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        $this->crud->addClause('where', 'user_id', '=', \Auth::user()->id);

        $this->crud->addFields([
            [
                'name' => 'first_name',
                'label' => '* نام',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            [
                'name' => 'last_name',
                'label' => '* نام خانوادگی',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            [
                'name' => 'national_code',
                'label' => '* کد ملی',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            [  // Select
                'label' => "* پایه",
                'type' => 'select2',
                'name' => 'group_id', // the db column for the foreign key
                'entity' => 'group', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Group", // foreign key model
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
                'filter' => ['key'=>'user_id', 'operator'=>'=', 'value'=>\Auth::user()->id] //updated select2 file for this
            ],
            [  // Select
                'label' => "* رشته",
                'type' => 'select2',
                'name' => 'field_id', // the db column for the foreign key
                'entity' => 'field', // the method that defines the relationship in your Model
                'attribute' => 'title', // foreign key attribute that is shown to user
                'model' => "App\Models\Field", // foreign key model
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
                'filter' => ['key'=>'user_id', 'operator'=>'=', 'value'=>\Auth::user()->id] //updated select2 file for this
            ],
            [
                'name'        => 'gender', // the name of the db column
                'label'       => '* جنسیت', // the input label
                'type'        => 'radio',
                'options'     => [ // the key will be stored in the db, the value will be shown as label;
                    Constant::$GENDER_FEMALE => 'دختر',
                    Constant::$GENDER_MALE => 'پسر',
                ],
                // optional
                'inline'      => true, // show the radios all on the same line?
            ],
        ], 'update/create/both');


        $this->crud->addColumns([
            [
                'name' => 'first_name',
                'label' => 'نام',
            ],
            [
                'name' => 'last_name',
                'label' => 'نام خانوادگی',
            ],
            [
                'name' => 'national_code',
                'label' => 'کد ملی'
            ],
            [
                'name' => 'parent_code',
                'label' => 'کد اولیا',
            ],
            [
                'label' =>  "پایه", // Table column heading
                'type' => "select",
                'name' => 'group_id', // the column that contains the ID of that connected entity;
                'entity' => 'group', // the method that defines the relationship in your Model
                'attribute' => "title", // foreign key attribute that is shown to user
                'model' => "App\Models\Group", // foreign key model
            ],
            [
                'label' =>  "رشته", // Table column heading
                'type' => "select",
                'name' => 'field_id', // the column that contains the ID of that connected entity;
                'entity' => 'field', // the method that defines the relationship in your Model
                'attribute' => "title", // foreign key attribute that is shown to user
                'model' => "App\Models\Field", // foreign key model
            ],
            [
                'name' => 'gender',
                'label' => "جنسیت",
                'type' => 'select_from_array',
                'options' => [Constant::$GENDER_MALE => 'پسر', Constant::$GENDER_FEMALE => 'دختر'],
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "user_id",
                'label' => "وضعیت ثبت نام", // Table column heading
                'type' => "model_function",
                'function_name' => 'getRegistered', // the method in your Model
            ],
        ]);


        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'is_registered',
            'label' => 'وضعیت ثبت نام'
        ], [
            1 => 'ثبت شده ها',
            2 => 'ثبت نشده ها',
        ],  function ($value) {
            if ($value == 1)
                $this->crud->addClause('where', 'is_registered', true);
            else if($value == 2)
                $this->crud->addClause('where', 'is_registered', false);
        }
        );


        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'dropdown',
            'name' => 'gender',
            'label' => 'جنسیت'
        ], [
            Constant::$GENDER_FEMALE => 'دختر',
            Constant::$GENDER_MALE => 'پسر',
        ],  function ($value) {
            $this->crud->addClause('where', 'gender', $value);
        }
        );

        $this->crud->addFilter([ // select2 filter
            'name' => 'field_id',
            'type' => 'select2',
            'label'=> 'رشته',
        ], function() {
            return \Auth::user()->fields()->get()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function($value) { // if the filter is active
            $this->crud->addClause('where', 'field_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'group_id',
            'type' => 'select2',
            'label'=> 'پایه',
        ], function() {
            return \Auth::user()->groups()->get()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function($value) { // if the filter is active
            $this->crud->addClause('where', 'group_id', $value);
        });
        // ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        // ------ CRUD COLUMNS
        // $this->crud->addColumn(); // add a single column, at the end of the stack
        // $this->crud->addColumns(); // add multiple columns, at the end of the stack
        // $this->crud->removeColumn('column_name'); // remove a column from the stack
        // $this->crud->removeColumns(['column_name_1', 'column_name_2']); // remove an array of columns from the stack
        // $this->crud->setColumnDetails('column_name', ['attribute' => 'value']); // adjusts the properties of the passed in column (by name)
        // $this->crud->setColumnsDetails(['column_1', 'column_2'], ['attribute' => 'value']);

        // ------ CRUD BUTTONS
        // possible positions: 'beginning' and 'end'; defaults to 'beginning' for the 'line' stack, 'end' for the others;
        // $this->crud->addButton($stack, $name, $type, $content, $position); // add a button; possible types are: view, model_function
        // $this->crud->addButtonFromModelFunction($stack, $name, $model_function_name, $position); // add a button whose HTML is returned by a method in the CRUD model
        $this->crud->addButtonFromView('top',
            'import_student',
            'import_student',
            'beginning');

        $this->crud->addButtonFromView('top',
            'import_workbook',
            'import_workbook',
            'beginning');

        $this->crud->addButtonFromView('line', 'student_workbooks', 'student_workbooks', 'beginning');

        // add a button whose HTML is in a view placed at resources\views\vendor\backpack\crud\buttons
        // $this->crud->removeButton($name);
        // $this->crud->removeButtonFromStack($name, $stack);
        // $this->crud->removeAllButtons();
        // $this->crud->removeAllButtonsFromStack('line');

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
        // $this->crud->enableDetailsRow();
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('details_row');
        // NOTE: you also need to do overwrite the showDetailsRow($id) method in your EntityCrudController to show whatever you'd like in the details row OR overwrite the views/backpack/crud/details_row.blade.php

        // ------ REVISIONS
        // You also need to use \Venturecraft\Revisionable\RevisionableTrait;
        // Please check out: https://laravel-backpack.readme.io/docs/crud#revisions
        // $this->crud->allowAccess('revisions');

        // ------ AJAX TABLE VIEW
        // Please note the drawbacks of this though:
        // - 1-n and n-n columns are not searchable
        // - date and datetime columns won't be sortable anymore
        // $this->crud->enableAjaxTable();

        // ------ DATATABLE EXPORT BUTTONS
        // Show export to PDF, CSV, XLS and Print buttons on the table view.
        // Does not work well with AJAX datatables.
        $this->crud->enableExportButtons();


        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        //$this->crud->addClause('where', 'user_id', '==', 2);
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        //$this->crud->addClause('withoutGlobalScopes');
        // $this->crud->addClause('withoutGlobalScope', VisibleScope::class);
        // $this->crud->with(); // eager load relationships
        // $this->crud->orderBy();
        // $this->crud->groupBy();
        // $this->crud->limit();
    }


    public function create()
    {
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getCreateFields();
        $this->data['title'] = trans('backpack::crud.add').' '.$this->crud->entity_name;

        $user = \Auth::user();
        $count = Student::where([
            ['user_id', '=', $user->id],
        ])->count();

        if ($count >= $user->student_count_limit){
            $message = '.متاسفانه نمی توانید بیشتر از ' . $user->student_count_limit . ' دانش آموز اضافه کنید';
            return back()->withErrors(['custom_fail' => true, 'errors' => [$message]]);
        }

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getCreateView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
        $student = Student::where([
            ['user_id', '=', \Auth::user()->id],
            ['national_code', '=', $request->input('national_code')]
        ])->first();

        if ($student)
            return back()->withErrors(['custom_fail' => true, 'errors' => ['.کد ملی تکراری میباشد']]);

        $redirect_location = parent::storeCrud($request);

        $student = $this->data['entry'];
        $student->user_id = \Auth::user()->id;
        $student->save();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $student = Student::where([
            ['user_id', '=', \Auth::user()->id],
            ['national_code', '=', $request->input('national_code')],
            ['id', '!=', $request->input('id')],
        ])->first();

        if ($student)
            return back()->withErrors(['custom_fail' => true, 'errors' => ['.کد ملی تکراری میباشد']]);

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $student = Student::find($id);
        $student->workbooks()->delete();

        return $this->crud->delete($id);
    }
}
