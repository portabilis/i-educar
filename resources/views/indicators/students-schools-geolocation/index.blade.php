@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <div class="painel-geoloation-report">
        <button class="btn-green" id="report" type="button">Emitir Relatório</button>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $j(document).ready(function () {
            $j("#report").on('click', function (){
                window.open('https://datastudio.google.com/u/0/reporting/4758c5e6-49e9-4e41-91c7-966e44da4362/page/n6wPC');
            });
        });
    </script>
@endpush
