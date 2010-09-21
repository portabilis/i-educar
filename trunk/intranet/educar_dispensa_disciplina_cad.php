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
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Dispensa Componente Curricular');
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

  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_tipo_dispensa;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $observacao;

  var $ref_cod_matricula;
  var $ref_cod_turma;
  var $ref_cod_serie;
  var $ref_cod_disciplina;
  var $ref_sequencial;
  var $ref_cod_instituicao;
  var $ref_cod_escola;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
    $this->ref_cod_matricula  = $_GET['ref_cod_matricula'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
      'educar_dispensa_disciplina_lst.php?ref_ref_cod_matricula=' . $this->ref_cod_matricula);

    if (is_numeric($this->ref_cod_matricula)) {
      $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula, NULL,
         NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      $det_matricula = $obj_matricula->detalhe();

      if (!$det_matricula) {
        header('Location: educar_matricula_lst.php');
        die();
      }

      $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
      $this->ref_cod_serie  = $det_matricula['ref_ref_cod_serie'];
    }
    else {
      header('Location: educar_matricula_lst.php');
      die();
    }

    if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
      is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
    ) {
      $obj = new clsPmieducarDispensaDisciplina($this->ref_cod_matricula,
        $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina);

      $registro  = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val)   {
          $this->$campo = $val;
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = $retorno == 'Editar' ?
      sprintf('educar_dispensa_disciplina_det.php?ref_cod_matricula=%d&ref_cod_serie=%d&ref_cod_escola=%d&ref_cod_disciplina=%d',
        $registro['ref_cod_matricula'], $registro['ref_cod_serie'],
        $registro['ref_cod_escola'], $registro['ref_cod_disciplina']) :
      'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    /**
     * Busca dados da matricula
     */
    $obj_ref_cod_matricula = new clsPmieducarMatricula();
    $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

    $obj_aluno = new clsPmieducarAluno();
    $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1));

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, 1);

    $det_escola = $obj_escola->detalhe();
    $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
    $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
       NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie, NULL,
       $this->ref_cod_escola);

    if (is_array($lst_matricula_turma)) {
      $det = array_shift($lst_matricula_turma);
      $this->ref_cod_turma  = $det['ref_cod_turma'];
      $this->ref_sequencial = $det['sequencial'];
    }

    $this->campoRotulo('nm_aluno', 'Nome do Aluno', $det_aluno['nome_aluno']);

    if (!isset($this->ref_cod_turma)) {
      $this->mensagem = 'Para dispensar um aluno de um componente curricular, é necessário que este esteja enturmado.';
      return;
    }

    // primary keys
    $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
    $this->campoOculto('ref_cod_serie', $this->ref_cod_serie);
    $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

    $opcoes = array('' => 'Selecione');

    // Seleciona os componentes curriculares da turma
    try {
      $componentes = App_Model_IedFinder::getComponentesTurma($this->ref_cod_serie,
        $this->ref_cod_escola, $this->ref_cod_turma);
    }
    catch (App_Model_Exception $e) {
      $this->mensagem = $e->getMessage();
      return;
    }

    foreach ($componentes as $componente) {
      $opcoes[$componente->id] = $componente->nome;
    }

    if ($this->ref_cod_disciplina) {
      $this->campoRotulo('nm_disciplina', 'Disciplina', $opcoes[$this->ref_cod_disciplina]);
      $this->campoOculto('ref_cod_disciplina', $this->ref_cod_disciplina);
    }
    else {
      $this->campoLista('ref_cod_disciplina', 'Disciplina', $opcoes,
        $this->ref_cod_disciplina);
    }

    $opcoes = array('' => 'Selecione');

    $objTemp = new clsPmieducarTipoDispensa();

    if ($this->ref_cod_instituicao) {
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1, $this->ref_cod_instituicao);
    }
    else {
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
    }

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $opcoes[$registro['cod_tipo_dispensa']] = $registro['nm_tipo'];
      }
    }

    $this->campoLista('ref_cod_tipo_dispensa', 'Tipo Dispensa', $opcoes,
      $this->ref_cod_tipo_dispensa);

    $this->campoMemo('observacao', 'Observação', $this->observacao, 60, 10, FALSE);
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
      'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

    $sql = 'SELECT MAX(cod_dispensa) + 1 FROM pmieducar.dispensa_disciplina';
    $db  = new clsBanco();
    $max_cod_dispensa = $db->CampoUnico($sql);

    // Caso não exista nenhuma dispensa, atribui o código 1, tabela não utiliza sequences
    $max_cod_dispensa = $max_cod_dispensa > 0 ? $max_cod_dispensa : 1;

    $obj = new clsPmieducarDispensaDisciplina($this->ref_cod_matricula,
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina, NULL,
      $this->pessoa_logada, $this->ref_cod_tipo_dispensa, NULL, NULL, 1,
      $this->observacao, $max_cod_dispensa);

    if ($obj->existe()) {
      $obj = new clsPmieducarDispensaDisciplina($this->ref_cod_matricula,
        $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina,
        $this->pessoa_logada, NULL, $this->ref_cod_tipo_dispensa, NULL, NULL, 1,
        $this->observacao);

      $obj->edita();
      header('Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
      die();
    }

    $cadastrou = $obj->cadastra();
    if ($cadastrou) {
      $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
      header('Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
      die();
    }

    $this->mensagem = 'Cadastro não realizado.<br />';
    echo "<!--\nErro ao cadastrar clsPmieducarDispensaDisciplina\nvalores obrigatorios\n is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) && is_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_tipo_dispensa )\n-->";
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
      'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

    $obj = new clsPmieducarDispensaDisciplina($this->ref_cod_matricula,
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina,
      $this->pessoa_logada, NULL, $this->ref_cod_tipo_dispensa, NULL, NULL, 1,
      $this->observacao);

    $editou = $obj->edita();
    if ($editou) {
      $this->mensagem .= 'Edição efetuada com sucesso.<br />';
      header('Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
      die();
    }

    $this->mensagem = 'Edição não realizada.<br />';
    echo "<!--\nErro ao editar clsPmieducarDispensaDisciplina\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) && is_numeric( $this->pessoa_logada ) )\n-->";
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7,
      'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

    $obj = new clsPmieducarDispensaDisciplina($this->ref_cod_matricula,
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina,
      $this->pessoa_logada, null, $this->ref_cod_tipo_dispensa, NULL, NULL, 0,
      $this->observacao);

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
      header('Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
      die();
    }

    $this->mensagem = 'Exclusão não realizada.<br />';
    echo "<!--\nErro ao excluir clsPmieducarDispensaDisciplina\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) && is_numeric( $this->pessoa_logada ) )\n-->";
    return FALSE;
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