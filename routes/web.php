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

Route::get('/about_us', function (){
    return view('about_us');
});

Route::get('/rules', function (){
    return view('rules');
});

//Webservice Routes
Route::post('/api/{user_id}/student/authorize', 'WebserviceController@authorize_student');
Route::post('/api/{user_id}/student/confirm', 'WebserviceController@confirm_student');
Route::post('/api/{user_id}/student/get_password', 'WebserviceController@send_password');
Route::post('/api/{user_id}/student/change_password', 'WebserviceController@change_password');
Route::post('/api/{user_id}/student/login', 'WebserviceController@login_student');
Route::post('/api/{user_id}/student/workbook', 'WebserviceController@get_student_workbook');
Route::post('/api/{user_id}/student/info', 'WebserviceController@get_student_info');
Route::post('/api/{user_id}/send_ticket', 'WebserviceController@save_ticket');
Route::get('/api/{user_id}/notification/{date}', 'WebserviceController@get_last_notifications');
Route::get('/api/{user_id}/post/{type}/{id}', 'WebserviceController@get_post');
Route::get('/api/{user_id}/posts/{type}/{chunk_count}/{page_count}/{search_phrase}', 'WebserviceController@get_posts');
Route::get('/api/post/{id}', 'WebserviceController@show_post');

Route::get('/get_hash', function()
{
	return Hash::make('123456');
});
