<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
   CRUD::resource('student', 'StudentCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('post', 'PostCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('media', 'MediaCrudController');
});

// Route for view/blade file.
Route::get('import_workbook', 'MaatwebsiteController@import_workbook');

// Route for import excel data to database.
Route::post('import_excel', 'MaatwebsiteController@import_excel');

Route::get('/', function () {
    return view('base');
});


//Webservice Routes
Route::post('/api/{user_id}/student/authorize', 'WebserviceController@authorize_student');
Route::post('/api/{user_id}/student/confirm', 'WebserviceController@confirm_student');
Route::post('/api/{user_id}/student/login', 'WebserviceController@login_student');
Route::post('/api/{user_id}/student/workbook', 'WebserviceController@get_student_workbook');
Route::post('/api/{user_id}/notification', 'WebserviceController@get_last_notifications');
Route::get('/api/{user_id}/posts/{type}/{chunk_count}/{page_count}', 'WebserviceController@get_posts');
