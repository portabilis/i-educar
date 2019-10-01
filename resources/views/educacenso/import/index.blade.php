@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <table class="table-default">
        <tr class="titulo-tabela-listagem">
            <th colspan="5">Importações - Listagem</th>
        </tr>
        <tr>
            <td style="font-weight:bold;">Ano</td>
            <td style="font-weight:bold;">Escola</td>
            <td style="font-weight:bold;">Usuário</td>
            <td style="font-weight:bold;">Data</td>
            <td style="font-weight:bold;">Situação</td>
        </tr>
        @forelse($imports as $import)
            <tr>
                <td>
                    {{ $import->year }}
                </td>
                <td>
                    {{ $import->school }}
                </td>
                <td>
                    {{ $import->user->realName }}
                </td>
                <td>
                    {{ $import->created_at->format('d/m/y H:i') }}
                </td>
                <td>
                    {{ $import->finished ? 'Finalizada' : 'Processando' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" align=center>Não há informação para ser apresentada</td>
            </tr>
        @endforelse
    </table>
@endsection
