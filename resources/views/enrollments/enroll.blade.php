@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="formcadastro" action="{{ route('enrollments.batch.enroll', ['schoolClass' => $schoolClass->id]) }}" method="post" class="open-sans">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
                <tr>
                    <td class="formdktd" colspan="2" height="24"><b>Enturmar</b></td>
                </tr>
                <tr id="tr_nm_aluno">
                    <td class="formmdtd" valign="top"><span class="form">Nome do aluno:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $registration->student->person->name ?? null }}</span></td>
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
                    <td class="formmdtd" valign="top"><span class="form">Turma selecionada:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->name ?? null }}</span></td>
                </tr>
                <tr id="tr_total_vagas">
                    <td class="formlttd" valign="top"><span class="form">Total de vagas:</span></td>
                    <td class="formlttd" valign="top"><span class="form">{{ $schoolClass->max_aluno }}</span></td>
                </tr>
                <tr id="tr_vagas_disponiveis">
                    <td class="formmdtd" valign="top"><span class="form">Vagas disponíveis:</span></td>
                    <td class="formmdtd" valign="top"><span class="form">{{ $schoolClass->vacancies }}</span></td>
                </tr>
                <tr id="tr_data_enturmacao">
                    <td class="formlttd" valign="top"><span class="form">
                        <label for="">
                            <div>Data da enturmação<span class="campo_obrigatorio">*</span></div>
                            <div><small class="text-muted">dd/mm/aaaa</small></div>
                        </label>
                        </span>
                    </td>
                    <td class="formlttd" valign="top">
                        <span class="form">
                            <input name="enrollment_date" value="{{ old('enrollment_date') }}" onkeypress="formataData(this, event);" class="form-input {{ $errors->has('enrollment_date') ? 'error' : '' }}" type="text" maxlength="10" placeholder="Data da enturmação">

                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
@endsection
