@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <table class="table-default">
        <thead>
        <tr>
            <th>Ano</th>
            <th>Status</th>
            <th>Usuário</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        @forelse($operations as $operation)
            <tr>
                <td><a href="{{ route('component-batch-manager.show', $operation) }}">{{ $operation->data['year'] ?? '-' }}</a></td>
                <td>
                    @if($operation->view_is_stale)
                        A operação não pôde ser concluída
                    @else
                        <span class="label label-{{ $operation->view_status->color() }}">{{ $operation->view_status->label() }}</span>
                    @endif
                </td>
                <td>{{ $operation->user?->name ?? 'Usuário #' . $operation->user_id }}</td>
                <td>{{ $operation->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Nenhuma operação realizada</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        {{ $operations->links() }}
    </div>

    <div style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ route('component-batch-manager.create') }}" class="btn-green" style="text-decoration: none;">Nova Operação</a>
    </div>
@endsection
