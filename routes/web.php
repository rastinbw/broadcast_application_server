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

use Illuminate\Support\Facades\URL;

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('ustudent', 'UstudentCrudController');
});

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

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('program', 'ProgramCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('message', 'MessageCrudController');
});


Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('group', 'GroupCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('field', 'FieldCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('staff', 'StaffCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('slider', 'SliderCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('about', 'AboutCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('course', 'CourseCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('workbook', 'WorkbookCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('test', 'TestCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('ctr', 'CtrCrudController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function()
{
    Route::group(['prefix' => 'course/search/{course_id}'], function()
    {
        Route::get('/ctr_list/{id}/{user_id}', 'AdminController@get_ctr_list');
        Route::get('/grades_list/{id}/{user_id}', 'AdminController@get_grades_list');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('student', 'StudentCrudController');
    CRUD::resource('workbook', 'WorkbookCrudController');

    Route::group(['prefix' => 'student/search/{student_id}'], function()
    {
        CRUD::resource('workbook', 'StudentWorkbookCrudController');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('course', 'CourseCrudController');
    CRUD::resource('test', 'TestCrudController');

    Route::group(['prefix' => 'course/search/{course_id}'], function()
    {
        CRUD::resource('test', 'CourseTestCrudController');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function()
{
    CRUD::resource('course', 'CourseCrudController');
    CRUD::resource('ctr', 'CtrCrudController');

    Route::group(['prefix' => 'course/search/{course_id}'], function()
    {
        CRUD::resource('ctr', 'CourseCtrCrudController');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function() {
    Route::get('/import_student', 'AdminController@import_student');
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function() {
    Route::get('/import_workbook', 'AdminController@import_workbook');
});

// Route for import excel data to database.
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function() {
    Route::post('/update_slider_images', 'AdminController@update_slider_images');
});

// Route for import excel data to database.
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function() {
    Route::post('/import_workbook_excel', 'AdminController@import_workbook_excel');
});

// Route for import excel data to database.
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function() {
    Route::post('/import_student_excel', 'AdminController@import_student_excel');
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function() {
    Route::post('/update_ctr_list/{course_id}/{ctr_id}', 'AdminController@update_ctr_list');
});

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth'], 'namespace' => 'Admin'], function() {
    Route::post('/update_grades_list/{course_id}/{test_id}', 'AdminController@update_grades_list');
});


Route::get( '/download/{filename}', 'Admin\AdminController@download_apk');

Route::post( '/upload/apk', 'Admin\AdminController@upload_apk');

Route::get( '/apk_link', 'Admin\AdminController@send_apk_link');

Route::get('/post/{id}', 'Admin\AdminController@show_post');

Route::get('/program/{id}', 'Admin\AdminController@show_program');

Route::get('/change_password/{user_id}/{token}', 'Admin\AdminController@reset_password_form');

Route::post('/reset_password', 'Admin\AdminController@reset_password');

Route::get('/help', function (){
    return view('help');
});

Route::get('/', function () {
    return redirect(URL::to('/admin'));
});


//Webservice Routes
Route::post('/api/{user_id}/ustudent/register', 'API\WebserviceController@register_ustudent');
Route::post('/api/{user_id}/ustudent/verification_code', 'API\WebserviceController@send_verification_code');
Route::post('/api/{user_id}/ustudent/confirm', 'API\WebserviceController@confirm_ustudent');
Route::post('/api/{user_id}/ustudent/login', 'API\WebserviceController@login_ustudent');
Route::post('/api/{user_id}/ustudent/login/parent', 'API\WebserviceController@login_as_parent');
Route::post('/api/{user_id}/ustudent/forget_password', 'API\WebserviceController@send_change_password_link');


Route::get('/api/{user_id}/ustudent/check_version/{version}', 'API\WebserviceController@check_version');
Route::post('/api/{user_id}/ustudent/check_token', 'API\WebserviceController@check_token');
Route::post('/api/{user_id}/ustudent/change_password', 'API\WebserviceController@change_password');
Route::post('/api/{user_id}/ustudent/info', 'API\WebserviceController@get_ustudent_info');
Route::post('/api/{user_id}/ustudent/info/update', 'API\WebserviceController@update_ustudent_info');
Route::post('/api/{user_id}/ustudent/workbook', 'API\WebserviceController@get_student_workbook');
Route::post('/api/{user_id}/ustudent/courses', 'API\WebserviceController@get_student_courses');
Route::post('/api/{user_id}/ustudent/absents', 'API\WebserviceController@get_student_course_absents');
Route::post('/api/{user_id}/ustudent/grades', 'API\WebserviceController@get_student_course_grades');


Route::post('/api/{user_id}/send_ticket', 'API\WebserviceController@save_ticket');
Route::post('/api/{user_id}/messages', 'API\WebserviceController@get_messages');
Route::get('/api/{user_id}/groups', 'API\WebserviceController@get_user_group_list');
Route::get('/api/{user_id}/fields', 'API\WebserviceController@get_user_field_list');
Route::post('/api/{user_id}/staff', 'API\WebserviceController@get_staff');
Route::get('/api/{user_id}/slider', 'API\WebserviceController@get_slider');
Route::get('/api/{user_id}/about', 'API\WebserviceController@get_about');
Route::get('/api/{user_id}/staff/updated', 'API\WebserviceController@get_staff_updated');
Route::get('/api/{user_id}/slider/updated', 'API\WebserviceController@get_slider_updated');
Route::get('/api/{user_id}/posts/{type}/{chunk_count}/{page_count}/{search_phrase}/{group_id}/{field_id}', 'API\WebserviceController@get_posts');



//Admin Webservice Routes
Route::post('/api/admin/login', 'API\AdminWebserviceController@login_admin');
Route::post('/api/admin/check_token', 'API\AdminWebserviceController@check_token');
Route::post('/api/admin/groups', 'API\AdminWebserviceController@get_group_list');
Route::post('/api/admin/fields', 'API\AdminWebserviceController@get_field_list');
Route::post('/api/admin/posts/{type}/{chunk_count}/{page_count}/{search_phrase}/{group_id}/{field_id}', 'API\AdminWebserviceController@get_posts');

Route::post('/api/admin/post/create', 'API\AdminWebserviceController@create_post');
Route::post('/api/admin/post/update/{id}', 'API\AdminWebserviceController@update_post');
Route::post('/api/admin/post/delete/{id}', 'API\AdminWebserviceController@delete_post');

Route::post('/api/admin/program/create', 'API\AdminWebserviceController@create_program');
Route::post('/api/admin/program/update/{id}', 'API\AdminWebserviceController@update_program');
Route::post('/api/admin/program/delete/{id}', 'API\AdminWebserviceController@delete_program');

Route::post('/api/admin/message/create', 'API\AdminWebserviceController@create_message');
Route::post('/api/admin/message/update/{id}', 'API\AdminWebserviceController@update_message');
Route::post('/api/admin/message/delete/{id}', 'API\AdminWebserviceController@delete_message');

Route::post('/api/admin/media/create', 'API\AdminWebserviceController@create_media');
Route::post('/api/admin/media/update/{id}', 'API\AdminWebserviceController@update_media');
Route::post('/api/admin/media/delete/{id}', 'API\AdminWebserviceController@delete_media');