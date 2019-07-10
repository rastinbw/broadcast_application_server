@extends('list_layout')


@section('css')
    <style type="text/css">

        table {
            border-collapse: collapse;
        }

        p {
            font-size: 1.1em;
            direction: rtl;
        }


        .table-data {
            text-align: center;
        }

    </style>
@stop

@section('header')
    <section class="content-header" style="padding-top: 15px">
        <h1 dir="rtl" style="text-align: right; font-size: 22px;">
            <span class="text-capitalize">لیست نمرات کلاس <strong>{{$course->title}}</strong> آزمون <strong>{{$test->title}}</strong> تاریخ <strong>{{$test->date}}</strong></span>
        </h1>
    </section>
@endsection

@section('content')
    <div class="box">

        <form action="{{ URL::to('/admin/update_grades_list/' . $course->id . '/' . $test->id) }}"
              class="form-horizontal"
              method="post"
              enctype="multipart/form-data"
              name="myForm">

            <div style="text-align: right" class="box-header with-border ">
                <button style="width: 100px" id="submit" class="btn btn-success ladda-button" data-style="zoom-in">
                    <span class="ladda-label"  style="font-weight: bold">ذخیره</span>
                </button>
            </div>

            <div class="box-body overflow-hidden">

                {{ csrf_field() }}
                <input name="user_id" type="hidden" value="{{$user_id}}">

                <div style="margin-top: 10px;margin-bottom: 10px;">
                    <table dir="rtl" id="crudTable"
                           class="table table-bordered table-striped table-hover display responsive nowrap"
                           cellspacing="0">
                        <tr>
                            <th class="table-data" style="width: 50px;">
                                ردیف
                            </th>

                            <th class="table-data" style="width: 200px;">
                                نام
                            </th>

                            <th class="table-data" style="width: 250px;">
                                نام خوانوادگی
                            </th>

                            <th class="table-data" style="width: 150px;">
                                شماره شناسنامه
                            </th>

                            <th class="table-data" style="width: 30px;">
                                نمره
                            </th>

                        </tr>

                        <!-- we put data here -->
                        <?php $counter = 1; ?>

                        @foreach($students as $s)
                            <tr>
                                <td class="table-data">
                                    {{$counter++}}
                                </td>
                                <td class="table-data">
                                    {{$s->first_name}}
                                </td>
                                <td class="table-data">
                                    {{$s->last_name}}
                                </td>
                                <td class="table-data">
                                    {{$s->national_code}}
                                </td>
                                <td class="table-data">
                                    <div>
                                        <input style="text-align: center"
                                               name="{{ $s->national_code }}"
                                               type="text"
                                               value="{{ isset($grades[$s->national_code]) ?  $grades[$s->national_code] : "-"}}">
                                    </div>
                                </td>
                            </tr>
                    @endforeach
                    <!-- end of data -->

                    </table>

                </div>

            </div>

        </form>

    </div>


@stop