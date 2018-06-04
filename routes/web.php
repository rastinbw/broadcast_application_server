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


Route::get('/', function () {
    return view('welcome');
});

//Webservice Routes
Route::post('/api/{user_id}/student/authorize', 'WebserviceController@authorize_student');
Route::post('/api/{user_id}/student/confirm', 'WebserviceController@confirm_student');
Route::post('/api/{user_id}/student/login', 'WebserviceController@login_student');
Route::post('/api/{user_id}/notification', 'WebserviceController@get_last_notifications');
Route::get('/api/{user_id}/posts/{chunk_count}/{page_count}', 'WebserviceController@get_posts');