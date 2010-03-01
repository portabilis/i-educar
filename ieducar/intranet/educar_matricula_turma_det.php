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
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

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
class indice extends clsDetalhe
{
  var $titulo;

  var $ref_cod_matricula;
  var $ref_cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  var $ref_cod_serie;
  var $ref_cod_escola;
  var $ref_cod_turma_origem;
  var $ref_cod_curso;

  var $sequencial;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Matricula Turma - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    foreach ($_POST as $key =>$value) {
      $this->$key = $value;
    }

    $obj_mat_turma = new clsPmieducarMatriculaTurma();
    $det_mat_turma = $obj_mat_turma->lista($this->ref_cod_matricula, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1);

    if ($det_mat_turma){
      $det_mat_turma  = array_shift($det_mat_turma);
      $obj_turma      = new clsPmieducarTurma($det_mat_turma['ref_cod_turma']);
      $det_turma      = $obj_turma->detalhe();
      $this->nm_turma = $det_turma['nm_turma'];

      $this->ref_cod_turma_origem = $det_turma['cod_turma'];
      $this->sequencial = $det_mat_turma['sequencial'];
    }

    $tmp_obj = new clsPmieducarMatriculaTurma( );
    $lista = $tmp_obj->lista(NULL, $this->ref_cod_turma, NULL, NULL, NULL, NULL,
      NULL, NULL, 1);

    $total_alunos = 0;
    if ($lista) {
      $total_alunos = count($lista);
    }

    $tmp_obj  = new clsPmieducarTurma();
    $lst_obj  = $tmp_obj->lista($this->ref_cod_turma);
    $registro = array_shift($lst_obj);

    $this->ref_cod_curso = $registro['ref_cod_curso'];

    if (!$registro || !$_POST) {
      header('Location: educar_matricula_lst.php');
      die();
    }

    // Tipo da turma
    $obj_ref_cod_turma_tipo = new clsPmieducarTurmaTipo($registro['ref_cod_turma_tipo']);
    $det_ref_cod_turma_tipo = $obj_ref_cod_turma_tipo->detalhe();
    $registro['ref_cod_turma_tipo'] = $det_ref_cod_turma_tipo['nm_tipo'];

    // Código da instituição
    $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

    // Nome da escola
    $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

    // Nome do curso
    $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];
    $padrao_ano_escolar = $det_ref_cod_curso['padrao_ano_escolar'];

    // Nome da série
    $obj_ser = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
    $det_ser = $obj_ser->detalhe();
    $registro['ref_ref_cod_serie'] = $det_ser['nm_serie'];

    // Matrícula
    $obj_ref_cod_matricula = new clsPmieducarMatricula();
    $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

    $obj_aluno = new clsPmieducarAluno();
    $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1));

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, 1);
    $det_escola = $obj_escola->detalhe();

    $this->addDetalhe(array('Nome do Aluno', $det_aluno['nome_aluno']));

    $objTemp = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $objTemp->detalhe();

    if ($registro['ref_ref_cod_escola']) {
      $this->addDetalhe(array('Escola', $registro['ref_ref_cod_escola']));
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('S&eacute;rie', $registro['ref_ref_cod_serie']));
    }

    $this->addDetalhe(array('Turma atual', $this->nm_turma));

    if ($registro['nm_turma']) {
      $this->addDetalhe(array('Turma destino' , $registro['nm_turma']));
    }

    if ($registro['max_aluno']) {
      $this->addDetalhe(array('Total de vagas', $registro['max_aluno']));
    }

    if (is_numeric($total_alunos)) {
      $this->addDetalhe(array('Alunos nesta turma', $total_alunos));
      $this->addDetalhe(array('Vagas restantes', $registro['max_aluno'] - $total_alunos));
    }

    $this->addDetalhe(array(
      '-',
      sprintf('
        <form name="formcadastro" method="post" action="educar_matricula_turma_cad.php">
          <input type="hidden" name="ref_cod_matricula" value="">
          <input type="hidden" name="ref_cod_serie" value="">
          <input type="hidden" name="ref_cod_escola" value="">
          <input type="hidden" name="ref_cod_turma_origem" value="%d">
          <input type="hidden" name="ref_cod_turma_destino" value="">
          <input type="hidden" name="sequencial" value="%d">
        </form>
      ', $this->ref_cod_turma_origem, $this->sequencial)
    ));

    if ($registro['max_aluno'] - $total_alunos <= 0) {
      $msg = sprintf('Atenção! Turma sem vagas! Deseja continuar com a enturmação mesmo assim?');
      $valida = sprintf('if (!confirm("%s")) return false;', $msg);
    }
    else {
      $valida = 'if (!confirm("Confirmar a enturmação?")) return false;';
    }

    $script = sprintf('
      <script type="text/javascript">
        function enturmar(ref_cod_matricula, ref_cod_turma_destino){
          %s
          document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
          document.formcadastro.ref_cod_turma_destino.value = ref_cod_turma_destino;
          document.formcadastro.submit();
        }
      </script>', $valida);

    print $script;

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
      $script = "enturmar({$this->ref_cod_matricula},{$this->ref_cod_turma})";
      $this->array_botao = array('Transferir Aluno');
      $this->array_botao_url_script = array($script);
    }

    $this->array_botao[] = 'Voltar';
    $this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$this->ref_cod_matricula}\");";

    $this->largura = '100%';
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