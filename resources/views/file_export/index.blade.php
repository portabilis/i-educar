@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <h2>Exportações de documentos dos alunos</h2>
    <table class="table-default">
        <thead>
        <tr>
            <th>Arquivo</th>
            <th>Status</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        @forelse($exports as $export)
            <tr>
                <td>{{ $export->filename }}</td>
                <td>
                    @if($export->statusIsError())
                        O arquivo não pode ser exportado
                    @elseif($export->statusIsSuccess())
                        <a href="{{ $export->presignedUrl }}" style="font-size: 14px"><i class="fa fa-download"></i> <strong>Fazer download</strong> ({{ $export->sizeFormat }})</a>
                    @else
                        Aguardando a exportação do arquivo ser finalizada
                    @endif
                </td>
                <td>{{  $export->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Não existe nenhuma exportação</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        {{ $exports->links() }}
    </div>

    <div style="text-align: center; margin-top: 30px; margin-bottom: 30px">
        <a href="{{ route('file.export.create') }}" class="btn-green">Nova Exportação</a>
    </div>
@endsection
