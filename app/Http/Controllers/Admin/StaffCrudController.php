<?php

namespace App\Http\Controllers\Admin;

use App\Models\Staff;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\StaffRequest as StoreRequest;
use App\Http\Requests\StaffRequest as UpdateRequest;
use Exception;

class StaffCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Staff');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/staff');
        $this->crud->setEntityNameStrings('عضو', 'اعضای مجموعه');

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
                'name' => 'profession',
                'label' => '* تخصص',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            [
                'name' => 'email',
                'label' => 'ایمیل',
                'type' => 'email',
                'attributes' => [
                    'dir' => 'ltr'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            [
                'name' => 'description',
                'label' => 'توضیحات',
                'type' => 'ckeditor',
                'attributes' => [
                    'dir' => 'rtl'
                ],
                'wrapperAttributes' => [
                    'dir' => 'rtl'
                ],
            ],
            [ // base64_image
                'label' => '<label style="color:#e55619">( فایل انتخابی باید به فرمت
                            <label style="font-family:Arial, Helvetica, sans-serif;">jpeg, jpg</label> و حداکثر حجم 3 مگابایت باشد )</label> تصویر پرسنل',
                'name' => "photo",
                'filename' => NULL, // set to null if not needed
                'type' => 'base64_image',
                'aspect_ratio' => 1, // set to 0 to allow any aspect ratio
                'crop' => true, // set to true to allow cropping, false to disable
            ]

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
                'name' => 'profession',
                'label' => 'تخصص',
            ],
            [
                'name' => 'email',
                'label' => 'ایمیل'
            ],

        ]);


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

    public function create()
    {
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getCreateFields();
        $this->data['title'] = trans('backpack::crud.add').' '.$this->crud->entity_name;

        $user = \Auth::user();
        $count = Staff::where([
            ['user_id', '=', $user->id],
        ])->count();

        if ($count >= $user->staff_count_limit){
            $message = '.متاسفانه نمی توانید بیشتر از ' . $user->staff_count_limit . ' عضو اضافه کنید';
            return back()->withErrors(['custom_fail' => true, 'errors' => [$message]]);
        }

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getCreateView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
//        print("<pre>".print_r($request->input('photo'),true)."</pre>");

        $size = $this->getBase64ImageSize($request->input('photo'));
        try{
            if ($size > 4000){
                return back()->withErrors(['custom_fail' => true, 'errors' => ['.حجم تصویر انتخاب شده بیشتر از 3 مگابایت است']]);
            }
        }catch (Exception $e){
            abort(500);
        }

        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        $staff = $this->crud->entry;
        $staff->user_id = \Auth::user()->id;
        $staff->save();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $size = $this->getBase64ImageSize($request->input('photo'));
        try{
            if ($size > 4000){
                return back()->withErrors(['custom_fail' => true, 'errors' => ['.حجم تصویر انتخاب شده بیشتر از 3 مگابایت است']]);
            }
        }catch (Exception $e){
            abort(500);
        }

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function getBase64ImageSize($base64Image){ //return memory size in B, KB, MB
        try{
            $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
            $size_in_kb    = $size_in_bytes / 1024;

            return $size_in_kb;
        }
        catch(Exception $e){
            return $e;
        }
    }
}
