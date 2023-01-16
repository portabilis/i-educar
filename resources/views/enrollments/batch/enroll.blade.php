@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="formcadastro" action="{{ Asset::get('/enturmacao-em-lote/' . $schoolClass->id) }}" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
                <tr>
                    <td class="formdktd" colspan="2" height="24"><b>Enturmar em lote</b></td>
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
                    @if($schoolClass->begin_academic_year && $schoolClass->end_academic_year)
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->begin_academic_year->format('d/m/Y') }} à {{ $schoolClass->end_academic_year->format('d/m/Y') }}</span></td>
                    @else
                    <td class="formmdtd" valign="top"><span class="form">O calendário letivo não está definido para a turma.</span></td>
                    @endif
                </tr>
            </tbody>
        </table>
        <h3>Alunos matriculados e não enturmados</h3>

        <p>
            <div>
            @if($schoolClass->school->institution->allowRegistrationOutAcademicYear)
                <span class="text-muted">A data de enturmação deve ser maior que a data da matrícula e maior que a data de saída da última enturmação do aluno.</span>
            @elseif($schoolClass->begin_academic_year && $schoolClass->end_academic_year)
                <span class="text-muted">A data da enturmação deve ser entre:</span> <strong>{{ $schoolClass->begin_academic_year->format('d/m/Y') }}</strong> e <strong>{{ $schoolClass->end_academic_year->format('d/m/Y') }}</strong><span class="text-muted">, maior que a data da matrícula e maior que a data de saída da última enturmação do aluno.</span>
            @else
                <span class="text-muted"><strong>O calendário letivo não está definido para a turma.</strong></span>
            @endif
            </div>
        </p>

        <div class="form-row">
            <div class="form-col">
                <label for="">
                    <div>Data da enturmação<span class="campo_obrigatorio">*</span></div>
                    <div><small class="text-muted">dd/mm/aaaa</small></div>
                </label>
            </div>
            <div class="form-col">
                <input name="date" value="{{ old('date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('date') ? 'error' : '' }}" type="text" maxlength="10" placeholder="Data da enturmação">
            </div>
            <div class="form-col">
            </div>
        </div>

        <table class="table-default">
            <thead>
            <tr>
                <th width="25"><label><input class="registration-check-master" type="checkbox"></label></th>
                <th width="100">Matrícula</th>
                <th>Nome</th>
                <th>Data de saída</th>
                <th>Data da matrícula</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
                @foreach($registrations as $registration)
                    <tr class="{{ $fails->has($registration->id) ? 'form-error' : '' }} {{ $success->has($registration->id) ? 'form-success' : '' }}">
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="registrations[{{ $registration->id }}]"
                                       value="{{ $registration->id }}"
                                       class="registration-check"
                                       {{ old('registrations.' . $registration->id) ? 'checked' : '' }}
                                       {{ $success->first($registration->id) ? 'disabled' : '' }} />
                            </label>
                        </td>
                        <td>{{ $registration->id }}</td>
                        <td>{{ $registration->student->person->name }}</td>
                        @if($registration->lastEnrollment)
                            <td>
                                {{ $registration->lastEnrollment->data_exclusao ? $registration->lastEnrollment->data_exclusao->format('d/m/Y') : '' }}
                                <small>({{ $registration->lastEnrollment->schoolClass->nm_turma }})</small>
                            </td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $registration->data_matricula->format('d/m/Y') }}</td>
                        <td>
                            {{ $success->first($registration->id) }}
                            {{ $fails->first($registration->id) }}
                            @if(empty($success->first($registration->id)) && empty($fails->first($registration->id)))
                                Aluno não enturmado.
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($registrations->isEmpty())
                    <tr>
                        <td>
                        </td>
                        <td colspan="5">Esta turma não possui nenhum matrícula disponível para enturmação.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Enturmar</button>
            <a href="javascript:void(0)" class="btn registration-btn-check" >Selecionar todos</a>
            <a href="{{ Asset::get('/cancelar-enturmacao-em-lote/' . $schoolClass->id) }}" class="btn">Desenturmar em lote</a>
            <a href="{{ Asset::get('intranet/educar_matricula_cad.php?ref_cod_turma_copiar_enturmacoes=' . $schoolClass->id) }}" class="btn">Copiar enturmações</a>
            <a href="{{ Asset::get('intranet/educar_matriculas_turma_lst.php') }}" class="btn">Cancelar</a>
        </div>

    </form>

    <script>
        $j(document).ready(function () {
            $j('#formcadastro').submit(function (e) {
                $j('button[type="submit"]').attr("disabled", true).text('Enturmando ...');
                return true;
            });
            $j('.registration-check-master').change(function () {
                if ($j(this).prop('checked')) {
                    $j('.registration-check').prop('checked', true);
                } else {
                    $j('.registration-check').prop('checked', false);
                }
            });
            $j('.registration-check').change(function () {
                if ($j(this).prop('checked') === false) {
                    $j('.registration-check-master').prop('checked', false);
                }
            });
            $j('.registration-btn-check').click(function () {
                $j('.registration-check-master').prop('checked', true);
                $j('.registration-check').prop('checked', true);
            });
        });
    </script>

@endsection
