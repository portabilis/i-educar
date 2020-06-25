@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Período de lançamento de notas e faltas por etapa</b>
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

            <tr id="tr_nm_instituicao">
                <td class="formlttd" valign="top">
                    <span class="form">Escola</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-school-multiple')
                    <a href="javascript:void(0)" id="link-select-all">Selecionar todas</a>
                </td>
            </tr>

            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Data de entrada antiga</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input onkeypress="formataData(this, event);" type="text" name="data_entrada_antiga"
                              value="{{ old('data_entrada_antiga', Request::get('data_entrada_antiga')) }}"
                              id="data_entrada_antiga" size="9" maxlength="10" placeholder="dd/mm/aaaa">
                    </span>
                </td>
            </tr>
            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Data de enturmação antiga</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input onkeypress="formataData(this, event);" type="text" name="data_enturmacao_antiga"
                              value="{{ old('data_enturmacao_antiga', Request::get('data_enturmacao_antiga')) }}"
                              id="data_enturmacao_antiga" size="9" maxlength="10" placeholder="dd/mm/aaaa">
                    </span>
                </td>
            </tr>

            </tbody>
        </table>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
        </div>

    </form>
@endsection

@prepend('scripts')
    <script type='text/javascript'>
        (function ($) {
            $j('#link-select-all').click(function () {
                $j('#escola option').prop('selected', true); // Selects all options
                $j('#escola').trigger('chosen:updated');
            });
        })(jQuery);
    </script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
@endprepend
