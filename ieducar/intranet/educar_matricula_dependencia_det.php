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
require_once 'include/pmieducar/clsPermissoes.inc.php';

require_once 'App/Model/MatriculaSituacao.php';
require_once 'Portabilis/View/Helper/Application.php';
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matrícula');
    $this->processoAp = 578;
    $this->addEstilo("localizacaoSistema");
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
  var $ref_cod_aluno;
  var $cod_matricula_dependencia;
  var $ano;
  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $componente_curricular_id;
  var $aprovado;

  function Gerar()
  {

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = "Matrícula depend&ecirc;ncia - Detalhe";
    $this->addBanner("imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet");

    $this->cod_matricula_dependencia = $_GET["cod_matricula_dependencia"];

    $obj_matricula = new clsPmieducarMatriculaDependencia($this->cod_matricula_dependencia);
    $registro = $obj_matricula->detalhe();

    if (! $registro) {
      header("Location: educar_aluno_lst.php");
      die();
    }

    // Curso
    $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

    // Série
    $obj_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
    $det_serie = $obj_serie->detalhe();
    $registro['ref_cod_serie'] = $det_serie['nm_serie'];

    // Nome da instituição
    $obj_cod_instituicao = new clsPmieducarInstituicao( $registro['ref_cod_instituicao'] );
    $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

    // Nome da escola
    $obj_ref_cod_escola = new clsPmieducarEscola( $registro['ref_cod_escola'] );
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];

    // Nome do aluno
    $obj_aluno = new clsPmieducarAluno();
    $lst_aluno = $obj_aluno->lista($registro['ref_cod_aluno'], NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_aluno)) {
      $det_aluno = array_shift($lst_aluno);
      $nm_aluno = $det_aluno['nome_aluno'];
    }

    if ($registro['cod_matricula_dependencia']) {
      $this->addDetalhe(array('Número matrícula', $registro['ref_cod_matricula']));
    }
    if ($registro['cod_matricula_dependencia']) {
      $this->addDetalhe(array('Número matrícula dependência ', $registro['cod_matricula_dependencia']));
    }

    if ($nm_aluno) {
      $this->addDetalhe(array('Aluno', $nm_aluno));
    }

    if ($registro['ref_cod_instituicao']) {
      $this->addDetalhe(array('Instituição', $registro['ref_cod_instituicao']));
    }

    if ($registro['ref_ref_cod_escola']) {
      $this->addDetalhe(array('Escola', $registro['ref_ref_cod_escola']));
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('Série', $registro['ref_ref_cod_serie']));
    }

    if ($registro['componente_curricular_id']) {

      $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

      $componente = $componenteMapper->find($registro['componente_curricular_id']);
      $this->addDetalhe(array('Disciplina',$componente->nome));
    }


    if ($registro['aprovado']) {
      if ($registro['aprovado'] == 1) {
        $aprovado = 'Aprovado';
      }
      elseif ($registro['aprovado'] == 2) {
        $aprovado = 'Reprovado';
      }
      elseif ($registro['aprovado'] == 3) {
        $aprovado = 'Em Andamento';
      }
      elseif ($registro['aprovado'] == 4) {
        $aprovado = 'Transferido';
      }
      elseif ($registro['aprovado'] == 5) {
        $aprovado = 'Reclassificado';
      }
      elseif ($registro['aprovado'] == 6) {
        $aprovado = 'Abandono';
        $campoObs = true;
      }
      elseif ($registro['aprovado'] == 7) {
        $aprovado = 'Em Exame';
      }
      elseif ($registro['aprovado'] == 12) {
        $aprovado = 'Aprovado com depend&ecirc;ncia';
      }

      $this->addDetalhe(array('Situação', $aprovado));
    }

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {

      $this->url_novo = "educar_matricula_dependencia_cad.php?ref_cod_matricula=". $registro['ref_cod_matricula'];
      $this->url_editar = "educar_matricula_dependencia_cad.php?ref_cod_matricula=". $registro['ref_cod_matricula']
        . "&cod_matricula_dependencia=".$registro['cod_matricula_dependencia'];
    }

    $this->url_cancelar = 'educar_matricula_dependencia_lst.php?ref_cod_matricula=' . $registro['ref_cod_matricula'];
    $this->largura      = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""                                  => "Detalhe da matr&iacute;cula depend&ecirc;ncia"
    ));
    $this->enviaLocalizacao($localizacao->montar());
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
