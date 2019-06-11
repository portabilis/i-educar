@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
    <form id="formcadastro" method="post">
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
                        <input class="obrigatorio" type="text" name="ano" id="ano" value="2019" size="4" maxlength="4">
                    </td>
                </tr>
                <tr>
                    <td>Instituição<span class="campo_obrigatorio">*</span></td>
                    <td>
                        <select onchange="" class="obrigatorio" name="ref_cod_instituicao" id="ref_cod_instituicao" style="width: 314px;">
                            <option value="">Selecione uma instituição</option>
                            <option value="1">PREFEITURA MUNICIPAL DE AVELINO LOPES</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Escola<span class="campo_obrigatorio">*</span></td>
                    <td>
                        <select onchange="" class="obrigatorio" style="width: 314px;">
                            <option value="">Selecione uma escola</option>
                            <option value="1">PREFEITURA MUNICIPAL DE AVELINO LOPES</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Registro<span class="campo_obrigatorio">*</span></td>
                    <td>
                        <select onchange="" class="obrigatorio" style="width: 314px;">
                            <option value="">Selecione um registro</option>
                            <option value="1">Registro 20</option>
                            <option value="1">Registro 40</option>
                            <option value="1">Registro 50</option>
                            <option value="1">Registro 60</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Data de referência do Censo Escolar</td>
                    <td>
                        29/05/2019
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <div style="height: 30px;"></div>

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
            @if(false)
                <tr>
                    <td colspan="5">Nenhum registro encontrado.</td>
                </tr>
            @endif
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>Turminha</td>
                <td>Regular</td>
                <td>07:30 às 12:00</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>Turminha</td>
                <td>Regular</td>
                <td>07:30 às 12:00</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>Turminha</td>
                <td>Regular</td>
                <td>07:30 às 12:00</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>Turminha</td>
                <td>Regular</td>
                <td>07:30 às 12:00</td>
            </tr>
        </tbody>
    </table>

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
            @if(false)
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endif
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
        </tbody>
    </table>

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
            @if(false)
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endif
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
                <td>Efetivo</td>
            </tr>
        </tbody>
    </table>

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
            @if(false)
                <tr>
                    <td colspan="7">Nenhum registro encontrado.</td>
                </tr>
            @endif
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Escolinha</td>
                <td>123</td>
                <td>543</td>
                <td>Eder</td>
                <td>Diretor</td>
            </tr>
        </tbody>
    </table>

@endsection
