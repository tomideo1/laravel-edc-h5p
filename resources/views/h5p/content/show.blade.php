@extends( config('laravel-h5p.layout') )

@section( 'h5p' )
{{header('X-Frame-Options: *')}}
<div class="container-fluid">

    <div class="row">

        <div class="col-md-12">

            <div class="h5p-content-wrap">
                {!! $embed_code  !!}
            </div>

            <br/>
            <p class='text-center'>
                @if(Auth::user())
                    <a href="{{ url()->previous() }}" class="btn btn-default"><i class="fa fa-reply"></i> {{ trans('laravel-h5p.content.cancel') }}</a>

                @endif

            </p>
        </div>

    </div>

</div>
@endsection


{{--  http://h5m.edc.lms.test/vendor/h5p/content/9/images/image-59a037121bad0.png  --}}

{{--  http://h5m.edc.lms.test/vendor/h5p/content/9/images/image-5f0d805e17b78.png  --}}

@push( 'h5p-header-script' )
{{--    core styles       --}}
@foreach($settings['core']['styles'] as $style)
{{ Html::style($style) }}
@endforeach

@foreach($settings['loadedCss'] as $style)
{{ Html::style($style) }}
@endforeach
@endpush

@push( 'h5p-footer-script' )
<script type="text/javascript">
    H5PIntegration = {!! json_encode($settings) !!};
</script>

{{--    core script       --}}
@foreach($settings['core']['scripts'] as $script)
{{ Html::script($script) }}
@endforeach

@foreach($settings['loadedJs'] as $script)
{{ Html::script($script) }}
@endforeach

<script src="/storage/laravel-h5p/js/laravel-h5p-result.js"></script>

@endpush
