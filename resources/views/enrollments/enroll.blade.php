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
                    <th scope="row">Nome do aluno:</th>
                    <td>{{ $registration->student->person->name ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Escola:</th>
                    <td>{{ $schoolClass->school->person->name ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Curso:</th>
                    <td>{{ $schoolClass->course->name ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Série:</th>
                    <td>{{ $registration->grade->name ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Turma selecionada:</th>
                    <td>{{ $schoolClass->name ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Total de vagas:</th>
                    <td>{{ $schoolClass->max_aluno }}</td>
                </tr>
                <tr>
                    <th scope="row">Vagas disponíveis:</th>
                    <td>{{ $schoolClass->vacancies }}</td>
                </tr>
                <tr>
                    <th scope="row">Alunos enturmados:</th>
                    <td>{{ $schoolClass->getTotalEnrolled() }}</td>
                </tr>
                <tr>
                    <th scope="row">Período de enturmação:</th>
                    <td>{{ $schoolClass->begin_academic_year->format('d/m/Y') }} à {{ $schoolClass->end_academic_year->format('d/m/Y') }}</td>
                </tr>
                @if($enableCancelButton && $enrollments->count())
                <tr>
                    <th scope="row">Turma de origem<span class="campo_obrigatorio">*</span>:</th>
                    <td>
                        <select name="enrollment_from_id" class="select-default" required>
                            <option value="">Selecione a turma de origem</option>
                            @foreach($enrollments as $enrollment)
                                @if($enrollment->schoolClass->id == $schoolClass->id)
                                    <option value="{{ $enrollment->id }}">{{ $enrollment->schoolClass->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endif
                <tr>
                    <th scope="row">
                        @if($enableCancelButton)
                            Data da saída<span class="campo_obrigatorio">*</span>
                        @else
                            Data da enturmação<span class="campo_obrigatorio">*</span>
                        @endif
                        <br>
                        <small class="text-muted">dd/mm/aaaa</small>
                    </th>
                    <td>
                        <input name="enrollment_date" value="{{ old('enrollment_date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('enrollment_date') ? 'error' : '' }}" type="text" maxlength="10">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            @if($enableCancelButton)
                @if($canUnenroll)
                    <button class="btn"  onclick="modalConfirmacao('is_cancellation')" type="button" name="is_cancellation" value="1">Desenturmar</button>
                @endif
            @else
                @if($canEnroll)
                    <button class="btn" type="submit">Enturmar</button>
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

            return 'Deseja continuar?';
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
