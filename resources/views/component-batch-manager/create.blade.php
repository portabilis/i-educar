@extends('layout.default')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="{{ route('component-batch-manager.preview') }}" method="post">
        @csrf

        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Nova Operação em Lote</b></td>
            </tr>

            <tr>
                <td class="formmdtd" valign="top">
                    <label for="ano" class="form">Ano <span class="campo_obrigatorio">*</span></label>
                    <br><sub>Somente números</sub>
                </td>
                <td class="formmdtd" valign="top">
                    <input type="text" class="geral obrigatorio" name="ano" id="ano" maxlength="4" value="{{ old('ano', date('Y')) }}" size="4">
                </td>
            </tr>

            <tr>
                <td class="formlttd" valign="top">
                    <label for="ref_cod_instituicao" class="form">Instituição <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-institution', ['obrigatorio' => true])
                </td>
            </tr>

            <tr>
                <td class="formmdtd" valign="top">
                    <label for="escola" class="form">Escolas <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school-multiple')
                    <a href="javascript:void(0)" id="link-select-all-schools" style="margin-left: 10px; color: #47728f; text-decoration: none;">
                        Selecionar todas
                    </a>
                </td>
            </tr>

            <tr>
                <td class="formlttd" valign="top">
                    <label for="cursos" class="form">Cursos <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formlttd" valign="top">
                    <select name="curso[]" id="cursos" multiple style="width: 308px;">
                    </select>
                    <a href="javascript:void(0)" id="link-select-all-courses" style="margin-left: 10px; color: #47728f; text-decoration: none;">
                        Selecionar todos
                    </a>
                </td>
            </tr>

            <tr>
                <td class="formmdtd" valign="top">
                    <label for="ref_cod_serie" class="form">Séries <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formmdtd" valign="top">
                    <select name="ref_cod_serie[]" id="ref_cod_serie" multiple="multiple" style="width: 308px;">
                    </select>
                    <a href="javascript:void(0)" id="link-select-all-grades" style="margin-left: 10px; color: #47728f; text-decoration: none;">
                        Selecionar todas
                    </a>
                </td>
            </tr>

            <tr>
                <td class="formlttd" valign="top">
                    <label for="discipline_ids" class="form">Componentes Curriculares <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formlttd" valign="top">
                    <select name="discipline_ids[]" id="discipline_ids" multiple="multiple" style="width: 308px;">
                    </select>
                    <a href="javascript:void(0)" id="link-select-all-disciplines" style="margin-left: 10px; color: #47728f; text-decoration: none;">
                        Selecionar todos
                    </a>
                </td>
            </tr>

            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Operações a executar</b></td>
            </tr>

            <tr>
                <td class="formmdtd" valign="top">
                    <span class="form">Remover lançamentos</span>
                </td>
                <td class="formmdtd" valign="top">
                    <input type="checkbox" name="remove_records" id="remove_records" value="1" {{ old('remove_records', '1') ? 'checked' : '' }} class="operation-checkbox">
                </td>
            </tr>

            <tr>
                <td class="formlttd" valign="top">
                    <span class="form">Remover dispensas</span>
                </td>
                <td class="formlttd" valign="top">
                    <input type="checkbox" name="remove_exemptions" id="remove_exemptions" value="1" {{ old('remove_exemptions', '1') ? 'checked' : '' }} class="operation-checkbox">
                </td>
            </tr>

            <tr>
                <td class="formmdtd" valign="top">
                    <span class="form">Remover componentes da turma</span>
                </td>
                <td class="formmdtd" valign="top">
                    <input type="checkbox" name="unlink_class_components" id="unlink_class_components" value="1" {{ old('unlink_class_components', '1') ? 'checked' : '' }} class="operation-checkbox">
                </td>
            </tr>

            <tr>
                <td class="formlttd" valign="top">
                    <span class="form">Remover disciplinas do vínculo professor/turma</span>
                </td>
                <td class="formlttd" valign="top">
                    <input type="checkbox" name="unlink_teacher_disciplines" id="unlink_teacher_disciplines" value="1" {{ old('unlink_teacher_disciplines', '1') ? 'checked' : '' }} class="operation-checkbox">
                </td>
            </tr>

            <tr>
                <td class="formmdtd" valign="top">
                    <span class="form">Remover componentes da série da escola</span>
                </td>
                <td class="formmdtd" valign="top">
                    <input type="checkbox" name="unlink_school_grade_disciplines" id="unlink_school_grade_disciplines" value="1" {{ old('unlink_school_grade_disciplines', '1') ? 'checked' : '' }} class="operation-checkbox">
                </td>
            </tr>

            <tr>
                <td class="formlttd" valign="top">
                    <span class="form">Remover componentes da série</span>
                </td>
                <td class="formlttd" valign="top">
                    <input type="checkbox" name="unlink_grade_components" id="unlink_grade_components" value="1" {{ old('unlink_grade_components', '1') ? 'checked' : '' }} class="operation-checkbox">
                </td>
            </tr>

            </tbody>
        </table>

        <div style="text-align: center; margin-top: 10px; margin-bottom: 20px;">
            <a href="{{ route('component-batch-manager.index') }}" class="btn" style="margin-right: 10px; text-decoration: none;">Voltar</a>
            <button class="btn-green" type="submit">Continuar</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ Asset::get('/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js') }}"></script>
    <script src="{{ Asset::get('/vendor/legacy/DynamicInput/Assets/Javascripts/DynamicInput.js') }}"></script>
    <script>
        (function($) {
        $(document).ready(function() {

            // ================================================================
            // Constantes e seletores
            // ================================================================

            var SELECTORS = {
                ano:         $j('#ano'),
                escola:      $j('#escola'),
                cursos:      $j('#cursos'),
                series:      $j('#ref_cod_serie'),
                disciplinas: $j('#discipline_ids'),
                btnSubmit:   $j('.btn-green[type="submit"]')
            };

            var ROUTES = {
                cursos:      '{{ route("component-batch-manager.api.courses") }}',
                series:      '{{ route("component-batch-manager.api.grades") }}',
                disciplinas: '{{ route("component-batch-manager.api.disciplines") }}'
            };

            var oldValues = {
                cursos:      @json(old('curso', [])),
                series:      @json(old('ref_cod_serie', [])),
                disciplinas: @json(old('discipline_ids', []))
            };

            // ================================================================
            // Estado interno
            // ================================================================

            var pendingRequests = 0;
            var xhrCourses = null;
            var xhrGrades = null;
            var xhrDisciplines = null;
            var lastYear = SELECTORS.ano.val();
            var initialLoad = oldValues.cursos.length > 0 || oldValues.series.length > 0 || oldValues.disciplinas.length > 0;

            // ================================================================
            // Funções auxiliares
            // ================================================================

            function startLoading() {
                pendingRequests++;
                SELECTORS.btnSubmit.prop('disabled', true).css('opacity', '0.6');
            }

            function stopLoading() {
                pendingRequests = Math.max(0, pendingRequests - 1);
                if (pendingRequests === 0) {
                    SELECTORS.btnSubmit.prop('disabled', false).css('opacity', '');
                }
            }

            function abortXhr(xhr) {
                if (xhr) xhr.abort();
            }

            function isAborted(xhr) {
                return xhr.statusText === 'abort';
            }

            function disableSelect($select) {
                $select.empty().trigger('chosen:updated');
                $select.prop('disabled', true).trigger('chosen:updated');
            }

            function enableSelect($select) {
                $select.prop('disabled', false).trigger('chosen:updated');
            }

            function populateSelect($select, response) {
                var html = '';
                $j.each(response, function(id, name) {
                    html += '<option value="' + id + '">' + name + '</option>';
                });
                $select.html(html);
            }

            function restoreOld($select, values) {
                if (!values || values.length === 0) return;
                $select.val(values.map(String));
                $select.trigger('chosen:updated');
            }

            function selectAll($select) {
                $select.find('option').prop('selected', true);
                $select.trigger('chosen:updated');
            }

            function clearAllDependents() {
                disableSelect(SELECTORS.cursos);
                disableSelect(SELECTORS.series);
                disableSelect(SELECTORS.disciplinas);
            }

            // ================================================================
            // Cascata dinâmica: Escola → Curso → Série → Componente
            // ================================================================

            function loadCourses() {
                abortXhr(xhrCourses);
                abortXhr(xhrGrades);
                abortXhr(xhrDisciplines);

                var schoolIds = SELECTORS.escola.val();
                var year = SELECTORS.ano.val();

                if (!schoolIds || schoolIds.length === 0) {
                    clearAllDependents();
                    return;
                }

                disableSelect(SELECTORS.cursos);
                disableSelect(SELECTORS.series);
                disableSelect(SELECTORS.disciplinas);
                startLoading();

                xhrCourses = $j.ajax({
                    url: ROUTES.cursos,
                    data: { school_ids: schoolIds, year: year },
                    dataType: 'json',
                    success: function(response) {
                        populateSelect(SELECTORS.cursos, response);
                        if (initialLoad) restoreOld(SELECTORS.cursos, oldValues.cursos);
                        enableSelect(SELECTORS.cursos);
                        loadGrades();
                    },
                    error: function(xhr) {
                        if (!isAborted(xhr)) enableSelect(SELECTORS.cursos);
                    },
                    complete: stopLoading
                });
            }

            function loadGrades() {
                abortXhr(xhrGrades);
                abortXhr(xhrDisciplines);

                var courseIds = SELECTORS.cursos.val();

                if (!courseIds || courseIds.length === 0) {
                    disableSelect(SELECTORS.series);
                    disableSelect(SELECTORS.disciplinas);
                    return;
                }

                var schoolIds = SELECTORS.escola.val();
                var year = SELECTORS.ano.val();

                disableSelect(SELECTORS.series);
                disableSelect(SELECTORS.disciplinas);
                startLoading();

                xhrGrades = $j.ajax({
                    url: ROUTES.series,
                    data: { school_ids: schoolIds, course_ids: courseIds, year: year },
                    dataType: 'json',
                    success: function(response) {
                        populateSelect(SELECTORS.series, response);
                        if (initialLoad) restoreOld(SELECTORS.series, oldValues.series);
                        enableSelect(SELECTORS.series);
                        loadDisciplines();
                    },
                    error: function(xhr) {
                        if (!isAborted(xhr)) enableSelect(SELECTORS.series);
                    },
                    complete: stopLoading
                });
            }

            function loadDisciplines() {
                abortXhr(xhrDisciplines);

                var gradeIds = SELECTORS.series.val();

                if (!gradeIds || gradeIds.length === 0) {
                    disableSelect(SELECTORS.disciplinas);
                    return;
                }

                var schoolIds = SELECTORS.escola.val();
                var year = SELECTORS.ano.val();

                disableSelect(SELECTORS.disciplinas);
                startLoading();

                xhrDisciplines = $j.ajax({
                    url: ROUTES.disciplinas,
                    data: { school_ids: schoolIds, grade_ids: gradeIds, year: year },
                    dataType: 'json',
                    success: function(response) {
                        populateSelect(SELECTORS.disciplinas, response);
                        if (initialLoad) {
                            restoreOld(SELECTORS.disciplinas, oldValues.disciplinas);
                            initialLoad = false;
                            // Restauração completa: reabilitar escola
                            SELECTORS.escola.prop('disabled', false).trigger('chosen:updated');
                        }
                        enableSelect(SELECTORS.disciplinas);
                    },
                    error: function(xhr) {
                        if (!isAborted(xhr)) enableSelect(SELECTORS.disciplinas);
                    },
                    complete: stopLoading
                });
            }

            // ================================================================
            // Inicialização dos selects Chosen
            // ================================================================

            multipleSearchHelper.setup('cursos', '', 'multiple', 'multiple', { placeholder: 'Selecione os cursos' });
            multipleSearchHelper.setup('ref_cod_serie', '', 'multiple', 'multiple', { placeholder: 'Selecione as séries' });
            multipleSearchHelper.setup('discipline_ids', '', 'multiple', 'multiple', { placeholder: 'Selecione os componentes' });

            // Estado inicial: desabilitar selects dependentes até serem populados pela cascata
            SELECTORS.cursos.prop('disabled', true).trigger('chosen:updated');
            SELECTORS.series.prop('disabled', true).trigger('chosen:updated');
            SELECTORS.disciplinas.prop('disabled', true).trigger('chosen:updated');

            // Se está restaurando do preview, desabilitar escola e botão até a cascata completar
            if (initialLoad) {
                SELECTORS.escola.prop('disabled', true).trigger('chosen:updated');
                SELECTORS.btnSubmit.prop('disabled', true).css('opacity', '0.6');
            }

            // ================================================================
            // Eventos
            // ================================================================

            SELECTORS.escola.on('change', loadCourses);
            SELECTORS.cursos.on('change', loadGrades);
            SELECTORS.series.on('change', loadDisciplines);
            SELECTORS.ano.on('change', loadCourses);
            SELECTORS.ano.on('blur', function() {
                if (SELECTORS.ano.val() !== lastYear) {
                    lastYear = SELECTORS.ano.val();
                    loadCourses();
                }
            });

            $j('#link-select-all-schools').on('click', function() { selectAll(SELECTORS.escola); loadCourses(); });
            $j('#link-select-all-courses').on('click', function() { selectAll(SELECTORS.cursos); loadGrades(); });
            $j('#link-select-all-grades').on('click', function() { selectAll(SELECTORS.series); loadDisciplines(); });
            $j('#link-select-all-disciplines').on('click', function() { selectAll(SELECTORS.disciplinas); });

            // Hierarquia de operações: desmarcar pai desmarca filhos, marcar filho marca pais
            var deps = {
                'remove_records': [],
                'remove_exemptions': [],
                'unlink_class_components': ['remove_records', 'unlink_teacher_disciplines'],
                'unlink_teacher_disciplines': ['remove_records'],
                'unlink_school_grade_disciplines': ['unlink_class_components', 'unlink_teacher_disciplines', 'remove_records', 'remove_exemptions'],
                'unlink_grade_components': ['unlink_school_grade_disciplines', 'unlink_class_components', 'unlink_teacher_disciplines', 'remove_records', 'remove_exemptions']
            };

            var children = {
                'remove_records': ['unlink_class_components', 'unlink_teacher_disciplines', 'unlink_school_grade_disciplines', 'unlink_grade_components'],
                'remove_exemptions': ['unlink_school_grade_disciplines', 'unlink_grade_components'],
                'unlink_class_components': ['unlink_school_grade_disciplines', 'unlink_grade_components'],
                'unlink_school_grade_disciplines': ['unlink_grade_components'],
                'unlink_teacher_disciplines': [],
                'unlink_grade_components': []
            };

            $j('.operation-checkbox').on('change', function() {
                var id = $j(this).attr('id');
                if ($j(this).is(':checked')) {
                    $j.each(deps[id] || [], function(_, dep) {
                        $j('#' + dep).prop('checked', true);
                    });
                } else {
                    $j.each(children[id] || [], function(_, child) {
                        $j('#' + child).prop('checked', false);
                    });
                }
            });

            $j('#formcadastro').on('submit', function(e) {
                if ($j('.operation-checkbox:checked').length === 0) {
                    e.preventDefault();
                    messageUtils.error('Selecione ao menos uma operação.');
                }
            });
        });
        })(jQuery);
    </script>
@endpush
