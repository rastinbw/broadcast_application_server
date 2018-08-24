<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ backpack_url('dashboard') }}"><span>{{ trans('backpack::base.dashboard') }}</span> <i class="fa fa-dashboard"></i></a></li>
{{--<li><a href="{{ backpack_url('elfinder') }}"><span>{{ trans('backpack::crud.file_manager') }}</span> <i class="fa fa-files-o"></i></a></li>--}}
<li><a href="{{ backpack_url('ustudent') }}"><span>کاربران</span> <i class="fa fa-user "></i></a></li>
<li><a href="{{ backpack_url('student') }}"><span>دانش آموزان</span> <i class="fa fa-graduation-cap "></i></a></li>
<li><a href="{{ backpack_url('post') }}"><span>اطلاعیه ها</span> <i class="fa fa-newspaper-o"></i></a></li>
<li><a href="{{ backpack_url('media') }}"><span>رسانه ها</span> <i class="fa fa-microphone "></i></a></li>
<li><a href="{{ backpack_url('program') }}"><span>برنامه های کلاسی</span> <i class="fa fa-calendar-o "></i></a></li>
<li><a href="{{ backpack_url('group') }}"><span>گروه های آموزشی</span> <i class="fa fa-th-large "></i></a></li>
<li><a href="{{ backpack_url('staff') }}"><span>اعضای مجموعه</span> <i class="fa fa-users "></i></a></li>

<?php
    $id = \Auth::user()->slider_id;
?>
<li><a href="{{url(URL::to('admin/slider/'.$id.'/edit'))}}"><span>تصاویر اسلایدر</span> <i class="fa fa-image "></i></a></li>

