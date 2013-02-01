<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matricula Turma');
    $this->processoAp = 578;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
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

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_matricula_lst.php');

    //nova lógica
    if (is_numeric($this->ref_cod_matricula)) {

      if ($this->ref_cod_turma_origem == 'remover-enturmacao-destino')
        $this->removerEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma_destino);
      elseif (! is_numeric($this->ref_cod_turma_origem))
        $this->novaEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma_destino);
      else {
        $this->transferirEnturmacao($this->ref_cod_matricula, 
                                    $this->ref_cod_turma_origem, 
                                    $this->ref_cod_turma_destino);
      }

      header('Location: educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula);
      die();
    }
    else {
      header('Location: /intranet/educar_aluno_lst.php');
      die();
    }
  }

  function novaEnturmacao($matriculaId, $turmaDestinoId) {

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
    if (! $enturmacaoExists) {
      $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                   $turmaDestinoId,
                                                   $this->pessoa_logada, 
                                                   $this->pessoa_logada, 
                                                   NULL,
                                                   NULL, 
                                                   1);
      return $enturmacao->cadastra();
    }
    return false;
  }

  
  function transferirEnturmacao($matriculaId, $turmaOrigemId, $turmaDestinoId) {
    if($this->removerEnturmacao($matriculaId, $turmaOrigemId))
      return $this->novaEnturmacao($matriculaId, $turmaDestinoId);
    return false;
  }


  function removerEnturmacao($matriculaId, $turmaId) {
    $sequencialEnturmacao = $this->getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId);
    $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                 $turmaId,
                                                 $this->pessoa_logada, 
                                                 NULL, 
                                                 NULL,
                                                 NULL, 
                                                 0,
                                                 NULL,
                                                 $sequencialEnturmacao);

    return $enturmacao->edita();
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
