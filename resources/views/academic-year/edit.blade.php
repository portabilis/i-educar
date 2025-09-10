@extends('layout.default')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ Asset::get('css/ieducar.css') }}"/>
    <link rel="stylesheet" href="{{ Asset::get('vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css') }}"/>

    <style>
        #tr_titulo-alocacoes-vinculos td {
            background-color: #ccdce6 !important;
            font-family: "Open Sans" !important;
            font-size: 16px !important;
            font-weight: bold;
            font-style: normal;
            font-stretch: normal;
            color: #47728f;
            padding: 8px 15px !important
        }

        #tr_informativo1-alocacoes-vinculos td,
        #tr_informativo2-alocacoes-vinculos td {
            font-weight: bold;
            line-height: 1.7;
            padding: 8px !important;
        }

        #tr_copiar_alocacoes_e_vinculos_professores_ td,
        #tr_copiar_alocacoes_demais_servidores_ td,
        #tr_informativo1-alocacoes-vinculos td,
        #tr_informativo2-alocacoes-vinculos td {
            background-color: #f5f9fd;
        }
    </style>
@endpush

@php
    $stageTypes = App\Models\LegacyStageType::where('ativo', 1)->get(['cod_modulo', 'nm_tipo', 'num_etapas']);
    $stageTypesData = $stageTypes->pluck('num_etapas', 'cod_modulo')->toArray();
    $isAdmin = Auth::user() && Auth::user()->isAdmin();
@endphp

@section('content')
    <form id="formcadastro" method="post">
        @csrf
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Ano Letivo em Lote</b></td>
            </tr>
            <tr id="tr_nm_ano">
                <td class="formmdtd" valign="top">
                    <label for="year" class="form">Ano <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-year', ['obrigatorio' => true])
                </td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formlttd" valign="top">
                    <label for="institution" class="form">Instituição <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-institution', ['obrigatorio' => true])
                </td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top">
                    <label for="schools" class="form">Escola <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school-multiple', ['obrigatorio' => true])
                </td>
            </tr>
            <tr>
                <td class="formlttd" valign="top">
                    <label for="cursos" class="form">Tipo de Etapa <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formlttd" valign="top">
                    <select class="geral obrigatorio" name="ref_cod_modulo" id="ref_cod_eref_cod_moduloscola" style="width: 308px;">
                        <option value="">Selecione as opções</option>
                        @foreach($stageTypes as $stageType)
                            <option value="{{$stageType->cod_modulo}}" @if(old('ref_cod_modulo', Request::get('ref_cod_modulo')) == $stageType->cod_modulo) selected @endif>{{ Str::upper($stageType->nm_tipo) }} - {{ $stageType->num_etapas }} etapa(s)</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr id="tr_modulos_ano_letivo" class="formlttd">
                <td style="vertical-align: top; text-align: center" colspan="2">
                    <table cellspacing="0" id="modulos_ano_letivo" class="tabela-adicao" cellpadding="2" style="margin:10px 0px 10px 0px;">
                        <tbody>
                        <tr id="tr_modulos_ano_letivo_tit" style="font-weight:bold; text-align: center" class="formdktd">
                            <td colspan="3">Etapas do ano letivo</td>
                        </tr>
                        <tr style="font-weight:bold; text-align: center;" id="tr_modulos_ano_letivo_cab">
                            <td class="formmdtd" id="td_data_inicial" style="text-align: center"><span class="form">Data inicial</span><span class="campo_obrigatorio">*</span></td>
                            <td class="formmdtd" id="td_data_final" style="text-align: center"><span class="form">Data final</span><span class="campo_obrigatorio">*</span></td>
                            <td class="formmdtd" id="td_dias_letivos" style="text-align: center"><span class="form">Dias Letivos</span></td>
                        </tr>
                        <tr id="tr_modulos_ano_letivo[1]" name="tr_modulos_ano_letivo[1]" class="formmdtd dd tr_modulos_ano_letivo">
                            <td class="formmdtd dd data_inicio" id="td_data_inicio[1]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[1][data_inicio]" id="data_inicio[1]" value="" size="9" maxlength="10">
                            </td>
                            <td class="formmdtd dd data_fim" id="td_data_fim[1]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[1][data_fim]" id="data_fim[1]" value="" size="9" maxlength="10">
                            </td>
                            <td class="formmdtd dd dias_letivos" id="td_dias_letivos[1]" style="vertical-align: top">
                                <input class="geral" type="text" name="periodos[1][dias_letivos]" id="dias_letivos[1]" value="" size="6" maxlength="3">
                            </td>
                        </tr>
                        <tr id="tr_modulos_ano_letivo[2]" name="tr_modulos_ano_letivo[2]" class="formlttd dd tr_modulos_ano_letivo">
                            <td class="formlttd dd data_inicio" id="td_data_inicio[2]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[2][data_inicio]" id="data_inicio[2]" value="" size="9" maxlength="10">
                            </td>
                            <td class="formlttd dd data_fim" id="td_data_fim[2]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[2][data_fim]" id="data_fim[2]" value="" size="9" maxlength="10">
                            </td>
                            <td class="formlttd dd dias_letivos" id="td_dias_letivos[2]" style="vertical-align: top">
                                <input class="geral" type="text" name="periodos[2][dias_letivos]" id="dias_letivos[2]" value="" size="6" maxlength="3">
                            </td>
                        </tr>
                        <tr id="tr_modulos_ano_letivo[3]" name="tr_modulos_ano_letivo[3]" class="formmdtd dd tr_modulos_ano_letivo">
                            <td class="formmdtd dd data_inicio" id="td_data_inicio[3]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[3][data_inicio]" id="data_inicio[3]" value="" size="9" maxlength="10">
                            </td>
                            <td class="formmdtd dd data_fim" id="td_data_fim[3]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[3][data_fim]" id="data_fim[3]" value="" size="9" maxlength="10">
                            </td>
                            <td class="formmdtd dd dias_letivos" id="td_dias_letivos[3]" style="vertical-align: top">
                                <input class="geral" type="text" name="periodos[3][dias_letivos]" id="dias_letivos[3]" value="" size="6" maxlength="3">
                            </td>
                        </tr>
                        <tr id="tr_modulos_ano_letivo[4]" name="tr_modulos_ano_letivo[4]" class="formlttd dd tr_modulos_ano_letivo">
                            <td class="formlttd dd data_inicio" id="td_data_inicio[4]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[4][data_inicio]" id="data_inicio[4]" value="" size="9" maxlength="10">
                                </td>
                            <td class="formlttd dd data_fim" id="td_data_fim[4]" style="vertical-align: top">
                                <input onkeypress="formataData(this, event);" type="text" name="periodos[4][data_fim]" id="data_fim[4]" value="" size="9" maxlength="10">
                                </td>
                            <td class="formlttd dd dias_letivos" id="td_dias_letivos[4]" style="vertical-align: top">
                                <input class="geral" type="text" name="periodos[4][dias_letivos]" id="dias_letivos[4]" value="" size="6" maxlength="3">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr id="tr_titulo-alocacoes-vinculos">
                <td class="formmdtd" valign="top">
                    <span class="form">
                        <label for="titulo-alocacoes-vinculos">Alocações e vínculos</label>
                    </span>
                </td>
                <td class="formmdtd" valign="top">
                    <span class="form"></span>
                </td>
            </tr>
            <tr id="tr_informativo1-alocacoes-vinculos">
                <td class="formlttd" valign="top">
                    <span class="form">
                        <label for="informativo1-alocacoes-vinculos">
                            Ao definir um novo ano letivo, o i-Educar copia automaticamente as turmas do ano anterior. <br>
                            Gostaria de copiar também as alocações e vínculos?
                        </label>
                    </span>
                </td>
                <td class="formlttd" valign="top">
                    <span class="form"></span>
                </td>
            </tr>
            @if($isAdmin)
                <tr id="tr_copiar_turmas_">
                    <td class="formmdtd" valign="top">
                        <span class="form">
                            <label for="copiar_turmas_">
                                <input type="checkbox" checked id="copiar_turmas" name="copiar_turmas">
                                <label for="copiar_turmas">Copiar turmas do ano anterior</label>
                            </label>
                        </span>
                    </td>
                    <td class="formmdtd" valign="top">
                        <span class="form"></span>
                    </td>
                </tr>
            @endif
            <tr id="tr_copiar_alocacoes_e_vinculos_professores_">
                <td class="formlttd" valign="top">
                    <span class="form">
                        <label for="copiar_alocacoes_e_vinculos_professores_">
                            <input type="checkbox" id="copiar_alocacoes_e_vinculos_professores" name="copiar_alocacoes_e_vinculos_professores" style="opacity: 1;">
                            <label for="copiar_alocacoes_e_vinculos_professores">Copiar alocações e vínculos dos professores</label>
                        </label>
                    </span>
                </td>
                <td class="formlttd" valign="top">
                    <span class="form"></span>
                </td>
            </tr>
            <tr id="tr_copiar_alocacoes_demais_servidores_">
                <td class="formmdtd" valign="top">
                    <span class="form">
                        <label for="copiar_alocacoes_demais_servidores_">
                            <input type="checkbox" id="copiar_alocacoes_demais_servidores" name="copiar_alocacoes_demais_servidores" style="opacity: 1;">
                            <label for="copiar_alocacoes_demais_servidores">Copiar alocações dos demais servidores</label>
                        </label>
                    </span>
                </td>
                <td class="formmdtd" valign="top">
                    <span class="form"></span>
                </td>
            </tr>
            </tbody>
        </table>
        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ Asset::get('/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js') }}"></script>
    <script src="{{ Asset::get('/vendor/legacy/DynamicInput/Assets/Javascripts/DynamicInput.js') }}"></script>
    <script src="{{ Asset::get('/vendor/legacy/Portabilis/Assets/Javascripts/Validator.js') }}"></script>
    <script>
        (function($) {
            $(document).ready(function() {
                const stageTypesData = @json($stageTypesData);

                function updateEtapasRows() {
                    const selectedValue = $('#ref_cod_eref_cod_moduloscola').val();
                    const etapasRows = $('.tr_modulos_ano_letivo');

                    if (!selectedValue) {
                        etapasRows.show();
                        etapasRows.find('input[name*="data_inicio"], input[name*="data_fim"]').addClass('obrigatorio');
                        return;
                    }

                    const numEtapas = stageTypesData[selectedValue] || 4;

                    etapasRows.each(function(index) {
                        if (index < numEtapas) {
                            $(this).show();
                            $(this).find('input[name*="data_inicio"], input[name*="data_fim"]').addClass('obrigatorio');
                        } else {
                            $(this).hide();
                            $(this).find('input[name*="data_inicio"], input[name*="data_fim"]').removeClass('obrigatorio');
                        }
                    });
                }

                $('#ref_cod_eref_cod_moduloscola').change(function() {
                    updateEtapasRows();
                });

                updateEtapasRows();

                const copiarTurmasCheckbox = document.getElementById("copiar_turmas");
                const copiarProfessoresCheckbox = document.getElementById("copiar_alocacoes_e_vinculos_professores");
                const copiarServidoresCheckbox = document.getElementById("copiar_alocacoes_demais_servidores");

                function toggleAlocacoesFields() {
                    const isAdmin = {{ $isAdmin ? 'true' : 'false' }};

                    if (!isAdmin) {
                        // Se não é admin, "Copiar turmas" é sempre true, então os outros checkboxes devem estar habilitados
                        copiarProfessoresCheckbox.disabled = false;
                        copiarServidoresCheckbox.disabled = false;
                        return;
                    }

                    const isTurmasChecked = copiarTurmasCheckbox ? copiarTurmasCheckbox.checked : false;

                    // Apenas o checkbox de professores depende de "Copiar turmas"
                    copiarProfessoresCheckbox.disabled = !isTurmasChecked;

                    // O checkbox de servidores é independente
                    copiarServidoresCheckbox.disabled = false;

                    if (!isTurmasChecked) {
                        copiarProfessoresCheckbox.checked = false;
                        // Não desmarca o checkbox de servidores
                    }
                }

                if (copiarTurmasCheckbox) {
                    copiarTurmasCheckbox.addEventListener("change", toggleAlocacoesFields);
                }
                toggleAlocacoesFields();
            });

            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('formcadastro');

                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        if (validationUtils.validatesFields(true)) {
                            processForm();
                        }
                    });
                }

                function processForm() {
                    const submitButton = form.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;

                    submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processando...';
                    submitButton.disabled = true;

                    const formData = new FormData(form);

                    fetch('{{ route("academic-year.process") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = data.redirect;
                        } else {
                            showErrorModal(data);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showErrorModal({
                            message: 'Erro de comunicação com o servidor',
                            errors: [{'error': 'Erro de rede. Tente novamente.'}]
                        });
                    })
                    .finally(() => {
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    });
                }

                function showErrorModal(response) {
                    let errorHtml = '<div style="max-height: 400px; overflow-y: auto;">';

                    if (response.errors && response.errors.length > 0) {
                        response.errors.forEach(error => {
                            errorHtml += `
                                <div style="margin: 5px 0; padding: 8px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 3px; border-left: 3px solid #721c24;">
                                    <span style="color: #721c24; font-size: 13px;">${error.error}</span>
                                </div>
                            `;
                        });
                    }

                    errorHtml += '</div>';

                    if (typeof $j !== 'undefined' && $j.fn.dialog) {
                        let container = $j('#error-dialog');
                        if (container.length < 1) {
                            $j('body').append('<div id="error-dialog"></div>');
                            container = $j('#error-dialog');
                        }

                        container.html(errorHtml).dialog({
                            title: 'Erro ao processar ano letivo em lote',
                            width: 600,
                            modal: true,
                            buttons: [{
                                text: 'OK',
                                click: function() {
                                    $j(this).dialog('close');
                                }
                            }]
                        });
                    } else {
                        alert('Erro: ' + response.message);
                    }
                }
            });
        })(jQuery);
    </script>
@endpush

