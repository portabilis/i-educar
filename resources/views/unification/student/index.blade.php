@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="get">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Unificação de alunos</b></td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formmdtd" valign="top"><span class="form">Instituição:</span></td>
                <td class="formmdtd" valign="top">
                    @include('form.select-institution')
                </td>
            </tr>
            <tr id="tr_nm_escola">
                <td class="formlttd" valign="top"><span class="form">Escola:</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-school')
                </td>
            </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Buscar</button>
        </div>

    </form>

    <table class="table-default">
        <thead>
        <tr>
            <th>Aluno principal</th>
            <th>Aluno(s) unificado(s)</th>
            <th>Data da unificação</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($unifications as $unification)
            <tr>
                <td>
                    <a href="{{ route('student-log-unification.show', ['unification' => $unification->id]) }}">{{ $unification->getMainName()  }}</a>
                </td>
                <td>
                    <a href="{{ route('student-log-unification.show', ['unification' => $unification->id]) }}">{{ implode(', ', $unification->getDuplicatesName()) }}</a>
                </td>
                <td>
                    <a href="{{ route('student-log-unification.show', ['unification' => $unification->id]) }}">{{ $unification->created_at->format('d/m/Y')  }}</a>
                </td>
                <td>
                    <a href="{{ route('student-log-unification.show', ['unification' => $unification->id]) }}">@if($unification->active) Ativa @else Inativa @endif</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Não foi encontrado nenhum log de unificação</td>
            </tr>
        @endforelse

        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        {{ $unifications->appends(request()->except('page'))->links() }}
    </div>

    <div style="text-align: center">
        <a href="/intranet/educar_unifica_aluno.php">
            <button class="btn-green" type="button">Novo</button>
        </a>
    </div>

    </form>
@endsection

@prepend('scripts')
    <link type='text/css' rel='stylesheet' href='{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
    <script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
@endprepend
