@extends('layout.default')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ Asset::get('css/ieducar.css') }}"/>
    <link rel="stylesheet" href="{{ Asset::get('vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css') }}"/>
    <style>
        .tabela-adicao select[name="escolas[]"],
        .tabela-adicao select[name="series[]"] {
            width: 100% !important;
        }

        .tabela-adicao .chosen-container,
        .tabela-adicao .chosen-container .chosen-choices {
            width: 100% !important;
        }

        .tabela-adicao th:nth-child(1),
        .tabela-adicao td:nth-child(1) {
            width: 45%;
        }

        .tabela-adicao th:nth-child(2),
        .tabela-adicao td:nth-child(2) {
            width: 45%;
        }

        .tabela-adicao th:nth-child(3),
        .tabela-adicao td:nth-child(3) {
            width: 10%;
        }
    </style>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" role="presentation">
            <tbody>
            <tr>
                <td class="formdktd" colspan="3" height="24"><b>Atualização de séries da escola em lote</b></td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="3">
                    <p style="margin: 10px 0;">
                        Esta funcionalidade permite adicionar séries às escolas de forma manual, selecionando as escolas e séries desejadas.
                    </p>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="background-color: #d9edf7; border: 1px solid #bce8f1; color: #31708f; padding: 10px; margin: 10px 0; border-radius: 4px;">
                        <p style="margin: 0 0 10px 0; font-weight: bold;">⚠️ Informações Importantes:</p>
                        <ul style="margin: 0; padding-left: 20px;">
                            <li><strong>Componentes curriculares:</strong> Os componentes curriculares da série no ano selecionado serão automaticamente vinculados às séries das escolas</li>
                            <li><strong>Ano letivo da escola:</strong> O ano letivo deve estar aberto e iniciado (em andamento)</li>
                            <li><strong>Ano no curso:</strong> O ano deve estar cadastrado no curso da escola</li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr id="tr_nm_ano">
                <td class="formmdtd" valign="top">
                    <label for="year" class="form">Ano <span class="campo_obrigatorio">*</span></label> <br>
                    <sub>Somente números</sub>
                </td>
                <td class="formmdtd" valign="top" style="width: 45%">
                    @include('form.select-year')
                </td>
            </tr>
            <tr id="tr_bloquear_enturmacao">
                <td class="formmdtd" valign="top">
                    <span class="form">Bloquear enturmacão após atingir limite de vagas</span>
                </td>
                <td class="formmdtd" valign="top">
                        <span class="form">
                            <input type="checkbox" name="bloquear_enturmacao_sem_vagas" id="bloquear_enturmacao_sem_vagas" value="1" {{ old('bloquear_enturmacao_sem_vagas') ? 'checked' : '' }}>
                        </span>
                </td>
            </tr>
            <tr id="tr_bloquear_cadastro">
                <td class="formmdtd" valign="top">
                    <span class="form">Bloquear cadastro de novas turmas antes de atingir limite de vagas (no mesmo turno)</span>
                </td>
                <td class="formmdtd" valign="top">
                        <span class="form">
                            <input type="checkbox" name="bloquear_cadastro_turma_para_serie_com_vagas" id="bloquear_cadastro_turma" value="1" {{ old('bloquear_cadastro_turma_para_serie_com_vagas') ? 'checked' : '' }}>
                        </span>
                </td>
            </tr>
            <tr id="tr_series_table">
                <td colspan="2" class="formmdtd" style="text-align: center; vertical-align: top;">
                    <table id="series_table" class="tabela-adicao" style="margin: 10px 0;">
                        <thead>
                        <tr class="formdktd" style="font-weight: bold; text-align: center;">
                            <th id="th-series" colspan="3" style="text-align: left">Escola/Série</th>
                        </tr>
                        <tr class="formmdtd" style="font-weight: bold; text-align: center;">
                            <th id="th-escola" style="text-align: left">Escola</th>
                            <th id="th-serie" style="text-align: left">Série</th>
                            <th id="th-acoes" style="text-align: center; width: 100px;">Ações</th>
                        </tr>
                        </thead>
                        <tbody id="series_tbody">
                        <tr class="formmdtd dd">
                            <td>
                                <span class="form">
                                    <select name="escola_serie[1][escolas]" id="escola" multiple="multiple" style="width: 100%;">
                                        @php
                                            $permissoes = new clsPermissoes;
                                            $instituicaoId = $permissoes->getInstituicao(Auth::id());
                                            $escolas = App_Model_IedFinder::getEscolasByUser($instituicaoId);
                                        @endphp
                                        @foreach($escolas as $id => $name)
                                            <option value="{{$id}}">{{$name}}</option>
                                        @endforeach
                                    </select>
                                </span>
                            </td>
                            <td>
                                <span class="form">
                                    <select name="escola_serie[1][series]" id="series" multiple="multiple" style="width: 100%;">
                                        @php
                                            $series = App_Model_IedFinder::getSeriesWithCourse(null);
                                        @endphp
                                        @foreach($series as $id => $label)
                                            <option value="{{$id}}">{{$label}}</option>
                                        @endforeach
                                    </select>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0)" style="outline: none;" id="btn-add-row">
                                    <img src="/intranet/imagens/nvp_bot_novo.png" border="0" alt="incluir">
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <div style="text-align: center">
            <button class="btn-green" type="submit">Processar Atualização</button>
        </div>
    </form>
@endsection


@push('scripts')
    <script src="{{ Asset::get('/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js') }}"></script>
    <script src="{{ Asset::get('/vendor/legacy/DynamicInput/Assets/Javascripts/DynamicInput.js') }}"></script>
    <script type='text/javascript' src='{{ Asset::get('/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script>
        (function ($) {
            multipleSearchSerieOptions = {}
            multipleSearchSerieOptions.placeholder = 'Selecione as séries';
            multipleSearchHelper.setup('series', '', 'multiple', 'multiple', multipleSearchSerieOptions);
            $j('#series').trigger('chosen:updated');

            multipleSearchEscolaOptions = {}
            multipleSearchEscolaOptions.placeholder = 'Selecione as escolas';
            multipleSearchHelper.setup('escola', '', 'multiple', 'multiple', multipleSearchEscolaOptions);
            $j('#escola').trigger('chosen:updated');

                if (typeof messageUtils !== 'undefined' && messageUtils.clear) {
                    messageUtils.clear();
                }

            const config = {
                chosen: {
                    placeholder_text_multiple: 'Selecione as opções',
                    no_results_text: 'Sem resultados para ',
                    search_contains: true,
                    width: '100%',
                    allow_single_deselect: true
                },
                selectors: {
                    form: '#formcadastro',
                    tbody: '#series_tbody',
                    year: '#ano',
                    schools: '#escola',
                    grades: '#series'
                }
            };

            const TemplateManager = {
                counter: 1,
                escolasData: [],
                seriesData: [],

                init() {
                    this.loadStaticData();
                    this.bindEvents();
                    this.updateButtons();
                    this.initChosen();
                },

                loadStaticData() {
                    $(config.selectors.schools).find('option').each(function() {
                        const $option = $(this);
                        TemplateManager.escolasData.push({
                            value: $option.val(),
                            text: $option.text()
                        });
                    });

                    $(config.selectors.grades).find('option').each(function() {
                        const $option = $(this);
                        TemplateManager.seriesData.push({
                            value: $option.val(),
                            text: $option.text()
                        });
                    });
                },

                createRowHTML() {
                        this.counter++;
                    const rowId = `row_${this.counter}`;
                    const selectId = `escola_${this.counter}`;
                    const serieId = `serie_${this.counter}`;

                        return `
                            <tr class="formmdtd dd" id="${rowId}">
                                <td>
                                    <span class="form">
                                    <select name="escola_serie[${this.counter}][escolas][]" id="${selectId}" multiple="multiple" style="width: 100%;">
                                            <option value="">Selecione as escolas</option>
                                        </select>
                                    </span>
                                </td>
                                <td>
                                <select name="escola_serie[${this.counter}][series][]" id="${serieId}" multiple="multiple" class="geral" style="width: 100%;">
                                        <option value="">Selecione as séries</option>
                                    </select>
                                </td>
                                <td style="text-align: center;">
                                    <a href="javascript:void(0)" class="btn-remove" style="outline: none;">
                                        <img src="/intranet/imagens/banco_imagens/excluirrr.png" border="0" alt="Excluir">
                                    </a>
                                </td>
                            </tr>
                        `;
                    },

                addRow() {
                    const $tbody = $(config.selectors.tbody);
                    const $newRow = $(this.createRowHTML());

                        $tbody.append($newRow);
                    this.copyOptions($newRow.find(`select[name="escola_serie[${this.counter}][escolas][]"]`), 'escolas', 'Selecione as escolas');
                    this.copyOptions($newRow.find(`select[name="escola_serie[${this.counter}][series][]"]`), 'series', 'Selecione as séries');
                        this.updateButtons();
                    },

                copyOptions($target, dataType, placeholder) {
                    if ($target.length) {
                        $target.find('option:not(:first)').remove();

                        const data = dataType === 'escolas' ? this.escolasData : this.seriesData;

                        data.forEach(item => {
                            if (item.value !== '' || $target.find('option[value=""]').length === 0) {
                                $target.append(`<option value="${item.value}">${item.text}</option>`);
                            }
                        });

                        $target.chosen({
                            ...config.chosen,
                            placeholder_text_multiple: placeholder
                        });

                        $target.trigger('chosen:updated');
                    }
                },

                removeRow($row) {
                    if ($('.formmdtd.dd').length > 1) {
                        $row.find('select').each(function() {
                            const $select = $(this);
                            if ($select.hasClass('chosen-select') || $select.next('.chosen-container').length > 0) {
                                $select.chosen('destroy');
                            }
                        });
                        $row.remove();
                        this.updateButtons();
                    }
                },

                updateButtons() {
                    const $rows = $('.formmdtd.dd');
                    $rows.each((index, row) => {
                        const $lastCell = $(row).find('td:last-child');
                        const isLast = index === $rows.length - 1;
                        const buttonClass = isLast ? 'btn-add-row' : 'btn-remove';
                        const buttonImg = isLast ? 'nvp_bot_novo.png' : 'excluirrr.png';
                        const buttonAlt = isLast ? 'incluir' : 'Excluir';
                        const imgPath = isLast ? '/intranet/imagens/nvp_bot_novo.png' : '/intranet/imagens/banco_imagens/excluirrr.png';

                        $lastCell.html(`<a href="javascript:void(0)" class="${buttonClass}" style="outline: none;"><img src="${imgPath}" border="0" alt="${buttonAlt}"></a>`);
                        });
                    },

                bindEvents() {
                    $(document).on('click', '.btn-add-row', (e) => {
                            e.preventDefault();
                        this.addRow();
                        });

                    $(document).on('click', '.btn-remove', (e) => {
                            e.preventDefault();
                        this.removeRow($(e.target).closest('tr'));
                        });
                    },

                initChosen() {
                    setTimeout(() => {
                        $(config.selectors.schools).chosen({
                            ...config.chosen,
                            placeholder_text_multiple: 'Selecione as escolas'
                        });

                        $(config.selectors.grades).chosen({
                            ...config.chosen,
                            placeholder_text_multiple: 'Selecione as séries'
                        });
                    }, 1000);
                }
            };

            const FormManager = {
                init() {
                    this.bindSubmit();
                },

                validateForm() {
                    // Validar apenas o ano
                    const yearValue = $(config.selectors.year).val();
                    if (!yearValue) {
                            messageUtils.error('Selecione um ano letivo.');
                            return false;
                        }

                    const incompleteLines = this.validateIncompleteLines();
                    if (incompleteLines.length > 0) {
                        const message = this.buildIncompleteLinesMessage(incompleteLines);
                        messageUtils.error(message);
                            return false;
                        }

                        return true;
                },

                validateIncompleteLines() {
                    const incompleteLines = [];
                    let lineNumber = 1;

                    $('.formmdtd.dd').each(function() {
                        const $row = $(this);
                        const $schoolsSelect = $row.find('select[name*="[escolas]"]');
                        const $seriesSelect = $row.find('select[name*="[series]"]');

                        const hasSchools = $schoolsSelect.length > 0 && $schoolsSelect.val() && $schoolsSelect.val().length > 0;
                        const hasSeries = $seriesSelect.length > 0 && $seriesSelect.val() && $seriesSelect.val().length > 0;

                        if ((hasSchools && !hasSeries) || (!hasSchools && hasSeries)) {
                            incompleteLines.push(lineNumber);
                        }

                        lineNumber++;
                    });

                    return incompleteLines;
                },

                buildIncompleteLinesMessage(incompleteLines) {
                    if (incompleteLines.length === 1) {
                        return `A linha ${incompleteLines[0]} está incompleta. Preencha tanto as escolas quanto as séries, ou deixe ambos vazios.`;
                    } else {
                        const linesText = incompleteLines.join(', ');
                        return `As linhas ${linesText} estão incompletas. Preencha tanto as escolas quanto as séries, ou deixe ambos vazios.`;
                    }
                },

                bindSubmit() {
                    $(config.selectors.form).submit((e) => {
                        e.preventDefault();
                        if (this.validateForm()) {
                            this.showPreviewModal();
                        }
                    });
                },

                showPreviewModal() {
                    const $form = $(config.selectors.form);
                    const $submitButton = $form.find('button[type="submit"]');

                    this.setButtonLoading($submitButton, true);

                    $j.ajax({
                        url: '{{ route("school-grade.batch-update.preview") }}',
                        type: 'GET',
                        data: $form.serialize(),
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: (response) => {
                            this.setButtonLoading($submitButton, false);

                            if (response.status === 'success') {
                                this.showConfirmationModal(response.preview, $form);
                            } else {
                                this.showErrorModal(response);
                            }
                        },
                        error: (xhr, status, error) => {
                            this.setButtonLoading($submitButton, false);
                            this.showErrorModal(this.buildErrorResponse(xhr, status, error));
                        }
                    });
                },

                showConfirmationModal(previewData, $form) {
                    const modalContent = this.buildConfirmationModalContent(previewData);

                    this.makeDialog({
                        content: modalContent,
                        title: 'Confirmação de Atualização em Lote',
                        maxWidth: 800,
                        width: 800,
                        modal: true,
                        close: () => $j('#dialog-container').dialog('destroy'),
                        buttons: [
                            {
                                text: 'Cancelar',
                                click: () => $j('#dialog-container').dialog('destroy')
                            },
                            {
                                text: 'Confirmar e Executar',
                                click: () => {
                                    $j('#dialog-container').dialog('destroy');
                                    this.executeUpdate($form);
                                }
                            }
                        ]
                    });
                },

                buildConfirmationModalContent(previewData) {
                    let tableRows = '';
                    previewData.table_data.forEach(schoolData => {
                        const schoolName = schoolData.school.name;
                        const rowspan = schoolData.courses.length;

                        schoolData.courses.forEach((course, index) => {
                            const isFirstRow = index === 0;
                            tableRows += `
                                <tr>
                                    ${isFirstRow ? `<td rowspan="${rowspan}" style="border: 1px solid #ddd; padding: 3px; vertical-align: middle; font-weight: bold;">${schoolName}</td>` : ''}
                                    <td style="border: 1px solid #ddd; padding: 3px;">${course.course_name}</td>
                                    <td style="border: 1px solid #ddd; padding: 3px;">${course.series}</td>
                                </tr>
                            `;
                        });
                    });

                    const summaryText = `
                        <div style="margin-bottom: 15px; padding: 10px; background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 4px; font-size: 0.85em;">
                            <p style="margin: 0; font-weight: bold; color: #0066cc;">
                                📊 Resumo da Atualização:
                            </p>
                            <ul style="margin: 5px 0 0 0; padding-left: 20px; color: #0066cc;">
                                <li><strong>Linhas de entrada:</strong> ${previewData.total_lines || 0}</li>
                                <li><strong>Combinações escola/série:</strong> ${previewData.total_combinations || 0}</li>
                                <li><strong>Ano letivo:</strong> ${previewData.year}</li>
                            </ul>
                        </div>
                    `;

                    return `
                        <div style="height: 400px; overflow-y: auto; padding: 10px;">
                            <p style="margin: 0 0 20px 0; font-size: 0.9em;">Confira antes de continuar</p>

                            ${summaryText}

                            <table style="width: 100%; border-collapse: collapse; margin: 0; font-size: 0.8em;">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Escola</th>
                                        <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Cursos</th>
                                        <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Séries</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRows}
                                </tbody>
                            </table>
                        </div>
                    `;
                },

                executeUpdate($form) {
                    const $submitButton = $form.find('button[type="submit"]');
                    this.setButtonLoading($submitButton, true);

                    $j.ajax({
                        url: '{{ route("school-grade.batch-update.process") }}',
                        type: 'POST',
                        data: $form.serialize(),
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: (response) => {
                            if (response.status === 'success') {
                                window.location.href = response.redirect;
                            } else if (response.status === 'error') {
                                this.showErrorModal(response);
                                this.setButtonLoading($submitButton, false);
                            }
                        },
                        error: (xhr, status, error) => {
                            this.showErrorModal(this.buildErrorResponse(xhr, status, error));
                            this.setButtonLoading($submitButton, false);
                        }
                    });
                },

                setButtonLoading($button, loading) {
                    const states = {
                        loading: {
                            disabled: true,
                            html: '<i class="fa fa-spinner fa-spin"></i> Processando...',
                            bgColor: '#6c757d'
                        },
                        normal: {
                            disabled: false,
                            html: 'Processar Atualização',
                            bgColor: ''
                        }
                    };

                    const state = loading ? states.loading : states.normal;
                    $button.prop('disabled', state.disabled)
                        .html(state.html)
                        .css('background-color', state.bgColor);
                },

                buildErrorResponse(xhr, status, error) {
                    const defaultError = {
                                message: 'Erro de comunicação com o servidor.',
                                errors: [{'error': 'Erro de rede: ' + error}]
                            };

                            if (status === 'timeout') {
                        return {
                            message: 'Tempo limite excedido.',
                            errors: [{'error': 'A requisição demorou muito para responder. Tente novamente.'}]
                        };
                            }

                            try {
                                if (xhr.responseText) {
                            const parsedResponse = JSON.parse(xhr.responseText);
                            return {
                                message: parsedResponse.message || defaultError.message,
                                errors: parsedResponse.errors || defaultError.errors
                            };
                        }
                    } catch (e) {
                    }

                    return defaultError;
                },

                showErrorModal(response) {
                    this.makeDialog({
                        content: this.buildModalContent(response),
                        title: 'Atualização falhou devido a erros encontrados:',
                        maxWidth: 800,
                        width: 800,
                        modal: true,
                        close: () => $j('#dialog-container').dialog('destroy'),
                        buttons: [{
                            text: 'OK',
                            click: () => $j('#dialog-container').dialog('destroy')
                        }]
                    });
                },

                buildModalContent(response) {
                    const buildErrorSection = (items, bgColor, borderColor, textColor) => {
                        if (!items || items.length === 0) return '';

                        return `
                            <div style="margin-bottom: 15px;">
                                <div style="max-height: 250px; overflow-y: auto;">
                                    ${items.map(item => `
                                        <div style="margin: 5px 0; padding: 8px; background-color: ${bgColor}; border: 1px solid ${borderColor}; border-radius: 3px; border-left: 3px solid ${textColor};">
                                            <span style="color: ${textColor}; font-size: 13px;">${item.error || item.message}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    };

                    return `
                        <div style="max-height: 600px; overflow-y: auto;">
                            ${buildErrorSection(response.errors, '#f8d7da', '#f5c6cb', '#721c24')}
                            ${buildErrorSection(response.details, '#d1ecf1', '#bee5eb', '#0c5460')}
                        </div>
                    `;
                },

                makeDialog(params) {
                    if (typeof $j === 'undefined' || !$j.fn.dialog) {
                        console.error('jQuery UI não está disponível');
                        alert('Erro: jQuery UI não está disponível. Erro: ' + params.content);
                        return;
                    }

                    const defaultParams = {
                        closeOnEscape: false,
                        draggable: false,
                        modal: true,
                        size: '500'
                    };

                    const finalParams = {...defaultParams, ...params};
                    const size = finalParams.size;
                    let container = $j('#dialog-container');

                    if (container.length < 1) {
                        $j('body').append(`<div id="dialog-container" style="width: ${size}px;"></div>`);
                        container = $j('#dialog-container');
                    }

                    if (container.hasClass('ui-dialog-content')) {
                        container.dialog('destroy');
                    }

                    container.empty().html(finalParams.content);
                    delete finalParams.content;
                    container.dialog(finalParams);
                }
            };

            TemplateManager.init();
            FormManager.init();
        })(jQuery);
    </script>
@endpush
