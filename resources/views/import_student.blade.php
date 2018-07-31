@extends('base')

@section('content')
    <div class="container">
        @if($message = Session::get('success'))
            <div class="alert alert-info alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>Success!</strong> {{ $message }}
            </div>
        @endif
        {!! Session::forget('success') !!}
        <br />
        <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('import_student_excel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="text" name="email" value="email">
            <input type="text" name="password" value="password">
            <input type="file" name="import_file" />
            <button class="btn btn-primary">Import File</button>
        </form>
    </div>
@endsection