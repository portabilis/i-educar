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

            <tr>
                <td class="formmdtd" valign="top">
                    <span class="form">Nome</span>
                </td>
                <td class="formmdtd" valign="top">
                    <span class="form">
                        <input class="obrigatorio" type="text" name="name" id="name" value="{{old('name', Request::get('name'))}}" size="50" maxlength="255">
                    </span>
                </td>
            </tr>
            <tr>
                <td class="formlttd" valign="top">
                    <span class="form">CPF</span><br>
                    <sub style="vertical-align:top;">nnn.nnn.nnn-nn</sub>
                </td>
                <td class="formlttd" valign="top">
                    <span class="form">
                        <input onkeypress="formataCPF(this, event);" type="text" name="cpf"
                               id="cpf"
                               size="16" maxlength="14" value="{{old('cpf', Request::get('cpf'))}}">
                    </span>
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
            <th>Pessoa principal</th>
            <th>Pessoa(s) unificada(s)</th>
            <th>Data da unificação</th>
        </tr>
        </thead>
        <tbody>
        @forelse($unifications as $unification)
            <tr>
                <td>
                    <a href="{{ route('person-log-unification.show', ['unification' => $unification->id]) }}">{{ $unification->getMainName()  }}</a>
                </td>
                <td>
                    <a href="{{ route('person-log-unification.show', ['unification' => $unification->id]) }}">{{ implode(', ', $unification->getDuplicatesName()) }}</a>
                </td>
                <td>
                    <a href="{{ route('person-log-unification.show', ['unification' => $unification->id]) }}">{{ $unification->created_at->format('d/m/Y')  }}</a>
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
        {{ $unifications->appends(request()->except('page'))->links() }}
    </div>

    <div style="text-align: center">
        <a href="/intranet/educar_unifica_pessoa.php">
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
