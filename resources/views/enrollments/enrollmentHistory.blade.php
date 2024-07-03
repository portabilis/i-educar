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
                    <th scope="row">Escola:</th>
                    <td>{{ $registration->school->name ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Nome do aluno:</th>
                    <td>{{ $registration->student->person->name ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Matrícula:</th>
                    <td>{{ $registration->id ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Situação:</th>
                    <td>{{ $registration->status_description ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Data saída:</th>
                    <td>{{ dataToBrasil($registration->data_cancel) ?? null }}</td>
                </tr>
            </tbody>
            <tr>
                <td class="formdktd" colspan="2"></td>
            </tr>
        </table>
    </div>
    <div>
        <table class="tablelistagem" style="width: 100%">
            <tbody>
                <tr>
                    <td class="titulo-tabela-listagem" colspan="14">Lista de enturmações da matrícula</td>
                </tr>
                <tr>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Sequencial</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Turma</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Turno do aluno</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Ativo</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Data de enturmação</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Data de saída</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Transferido</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Remanejado</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Reclassificado</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Abandono</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Falecido</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Usuário criou</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">Usuário editou</th>
                    <th scope="col" class="formdktd" style="font-weight:bold;">
                        Editar
                    </th>
                </tr>
                @foreach($registration->enrollments->sortBy('sequencial') as $enrollment)
                <tr>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->sequencial }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->schoolClass->name }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/intranet/educar_matricula_historico_cad.php?ref_cod_matricula={{ $registration->id }}&ref_cod_turma={{ $enrollment->schoolClass->id }}&sequencial={{ $enrollment->sequencial }}">{{ $enrollment->period->name }}</a>
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
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="{{ route('enrollments.enrollment-inep.edit', $enrollment->getKey()) }}">
                            INEP da Matrícula
                        </a>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td class="formdktd" colspan="14">
                        <small>A coluna "Turno do aluno" permanecerá em branco quando o turno do aluno for o mesmo da turma.</small>
                    </td>
                </tr>
                <tr>
                    <td colspan="14" class="text-center">
                        <input type="button" class="btn-green botaolistagem" onclick="javascript: go('/intranet/educar_matricula_det.php?cod_matricula={{ $registration->id }}')" value=" Voltar ">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


