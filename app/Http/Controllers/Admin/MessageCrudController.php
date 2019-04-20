<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use App\Models\Notification;
use App\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\MessageRequest as StoreRequest;
use App\Http\Requests\MessageRequest as UpdateRequest;

class MessageCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Message');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/message');
        $this->crud->setEntityNameStrings('پیام', 'پیام ها');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        $this->crud->addClause('where', 'user_id', '=', \Auth::user()->id);

        $this->crud->addFields([
            [
                'name' => 'title',
                'label' => '* عنوان',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            [
                'name' => 'content',
                'label' => '* متن',
                'type' => 'textarea',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
        ], 'update/create/both');

        $this->crud->addFields([
            [  // Select
                'label' => "* پایه ( میتوانید در بخش پایه های تحصیلی اقدام به اضافه کردن پایه های جدید نمایید )",
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
                'label' => "* رشته ( میتوانید در بخش رشته های تحصیلی اقدام به اضافه کردن رشته های جدید نمایید )",
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
                'label'       => 'جنسیت', // the input label
                'type'        => 'radio',
                'options'     => [ // the key will be stored in the db, the value will be shown as label;
                    Constant::$GENDER_FEMALE => 'دختر',
                    Constant::$GENDER_MALE => 'پسر',
                ],
                // optional
                'inline'      => true, // show the radios all on the same line?
            ],
        ], 'create');

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
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
                'name' => "created_at",
                'label' => "تاریخ ارسال", // Table column heading
                'type' => "model_function",
                'function_name' => 'getDate', // the method in your Model
            ],
        ]);


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
        // $this->crud->addButtonFromView($stack, $name, $view, $position); // add a button whose HTML is in a view placed at resources\views\vendor\backpack\crud\buttons
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
        // $this->crud->enableExportButtons();

        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        // $this->crud->addClause('withoutGlobalScopes');
        // $this->crud->addClause('withoutGlobalScope', VisibleScope::class);
        // $this->crud->with(); // eager load relationships
        // $this->crud->orderBy();
        // $this->crud->groupBy();
        // $this->crud->limit();
    }

    public function index()
    {
        $this->crud->hasAccessOrFail('list');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = ucfirst($this->crud->entity_name_plural);
        $this->data['message_log'] = true;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getListView(), $this->data);
    }

    public function create()
    {
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getCreateFields();
        $this->data['title'] = 'فرستادن'.' '.$this->crud->entity_name;
        $this->data['message_log'] = true;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getCreateView(), $this->data);
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
        $this->data['message_log'] = true;

        return view($this->crud->getEditView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);

        $user = \Auth::user();

        $message = $this->crud->entry;
        $message->user_id = $user->id;
        $message->save();

        $to = '/topics/group_'.$message->gender.$message->group_id.$message->field_id;
        AdminController::notify("پیام جدید", $message->title, $user->fire_base_server_key, $to);

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
