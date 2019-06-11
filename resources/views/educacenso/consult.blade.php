@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="formcadastro" method="post" action="{{ route('educacenso.consult') }}">
        <table class="table-default">
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
                        <input name="year" class="obrigatorio" type="text" value="2019" size="4" maxlength="4">
                    </td>
                </tr>
                <tr>
                    <td>Escola<span class="campo_obrigatorio">*</span></td>
                    <td>
                        <select name="school" class="obrigatorio" style="width: 314px;">
                            <option value="">Selecione uma escola</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->getKey() }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Registro<span class="campo_obrigatorio">*</span></td>
                    <td>
                        <select name="record" class="obrigatorio" style="width: 314px;">
                            <option value="">Selecione um registro</option>
                            <option value="20">Registro 20</option>
                            <option value="40">Registro 40</option>
                            <option value="50">Registro 50</option>
                            <option value="60">Registro 60</option>
                        </select>
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
        <div class="text-center">
            <button class="btn-green">Consultar</button>
        </div>
    </form>

    <div style="height: 30px;"></div>

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
            @foreach($record20 as $item)
                <tr>
                    <td>{{ $item->codigoEscolaInep }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->nomeTurma }}</td>
                    <td>{{ \iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma::getDescription($item->tipoAtendimento) }}</td>
                    <td>{{ $item->horaInicial }} - {{ $item->horaFinal }}</td>
                </tr>
            @endforeach
            @empty($record20)
            <tr>
                <td colspan="5">Nenhum registro encontrado.</td>
            </tr>
            @endempty
        </tbody>
    </table>
    @endisset

    @isset($record40)
    <h2>Registro 40</h2>

    <table class="table-default">
        <thead>
            <tr>
                <th>INEP da escola</th>
                <th>Nome da escola</th>
                <th>Código da pessoa</th>
                <th>INEP do gestor</th>
                <th>Nome do gestor</th>
                <th>Cargo</th>
                <th>Tipo de vínculo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record40 as $item)
                <tr>
                    <td>{{ $item->inepEscola }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->codigoPessoa }}</td>
                    <td>{{ $item->nomePessoa }}</td>
                    <td>{{ $item->inepGestor }}</td>
                    <td>{{ \iEducar\Modules\Educacenso\Model\SchoolManagerRole::getDescription($item->cargo) }}</td>
                    <td>{{ \iEducar\Modules\Servidores\Model\TipoVinculo::getDescription($item->tipoVinculo) }}</td>
                </tr>
            @endforeach
            @empty($record40)
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endempty
        </tbody>
    </table>
    @endisset

    @isset($record50)
    <h2>Registro 50</h2>

    <table class="table-default">
        <thead>
            <tr>
                <th>INEP da escola</th>
                <th>Nome da escola</th>
                <th>Código da pessoa</th>
                <th>INEP do docente</th>
                <th>Nome do docente</th>
                <th>Função</th>
                <th>Disciplinas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record50 as $item)
                <tr>
                    <td>{{ $item->inepEscola }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->codigoPessoa }}</td>
                    <td>{{ $item->inepDocente }}</td>
                    <td>{{ $item->nomeDocente }}</td>
                    <td>{{ \iEducar\Modules\Servidores\Model\FuncaoExercida::getDescription($item->funcaoDocente) }}</td>
                    <td>{{ implode(',', $item->componentes) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endisset

    @isset($record60)
    <h2>Registro 60</h2>

    <table class="table-default">
        <thead>
            <tr>
                <th>INEP da escola</th>
                <th>Nome da escola</th>
                <th>Código da pessoa</th>
                <th>INEP do aluno</th>
                <th>Nome do aluno</th>
                <th>Turma</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record60 as $item)
                <tr>
                    <td>{{ $item->inepEscola }}</td>
                    <td>{{ $item->nomeEscola }}</td>
                    <td>{{ $item->codigoPessoa }}</td>
                    <td>{{ $item->inepAluno }}</td>
                    <td>{{ $item->nomeAluno }}</td>
                    <td>{{ $item->nomeTurma }}</td>
                </tr>
            @endforeach
            @empty($record60)
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endempty
        </tbody>
    </table>
    @endif

@endsection
