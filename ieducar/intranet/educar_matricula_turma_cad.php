<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matricula Turma');
    $this->processoAp = 578;
    $this->addEstilo("localizacaoSistema");
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_matricula;

  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  var $ref_cod_turma_origem;
  var $ref_cod_turma_destino;
  var $ref_cod_curso;
  var $data_enturmacao;

  var $sequencial;

  function Inicializar()
  {
    $retorno = "Novo";
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if (! $_POST) {
      header('Location: educar_matricula_lst.php');
      die;
    }

    foreach ($_POST as $key =>$value) {
      $this->$key = $value;
    }

    $this->data_enturmacao = Portabilis_Date_Utils::brToPgSQL($this->data_enturmacao);

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_matricula_lst.php');

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php" => "Escola",
         "" => "Enturma&ccedil;&atilde;o da matr&iacute;cula"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    //nova lógica
    $retorno = false;
    if (is_numeric($this->ref_cod_matricula)) {
      if ($this->ref_cod_turma_origem == 'remover-enturmacao-destino') {
        $retorno = $this->removerEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma_destino);
      } elseif (! is_numeric($this->ref_cod_turma_origem)) {
        $retorno = $this->novaEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma_destino);
      } else {
        $retorno = $this->transferirEnturmacao(
          $this->ref_cod_matricula,
          $this->ref_cod_turma_origem,
          $this->ref_cod_turma_destino
        );
      }
      if (!$retorno) {
          $alert = sprintf('
                <script type="text/javascript">
                    window.alert("%s");
                    window.location.href= "./educar_matricula_det.php?cod_matricula=%u";
                </script>', $this->mensagem, $this->ref_cod_matricula);
          echo $alert;
      } else {
        header('Location: educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula);
        die();
      }
    }
    else {
      header('Location: /intranet/educar_aluno_lst.php');
      die();
    }
  }

  function novaEnturmacao($matriculaId, $turmaDestinoId) {
    if (!$this->validaDataEnturmacao($matriculaId, $turmaDestinoId)) {
        return false;
    }

    $enturmacaoExists = new clsPmieducarMatriculaTurma();
    $enturmacaoExists = $enturmacaoExists->lista($matriculaId,
                                                 $turmaDestinoId,
                                                 NULL,
                                                 NULL,
                                                 NULL,
                                                 NULL,
                                                 NULL,
                                                 NULL,
                                                 1);

    $enturmacaoExists = is_array($enturmacaoExists) && count($enturmacaoExists) > 0;

    if (!$enturmacaoExists) {
      $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                   $turmaDestinoId,
                                                   $this->pessoa_logada,
                                                   $this->pessoa_logada,
                                                   NULL,
                                                   NULL,
                                                   1);

      $enturmacao->data_enturmacao = $this->data_enturmacao;
      $this->atualizaUltimaEnturmacao($matriculaId);
      return $enturmacao->cadastra();
    }
    return false;
  }

  public function validaDataEnturmacao($matriculaId, $turmaDestinoId, $transferir = false)
  {
    $dataObj = new \DateTime($this->data_enturmacao . ' 23:59:59');
    $matriculaObj = new clsPmieducarMatricula();
    $enturmacaoObj = new clsPmieducarMatriculaTurma();
    $dataAnoLetivoInicio = $matriculaObj->pegaDataAnoLetivoInicio($turmaDestinoId);
    $dataAnoLetivoFim = $matriculaObj->pegaDataAnoLetivoFim($turmaDestinoId);
    $exclusaoEnturmacao = $enturmacaoObj->getDataExclusaoUltimaEnturmacao($matriculaId);
    $maiorDataEnturmacao = $enturmacaoObj->getMaiorDataEnturmacao($matriculaId);
    $dataSaidaDaTurma = !empty($exclusaoEnturmacao)
        ? new \DateTime($exclusaoEnturmacao)
        : null;

    $maiorDataEnturmacao = !empty($maiorDataEnturmacao)
        ? new \DateTime($maiorDataEnturmacao)
        : null;

    if ($dataObj > $dataAnoLetivoFim) {
        $this->mensagem = 'Não foi possível enturmar, data de enturmação maior que data do fim do ano letivo.';
        return false;
    }

    if ($transferir && !empty($maiorDataEnturmacao) && $dataObj < $maiorDataEnturmacao ) {
        $this->mensagem = 'Não foi possível enturmar, data de enturmação menor que data de entrada da última enturmação.';
        return false;
    } elseif ($dataSaidaDaTurma !== null && $dataObj < $dataSaidaDaTurma) {
        $this->mensagem = 'Não foi possível enturmar, data de enturmação menor que data de saída da última enturmação.';
        return false;
    } elseif ($dataObj < $dataAnoLetivoInicio) {
        $this->mensagem = 'Não foi possível enturmar, data de enturmação menor que data do início do ano letivo.';
        return false;
    }

    return true;
  }

  function transferirEnturmacao($matriculaId, $turmaOrigemId, $turmaDestinoId) {
    if (!$this->validaDataEnturmacao($matriculaId, $turmaDestinoId, true)) {
        return false;
    }

    if($this->removerEnturmacao($matriculaId, $turmaOrigemId, TRUE)) {
      return $this->novaEnturmacao($matriculaId, $turmaDestinoId);
    }

    return false;
  }

    /**
     * Retorna a data base de remanejamento para a instituição.
     *
     * @param int $instituicao
     *
     * @return string|null
     */
  public function getDataBaseRemanejamento($instituicao)
  {
      $instituicao = new clsPmieducarInstituicao($instituicao);

      $instituicao = $instituicao->detalhe();

      return $instituicao['data_base_remanejamento'];
  }

  function removerEnturmacao($matriculaId, $turmaId, $remanejado = FALSE) {

    if (!$this->data_enturmacao) {
      $this->data_enturmacao = date('Y-m-d');
    }

    $sequencialEnturmacao = $this->getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId);
    $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                 $turmaId,
                                                 $this->pessoa_logada,
                                                 NULL,
                                                 NULL,
                                                 $this->data_enturmacao,
                                                 0,
                                                 NULL,
                                                 $sequencialEnturmacao);
    $detEnturmacao = $enturmacao->detalhe();
    $detEnturmacao = $detEnturmacao['data_enturmacao'];
    $enturmacao->data_enturmacao = $detEnturmacao;

    $instituicao = $enturmacao->getInstituicao($matriculaId);
    $instituicao = new clsPmieducarInstituicao($instituicao);
    $det_instituicao = $instituicao->detalhe();
    $data_base_remanejamento = $det_instituicao['data_base_remanejamento'];
    if (($data_base_remanejamento > $this->data_enturmacao) || (! $data_base_remanejamento)){
      $enturmacao->removerSequencial = TRUE;
    }

    if ($enturmacao->edita()){
      if ($remanejado) {
        $enturmacao->marcaAlunoRemanejado($this->data_enturmacao);
      }
      return true;
    }else
      return false;
  }


  function getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId) {
    $db = new clsBanco();
    $sql = 'select coalesce(max(sequencial), 1) from pmieducar.matricula_turma where ativo = 1 and ref_cod_matricula = $1 and ref_cod_turma = $2';

    if ($db->execPreparedQuery($sql, array($matriculaId, $turmaId)) != false) {
      $db->ProximoRegistro();
      $sequencial = $db->Tupla();
      return $sequencial[0];
    }
    return 1;
  }

  function atualizaUltimaEnturmacao($matriculaId)
  {
    $objMatriculaTurma = new clsPmieducarMatriculaTurma($matriculaId);
    $ultima_turma = $objMatriculaTurma->getUltimaTurmaEnturmacao($matriculaId);
    $sequencial = $objMatriculaTurma->getMaxSequencialEnturmacao($matriculaId);
    $lst_ativo = $objMatriculaTurma->lista($matriculaId, $ultima_turma, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $sequencial);

    $ativo = $lst_ativo[0]['ativo'];
    $data_exclusao = $lst_ativo[0]['data_exclusao'];

    $dataBaseRemanejamento = $this->getDataBaseRemanejamento(
        $objMatriculaTurma->getInstituicao()
    );

    $marcarAlunoComoRemanejado = is_null($dataBaseRemanejamento) || strtotime($dataBaseRemanejamento) < strtotime(date('Y-m-d'));

    if ($sequencial >= 1 && $marcarAlunoComoRemanejado) {
        $remanejado = TRUE;
        $enturmacao = new clsPmieducarMatriculaTurma(
            $matriculaId,
            $ultima_turma,
            $this->pessoa_logada,
            $this->pessoa_logada,
            NULL,
            $data_exclusao,
            $ativo,
            NULL,
            $sequencial,
            NULL,
            NULL,
            NULL,
            $remanejado);
            return $enturmacao->edita();
        }
        return false;
  }



  function Gerar()
  {
    die;
  }

  function Novo()
  {
  }

  function Editar()
  {
  }

  function Excluir()
  {
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
