@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="formcadastro" action="{{ Asset::get('/cancelar-enturmacao-em-lote/' . $schoolClass->id, env('ASSETS_SECURE', false)) }}" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
                <tr>
                    <td class="formdktd" colspan="2" height="24"><b>Desenturmar em lote</b></td>
                </tr>
                <tr id="tr_nm_instituicao">
                    <td class="formmdtd" valign="top"><span class="form">Instituição:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->school->institution->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_escola">
                    <td class="formlttd" valign="top"><span class="form">Escola:</span></td>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->school->person->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_curso">
                    <td class="formmdtd" valign="top"><span class="form">Curso:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->course->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_serie">
                    <td class="formlttd" valign="top"><span class="form">Série:</span></td>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->grade->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_turma">
                    <td class="formmdtd" valign="top"><span class="form">Turma:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->name ?? null }}</span></td>
                </tr>
                <tr id="tr_ano">
                    <td class="formlttd" valign="top"><span class="form">Ano:</span></td>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->year ?? null }}</span></td>
                </tr>
                <tr id="tr_ano">
                    <td class="formmdtd" valign="top"><span class="form">Total de vagas na turma:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->max_aluno }}</span></td>
                </tr>
                <tr id="tr_ano">
                    <td class="formlttd" valign="top"><span class="form">Vagas disponíveis na turma:</span></td>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->vacancies }}</span></td>
                </tr>
                <tr id="tr_ano">
                    <td class="formmdtd" valign="top"><span class="form">Calendário letivo:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->begin_academic_year->format('d/m/Y') }} à {{ $schoolClass->end_academic_year->format('d/m/Y') }}</span></td>
                </tr>
            </tbody>
        </table>
    </form>

    <form  id="enrollments-unenroll"  action="{{ Asset::get('/cancelar-enturmacao-em-lote/' . $schoolClass->id) }}" method="post" class="open-sans">

        <h3>Alunos matriculados e enturmados</h3>

        <p>
            <div>
                @if($schoolClass->school->institution->allowRegistrationOutAcademicYear)
                    <span class="text-muted">A data de saída deve ser maior que a data da enturmação do aluno.</span>
                @else
                <span class="text-muted">A data de saída deve ser entre:</span> <strong>{{ $schoolClass->begin_academic_year->format('d/m/Y') }}</strong> e <strong>{{ $schoolClass->end_academic_year->format('d/m/Y') }}</strong> <span class="text-muted">e maior que a data da enturmação do aluno.</span>
                @endif
            </div>
        </p>

        <div class="form-row">
            <div class="form-col">
                <label for="">
                    <div>Data da saída<span class="campo_obrigatorio">*</span></div>
                    <div><small class="text-muted">dd/mm/aaaa</small></div>
                </label>
            </div>
            <div class="form-col">
                <input name="date" value="{{ old('date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('date') ? 'error' : '' }}" type="text" maxlength="10" placeholder="Data da saída">
            </div>
            <div class="form-col">
            </div>
        </div>

        <table class="table-default">
            <thead>
            <tr>
                <th width="25"><label><input class="enrollment-check-master" type="checkbox"></label></th>
                <th width="100">Matrícula</th>
                <th>Nome</th>
                <th>Data da enturmação</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enrollment)
                    <tr class="{{ $fails->has($enrollment->id) ? 'form-error' : '' }} {{ $success->has($enrollment->id) ? 'form-success' : '' }}">
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="enrollments[{{ $enrollment->id }}]"
                                       value="{{ $enrollment->id }}"
                                       class="enrollment-check"
                                       {{ old('enrollments.' . $enrollment->id) ? 'checked' : '' }}
                                       {{ $success->first($enrollment->id) ? 'disabled' : '' }} />
                            </label>
                        </td>
                        <td>{{ $enrollment?->registration?->cod_matricula }}</td>
                        <td>{{ $enrollment?->student_name }}</td>
                        <td>{{ $enrollment?->data_enturmacao->format('d/m/Y') }}</td>
                        <td>
                            {{ $success->first($enrollment->id) }}
                            {{ $fails->first($enrollment->id) }}
                            @if(empty($success->first($enrollment->id)) && empty($fails->first($enrollment->id)))
                                Aluno enturmado.
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($enrollments->isEmpty())
                    <tr>
                        <td>
                        </td>
                        <td colspan="4">Esta turma não possui nenhum aluno enturmado.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Desenturmar</button>
            <a href="javascript:void(0)" class="btn enrollment-btn-check">Selecionar todos</a>

            <a href="{{ Asset::get('/enturmacao-em-lote/' . $schoolClass->id) }}" class="btn">Enturmar em lote</a>
            <a href="{{ Asset::get('intranet/educar_matriculas_turma_lst.php') }}" class="btn">Cancelar</a>
        </div>

    </form>

    <script>

        $j(document).ready(function () {
            $j('.enrollment-check-master').change(function () {
                if ($j(this).prop('checked')) {
                    $j('.enrollment-check').prop('checked', true);
                } else {
                    $j('.enrollment-check').prop('checked', false);
                }
            });
            $j('.enrollment-check').change(function () {
                if ($j(this).prop('checked') === false) {
                    $j('.enrollment-check-master').prop('checked', false);
                }
            });
            $j('.enrollment-btn-check').click(function () {
                $j('.enrollment-check-master').prop('checked', true);
                $j('.enrollment-check').prop('checked', true);
            });
        });

        $j('#enrollments-unenroll').submit(function (e) {
            e.preventDefault();
            makeDialog({
                title: 'Atenção!',
                content: 'O processo de desenturmação e enturmação manual ' +
                    'não será considerado como remanejamento ou troca de turma, ' +
                    'para isso você deve selecionar a turma nova e remanejar. Deseja continuar?',
                maxWidth: 860,
                width: 860,
                modal: true,
                buttons: [{
                    text: 'OK',
                    click: function () {
                        e.currentTarget.submit();
                        $j(this).dialog('destroy');
                    }
                },{
                    text: 'Cancelar',
                    click: function () {
                        $j(this).dialog('destroy');
                    }
                }]
            });
        });

        function makeDialog (params) {
            let container = $j('#dialog-container');
            if (container.length < 1) {
                $j('body').append('<div id="dialog-container" style="width: 400px;"></div>');
                container = $j('#dialog-container');
            }

            if (container.hasClass('ui-dialog-content')) {
                container.dialog('destroy');
            }

            container.empty();
            container.html(params.content);
            delete params['content'];

            container.dialog(params);
        }
    </script>

@endsection
