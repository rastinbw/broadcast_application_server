@extends('backpack::layout')

@section('header')
	<section style="padding-top: 5px" class="content-header">
		<h1 style="text-align: right;">
			<span  style="font-size: 25px" >{{ $crud->entity_name }} جدید </span>
		</h1>
	</section>
@endsection

@section('content')
<div class="row" style="margin-right: 60px;margin-left: 60px">
	<div  class="col-md-12 col-md-offset-2" style="margin: auto; text-align: right">
		<!-- Default box -->
		@if ($crud->hasAccess('list'))
			<a href="{{ url($crud->route) }}">{{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span>  &nbsp<i class="fa fa-angle-double-right"></i></a><br><br>
		@endif

		@include('crud::inc.grouped_errors')

		  <form method="post"
		  		action="{{ url($crud->route) }}"
				@if ($crud->hasUploadFields('create'))
				enctype="multipart/form-data"
				@endif
		  		>
		  {!! csrf_field() !!}
		  <div class="box">

		    <div class="box-body row display-flex-wrap" style="display: flex; flex-wrap: wrap;">
		      <!-- load the view from the application if it exists, otherwise load the one in the package -->
		      @if(view()->exists('vendor.backpack.crud.form_content'))
		      	@include('vendor.backpack.crud.form_content', [ 'fields' => $crud->getFields('create'), 'action' => 'create' ])
		      @else
		      	@include('crud::form_content', [ 'fields' => $crud->getFields('create'), 'action' => 'create' ])
		      @endif
		    </div><!-- /.box-body -->
		    <div style="text-align: right" class="box-footer">

                @include('crud::inc.form_save_buttons')

		    </div><!-- /.box-footer-->

		  </div><!-- /.box -->
		  </form>
	</div>
</div>

@endsection
