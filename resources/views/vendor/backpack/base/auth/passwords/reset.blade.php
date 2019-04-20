@extends('backpack::layout')

@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1" style="text-align: right">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div style="font-size: 18px" class="box-title">{{ trans('backpack::base.reset_password') }}</div>
                </div>

                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('backpack.auth.password.reset') }}">
                        {!! csrf_field() !!}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="col-md-8 col-md-offset-2">
                                <input type="email" class="form-control" name="email" value="{{ $email or old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label style="text-align: left" class="col-md-2 control-label">{{ trans('backpack::base.email_address') }}</label>
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

                            <label style="text-align: left" class="col-md-2 control-label">{{ trans('backpack::base.password') }}</label>
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

                            <label style="text-align: left" class="col-md-2 control-label">{{ trans('backpack::base.confirm_password') }}</label>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('backpack::base.reset_password') }} <i style="margin-left: 5px" class="fa fa-btn fa-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
