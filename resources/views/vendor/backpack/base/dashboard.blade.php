@extends('backpack::layout')

@section('header')
    <section style="padding-top: 5px" class="content-header">
        <h1 style="text-align: right;">
            <span  style="font-size: 25px" >{{ trans('backpack::base.dashboard') }}</span>
        </h1>
    </section>
@endsection


@section('content')
    <div class="row" style="text-align: right">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">{{ trans('backpack::base.login_status') }}</div>
                </div>

                <div class="box-body">{{ trans('backpack::base.logged_in') }}</div>
            </div>
        </div>
    </div>
@endsection
