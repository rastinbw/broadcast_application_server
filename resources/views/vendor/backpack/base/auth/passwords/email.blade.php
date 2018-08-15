@extends('backpack::layout')

<!-- Main Content -->
@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1" style="text-align: right">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div style="font-size: 18px" class="box-title">{{ trans('backpack::base.reset_password') }}</div>
                </div>
                <div class="box-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('backpack.auth.password.email') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                            <div class="col-md-8 col-md-offset-2">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label style="text-align: left" class="col-md-2 control-label">{{ trans('backpack::base.email_address') }}</label>

                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <button style="font-size: 18px; padding:2px 30px 4px 30px;" type="submit" class="btn btn-primary">
                                     {{ trans('backpack::base.send_reset_link') }} <i style="margin-left: 5px" class="fa fa-btn fa-envelope"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
