<div id="saveActions" class="form-group">

    @if(!isset($slider) && !isset($about))
        <a style="font-size: 15px;font-weight: 600" href="{{ url($crud->route) }}" class="btn btn-danger"> لغو عملیات &nbsp;<span class="fa fa-ban"></span></a>
    @endif

    <input type="hidden" name="save_action" value="{{ $saveAction['active']['value'] }}">

    <div class="btn-group">


        <button style="font-size: 15px;font-weight: 600" type="submit" class="btn btn-success">
            <span data-value="{{ $saveAction['active']['value'] }}">
                @if(isset($slider) || isset($about))
                    ذخیره
                @elseif(isset($message_log))
                    ارسال و بازگشت
                @else
                    ذخیره و بازگشت
                @endif
            </span>
            &nbsp;
            <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
        </button>

        {{--<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aira-expanded="false">--}}
        {{--<span class="caret"></span>--}}
        {{--<span class="sr-only">&#x25BC;</span>--}}
        {{--</button>--}}

        {{--<ul class="dropdown-menu">--}}
        {{--@foreach( $saveAction['options'] as $value => $label)--}}
        {{--<li><a href="javascript:void(0);" data-value="{{ $value }}">{{ $label }}</a></li>--}}
        {{--@endforeach--}}
        {{--</ul>--}}

    </div>

</div>