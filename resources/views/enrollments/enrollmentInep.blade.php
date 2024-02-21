@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="formcadastro" action="{{ route('enrollments.enrollment-inep.update', $enrollment->getKey()) }}" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
                <tr>
                    <td class="formlttd" valign="top">
                        <span class="form">
                            Nome do aluno:
                        </span>
                    </td>
                    <td class="formlttd" valign="top">
                        {{ $enrollment->registration->student->person->name ?? null }}
                    </td>
                </tr>
                <tr style="min-height: 45px">
                    <td class="formmdtd" valign="top">
                        <span class="form">
                            Escola:
                        </span>
                    </td>
                    <td class="formmdtd" valign="top">
                        {{ $enrollment->schoolClass->school->person->name ?? null }}
                    </td>
                </tr>
                <tr style="min-height: 45px">
                    <td class="formlttd" valign="top">
                        <span class="form">
                            Curso:
                        </span>
                    </td>
                    <td class="formlttd" valign="top">
                        {{ $enrollment->schoolClass->course->name ?? null }}
                    </td>
                </tr>
                <tr style="min-height: 45px">
                    <td class="formmdtd" valign="top">
                        <span class="form">
                            Série:
                        </span>
                    </td>
                    <td class="formmdtd" valign="top">
                        {{ $enrollment->registration->grade->name ?? null }}
                    </td>
                </tr>
                <tr style="min-height: 45px">
                    <td class="formlttd" valign="top">
                        <span class="form">
                            Turma selecionada:
                        </span>
                    </td>
                    <td class="formlttd" valign="top">
                        {{ $enrollment->schoolClass->name ?? null }}
                    </td>
                </tr>
                <tr style="min-height: 45px">
                    <td class="formmdtd" valign="top">
                        <span class="form">
                            Código INEP da Matrícula
                        </span>
                        <span class="campo_obrigatorio">*</span>
                        <br>
                        <sub style="vertical-align:top;" class="text-muted">somente números</sub>
                    </td>
                    <td class="formmdtd" valign="top">
                        <span class="form">
                            <input type="text"
                                   class="geral"
                                   name="matricula_inep"
                                   id="matricula_inep"
                                   maxlength="12"
                                   minlength="12"
                                   inputmode="numeric"
                                   value="{{ $enrollment->inep->matricula_inep ?? null }}"
                                   placeholder="Código INEP"
                                   autocomplete="off">
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div id="export-button" style="text-align: center; margin-bottom: 10px">
            <button class="btn-green" type="submit">
                Salvar
            </button>
            <input type="button" class="botaolistagem"
                   onclick="javascript:goOrClose('{{ route('enrollments.enrollment-history', $enrollment->getKey()) }}');"
                   value="Cancelar" autocomplete="off">
        </div>
    </form>
@endsection
