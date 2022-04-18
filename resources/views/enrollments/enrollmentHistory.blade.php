@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <div>
        <table class="table-default">
            <thead>
                <tr>
                    <td colspan="2"><strong>Filtros de busca</strong></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Escola:</td>
                    <td>{{ $registration->school->getNameAttribute() ?? null }}</td>
                </tr>
                <tr>
                    <td>Nome do aluno:</td>
                    <td>{{ $registration->student->person->name ?? null }}</td>
                </tr>
                <tr>
                    <td>Matrícula:</td>
                    <td>{{ $registration->id ?? null }}</td>
                </tr>
                <tr>
                    <td>Situação:</td>
                    <td>{{ $registration->getStatusDescriptionAttribute() ?? null }}</td>
                </tr>
                <tr>
                    <td>Data saída:</td>
                    <td>{{ dataToBrasil($registration->data_cancel) ?? null }}</td>
                </tr>
            </tbody>
            <tr>
                <td class="formdktd" colspan="2"></td>
            </tr>
        </table>
    </div>
    <div>
        <table class="tablelistagem" width="100%">
            <tbody>
                <tr>
                    <td class="titulo-tabela-listagem" colspan="13">Lista de enturmações da matrícula</td>
                </tr>
                <tr>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Sequencial</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Turma</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Turno do aluno</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Ativo</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Data de enturmação</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Data de saída</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Transferido</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Remanejado</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Reclassificado</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Abandono</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Falecido</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Usuário criou</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Usuário editou</td>
                </tr>
                @foreach($registration->enrollments->sortBy('sequencial') as $enrollment)
                <tr>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->sequencial }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->schoolClass->getNameAttribute() }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->period->getNameAttribute() }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->ativo ? 'Sim' : 'Não'}}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->date?->format('d/m/Y') }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->date_departed?->format('d/m/Y') }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->transferido ? 'Sim' : 'Não' }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->remanejado ? 'Sim' : 'Não' }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->reclassificado ? 'Sim' : 'Não' }}</a>
                    </td><td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->abandono ? 'Sim' : 'Não' }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->falecido ? 'Sim' : 'Não' }}</a>
                    </td><td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->createdBy->person->name ?? null }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->updatedBy->person->name ?? null }}</a>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td class="formdktd" valign="top" align="left" colspan="13">
                        <small>A coluna "Turno do aluno" permanecerá em branco quando o turno do aluno for o mesmo da turma.</small>
                    </td>
                </tr>
                <tr>
                    <td colspan="13" align="center">
                        <input type="button" class="btn-green botaolistagem" onclick="javascript: go('/intranet/educar_matricula_det.php?cod_matricula={{ $registration->id }}')" value=" Voltar ">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


