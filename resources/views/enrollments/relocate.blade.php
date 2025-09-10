@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="enrollments-relocate" action="{{ Asset::get('/matricula/' .  $registration->id . '/remanejar/' . $schoolClass->id)}}" method="post">
        <table class="table-default">
            <thead>
                <tr>
                    <td colspan="2"><b>Remanejar</b></td>
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
                    <th scope="row">Turma de destino:</th>
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
                @if($enrollments->count())
                <tr>
                    <th scope="row">Turma de origem<span class="campo_obrigatorio">*</span>:</th>
                    <td>
                        <select name="enrollment_from_id" class="select-default" required>
                            <option value="">Selecione a turma de origem</option>
                            @foreach($enrollments as $enrollment)
                                @if(!$enableCancelButton || $enrollment->schoolClass->id != $schoolClass->id)
                                    <option value="{{ $enrollment->id }}">{{ $enrollment->schoolClass->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endif
                <tr>
                    <th scope="row">
                        Data da enturmação/saída<span class="campo_obrigatorio">*</span>
                        <br>
                        <small class="text-muted">dd/mm/aaaa</small>
                    </th>
                    <td>
                        <input name="enrollment_date" value="{{ old('enrollment_date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('enrollment_date') ? 'error' : '' }}" type="text" maxlength="10" required>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            @if($enrollments->count())
                <button class="btn" type="submit">Transferir para a turma (Remanejar)</button>
            @else
                <p>O aluno não possui enturmações ativas para remanejar.</p>
            @endif
            <a href="{{ Asset::get('/intranet/educar_matricula_turma_lst.php?ref_cod_matricula=' . $registration->id . '&ano_letivo=' . $registration->year . '&acao=remanejar') }}" class="btn">Cancelar</a>
        </div>
    </form>

    <script>
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