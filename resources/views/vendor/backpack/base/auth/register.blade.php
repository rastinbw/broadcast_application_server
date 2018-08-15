@extends('backpack::layout')

@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1" style="text-align: right">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div style="font-size: 18px" class="box-title">{{ trans('backpack::base.register') }}</div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('backpack.auth.register') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                            <div class="col-md-8 col-md-offset-2">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label style="text-align: left" class="col-md-2 control-label">{{ trans('backpack::base.name') }}</label>

                        </div>


                        <div class="form-group{{ $errors->has(backpack_authentication_column()) ? ' has-error' : '' }}">
                            <div class="col-md-8 col-md-offset-2">
                                <input type="{{ backpack_authentication_column()=='email'?'email':'text'}}" class="form-control" name="{{ backpack_authentication_column() }}" value="{{ old(backpack_authentication_column()) }}">

                                @if ($errors->has(backpack_authentication_column()))
                                    <span class="help-block">
                                        <strong>{{ $errors->first(backpack_authentication_column()) }}</strong>
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

                            <label  style="text-align: left" class="col-md-2 control-label">{{ trans('backpack::base.password') }}</label>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">

                            <div class="col-md-8 col-md-offset-2">
                                <input type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label  style="text-align: left" class="col-md-2 control-label">{{ trans('backpack::base.confirm_password') }}</label>

                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <button style="font-size: 18px; padding:0 30px 2px 30px;" type="submit" class="btn btn-primary">
                                    {{ trans('backpack::base.register') }} <i style="margin-left: 5px" class="fa fa-btn fa-user"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
