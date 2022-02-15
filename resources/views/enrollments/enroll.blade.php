@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="enrollments-enroll" action="{{ Asset::get('/matricula/' .  $registration->id . '/enturmar/' . $schoolClass->id)}}" method="post">
        <table class="table-default">
            <thead>
                <tr>
                    <td colspan="2"><b>Enturmar</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nome do aluno:</td>
                    <td>{{ $registration->student->person->name ?? null }}</td>
                </tr>
                <tr>
                    <td>Escola:</td>
                    <td>{{ $schoolClass->school->person->name ?? null }}</td>
                </tr>
                <tr>
                    <td>Curso:</td>
                    <td>{{ $schoolClass->course->name ?? null }}</td>
                </tr>
                <tr>
                    <td>Série:</td>
                    <td>{{ $registration->grade->name ?? null }}</td>
                </tr>
                <tr>
                    <td>Turma selecionada:</td>
                    <td>{{ $schoolClass->name ?? null }}</td>
                </tr>
                <tr>
                    <td>Total de vagas:</td>
                    <td>{{ $schoolClass->max_aluno }}</td>
                </tr>
                <tr>
                    <td>Vagas disponíveis:</td>
                    <td>{{ $schoolClass->vacancies }}</td>
                </tr>
                <tr>
                    <td>Alunos enturmados:</td>
                    <td>{{ $schoolClass->getTotalEnrolled() }}</td>
                </tr>
                <tr>
                    <td>Período de enturmação:</td>
                    <td>{{ $schoolClass->begin_academic_year->format('d/m/Y') }} à {{ $schoolClass->end_academic_year->format('d/m/Y') }}</td>
                </tr>
                @if($enrollments->count())
                <tr>
                    <td>Turma de origem:</td>
                    <td>
                        <select name="enrollment_from_id" class="select-default">
                            @foreach($enrollments as $enrollment)
                                @if($enableCancelButton)
                                    @if($enrollment->schoolClass->id == $schoolClass->id)
                                        <option value="{{ $enrollment->id }}">{{ $enrollment->schoolClass->name }}</option>
                                    @endif
                                @else
                                    <option value="{{ $enrollment->id }}">{{ $enrollment->schoolClass->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endif
                <tr>
                    <td>
                        Data da enturmação/saída<span class="campo_obrigatorio">*</span>
                        <br>
                        <small class="text-muted">dd/mm/aaaa</small>
                    </td>
                    <td>
                        <input name="enrollment_date" value="{{ old('enrollment_date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('enrollment_date') ? 'error' : '' }}" type="text" maxlength="10">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            @if($enableCancelButton)
                <button class="btn"  onclick="modalConfirmacao('is_cancellation')" type="button" name="is_cancellation" value="1">Desenturmar</button>
            @else
                <button class="btn" type="submit">Enturmar</button>
                @if($enrollments->count())
                    <button class="btn" type="submit" name="is_relocation" value="1">Transferir para turma (remanejar)</button>
                @endif
            @endif
            <a href="{{ Asset::get('/intranet/educar_matricula_turma_lst.php?ref_cod_matricula=' . $registration->id . '&ano_letivo=' . $registration->year) }}" class="btn">Cancelar</a>
        </div>
    </form>

    <script>
        function modalConfirmacao(val) {
            let mensagem = buscaMensagem(val);
            makeDialog({
                title: 'Atenção!',
                content: mensagem,
                maxWidth: 860,
                width: 860,
                modal: true,
                buttons: [{
                    text: 'OK',
                    click: function () {
                        $j('#enrollments-enroll').append('<input  type="hidden" name="'+val+'" value="1" id="'+val+'"/>')
                        $j('#enrollments-enroll').submit()
                        $j(this).dialog('destroy');
                    }
                },{
                    text: 'Cancelar',
                    click: function () {
                        $j(this).dialog('destroy');
                    }
                }]
            });
        }

        function buscaMensagem(val) {
            if (val === 'is_cancellation') {
                return 'O processo de desenturmação e enturmação manual ' +
                       'não será considerado como remanejamento ou troca de turma, ' +
                       'para isso você deve selecionar a turma nova e remanejar. Deseja continuar?';
            }

            if (val === 'is_enturmacao') {
                return 'Na opção <b>Enturmar</b> você está criando uma nova ' +
                       'enturmação e esta não será considerada como remanejamento ou ' +
                       'troca de turma. Deseja continuar?';
            }

            return 'Esta ação será considerada como remanejamento/troca de turma e ' +
                    'será contabilizada nas movimentações do(a) aluno(a). Deseja continuar?';
        }

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
