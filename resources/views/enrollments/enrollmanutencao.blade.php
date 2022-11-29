@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
	<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
    <form action="{{ route('manutencao.enrollmanutencao', ['schoolClass' => $schoolClass, 'registration' => $registration]) }}" method="post">
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
                @if($enrollments->count())
                <tr>
                    <td>Turma de origem:</td>
                    <td>
                        <select name="enrollment_from_id" class="select-default">
                            @foreach($enrollments as $enrollment)
                                @if($enableCancelButton)
                                    @if($enrollment->schoolClass->id == $schoolClass->id)
                                        <option value="{{ $enrollment->id }}">{{ $enrollment->schoolClass->name }} - {{$enrollment->schoolClass->id}}</option>
                                    @endif
                                @else
                                    <option value="{{ $enrollment->id }}">{{ $enrollment->schoolClass->name }} - {{$enrollment->schoolClass->id}}</option>
                                @endif

                                @php
                                $cod_turma_origem = $enrollment->schoolClass->id
                                @endphp

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
                        <input type="hidden" name="cod_turma_origem" value="{{ $cod_turma_origem}} ">
                        <input name="enrollment_date" value="{{ old('enrollment_date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('enrollment_date') ? 'error' : '' }}" type="text" maxlength="10">
                    </td>
                </tr>
                <tr id="tr_confirma_dados_unificacao">
                <td colspan="2">
              
                <label for="check_confirma_exclusao" style="color:red !important;">Atenção! Ao remanejar o aluno esta rotina excluirá todas as informações do diário do aluno posteriormente a data de movimentação.</label>
                </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>
     
        <div style="text-align: center" id="btn-botoes">
            @if($enableCancelButton)
                <button class="btn" type="submit" name="is_cancellation" value="1">Desenturmar</button>
            @else
                <button class="btn" type="submit">Enturmar</button>
                @if($anotherClassroomEnrollments->count())
                    <button class="btn btn-disabled" type="submit" name="is_relocation" value="1" id="btn-remanejar" >Transferir para turma (remanejar)</button>
                    <script>
                        $("#check_confirma_exclusao").on("change", function(e){
                        if($("#check_confirma_exclusao").attr("checked")){
                            $("#btn-remanejar").button("enable");
                        } else {
                            $("#btn-remanejar").button("disable");
                            
                        }
                        
                        });
                    </script>
                    
                @endif
            @endif
            <a href="{{ route('manutencao.indexmanutencao', ['ref_cod_matricula' => $registration->id, 'ano_letivo' => $registration->year]) }}" class="btn">Cancelar</a>
        </div>

    </form>
@endsection
