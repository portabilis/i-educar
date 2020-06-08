@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Unificação - Detalhe</b></td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top"><span class="form">Pessoa principal:</span></td>
                <td class="formmdtd" valign="top"><span class="form">{{ $unification->getMainName() }}</span></td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top"><span class="form">Pessoa(s) unificada(s):</span></td>
                <td class="formmdtd" valign="top"><span class="form">{{  implode(', ', $unification->getDuplicatesName()) }}</span></td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top"><span class="form">Data da unificação:</span></td>
                <td class="formmdtd" valign="top"><span class="form">{{ $unification->created_at->format('d/m/Y H:i:s') }}</span></td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top"><span class="form">Feita por:</span></td>
                <td class="formmdtd" valign="top"><span class="form"></span>{{ $unification->createdBy->real_name }}</td>
            </tr>
            </tbody>
        </table>
    </form>

    <div class="separator"></div>

    <div style="text-align: center">
        <a href="{{ route('person-log-unification.index') }}"><button class="btn" type="button">Voltar</button></a>
    </div>

    </form>
@endsection

@prepend('scripts')
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
@endprepend
