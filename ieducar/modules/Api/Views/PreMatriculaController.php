<?php

use App\Models\City;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use Illuminate\Support\Str;

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'include/pmieducar/geral.inc.php';

class PreMatriculaController extends ApiCoreController
{
    use LegacyAddressingFields;

    protected function canHomologarPreMatricula()
    {
        return (
            $this->validatesPresenceOf('ano_letivo') &&
            $this->validatesPresenceOf('curso_id') &&
            $this->validatesPresenceOf('serie_id') &&
            $this->validatesPresenceOf('escola_id') &&
            $this->validatesPresenceOf('turma_id') &&
            $this->validatesPresenceOf('nome_aluno') &&
            $this->validatesPresenceOf('data_nasc_aluno') &&
            $this->validatesPresenceOf('sexo_aluno') &&
            $this->validatesPresenceOf('cep') &&
            $this->validatesPresenceOf('rua') &&
            $this->validatesPresenceOf('numero') &&
            $this->validatesPresenceOf('bairro') &&
            $this->validatesPresenceOf('cidade') &&
            $this->validatesPresenceOf('estado') &&
            $this->validatesPresenceOf('pais') &&
            $this->validatesPresenceOf('matricula_id')
        );
    }

    protected function homologarPreMatricula()
    {
        if ($this->canHomologarPreMatricula()) {
            // Dados da matrícula
            $anoLetivo = $this->getRequest()->ano_letivo;
            $cursoId = $this->getRequest()->curso_id;
            $serieId = $this->getRequest()->serie_id;
            $escolaId = $this->getRequest()->escola_id;
            $turmaId = $this->getRequest()->turma_id;
            $matriculaId = $this->getRequest()->matricula_id;

            // Dados do aluno
            $nomeAluno = Portabilis_String_utils::toLatin1($this->getRequest()->nome_aluno);
            $dataNascAluno = $this->getRequest()->data_nasc_aluno;
            $deficiencias = $this->getRequest()->deficiencias;
            $sexoAluno = $this->getRequest()->sexo_aluno;
            $alunoIdParametro = $this->getRequest()->aluno_id;

            // Dados responsaveis
            $nomeMae = Portabilis_String_utils::toLatin1($this->getRequest()->nome_mae);
            $cpfMae = $this->getRequest()->cpf_mae;

            $nomeResponsavel = Portabilis_String_utils::toLatin1($this->getRequest()->nome_responsavel);
            $cpfResponsavel = $this->getRequest()->cpf_responsavel;
            $telefoneResponsavel = $this->getRequest()->telefone_responsavel;

            // Dados do endereço
            $cep = $this->getRequest()->cep;
            $rua = Portabilis_String_utils::toLatin1($this->getRequest()->rua);
            $numero = $this->getRequest()->numero;
            $complemento = Portabilis_String_utils::toLatin1($this->getRequest()->complemento);
            $bairro = Portabilis_String_utils::toLatin1($this->getRequest()->bairro);
            $cidade = Portabilis_String_utils::toLatin1($this->getRequest()->cidade);
            $estado = Portabilis_String_utils::toLatin1($this->getRequest()->estado);
            $pais = Portabilis_String_utils::toLatin1($this->getRequest()->pais);

            $this->atualizaPreMatricula($matriculaId, $escolaId);

            $obj_m = new clsPmieducarMatricula($matriculaId);

            $det_m = $obj_m->detalhe();
            $alunoIdMatricula = $det_m['ref_cod_aluno'];

            if ($det_m['aprovado'] != 11) {
                $this->messenger->append('Matrícula já homologada.');

                return ['cod_matricula' => 0];
            }

            // $this->messenger->append("max alunos turma: " . $this->_maxAlunosTurma($turmaId) . "alunos matriculados na turma: " . $this->_alunosMatriculadosTurma($turmaId));
            if ($this->_maxAlunosTurma($turmaId) <= $this->_alunosMatriculadosTurma($turmaId)) {
                $this->messenger->append('Aparentemente não existem vagas disponíveis para a seleção informada. Altere a seleção e tente novamente.');

                return ['cod_matricula' => 0];
            }

            $obj_a = null;
            $aluno_id = null;

            if ($alunoIdParametro) {
                $aluno_id = $alunoIdParametro;
                $obj_a = new clsPmieducarAluno($alunoIdParametro);

                if ($obj_a->detalhe()) {
                    $obj_m = new clsPmieducarMatricula($matriculaId);
                    $obj_m->ref_cod_aluno = $alunoIdParametro;
                    $obj_m->edita();

                    $obj_a->ativo = 1;
                    $obj_a->edita();

                    if ($alunoIdParametro != $alunoIdMatricula) {
                        $this->excluirInformacoesAluno($alunoIdMatricula);
                    }
                }
            } else {
                $aluno_id = $alunoIdMatricula;
                $obj_a = new clsPmieducarAluno($alunoIdMatricula);
            }

            $det_a = $obj_a->detalhe();
            $pessoaAlunoId = $det_a['ref_idpes'];

            $pessoa = new clsPessoa_($pessoaAlunoId);
            $pessoa->nome = addslashes($nomeAluno);
            $pessoa->tipo = 'F';
            $pessoa->edita();

            $pessoaMaeId = null;
            $pessoaResponsavelId = null;

            $pessoaFisicaAluno = new clsFisica($pessoaAlunoId);
            $pessoaFisicaAluno_det = $pessoaFisicaAluno->detalhe();

            $pessoaMaeId = $pessoaFisicaAluno_det['idpes_mae'];
            $pessoaResponsavelId = $pessoaFisicaAluno_det['idpes_responsavel'];
            $maeIsResponsavel = ($pessoaMaeId == $pessoaResponsavelId);

            if (is_numeric($pessoaMaeId)) {
                $pessoaMaeAluno = new clsPessoa_($pessoaMaeId);
                $pessoaMaeAluno->nome = $nomeMae;
                $pessoaMaeAluno->edita();

                $pessoaFisicaMaeAluno = new clsFisica($pessoaMaeId);
                $pessoaFisicaMaeAluno->cpf = $cpfMae;
                $pessoaFisicaMaeAluno->idpes_rev = 1;
                $pessoaFisicaMaeAluno->edita();
            } elseif (is_numeric($cpfMae)) {
                $pessoaMaeId = $this->createOrUpdatePessoaResponsavel($cpfMae, $nomeMae, $telefoneMae);
                $this->createOrUpdatePessoaFisicaResponsavel($pessoaMaeId, $cpfMae);
            }

            if (!$maeIsResponsavel) {
                if (is_numeric($pessoaResponsavelId)) {
                    $pessoaResponsavelAluno = new clsPessoa_($pessoaResponsavelId);
                    $pessoaResponsavelAluno->nome = $nomeResponsavel;
                    $pessoaResponsavelAluno->edita();

                    $pessoaFisicaResponsavelAluno = new clsFisica($pessoaResponsavelId);
                    $pessoaFisicaResponsavelAluno->cpf = $cpfResponsavel;
                    $pessoaFisicaResponsavelAluno->idpes_rev = 1;
                    $pessoaFisicaResponsavelAluno->edita();
                } elseif (is_numeric($cpfResponsavel)) {
                    $pessoaResponsavelId = $this->createOrUpdatePessoaResponsavel($cpfResponsavel, $nomeResponsavel, $telefoneResponsavel);
                    $this->createOrUpdatePessoaFisicaResponsavel($pessoaResponsavelId, $cpfResponsavel);
                }
            }

            $this->createOrUpdatePessoaFisica($pessoaAlunoId, $pessoaResponsavelId, $pessoaMaeId, $dataNascAluno, $sexoAluno);

            $alunoId = $this->createOrUpdateAluno($pessoaAlunoId, 1);

            $this->updateDeficiencias($pessoaAlunoId, $deficiencias);
            $this->createOrUpdateEndereco($pessoaAlunoId, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $pais);

            // $this->messenger->append("escola:" . $escolaId . " serie:" . $serieId . " anoletivo:" . $anoLetivo .
            // " curso: " . $cursoId . " aluno:" . $alunoId . " turma: " . $turmaId . "matricula: " . $matriculaId);

            return ['cod_matricula' => $this->enturmaPreMatricula($aluno_id, $turmaId, $matriculaId, $maeIsResponsavel)];
        }
    }
    protected function canRegistrarPreMatricula()
    {
        return (
            $this->validatesPresenceOf('ano_letivo') &&
            $this->validatesPresenceOf('curso_id') &&
            $this->validatesPresenceOf('serie_id') &&
            $this->validatesPresenceOf('escola_id') &&
            $this->validatesPresenceOf('turno_id') &&
            $this->validatesPresenceOf('nome_aluno') &&
            $this->validatesPresenceOf('data_nasc_aluno') &&
            $this->validatesPresenceOf('sexo_aluno')
        );
    }

    protected function registrarPreMatricula()
    {
        if ($this->canRegistrarPreMatricula()) {
            // Dados da matrícula
            $anoLetivo = $this->getRequest()->ano_letivo;
            $cursoId = $this->getRequest()->curso_id;
            $serieId = $this->getRequest()->serie_id;
            $escolaId = $this->getRequest()->escola_id;
            $turnoId = $this->getRequest()->turno_id;

            $qtdFila = $this->_getQtdAlunosFila($anoLetivo, $escolaId, $cursoId, $serieId, $turnoId);
            $maxAlunoTurno = $this->_getMaxAlunoTurno($anoLetivo, $escolaId, $serieId, $turnoId);
            $qtdMatriculaTurno = $this->_getQtdMatriculaTurno($anoLetivo, $escolaId, $cursoId, $serieId, $turnoId);

            if ($maxAlunoTurno <= $qtdFila + $qtdMatriculaTurno) {
                // $this->messenger->append("Quantidade de reservas: ".$qtdFila.". Máximo de alunos permitido no turno: ".$maxAlunoTurno.". Quantidade de alunos matriculados no turno: ".$qtdMatriculaTurno);
                $this->messenger->append('Aparentemente não existem vagas disponíveis para a seleção informada. Altere a seleção e tente novamente.');

                return ['cod_matricula' => 0];
            }

            // Dados do aluno
            $nomeAluno = Portabilis_String_utils::toLatin1($this->getRequest()->nome_aluno);
            $dataNascAluno = $this->getRequest()->data_nasc_aluno;
            $deficiencias = $this->getRequest()->deficiencias;
            $sexoAluno = $this->getRequest()->sexo_aluno;

            // Dados responsaveis
            $nomeMae = Portabilis_String_utils::toLatin1($this->getRequest()->nome_mae);
            $cpfMae = $this->getRequest()->cpf_mae;
            $telefoneMae = $this->getRequest()->telefone_mae;

            $nomeResponsavel = Portabilis_String_utils::toLatin1($this->getRequest()->nome_responsavel);
            $cpfResponsavel = $this->getRequest()->cpf_responsavel;
            $telefoneResponsavel = $this->getRequest()->telefone_responsavel;

            $pessoaAlunoId = $this->createPessoa($nomeAluno);
            $pessoaMaeId = null;
            $pessoaResponsavelId = null;

            if (is_numeric($cpfMae)) {
                $pessoaMaeId = $this->createOrGetPessoaResponsavel($cpfMae, $nomeMae, $telefoneMae);
                $this->createOrUpdatePessoaFisicaResponsavel($pessoaMaeId, $cpfMae);
            }

            if (is_numeric($cpfResponsavel)) {
                $pessoaResponsavelId = $this->createOrGetPessoaResponsavel($cpfResponsavel, $nomeResponsavel, $telefoneResponsavel);
                $this->createOrUpdatePessoaFisicaResponsavel($pessoaResponsavelId, $cpfResponsavel);
            }

            $this->createOrUpdatePessoaFisica($pessoaAlunoId, $pessoaResponsavelId, $pessoaMaeId, $dataNascAluno, $sexoAluno);

            $alunoId = $this->createOrUpdateAluno($pessoaAlunoId, 0);

            if (is_array($deficiencias)) {
                $this->updateDeficiencias($pessoaAlunoId, $deficiencias);
            }

            return ['cod_matricula' => $this->cadastraPreMatricula($escolaId, $serieId, $anoLetivo, $cursoId, $alunoId, $turnoId)];
        }
    }

    public function _getMaxAlunoTurno($ano, $escolaId, $serieId, $turnoId)
    {
        $obj_t = new clsPmieducarTurma();

        $lista_t = $obj_t->lista(
            $int_cod_turma = null,
            $int_ref_usuario_exc = null,
            $int_ref_usuario_cad = null,
            $int_ref_ref_cod_serie = $serieId,
            $int_ref_ref_cod_escola = $escolaId,
            $int_ref_cod_infra_predio_comodo = null,
            $str_nm_turma = null,
            $str_sgl_turma = null,
            $int_max_aluno = null,
            $int_multiseriada = null,
            $date_data_cadastro_ini = null,
            $date_data_cadastro_fim = null,
            $date_data_exclusao_ini = null,
            $date_data_exclusao_fim = null,
            $int_ativo = null,
            $int_ref_cod_turma_tipo = null,
            $time_hora_inicial_ini = null,
            $time_hora_inicial_fim = null,
            $time_hora_final_ini = null,
            $time_hora_final_fim = null,
            $time_hora_inicio_intervalo_ini = null,
            $time_hora_inicio_intervalo_fim = null,
            $time_hora_fim_intervalo_ini = null,
            $time_hora_fim_intervalo_fim = null,
            $int_ref_cod_curso = null,
            $int_ref_cod_instituicao = null,
            $int_ref_cod_regente = null,
            $int_ref_cod_instituicao_regente = null,
            $int_ref_ref_cod_escola_mult = null,
            $int_ref_ref_cod_serie_mult = null,
            $int_qtd_min_alunos_matriculados = null,
            $bool_verifica_serie_multiseriada = false,
            $bool_tem_alunos_aguardando_nota = null,
            $visivel = null,
            $turma_turno_id = $turnoId,
            $tipo_boletim = null,
            $ano = $ano,
            $somenteAnoLetivoEmAndamento = false
        );

        $max_aluno_turmas = 0;

        foreach ($lista_t as $reg) {
            $max_aluno_turmas += $reg['max_aluno'];
        }

        return $max_aluno_turmas;
    }

    public function _getQtdAlunosFila($ano, $escolaId, $cursoId, $serieId, $turnoId)
    {
        $sql = 'SELECT count(1) as qtd
              FROM pmieducar.matricula
              WHERE ano = $1
              AND ref_ref_cod_escola = $2
              AND ref_cod_curso = $3
              AND ref_ref_cod_serie = $4
              AND turno_pre_matricula = $5
              AND aprovado = 11 ';

        return (int) Portabilis_Utils_Database::selectField($sql, [$ano, $escolaId, $cursoId, $serieId, $turnoId]);
    }

    public function _getQtdMatriculaTurno($ano, $escolaId, $cursoId, $serieId, $turnoId)
    {
        $obj_mt = new clsPmieducarMatriculaTurma();

        return (int) count($obj_mt->lista(
            $int_ref_cod_matricula = null,
            $int_ref_cod_turma = null,
            $int_ref_usuario_exc = null,
            $int_ref_usuario_cad = null,
            $date_data_cadastro_ini = null,
            $date_data_cadastro_fim = null,
            $date_data_exclusao_ini = null,
            $date_data_exclusao_fim = null,
            $int_ativo = 1,
            $int_ref_cod_serie = $serieId,
            $int_ref_cod_curso = $cursoId,
            $int_ref_cod_escola = $escolaId,
            $int_ref_cod_instituicao = null,
            $int_ref_cod_aluno = null,
            $mes = null,
            $aprovado = null,
            $mes_menor_que = null,
            $int_sequencial = null,
            $int_ano_matricula = null,
            $tem_avaliacao = null,
            $bool_get_nome_aluno = false,
            $bool_aprovados_reprovados = null,
            $int_ultima_matricula = null,
            $bool_matricula_ativo = null,
            $bool_escola_andamento = false,
            $mes_matricula_inicial = false,
            $get_serie_mult = false,
            $int_ref_cod_serie_mult = null,
            $int_semestre = null,
            $pegar_ano_em_andamento = false,
            $parar=null,
            $diario = false,
            $int_turma_turno_id = $turnoId,
            $int_ano_turma = $ano
        ));
    }

    protected function cadastraPreMatricula($escolaId, $serieId, $anoLetivo, $cursoId, $alunoId, $turnoId)
    {
        $obj = new clsPmieducarMatricula(
            null,
            null,
            $escolaId,
            $serieId,
            null,
            1,
            $alunoId,
            11,
            null,
            null,
            1,
            $anoLetivo,
            1,
            null,
            null,
            null,
            null,
            $cursoId,
            null,
            null,
            date('Y-m-d')
        );

        $obj->turno_pre_matricula = $turnoId;

        $matriculaId = $obj->cadastra();

        return $matriculaId;
    }

    protected function atualizaPreMatricula($matriculaId, $escolaId)
    {
        $preMatricula = new clsPmieducarMatricula($matriculaId);
        $preMatricula->ref_ref_cod_escola = $escolaId;
        $preMatricula->edita();

        return $matriculaId;
    }

    protected function enturmaPreMatricula($alunoId, $turmaId, $matriculaId, $maeIsResponsavel)
    {
        // $this->messenger->append($escolaId, $serieId, $anoLetivo, $cursoId, $alunoId, $turmaId, $matriculaId);

        $obj_a = new clsPmieducarAluno($alunoId);
        $obj_a->ativo = 1;

        if ($maeIsResponsavel) {
            $obj_a->tipo_responsavel = 'm';
        }

        $obj_a->edita();

        $obj_m = new clsPmieducarMatricula($matriculaId);
        $obj_m->aprovado = 3;
        $obj_m->ativo = 1;
        $obj_m->edita();

        $enturmacao = new clsPmieducarMatriculaTurma(
            $matriculaId,
            $turmaId,
            1,
            1,
            null,
            null,
            1
        );

        $enturmacao->data_enturmacao = date('Y-m-d');
        $enturmacao->cadastra();

        return $matriculaId;
    }

    protected function updateDeficiencias($pessoaId, $deficiencias)
    {
        $sql = 'delete from cadastro.fisica_deficiencia where ref_idpes = $1';
        $this->fetchPreparedQuery($sql, $pessoaId, false);

        foreach ($deficiencias as $id) {
            if (!empty($id)) {
                $deficiencia = new clsCadastroFisicaDeficiencia($pessoaId, $id);
                $deficiencia->cadastra();
            }
        }
    }

    protected function createPessoa($nome)
    {
        $pessoa = new clsPessoa_();

        $pessoa->nome = addslashes($nome);
        $pessoa->tipo = 'F';

        return $pessoa->cadastra();
    }

    protected function createOrUpdatePessoaResponsavel($cpf, $nome, $telefone)
    {
        $pessoa = new clsPessoa_();
        $pessoa->nome = addslashes($nome);
        $pessoa->idpes_cad = 1;
        $pessoa->idpes_rev = 1;

        $sql = 'select idpes from cadastro.fisica WHERE cpf = $1 limit 1';
        $pessoaId = Portabilis_Utils_Database::selectField($sql, $cpf);

        if (! $pessoaId || !$pessoaId > 0) {
            $pessoa->tipo = 'F';
            $pessoaId = $pessoa->cadastra();
        } else {
            $pessoa->idpes = $pessoaId;
            $pessoa->data_rev = date('Y-m-d H:i:s', time());
            $pessoa->edita();
        }

        $telefone = str_replace(["-", "(", ")", " "], "", $telefone);

        $ddd_telefone = substr($telefone, 0, 2);
        $telefone = substr($telefone, 2);

        $telefoneObj = new clsPessoaTelefone($pessoaId, 1, $telefone, $ddd_telefone);

        if ($telefoneObj->detalhe()) {
            $results = $telefoneObj->edita();
        }
        else {
            $results = $telefoneObj->cadastra();
        }

        return $pessoaId;
    }

    protected function createOrGetPessoaResponsavel($cpf, $nome, $telefone)
    {
        $pessoa = new clsPessoa_();
        $pessoa->nome = addslashes($nome);
        $pessoa->idpes_cad = 1;
        $pessoa->idpes_rev = 1;

        $sql = 'select idpes from cadastro.fisica WHERE cpf = $1 limit 1';
        $pessoaId = Portabilis_Utils_Database::selectField($sql, $cpf);

        if (!$pessoaId || !$pessoaId > 0) {
            $pessoa->tipo = 'F';
            $pessoaId = $pessoa->cadastra();
        }

        $telefone = str_replace(["-", "(", ")", " "], "", $telefone);

        $ddd_telefone = substr($telefone, 0, 2);
        $telefone = substr($telefone, 2);

        $telefoneObj = new clsPessoaTelefone($pessoaId, 1, $telefone, $ddd_telefone);

        if ($telefoneObj->detalhe()) {
            $results = $telefoneObj->edita();
        }
        else {
            $results = $telefoneObj->cadastra();
        }

        return $pessoaId;
    }

    protected function createOrUpdatePessoaFisica($pessoaId, $pessoaResponsavelId, $pessoaMaeId, $dataNascimento, $sexo)
    {
        $fisica = new clsFisica();
        $fisica->idpes = $pessoaId;
        $fisica->data_nasc = $dataNascimento;
        $fisica->idpes_cad = 1;
        $fisica->idpes_rev = 1;
        $fisica->sexo = strtoupper($sexo);

        $sql = 'select 1 from cadastro.fisica WHERE idpes = $1 limit 1';

        if (is_numeric($pessoaResponsavelId)) {
            $fisica->idpes_responsavel = $pessoaResponsavelId;
        } elseif (is_numeric($pessoaMaeId)) {
            $fisica->idpes_mae = $pessoaMaeId;
            $fisica->idpes_responsavel = $pessoaMaeId;
        }

        if (is_numeric($pessoaResponsavelId) && is_numeric($pessoaMaeId)) {
            $fisica->idpes_mae = $pessoaMaeId;
        }

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $fisica->cadastra();
        } else {
            $fisica->edita();
        }
    }

    protected function createOrUpdatePessoaFisicaResponsavel($pessoaId, $cpf)
    {
        $fisica = new clsFisica();
        $fisica->idpes = $pessoaId;
        $fisica->cpf = $cpf;
        $fisica->idpes_cad = 1;
        $fisica->idpes_rev = 1;

        $sql = 'select 1 from cadastro.fisica WHERE idpes = $1 limit 1';

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $fisica->cadastra();
        } else {
            $fisica->edita();
        }
    }

    protected function createOrUpdateAluno($pessoaId, $ativo)
    {
        $aluno = new clsPmieducarAluno();
        $aluno->ref_idpes = $pessoaId;

        $detalhe = $aluno->detalhe();

        if (!$detalhe) {
            $retorno = $aluno->cadastra();
        } else {
            $retorno = $detalhe['cod_aluno'];
        }

        if ($ativo == 0) {
            $aluno = new clsPmieducarAluno($retorno);
            $aluno->ativo = 0;
            $aluno->edita();
        }

        return $retorno;
    }

    protected function _maxAlunosTurma($turmaId)
    {
        $obj_t = new clsPmieducarTurma($turmaId);
        $det_t = $obj_t->detalhe();
        $maxAlunosTurma = $det_t['max_aluno'];

        return $maxAlunosTurma;
    }

    protected function _alunosMatriculadosTurma($turmaId)
    {
        $obj_mt = new clsPmieducarMatriculaTurma($turmaId);

        return count(array_filter(($obj_mt->lista(
            $int_ref_cod_matricula = null,
            $int_ref_cod_turma = $turmaId,
            $int_ref_usuario_exc = null,
            $int_ref_usuario_cad = null,
            $date_data_cadastro_ini = null,
            $date_data_cadastro_fim = null,
            $date_data_exclusao_ini = null,
            $date_data_exclusao_fim = null,
            $int_ativo = 1,
            $int_ref_cod_serie = $this->ref_cod_serie,
            $int_ref_cod_curso = null,
            $int_ref_cod_escola = null,
            $int_ref_cod_instituicao = $this->getRequest()->instituicao_id
        ))));
    }

    protected function canCancelarPreMatricula()
    {
        return $this->validatesExistenceOf('matricula', $this->getRequest()->matricula_id);
    }

    protected function cancelarPreMatricula()
    {
        if ($this->canCancelarPreMatricula()) {
            $matriculaId = $this->getRequest()->matricula_id;

            $alunoId = Portabilis_Utils_Database::selectField('SELECT ref_cod_aluno FROM pmieducar.matricula WHERE cod_matricula = $1', [$matriculaId]);
            $pessoaId = Portabilis_Utils_Database::selectField('SELECT ref_idpes FROM pmieducar.aluno WHERE cod_aluno = $1', [$alunoId]);
            $pessoaMaeId = Portabilis_Utils_Database::selectField('SELECT idpes_mae FROM cadastro.fisica WHERE idpes = $1', [$pessoaId]);
            $pessoaRespId = Portabilis_Utils_Database::selectField('SELECT idpes_responsavel FROM cadastro.fisica WHERE idpes = $1', [$pessoaId]);

            if (is_numeric($matriculaId)) {
                $this->fetchPreparedQuery('DELETE FROM pmieducar.matricula_turma WHERE ref_cod_matricula = $1', [$matriculaId]);
                $this->fetchPreparedQuery('DELETE FROM pmieducar.matricula WHERE cod_matricula = $1', [$matriculaId]);
            }

            if (is_numeric($alunoId)) {
                $this->fetchPreparedQuery('DELETE FROM pmieducar.aluno WHERE cod_aluno = $1', $alunoId);
            }

            if (is_numeric($pessoaId)) {
                $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaId);
                $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaId);
            }

            if (is_numeric($pessoaMaeId)) {
                $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaMaeId);
                $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaMaeId);
            }

            if (is_numeric($pessoaRespId)) {
                $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaRespId);
                $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaRespId);
            }
        }
    }

    protected function createOrUpdateEndereco($pessoaAlunoId, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $pais)
    {
        $city = City::queryFindByName($cidade)->whereHas('state', function ($query) use ($estado) {
            $query->where('abbreviation', Str::upper($estado));
        })->first();

        if (empty($city)) {
            return;
        }

        $this->postal_code = $cep;
        $this->address = $rua;
        $this->number = $numero;
        $this->complement = $complemento;
        $this->neighborhood = $bairro;
        $this->city_id = $city->getKey();

        $this->saveAddress($pessoaAlunoId);
    }

    protected function excluirInformacoesAluno($alunoId)
    {
        $pessoaId = Portabilis_Utils_Database::selectField('SELECT ref_idpes FROM pmieducar.aluno WHERE cod_aluno = $1', [$alunoId]);

        if (is_numeric($pessoaId)) {
            $pessoaMaeId = Portabilis_Utils_Database::selectField('SELECT idpes_mae FROM cadastro.fisica WHERE idpes = $1', [$pessoaId]);
            $pessoaRespId = Portabilis_Utils_Database::selectField('SELECT idpes_responsavel FROM cadastro.fisica WHERE idpes = $1', [$pessoaId]);
        }

        if (is_numeric($alunoId)) {
            $this->fetchPreparedQuery('DELETE FROM pmieducar.matricula WHERE ref_cod_aluno = $1', [$alunoId]);
            $this->fetchPreparedQuery('DELETE FROM pmieducar.matricula_turma WHERE ref_cod_matricula in(SELECT cod_matricula from pmieducar.matricula WHERE ref_cod_aluno = $1)', [$alunoId]);
            $this->fetchPreparedQuery('DELETE FROM pmieducar.aluno WHERE cod_aluno = $1', $alunoId);
        }

        if (is_numeric($pessoaId)) {
            $this->fetchPreparedQuery('DELETE FROM cadastro.fisica_deficiencia WHERE ref_idpes = $1', $pessoaId);
            $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaId);
            $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaId);
        }

        if (is_numeric($pessoaMaeId)) {
            $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaMaeId);
            $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaMaeId);
        }

        if (is_numeric($pessoaRespId)) {
            $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaRespId);
            $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaRespId);
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'registrar-pre-matricula')) {
            $this->appendResponse($this->registrarPreMatricula());
        } elseif ($this->isRequestFor('post', 'homologar-pre-matricula')) {
            $this->appendResponse($this->homologarPreMatricula());
        } elseif ($this->isRequestFor('post', 'cancelar-pre-matricula')) {
            $this->appendResponse($this->cancelarPreMatricula());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
