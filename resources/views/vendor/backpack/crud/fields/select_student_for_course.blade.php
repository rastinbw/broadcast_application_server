<div @include('crud::inc.field_wrapper_attributes') >

<?php
    use App\Models\Student;
    use App\Models\Group;
    use App\Models\Field;
?>

<?php
  $user_id = $field['data']['user_id'];
  $student_list = Student::where([
      ['user_id', '=', $user_id],
  ])->get();
  $group_list = Group::where([
      ['user_id', '=', $user_id]
  ])->get();
  $field_list = Field::where([
      ['user_id', '=', $user_id]
  ])->get();

  $gender_list = [
      ['id' => \App\Includes\Constant::$GENDER_MALE, 'title' => "پسر"],
      ['id' => \App\Includes\Constant::$GENDER_FEMALE, 'title' => "دختر"]
  ];
  ?>

<label>{!! $field['label'] !!}</label>
    <div id="field_body">
        <div id="field_control_header">
            <div class="h_line"></div>

            <input id="searchBox" class="form-control input-sm" placeholder="جست و جو در لیست دانش آموزان"  type="search">

            <select id="groups"  name="group_id" dir="rtl" class="form-control select2_field selects">
                <option id="group_all">همه گروه ها</option>
                @foreach($group_list as $group)
                    <option id="group_{{$group->id}}"> {{$group->title}}</option>
                @endforeach
            </select>

            <select id="fields"  name="field_id" dir="rtl" class="form-control select2_field selects">
                <option id="field_all">همه رشته ها</option>
                @foreach($field_list as $field)
                    <option id="field_{{$field->id}}">{{$field->title}}</option>
                @endforeach
            </select>

            <select id="genders" name="gender" dir="rtl" class="form-control select2_field selects">
                <option id="gender_all">همه جنسیت ها</option>
                @foreach($gender_list as $gender)
                    <option id="gender_{{$gender['id']}}">{{$gender['title']}}</option>
                @endforeach
            </select>

            <a id="btn_add" class="btn btn-success ladda-button buttons" data-style="zoom-in">
                <span style="font-weight: bold" class="ladda-label">اضافه</span>
            </a>

            <a id="btn_remove" class="btn btn-danger ladda-button buttons" data-style="zoom-in">
                <span style="font-weight: bold" class="ladda-label">حذف</span>
            </a>
        </div>

        <div id="field_students_list">
            <ul class="lists" id="students_list">
            </ul>
        </div>

        <div id="field_course_students_list">
            <ul class="lists" id="course_students_list">
            </ul>
        </div>
    </div>
</div>


@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))
    {{-- FIELD EXTRA CSS  --}}
    {{-- push things in the after_styles section --}}

    @push('crud_fields_styles')
        <style>
            #field_body{
                width: 100%;
                height: 400px;
                background-color: #0e6f5c;
            }

            #field_control_header{
                width: 100%;
                height: 12%;
                background-color: #0d6aad;
                position: relative;
            }

            #field_students_list{
                width: 50%;
                height: 88%;
                background-color: #a32d00;
                float: right;
                position: relative;
            }

            #field_course_students_list{
                width: 50%;
                height: 88%;
                background-color: #ffdb1f;
                float: right;
                position: relative;
            }

            #searchBox{
                display: inline;
                position: relative;
                right: 8px;
                top: 5px;
                width: 200px;
                height: 70%;
            }

            .selects{
                position: relative;
                display: inline;
                right: 8px;
                top: 6px;
                width: 200px;
                font-size: 16px;
                padding: 5px;
            }

            .buttons{
                position: relative;
                right: 8px;
                top: 6px;
                width: 100px;
                font-size: 16px;
            }

            .h_line{
                width: 100%;
                height: 1px;
                background-color: #2f2f2f;
                position: absolute;
                bottom: 0;
                left: 0;
            }

            .lists {
                position: absolute;
                top: 20px;
                bottom: 20px;
                left: 20px;
                right: 20px;
                margin: 0;
                padding: 0;
                overflow: scroll;
                border: 2px solid #ccc;
                font-size: 16px;
                font-family: Arial, sans-serif;

                /*// Again, this is where the magic happens*/
                -webkit-overflow-scrolling: touch;
            }

            .lists li {
                padding: 10px 20px;
                border-bottom: 1px solid #ccc;
            }

            .lists li:last-child {
                border-bottom: none;
            }

            .lists li:nth-child(even) {
                background: #f8f8f8;
            }

            .lblStudents {
                display: block;
                padding-left: 15px;
                text-indent: -15px;
            }

            input[type='checkbox'] {
                width: 13px;
                height: 13px;
                padding: 0;
                margin:0;
                vertical-align: bottom;
                position: relative;
                top: -3px;
                *overflow: hidden;
            }
        </style>
    @endpush


    {{-- FIELD EXTRA JS --}}
    {{-- push things in the after_scripts section --}}

    @push('crud_fields_scripts')
        <script>
            let group_id = "";
            let field_id = "";
            let gender_id = "";
            let search_phrase = "";

            let course_students = [];

            let selected_students = [];
            let selected_course_students = [];

            function check_all_student_list(checked) {
                if (!checked) {
                    selected_students = [];
                    $("#checkbox_all_students").prop('checked', false);
                }

                for (var i = 0; i < $('#students_list li').length - 1; i++){
                    var sid = $('#sli_'+i+'').find('input[type="checkbox"]').attr('id').replace('student_','');
                    $("#student_"+sid).prop('checked', checked);

                    if (checked)
                        selected_students.push(sid);
                }
            }

            function check_all_course_student_list(checked) {
                if (!checked) {
                    selected_course_students = [];
                    $("#checkbox_all_course_students").prop('checked', false);
                }

                for (var i = 0; i < $('#course_students_list li').length - 1; i++){
                    var sid = $('#course_sli_'+i+'').find('input[type="checkbox"]').attr('id').replace('course_student_','');
                    $("#course_student_"+sid).prop('checked', checked);

                    if (checked)
                        selected_course_students.push(sid);
                }
            }

            function set_event_for_students_checkboxes() {
                $('#checkbox_all_students').change(function () {
                    check_all_student_list($(this).is(':checked'));
                });

                for (var i = 0; i < $('#students_list li').length - 1; i++){
                    var sid = $('#sli_'+i+'').find('input[type="checkbox"]').attr('id').replace('student_','');
                    $("#student_"+sid).change(function() {
                        var current_sid = $(this).attr('id').replace('student_','');
                        if ($(this).is(':checked')) {
                            selected_students.push(current_sid);
                            // console.log(selected_students);
                        }else {
                            selected_students.splice( selected_students.indexOf(current_sid), 1 );
                            // console.log(selected_students);
                        }
                    });

                }
            }

            function set_event_for_course_students_checkboxes() {
                $('#checkbox_all_course_students').change(function () {
                    check_all_course_student_list($(this).is(':checked'));
                });

                for (var i = 0; i < $('#course_students_list li').length - 1; i++){
                    var course_sid = $('#course_sli_'+i+'').find('input[type="checkbox"]').attr('id').replace('course_student_','');
                    $("#course_student_"+course_sid).change(function() {
                        var current_sid = $(this).attr('id').replace('course_student_','');
                        if ($(this).is(':checked')) {
                            selected_course_students.push(current_sid);
                            // console.log(selected_course_students);
                        }else {
                            selected_course_students.splice( selected_course_students.indexOf(current_sid), 1 );
                            // console.log(selected_course_students);
                        }
                    });

                }
            }

            function fill_course_student_list(list){
                $('#course_students_list').empty();

                if (list.length > 0)
                    $('#course_students_list').append(
                        '<li id="li_all_course_students">' +
                        '<label class="lblStudents">' +
                        '<input autocomplete="off" id="checkbox_all_course_students" type="checkbox" >' +
                        'همه دانش آموزان' +
                        '</label>' +
                        '</li>');

                for (var i = 0; i < list.length; i++){
                    var id = list[i]["id"];
                    var first_name = list[i]["first_name"];
                    var last_name =  list[i]["last_name"];
                    var national_code =  list[i]["national_code"];

                    var checked = '';
                    if (selected_course_students.includes(id+""))
                        checked = 'checked';

                    $("#course_students_list").append(
                        '<li id=course_sli_'+ i +'>' +
                        '<label for="course_student_' + id + '" class="lblStudents">' +
                        '<input autocomplete="off" id=course_student_' + id + ' type="checkbox" '+ checked + ' >'
                        + first_name + ' ' + last_name + ': ' + national_code +
                        '</label>' +
                        '</li>');
                }

                set_event_for_course_students_checkboxes();
            }


            function fill_student_list(list){
                $('#students_list').empty();

                $('#students_list').append(
                    '<li id="li_all_students">' +
                    '<label class="lblStudents">' +
                    '<input autocomplete="off" id="checkbox_all_students" type="checkbox" >' +
                    'همه دانش آموزان' +
                    '</label>' +
                    '</li>');

                for (var i = 0; i < list.length; i++){
                    var id = list[i]["id"];
                    var first_name = list[i]["first_name"];
                    var last_name =  list[i]["last_name"];
                    var national_code =  list[i]["national_code"];

                    var checked = '';
                    if (selected_students.includes(id+""))
                        checked = 'checked';

                    $("#students_list").append(
                        '<li id=sli_'+ i +'>' +
                        '<label for="student_' + id + '" class="lblStudents">' +
                        '<input autocomplete="off" id=student_' + id + ' type="checkbox" '+ checked + ' >'
                        + first_name + ' ' + last_name + ': ' + national_code +
                        '</label>' +
                        '</li>');
                }

                set_event_for_students_checkboxes();
            }

            // fill students list according to filters and search box
            function request_student_list(){
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "POST",
                    url: '{{ url("/get_filtered_students") }}',
                    data: {
                        group_id: group_id,
                        field_id: field_id,
                        gender_id: gender_id,
                        search_phrase: search_phrase,
                        user_id: '{{$user_id}}'
                    },
                    success: function(data)
                    {
                        console.log('success');
                        console.log(data);
                        fill_student_list(data['students']);
                    },
                    error: function(jqxhr, status, exception) {
                        console.log(jqxhr);
                        console.log(status);
                        console.log(exception);
                    }
                });
            }


            function request_course_student_list(){
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "POST",
                    url: '{{ url("/get_some_students") }}',
                    data: {
                        id_list: course_students,
                        user_id: '{{$user_id}}'
                    },
                    success: function(data)
                    {
                        console.log('success');
                        console.log(data);
                        fill_course_student_list(data['students']);
                    },
                    error: function(jqxhr, status, exception) {
                        console.log(jqxhr);
                        console.log(status);
                        console.log(exception);
                    }
                });
            }


            //setting events
            document.addEventListener("touchstart", function(){}, true);
            fill_student_list({!! json_encode($student_list->toArray()) !!});

            set_event_for_students_checkboxes();

            $("#groups").change(function() {
                var id = $(this).children(":selected").attr("id");
                if (id !== "group_all")
                    group_id = id.replace("group_", "");
                else
                    group_id = "";

                request_student_list();
            });

            $("#fields").change(function() {
                var id = $(this).children(":selected").attr("id");
                if (id !== "field_all")
                    field_id = id.replace("field_", "");
                else
                    field_id = "";

                request_student_list();
            });

            $("#genders").change(function() {
                var id = $(this).children(":selected").attr("id");
                if (id !== "gender_all")
                    gender_id = id.replace("gender_", "");
                else
                    gender_id = "";

                request_student_list();
            });

            $("#searchBox").keyup(function() {
                search_phrase = $(this).val();
                request_student_list();
            });

            $('#btn_add').click(function () {
                for (var i = 0; i < selected_students.length; i++) {
                    if (!course_students.includes(selected_students[i])){
                            course_students.push(selected_students[i]);
                    }
                }
                check_all_student_list(false);
                request_course_student_list();
            });

            $('#btn_remove').click(function () {
                for (var i = 0; i < selected_course_students.length; i++) {
                    course_students.splice( course_students.indexOf(selected_course_students[i]), 1 );
                }
                check_all_course_student_list(false);
                request_course_student_list();
            });

        </script>
    @endpush
@endif

{{-- Note: most of the times you'll want to use @if ($crud->checkIfFieldIsFirstOfItsType($field, $fields)) to only load CSS/JS once, even though there are multiple instances of it. --}}
