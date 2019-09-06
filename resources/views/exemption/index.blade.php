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
            <tr id="tr_nm_curso">
                <td class="formlttd" valign="top"><span class="form">Curso:</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-course')
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formlttd" valign="top"><span class="form">Serie:</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-grade')
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formlttd" valign="top"><span class="form">Componente curricular:</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-discipline')
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
            <th>Ano</th>
            <th>Aluno</th>
            <th>Disciplina</th>
            <th>Tipo de Dispensa</th>
            <th>Data da dispensa</th>
        </tr>
        </thead>
        <tbody>
        @forelse($exemptions as $exemption)
            @php $exemptionUrl = url('intranet/educar_dispensa_disciplina_det.php?ref_cod_matricula=' . $exemption->ref_cod_matricula . '&ref_cod_serie=' . $exemption->ref_cod_serie . '&ref_cod_escola=' . $exemption->ref_cod_escola . '&ref_cod_disciplina=' . $exemption->ref_cod_disciplina) @endphp
            <tr>
                <td>
                    <a href="{{ $exemptionUrl }}">{{ $exemption->registration->ano }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}">{{ $exemption->registration->student->person->nome }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}">{{ $exemption->discipline->nome }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}">{{ $exemption->type }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}">{{ $exemption->data_cadastro->format('d/m/Y') }}</a>
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
        {{ $exemptions->links() }}
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
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Curso.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Serie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/ComponenteCurricular.js") }}"></script>
@endprepend
