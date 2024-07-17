@extends('layout.default')

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24">
                    <b>Dispensa em lote</b>
                </td>
            </tr>
            <tr id="tr_nm_ano">
                <td class="formmdtd" valign="top">
                    <span class="form">Ano</span>
                    <span class="campo_obrigatorio">*</span>
                    <br>
                    <sub style="vertical-align:top;">somente números</sub>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-year')
                </td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formlttd" valign="top">
                    <span class="form">Instituição</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-institution')
                </td>
            </tr>
            <tr id="tr_nm_escola">
                <td class="formmdtd" valign="top">
                    <span class="form">Escola</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school')
                </td>
            </tr>
            <tr id="tr_nm_curso">
                <td class="formlttd" valign="top">
                    <span class="form">Curso</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-course')
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formmdtd" valign="top">
                    <span class="form">Série</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-grade')
                </td>
            </tr>
            <tr id="tr_nm_turma">
                <td class="formlttd" valign="top">
                    <span class="form">Turma</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-school-class')
                </td>
            </tr>

            <tr id="tr_nm_turma">
                <td class="formlttd" valign="top">
                    <span class="form">Matrícula</span>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-registration')
                </td>
            </tr>
            </tbody>
        </table>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Exportar</button>
        </div>
    </form>
@endsection

@prepend('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
    <link type='text/css' rel='stylesheet'
          href='{{ Asset::get("/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
@endprepend

@prepend('scripts')
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Curso.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Serie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Turma.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Matricula.js") }}"></script>
    <script type='text/javascript'
            src='{{ Asset::get('/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
@endprepend
