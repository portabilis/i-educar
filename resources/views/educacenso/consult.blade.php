@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('modules/Portabilis/Assets/Plugins/Chosen/chosen.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" method="get" action="{{ route('educacenso.consult') }}">
        <table class="table-default table-form">
            <thead>
                <tr>
                    <th colspan="2">Consulta</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Ano<span class="campo_obrigatorio">*</span>
                        <br>
                        <sub style="vertical-align:top;" class="text-muted">somente números</sub>
                    </td>
                    <td>
                        @include('layout.input.year')
                    </td>
                </tr>
                <tr>
                    <td>Instituição<span class="campo_obrigatorio">*</span></td>
                    <td>
                        @include('form.select-institution')
                    </td>
                </tr>
                <tr>
                    <td>Escola<span class="campo_obrigatorio">*</span></td>
                    <td>
                        @include('form.select-school')
                    </td>
                </tr>
                <tr>
                    <td>Registro<span class="campo_obrigatorio">*</span></td>
                    <td>
                        @include('layout.select.educacenso-records', ['records' => [20, 40, 50, 60]])
                    </td>
                </tr>
                <tr>
                    <td>Data de referência do Censo Escolar</td>
                    <td>
                        {{ $institution->educacenso_date ? $institution->educacenso_date->format('d/m/Y') : 'Não definida.' }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="separator"></div>
        <div style="text-align: center;">
            <button class="btn-green">Consultar</button>
        </div>
    </form>

    @isset($record20)
    <h2>Registro 20</h2>

    <table class="table-default">
        <thead>
            <tr>
                <th>INEP da escola</th>
                <th>Nome da escola</th>
                <th>Nome da turma</th>
                <th>Tipo de atendimento</th>
                <th>Horários</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record20 as $item)
                <tr>
                    <td>{{ $item->codigoEscolaInep }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->nomeTurma }}</td>
                    <td>{{ \iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma::getDescription($item->tipoAtendimento) }}</td>
                    <td>{{ substr($item->horaInicial, 0, 5) }} - {{ substr($item->horaFinal, 0, 5) }}</td>
                </tr>
            @empty
            <tr>
                <td colspan="5">Nenhum registro encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="separator"></div>
    @endisset

    @isset($record40)
    <h2>Registro 40</h2>

    <table class="table-default">
        <thead>
            <tr>
                <th>INEP da escola</th>
                <th>Nome da escola</th>
                <th>Código da pessoa</th>
                <th>INEP do(a) gestor(a)</th>
                <th>Nome do(a) gestor(a)</th>
                <th>Cargo</th>
                <th>Tipo de vínculo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record40 as $item)
                <tr>
                    <td>{{ $item->inepEscola }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->codigoPessoa }}</td>
                    <td>{{ $item->inepGestor }}</td>
                    <td>{{ $item->nomePessoa }}</td>
                    <td>{{ \iEducar\Modules\Educacenso\Model\SchoolManagerRole::getDescription($item->cargo) }}</td>
                    <td>{{ \iEducar\Modules\Servidores\Model\TipoVinculo::getDescription($item->tipoVinculo) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="separator"></div>
    @endisset

    @isset($record50)
    <h2>Registro 50</h2>

    <table class="table-default">
        <thead>
            <tr>
                <th>INEP da escola</th>
                <th>Nome da escola</th>
                <th>Código da pessoa</th>
                <th>INEP do(a) docente</th>
                <th>Nome do(a) docente</th>
                <th>Função</th>
                <th>Turma</th>
                <th>Disciplinas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record50 as $item)
                <tr>
                    <td>{{ $item->inepEscola }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->codigoPessoa }}</td>
                    <td>{{ $item->inepDocente }}</td>
                    <td>{{ $item->nomeDocente }}</td>
                    <td>{{ \iEducar\Modules\Servidores\Model\FuncaoExercida::getDescription($item->funcaoDocente) }}</td>
                    <td>{{ $item->nomeTurma }}</td>
                    <td>{{ implode(', ', $item->componentes) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="separator"></div>
    @endisset

    @isset($record60)
    <h2>Registro 60</h2>

    <table class="table-default">
        <thead>
            <tr>
                <th>INEP da escola</th>
                <th>Nome da escola</th>
                <th>Código da pessoa</th>
                <th>INEP do(a) aluno(a)</th>
                <th>Nome do(a) aluno(a)</th>
                <th>Turma</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record60 as $item)
                <tr>
                    <td>{{ $item->inepEscola }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->codigoPessoa }}</td>
                    <td>{{ $item->inepAluno }}</td>
                    <td>{{ $item->nomeAluno }}</td>
                    <td>{{ $item->nomeTurma }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="separator"></div>
    @endif

    @isset($paginate)
        {{ $paginate->appends(request()->query())->links() }}
    @endisset

    <div style="height: 30px;"></div>
@endsection

@prepend('scripts')
    <script type="text/javascript" src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript" src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript" src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
    <script type="text/javascript" src="{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js") }}"></script>
@endprepend
