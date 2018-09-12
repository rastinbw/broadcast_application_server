<?php

namespace App\Http\Controllers\Admin;

use App\Includes\Constant;
use App\Models\Media;
use App\Models\Notification;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\MediaRequest as StoreRequest;
use App\Http\Requests\MediaRequest as UpdateRequest;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Validator;

class MediaCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Media');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/media');
        $this->crud->setEntityNameStrings('رسانه', 'رسانه ها');

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
                'name' => 'description',
                'label' => 'توضیحات',
                'type' => 'text',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
        ], 'update/create/both');

        $this->crud->addField(
            [ // Upload
                'name' => 'media',
                'label' => '* انتخاب فایل صوتی <label style="color:#e55619"> ( فایل انتخابی باید به فرمت 
                            <label style="font-family:Arial, Helvetica, sans-serif;">mp&#x33</label> و حداکثر حجم 10 مگابایت باشد ) </label>',
                'type' => 'upload',
                'upload' => true,
                'attributes' => [
                    'dir' => 'rtl',
                    'accept' => '.mp3'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            'create');

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'عنوان',
            ],
            [
                'name' => 'description',
                'label' => 'توضیحات',
            ],
            [
                // run a function on the CRUD model and show its return value
                'name' => "created_at",
                'label' => "تاریخ ایجاد", // Table column heading
                'type' => "model_function",
                'function_name' => 'getDate', // the method in your Model
            ],
        ]);


        //$this->crud->setFromDb();

        // ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        // ------ CRUD COLUMNS

        // add a single column, at the end of the stack
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

    public function create()
    {
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getCreateFields();
        $this->data['title'] = trans('backpack::crud.add').' '.$this->crud->entity_name;

        $user = \Auth::user();
        $count = Media::where([
            ['user_id', '=', $user->id],
        ])->count();

        if ($count >= $user->media_count_limit){
            $message = '.متاسفانه نمی توانید بیشتر از ' . $user->media_count_limit . ' رسانه اضافه کنید';
            return back()->withErrors(['custom_fail' => true, 'errors' => [$message]]);
        }
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getCreateView(), $this->data);
    }


    public function store(StoreRequest $request)
    {
        if (!$request->file('media'))
            return back()->withErrors(['custom_fail' => true, 'errors' => ['.فایل صوتی را انتخاب کنید']]);

        $validator = Validator::make(
            array(
                'file'              =>      $request->file('media'),
                'extension'         =>      strtolower($request->file('media')->getClientOriginalExtension()),
            ),
            [
                'file'              =>      'required|max:10000',
                'extension'         =>      'required|in:mp3',
            ],
            [
                'file.max'              =>      '.حجم فایل انتخاب شده بیشتر از 10 مگابایت است',
                'extension.in'         =>      '.فرمت فایل صوتی درست نمی باشد',
            ]
        );

        $validator_results = $validator->errors()->messages();
        $errors = array();

        if (key_exists('file', $validator_results)){
            array_push($errors, $validator_results['file'][0]);
        }

        if (key_exists('extension', $validator_results)){
            array_push($errors, $validator_results['extension'][0]);
        }

        // return print("<pre>".print_r($errors,true)."</pre>");

        if($validator->fails()){
            return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        }

        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        $user = \Auth::user();

        $media = $this->crud->entry;
        $media->user_id = $user->id;
        $media->save();

        AdminController::notify("رسانه جدید", $media->title, $user->fire_base_server_key,'/topics/all');

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // if ($request->file('media')){
        //     $validator = Validator::make(
        //         array(
        //             'file'              =>      $request->file('media'),
        //             'extension'         =>      strtolower($request->file('media')->getClientOriginalExtension()),
        //         ),
        //         [
        //             'file'              =>      'required|max:10000',
        //             'extension'         =>      'required|in:mp3',
        //         ],
        //         [
        //             'file.max'              =>      '.حجم فایل انتخاب شده بیشتر از 10 مگابایت است',
        //             'extension.in'         =>      '.فرمت فایل صوتی درست نمی باشد',
        //         ]
        //     );

        //     $validator_results = $validator->errors()->messages();
        //     $errors = array();

        //     if (key_exists('file', $validator_results)){
        //         array_push($errors, $validator_results['file'][0]);
        //     }

        //     if (key_exists('extension', $validator_results)){
        //         array_push($errors, $validator_results['extension'][0]);
        //     }

        //     // return print("<pre>".print_r($errors,true)."</pre>");

        //     if($validator->fails()){
        //         return back()->withErrors(['custom_fail' => true, 'errors' => $errors]);
        //     }

        // }else{
        //     if (!$request->input('media'))
        //         return back()->withErrors(['custom_fail' => true, 'errors' => ['.فایل صوتی را انتخاب کنید']]);
        // }

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

}
