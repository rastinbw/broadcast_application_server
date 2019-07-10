<style>

    .elem{
        display: inline-block;
    }

    #container{
        text-align : right;
    }

    @font-face {
        font-family: 'nazanin';
        src: url("{{ asset('fonts/BNazanin.tff') }}") format('truetype'),
        url("{{ asset('fonts/BNazanin.eot') }}") format('eot'),
        url("{{ asset('fonts/BNazanin.woff') }}") format('woff');
    }

    *{
        font-family: 'nazanin';
    }


</style>

{{-- Show the errors, if any --}}
{{--$crud->groupedErrorsEnabled() && $errors->any()--}}
@if (false)
    <div class="callout callout-danger">
        <h4>لطفا ارور های زیر را برطرف کنید</h4>
        <ul dir="rtl">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif