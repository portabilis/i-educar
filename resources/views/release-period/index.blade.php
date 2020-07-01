@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="get">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Unificação de alunos</b></td>
            </tr>
            <tr id="tr_nm_ano">
                <td class="formmdtd" valign="top">
                    <span class="form">Ano</span><br>
                    <sub style="vertical-align:top;">somente números</sub>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-year')
                </td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formlttd" valign="top"><span class="form">Instituição</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-institution')
                </td>
            </tr>
            <tr id="tr_nm_escola">
                <td class="formmdtd" valign="top"><span class="form">Escola</span></td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school')
                </td>
            </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Buscar</button>
        </div>

    </form>

    <table class="table-default">
        <thead>
        <tr>
            <th>Escolas</th>
            <th>Etapa</th>
            <th>Datas</th>
        </tr>
        </thead>
        <tbody>
        @forelse($releasePeriods as $releasePeriod)
            <tr>
                <td>
                    <a href="{{ route('release-period.show', ['unification' => $releasePeriod->id]) }}">{{ implode(', ', $releasePeriod->schools->pluck('name')->toArray())  }}</a>
                </td>
                <td>
                    <a href="{{ route('release-period.show', ['unification' => $releasePeriod->id]) }}">{{ $releasePeriod->stage }}</a>
                </td>
                <td>
                    <a href="{{ route('release-period.show', ['unification' => $releasePeriod->id]) }}">{!! implode('<br>', $releasePeriod->getDatesArray()) !!}</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Não foi encontrado nenhum log de unificação</td>
            </tr>
        @endforelse

        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        {{ $releasePeriods->appends(request()->except('page'))->links() }}
    </div>

    <div style="text-align: center">
        <a href="{{url()->route('release-period.form')}}">
            <button class="btn-green" type="button">Novo</button>
        </a>
    </div>

    </form>
@endsection

@prepend('scripts')
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
@endprepend
