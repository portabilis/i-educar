@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="formcadastro" action="{{ Asset::get('/cancelar-matricula-em-lote/' . $schoolClass->id, env('ASSETS_SECURE', false)) }}" method="post">
        <table class="tablecadastro" style="width: 100%">
            <tbody>
                <tr>
                    <td class="formdktd" colspan="2"><b>Cancelar matrícula em lote</b></td>
                </tr>
                <tr id="tr_nm_instituicao">
                    <th scope="row" class="formmdtd" valign="top"><span class="form">Instituição:</span></th>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->school->institution->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_escola">
                    <th scope="row" class="formlttd" valign="top"><span class="form">Escola:</span></th>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->school->person->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_curso">
                    <th scope="row" class="formmdtd" valign="top"><span class="form">Curso:</span></th>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->course->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_serie">
                    <th scope="row" class="formlttd" valign="top"><span class="form">Série:</span></th>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->grade->name ?? null }}</span></td>
                </tr>
                <tr id="tr_nm_turma">
                    <th scope="row" class="formmdtd" valign="top"><span class="form">Turma:</span></th>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->name ?? null }}</span></td>
                </tr>
                <tr id="tr_ano">
                    <th scope="row" class="formlttd" valign="top"><span class="form">Ano:</span></th>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->year ?? null }}</span></td>
                </tr>
            </tbody>
        </table>
    </form>

    <form id="registrations-cancel" action="{{ Asset::get('/cancelar-matricula-em-lote/' . $schoolClass->id) }}" method="post" class="open-sans">

        <h3>Alunos matriculados e enturmados</h3>

        <p>
            <div>
                <span class="text-muted">Selecione os alunos cujas matrículas deseja cancelar. Esta ação irá desativar a matrícula e todas as enturmações associadas.</span>
            </div>
        </p>

        <table class="table-default">
            <thead>
            <tr>
                <th width="25"><input class="registration-check-master" type="checkbox" /></th>
                <th width="100">Matrícula</th>
                <th>Nome</th>
                <th>Data da enturmação</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enrollment)
                    <tr class="{{ $fails->has($enrollment->registration->cod_matricula) ? 'form-error' : '' }} {{ $success->has($enrollment->registration->cod_matricula) ? 'form-success' : '' }}">
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="registrations[{{ $enrollment->registration->cod_matricula }}]"
                                       value="{{ $enrollment->registration->cod_matricula }}"
                                       class="registration-check"
                                       {{ old('registrations.' . $enrollment->registration->cod_matricula) ? 'checked' : '' }}
                                       {{ $success->first($enrollment->registration->cod_matricula) ? 'disabled' : '' }} />
                            </label>
                        </td>
                        <td>{{ $enrollment->registration->cod_matricula }}</td>
                        <td>{{ $enrollment->student_name }}</td>
                        <td>{{ $enrollment->data_enturmacao->format('d/m/Y') }}</td>
                        <td>
                            {{ $success->first($enrollment->registration->cod_matricula) }}
                            {{ $fails->first($enrollment->registration->cod_matricula) }}
                            @if(empty($success->first($enrollment->registration->cod_matricula)) && empty($fails->first($enrollment->registration->cod_matricula)))
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
            <button class="btn-green" type="submit">Cancelar matrículas</button>
            <a href="javascript:void(0)" class="btn registration-btn-check">Selecionar todos</a>

            <a href="{{ Asset::get('/enturmacao-em-lote/' . $schoolClass->id) }}" class="btn">Enturmar em lote</a>
            <a href="{{ Asset::get('/cancelar-enturmacao-em-lote/' . $schoolClass->id) }}" class="btn">Desenturmar em lote</a>
            <a href="{{ Asset::get('intranet/educar_matriculas_turma_lst.php') }}" class="btn">Voltar</a>
        </div>

    </form>

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

        $j('#registrations-cancel').submit(function (e) {
            e.preventDefault();

            var checked = $j('.registration-check:checked').length;
            if (checked === 0) {
                alert('Selecione ao menos uma matrícula para cancelar.');
                return;
            }

            makeDialog({
                title: 'Atenção!',
                content: 'Você está prestes a cancelar ' + checked + ' matrícula(s). ' +
                    'Esta ação irá desativar a matrícula e todas as enturmações associadas. ' +
                    'Deseja continuar?',
                maxWidth: 860,
                width: 860,
                modal: true,
                buttons: [{
                    text: 'Confirmar cancelamento',
                    click: function () {
                        e.currentTarget.submit();
                        $j(this).dialog('destroy');
                    }
                },{
                    text: 'Voltar',
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
