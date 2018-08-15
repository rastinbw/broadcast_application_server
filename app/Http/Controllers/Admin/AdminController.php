<?php
/**
 * Created by PhpStorm.
 * User: nitsarof
 * Date: 8/13/18
 * Time: 7:16 PM
 */

namespace App\Http\Controllers\Admin;


class AdminController
{
    public function import_student(){
        return view('vendor/backpack/crud/import_student')
            ->with('title', 'وارد کردن لیست دانش آموزان')
            ->with('user_id', \Auth::user()->id);
    }

    public function import_workbook(){
        return view('vendor/backpack/crud/import_workbook')
            ->with('title', 'وارد کردن لیست کارنامه دانش آموزان')
            ->with('user_id', \Auth::user()->id);
    }
}