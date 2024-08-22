@extends('layout.default')

@inject('presigner', App\Services\UrlPresigner::class)

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <h2>Avisos</h2>
    <table class="table-default">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Tipos de Usuário</th>
            <th>Data</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($announcements as $announcement)
            <tr>
                @can('modify', \App\Process::ANNOUNCEMENT)
                    <td>
                        <a href="{{ route('announcement.publish.edit', $announcement) }}">{{ $announcement->name }}</a>
                    </td>
                    <td>
                        <a href="{{ route('announcement.publish.edit', $announcement) }}">{{ $announcement->userTypes->implode('nm_tipo', ', ') }}</a>
                    </td>
                    <td>
                        <a href="{{ route('announcement.publish.edit', $announcement) }}">{{ $announcement->created_at->format('d/m/Y H:i') }}</a>
                    </td>
                    <td>
                        <a href="{{ route('announcement.publish.edit', $announcement) }}">{{ $announcement->trashed() ? 'Desativado' : 'Ativado' }}</a>
                    </td>
                @else
                    <td>{{ $announcement->name }}/></td>
                    <td>{{ $announcement->userTypes->implode('nm_tipo', ', ') }}</td>
                    <td>{{ $announcement->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $announcement->trashed() ? 'Desativado' : 'Ativado' }}</td>
                @endcan

            </tr>
        @empty
            <tr>
                <td colspan="3">Não existe nenhum aviso</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center;">
        <div style="display: inline-block;">
            {{ $announcements->links() }}
        </div>
    </div>

    @can('create', \App\Process::ANNOUNCEMENT)
        <div style="text-align: center; margin-top: 30px; margin-bottom: 30px">
            <a href="{{ route('announcement.publish.create') }}" class="btn-green">Novo</a>
        </div>
    @endcan
@endsection
