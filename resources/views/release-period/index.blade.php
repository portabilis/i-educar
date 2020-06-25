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
            <tr>
                <td class="formmdtd" valign="top">
                    <span class="form">Escola</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school-multiple')
                    <a href="javascript:void(0)" id="link-select-all">Selecionar todas</a>
                </td>
            </tr>
            <tr>
                <td class="formlttd" valign="top">
                    <span class="form">Tipo de etapa</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.stage-type')
                </td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top">
                    <span class="form">Etapa</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                    <span class="form">
                        <select class="geral" name="stage" id="stage" style="width: 308px;">
                            <option value="">Selecione uma etapa</option>
                        </select>
                    </span>
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
            $('#link-select-all').click(function () {
                $('#escola option').prop('selected', true); // Selects all options
                $('#escola').trigger('chosen:updated');
            });

            let stageTypes = JSON.parse('{!! $stageTypes !!}')

            $('#stage_type').change(function () {
                stageSelect = $('#stage');
                stageSelect.find('option').not(':first').remove();

                stageType = stageTypes[$(this).val()];

                if (typeof stageType === 'undefined') {
                    return;
                }

                for (i = 1; i <= parseInt(stageType.num_etapas); i++) {
                    stageSelect.append($('<option>', {
                        value: i,
                        text: i + 'º ' + stageType.nm_tipo
                    }));
                }
            })
        })(jQuery);
    </script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
@endprepend
