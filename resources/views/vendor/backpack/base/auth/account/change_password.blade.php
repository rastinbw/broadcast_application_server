@extends('backpack::layout')

@section('after_styles')
<style media="screen">
    .backpack-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@section('header')
{{--<section class="content-header">--}}

    {{--<h1>--}}
        {{--{{ trans('backpack::base.my_account') }}--}}
    {{--</h1>--}}

    {{--<ol class="breadcrumb">--}}

        {{--<li>--}}
            {{--<a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a>--}}
        {{--</li>--}}

        {{--<li>--}}
            {{--<a href="{{ route('backpack.account.info') }}">{{ trans('backpack::base.my_account') }}</a>--}}
        {{--</li>--}}

        {{--<li class="active">--}}
            {{--{{ trans('backpack::base.change_password') }}--}}
        {{--</li>--}}

    {{--</ol>--}}

{{--</section>--}}
@endsection

@section('content')
<div class="row">
    {{--<div class="col-md-3">--}}
        {{--@include('backpack::auth.account.sidemenu')--}}
    {{--</div>--}}
    <div class="col-md-10 col-md-offset-1" style="text-align: right">

        <form class="form" action="{{ route('backpack.account.password') }}" method="post">

            {!! csrf_field() !!}

            <div class="box">

                <div class="box-body backpack-profile-form">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->count())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        @php
                            $label = trans('backpack::base.old_password');
                            $field = 'old_password';
                        @endphp
                        <label class="required">{{ $label }}</label>
                        <input style="text-align: right"  autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="" placeholder="{{ $label }}">
                    </div>

                    <div class="form-group">
                        @php
                            $label = trans('backpack::base.new_password');
                            $field = 'new_password';
                        @endphp
                        <label class="required">{{ $label }}</label>
                        <input style="text-align: right" autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="" placeholder="{{ $label }}">
                    </div>

                    <div class="form-group">
                        @php
                            $label = trans('backpack::base.confirm_password');
                            $field = 'confirm_password';
                        @endphp
                        <label class="required">{{ $label }}</label>
                        <input style="text-align: right" autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="" placeholder="{{ $label }}">
                    </div>

                </div>

                <div class="box-footer">
                    <a href="{{ backpack_url() }}" style="font-size: 15px; padding:5px 30px 5px 30px;" class="btn btn-danger"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
                    <button type="submit" style="font-size: 15px; padding:5px 30px 5px 30px;" class="btn btn-success"><span class="ladda-label"><i class="fa fa-save"></i> {{ trans('backpack::base.change_password') }}</span></button>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection
