@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <iframe id="panel" src="https://datastudio.google.com/embed/reporting/4cdd82b8-b2be-4f3a-a64c-077c1fd8f1da/page/hQlLC" frameborder="0" style="border:0" allowfullscreen>
    </iframe>
@endsection

@push('scripts')
    <script type="text/javascript">
        $j(document).ready(function () {
            const panel = $j("#panel");
            panel.width('100%');
            panel.height($j(window).height());
        });
    </script>
@endpush
