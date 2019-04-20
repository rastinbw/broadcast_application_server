
<style>
	@font-face {
		font-family: 'nazanin';
		src: url("{{ asset('fonts/BNazanin.tff') }}") format('truetype'),
		url("{{ asset('fonts/BNazanin.eot') }}") format('eot'),
		url("{{ asset('fonts/BNazanin.woff') }}") format('woff');
	}

	.title{
		font-family: 'nazanin';
		font-size: 18px;
		display: inline-block;
		color:black;
		font-weight: bold;
	}

	.value{
		font-family: 'nazanin';
		font-size: 17px;
		display: inline-block;
		color:#062f70;
	}

	#main{
		text-align: center;
	}

</style>

<div id="main" class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
	<div class="row">
		<div class="col-md-12" dir="rtl">
			{{$entry->grades}}
		</div>
	</div>
</div>
<div class="clearfix"></div>