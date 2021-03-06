@extends('backpack::layout')

@section('header')
    <section style="padding-top: 5px" class="content-header">
        <h1 style="text-align: right;">
            <span  style="font-size: 20px" >وارد کردن لیست کارنامه دانش آموزان </span>
        </h1>
    </section>
@endsection

@section('content')
    <div class="row" style="margin-right: 60px;margin-left: 60px">
        <div  class="col-md-12 col-md-offset-2" style="margin: auto; text-align: right">
            <a href="{{ url(URL::to('/admin/student')) }}">{{ trans('backpack::crud.back_to_all') }} دانش آموزان &nbsp<i class="fa fa-angle-double-right"></i></a><br><br>

            <div class="box box-default">
                <div class="box-body">

                    @if($message = $errors->first('error'))
                        <div style="padding: 10px" class="alert alert-error alert-dismissible fade in" role="alert">
                            <label>{{ $message }}</label>
                            <label data-dismiss="alert" style="cursor: pointer;margin-left: 10px; color: #ffffffff">&#10006;</label>
                        </div>
                    @endif
                    {!! Session::forget('error') !!}
                    <br />

                    <form action="{{ URL::to('/admin/import_workbook_excel') }}"
                          class="form-horizontal"
                          method="post"
                          enctype="multipart/form-data"
                          name="myForm"
                          onsubmit="return validate()">

                        {{ csrf_field() }}
                        <input name="user_id" type="hidden" value="{{$user_id}}">

                        <div class="form-group" id="input_year" style="margin-right: 5px; margin-left: 5px">
                            <div class="col-md-12">
                                <div style=" padding: 5px" class="col-md-8 col-md-offset-2">
                                    <input style="text-align: right; font-size: 18px" class="form-control"  type="text" name="year">
                                </div>

                                <label style="text-align: left; " class="col-md-2 control-label">سال تحصیلی *</label>
                            </div>

                            <span id="input_year_error" style="display: none" class="help-block col-md-8 col-md-offset-2">
                                <strong>.لطفا سال تحصیلی را وارد کنید</strong>
                            </span>
                        </div>


                        <div class="form-group" id="input_month" style="margin-right: 5px; margin-left: 5px">
                            <div class="col-md-12">
                                <div style="padding: 5px" class="col-md-8 col-md-offset-2">
                                    <input style="text-align: right; font-size: 18px" class="form-control" type="text" name="month">
                                </div>

                                <label style="text-align: left" class="col-md-2 control-label">ماه کارنامه *</label>
                            </div>

                            <span id="input_month_error" style="display: none" class="help-block col-md-8 col-md-offset-2">
                                <strong>.لطفا ماه ارائه کارنامه را مشخص کنید</strong>
                            </span>
                        </div>


                        {{--<div class="form-group" id="input_group" style="margin-right: 5px; margin-left: 5px">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<div style="padding: 5px" class="col-md-8 col-md-offset-2">--}}
                                    {{--<select style="text-align: right; font-size: 18px" class="form-control" name="group">--}}
                                        {{--@foreach($groups as $group)--}}
                                            {{--<option>{{ $group->title }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}

                                {{--<label style="text-align: left" class="col-md-2 control-label">پایه تحصیلی *</label>--}}
                            {{--</div>--}}

                            {{--<span id="input_group_error" style="display: none" class="help-block col-md-8 col-md-offset-2">--}}
                                {{--<strong>.لطفا پایه تحصیلی را مشخص کنید</strong>--}}
                            {{--</span>--}}
                        {{--</div>--}}


                        {{--<div class="form-group" id="input_field" style="margin-right: 5px; margin-left: 5px">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<div style="padding: 5px" class="col-md-8 col-md-offset-2">--}}
                                    {{--<select style="text-align: right; font-size: 18px" class="form-control" name="field">--}}
                                        {{--@foreach($fields as $field)--}}
                                            {{--<option>{{ $field->title }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}

                                {{--<label style="text-align: left" class="col-md-2 control-label">رشته تحصیلی *</label>--}}
                            {{--</div>--}}

                            {{--<span id="input_field_error" style="display: none" class="help-block col-md-8 col-md-offset-2">--}}
                                {{--<strong>.لطفا رشته تحصیلی را مشخص کنید</strong>--}}
                            {{--</span>--}}
                        {{--</div>--}}


                        {{--<div class="form-group" id="input_gender" style="margin-right: 5px; margin-left: 5px">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<div style="padding: 5px" class="col-md-8 col-md-offset-2">--}}
                                    {{--<select style="text-align: right; font-size: 18px" class="form-control" name="gender">--}}
                                        {{--<option>{{ \App\Includes\Constant::$GENDER_MALE_TITLE }}</option>--}}
                                        {{--<option>{{ \App\Includes\Constant::$GENDER_FEMALE_TITLE }}</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}

                                {{--<label style="text-align: left" class="col-md-2 control-label">جنسیت *</label>--}}
                            {{--</div>--}}

                            {{--<span id="input_gender_error" style="display: none" class="help-block col-md-8 col-md-offset-2">--}}
                                {{--<strong>.لطفا جنسیت را مشخص کنید</strong>--}}
                            {{--</span>--}}
                        {{--</div>--}}


                        <div class="form-group" id="input_scale" style="margin-right: 5px; margin-left: 5px">
                            <div class="col-md-12">
                                <div style="padding: 5px" class="col-md-8 col-md-offset-2">
                                    <input style="text-align: right; font-size: 18px" value="20" class="form-control" type="text" name="scale">
                                </div>

                                <label style="text-align: left" class="col-md-2 control-label">مقیاس *</label>
                            </div>

                            <span id="input_scale_error" style="display: none" class="help-block col-md-8 col-md-offset-2">
                                <strong>.لطفا مقیاس کارنامه را مشخص کنید</strong>
                            </span>
                        </div>

                        <div class="form-group" id="input_excel" style="margin-right: 5px; margin-left: 5px">
                            <div class="col-md-12">
                                <div style="padding: 5px" class="col-md-8 col-md-offset-2">
                                    <input class="form-control" accept=".xls,.xlsx"  type="file" name="file" />
                                </div>

                                <label style="text-align: left" class="col-md-2 control-label">انتخاب فایل اکسل *</label>
                            </div>

                            <span id="input_excel_error" style="display: none" class="help-block col-md-8 col-md-offset-2">
                                <strong>.لطفا مسیر فابل اکسل را انتخاب کنید</strong>
                            </span>
                        </div>


                        <div class="form-group" style="margin-right: 5px; margin-left: 5px">
                            <div class="col-md-8 col-md-offset-2">
                                <button style="font-size: 18px; padding:0 30px 2px 30px;"
                                        id="submit"
                                        class="btn btn-primary">بارگزاری</button>
                            </div>
                        </div>
                    </form>

                </div><!-- /.box-body -->

            </div><!-- /.box -->

        </div>
    </div>

    <script>
        function validate()
        {
            return (verifyNull());
        }


        function verifyNull(){
            var isValid = true;

            if (!document.forms["myForm"]["file"].value.trim().length) {
                document.getElementById("input_excel").classList.add('has-error');
                document.getElementById("input_excel_error").style.display = " inline-block";
                isValid = false;
            }else {
                document.getElementById("input_excel").classList.remove('has-error');
                document.getElementById("input_excel_error").style.display = 'none';
            }


            if (!document.forms["myForm"]["year"].value.trim().length) {
                document.getElementById("input_year").classList.add('has-error');
                document.getElementById("input_year_error").style.display = " inline-block";
                isValid = false;
            }else {
                document.getElementById("input_year").classList.remove('has-error');
                document.getElementById("input_year_error").style.display = 'none';
            }


            if (!document.forms["myForm"]["month"].value.trim().length) {
                document.getElementById("input_month").classList.add('has-error');
                document.getElementById("input_month_error").style.display = " inline-block";
                isValid = false;
            }else {
                document.getElementById("input_month").classList.remove('has-error');
                document.getElementById("input_month_error").style.display = 'none';
            }

            if (!document.forms["myForm"]["scale"].value.trim().length) {
                document.getElementById("input_scale").classList.add('has-error');
                document.getElementById("input_scale_error").style.display = " inline-block";
                isValid = false;
            }else {
                document.getElementById("input_scale").classList.remove('has-error');
                document.getElementById("input_scale_error").style.display = 'none';
            }

            return isValid;
        }
    </script>
@endsection
