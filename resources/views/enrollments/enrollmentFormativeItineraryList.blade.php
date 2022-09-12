@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <div>
        <table class="tablelistagem" width="100%">
            <tbody>
                <tr>
                    <td class="titulo-tabela-listagem" colspan="13">Lista de enturmações da matrícula</td>
                </tr>
                <tr>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Sequencial</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Turma</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Ativo</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Data de enturmação</td>
                    <td class="formdktd" valign="top" align="left" style="font-weight:bold;">Data de saída</td>
                </tr>
                @foreach($registration->enrollments->sortBy('sequencial') as $enrollment)
                <tr>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/enrollment-formative-itinerary/{{ $enrollment->id }}">{{ $enrollment->sequencial }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/enrollment-formative-itinerary/{{ $enrollment->id }}">{{ $enrollment->schoolClass->getNameAttribute() }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/enrollment-formative-itinerary/{{ $enrollment->id }}">{{ $enrollment->ativo ? 'Sim' : 'Não'}}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/enrollment-formative-itinerary/{{ $enrollment->id }}">{{ $enrollment->date?->format('d/m/Y') }}</a>
                    </td>
                    <td {!! ($loop->iteration % 2) == 1 ? 'class="formlttd"' : 'class="formmdtd"' !!} valign="top" align="left">
                        <a href="/enrollment-formative-itinerary/{{ $enrollment->id }}">{{ $enrollment->date_departed?->format('d/m/Y') }}</a>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="13" align="center">
                        <input type="button" class="btn-green botaolistagem" onclick="javascript: go('/intranet/educar_matricula_det.php?cod_matricula={{ $registration->id }}')" value=" Voltar ">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


