@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form action="{{ route('enrollments.batch.enroll', ['schoolClass' => $schoolClass->id]) }}" method="post">
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
                    <td>{{ $schoolClass->grade->name ?? null }}</td>
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
                <tr>
                    <td>
                        Data da enturmação<span class="campo_obrigatorio">*</span>
                        <br>
                        <small class="text-muted">dd/mm/aaaa</small>
                    </td>
                    <td>
                        <input name="enrollment_date" value="{{ old('enrollment_date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('enrollment_date') ? 'error' : '' }}" type="text" maxlength="10" placeholder="Data da enturmação">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Enturmar</button>
            <a href="javascript:void(0)" class="btn registration-btn-check" >Selecionar todos</a>
            <a href="{{ route('enrollments.batch.cancel.index', ['schoolClass' => $schoolClass->id]) }}" class="btn">Desenturmar em lote</a>
            <a href="{{ url('intranet/educar_matricula_cad.php?ref_cod_turma_copiar_enturmacoes=' . $schoolClass->id) }}" class="btn">Copiar enturmações</a>
            <a href="{{ url('intranet/educar_matriculas_turma_lst.php') }}" class="btn">Cancelar</a>
        </div>

    </form>
@endsection
