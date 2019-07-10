@extends('backpack::layout')

@section('content')
    <style>
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

    <div class="row">
        <div class="col-md-10 col-md-offset-1" style="text-align: right">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div style="font-size: 18px" class="box-title">{{ trans('backpack::base.login') }}</div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('backpack.auth.login') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has($username) ? ' has-error' : '' }}">

                            <div class="col-md-8 col-md-offset-2">
                                <input  type="text" class="form-control" name="{{ $username }}" value="{{ old($username) }}">

                                @if ($errors->has($username))
                                    <span class="help-block">
                                        <strong>{{ $errors->first($username) }}</strong>
                                    </span>
                                @endif

                            </div>

                            <label style="text-align: left" class="col-md-2 control-label">ایمیل</label>

                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <div class="col-md-8 col-md-offset-2">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label style="text-align: left" class="col-md-2 control-label">رمز عبور</label>

                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="checkbox" >
                                    <label>
                                        <label for="checkbox" style="margin-right: 25px">{{ trans('backpack::base.remember_me') }} </label>
                                        <input type="checkbox" name="remember" id="checkbox">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">

                                @if (backpack_users_have_email())
                                <a class="btn btn-link" style="font-size: 18px" href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a>
                                @endif

                                <button style="font-size: 18px; padding:0 30px 2px 30px;" type="submit" class="btn btn-primary">
                                    {{ trans('backpack::base.login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
