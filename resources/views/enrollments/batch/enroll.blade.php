@extends('layout.default')

@section('content')
    <form id="formcadastro" action="" method="post">
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
                    <td class="formmdtd" valign="top"><span class="form">Período de enturmação:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->begin_academic_year }} à {{ $schoolClass->end_academic_year }}</span></td>
                </tr>
            </tbody>
        </table>
    </form>

    <form action="{{ route('enrollments.batch.enroll', ['schoolClass' => $schoolClass->id]) }}" method="post" class="open-sans">

        <h3>Alunos matriculados e não enturmados</h3>

        @if($success->count() == 1)
            <div class="alert-success">Foi enturmado 1 aluno.</div>
        @elseif($success->count())
            <div class="alert-success">Foram enturmados {{ $success->count() }} alunos.</div>
        @endif

        @if($fails->count() == 1)
            <div class="alert-error">Não foi possivel enturmar 1 aluno.</div>
        @elseif($fails->count())
            <div class="alert-error">Não foi possivel enturmar {{ $fails->count() }} alunos.</div>
        @endif

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <p>
            <div><span class="text-muted">A data da enturmação deve ser entre:</span> <strong>{{ $schoolClass->begin_academic_year }}</strong> e <strong>{{ $schoolClass->end_academic_year }}</strong><span class="text-muted">, maior que a data da matrícula e maior que a data de saída da última enturmação do aluno.</span></div>
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
                                {{ $registration->lastEnrollment->data_exclusao->format('d/m/Y') }}
                                <small>({{ $registration->lastEnrollment->schoolClass->nm_turma }})</small>
                            </td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $registration->data_matricula->format('d/m/Y') }}</td>
                        <td>{{ $success->first($registration->id) }} {{ $fails->first($registration->id) }}</td>
                    </tr>
                @endforeach
                @if($registrations->isEmpty())
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
            <button class="btn-green" type="submit">Enturmar</button>
            <button class="btn registration-btn-check" type="button">Selecionar Todos</button>
            <button class="btn" type="button">Cancelar</button>
        </div>

    </form>

    <style>
        input.form-input {
            border-radius: 3px;
            background-color: #ffffff;
            border: solid 1px #cddce6;
            padding: 8px;
            font-size: 14px;
            color: #47728f;
            box-sizing: border-box;
        }
        .form-row {
            margin: 1rem 0;
            display: table;
        }
        .form-col {
            display: table-cell;
            vertical-align: middle;
            padding-right: 16px;
        }
        .open-sans {
            font-family: "Open Sans", sans-serif;
            font-size: 14px;
        }
        .table-default {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }
        .table-default thead tr {
            font-weight: bold;
            background-color: #ccdce6;
        }
        .table-default thead tr th {
            font-weight: bold;
        }
        .table-default tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        .table-default tbody tr:nth-child(even) {
            background-color: #f5f9fd;
        }
        .table-default th {
            padding: 8px
        }
        .table-default td {
            padding: 8px;
            font-size: 14px;
            font-weight: normal;
        }
        .text-muted {
            color: #999999;
        }
        .separator {
            background-color: #ccdce6;
            height: 5px;
        }

        .alert-error,
        .alert-flash ,
        .alert-success ,
        .alert-exclamation {
            color: #ff4e4e;
            border: 1px solid #ffe5e5;
            background-color: #ffeeee;
            padding: 10px;
            margin: 10px 0;
        }

        .alert-flash {
            color: #b0af84;
            border: 1px solid #fdffac;
            background-color: #feffce;
        }

        .alert-success {
            color: #157100;
            border: 1px solid #e0f9d8;
            background-color: #f0f9ee;
        }

        .alert-exclamation {
            color: #ffba00;
            border: 1px solid #fff3d3;
            background-color: #fff7e2;
        }

        .form-error {
            color: red;
        }
        .form-success {
            color: green;
        }
        .btn,
        .btn-green {
            background-color: #FFF;
            font-size: 14px;
            color: #47728f;
            padding: 10px 30px;
            border-radius: 3px;
            font-weight: normal;
            margin: 15px 0;
            font-family: "Open Sans", sans-serif;
            cursor: pointer;
            border-color: #cddce6 !important;
        }
    </style>

    <script>
        $j(document).ready(function () {
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
