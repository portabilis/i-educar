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

require_once 'App/Model/IedFinder.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escola S&eacute;rie');
    $this->processoAp = '585';
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

  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $hora_inicio_intervalo;
  var $hora_fim_intervalo;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Escola S&eacute;rie - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->ref_cod_serie = $_GET['ref_cod_serie'];
    $this->ref_cod_escola = $_GET['ref_cod_escola'];

    $tmp_obj = new clsPmieducarEscolaSerie();
    $lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
    $registro = array_shift($lst_obj);

    if (! $registro) {
      header('Location: educar_escola_serie_lst.php');
      die();
    }

    $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

    $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $nm_escola = $det_ref_cod_escola['nome'];

    $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
    $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
    $nm_serie = $det_ref_cod_serie['nm_serie'];

    $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_curso = $obj_curso->detalhe();
    $registro['ref_cod_curso'] = $det_curso['nm_curso'];

    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      if ($registro["ref_cod_instituicao"]) {
        $this->addDetalhe(array('Institui&ccedil;&atilde;o', $registro['ref_cod_instituicao']));
      }
    }

    if ($nivel_usuario == 1 || $nivel_usuario == 2) {
      if ($nm_escola) {
        $this->addDetalhe(array('Escola', $nm_escola));
      }
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($nm_serie) {
      $this->addDetalhe(array('S&eacute;rie', $nm_serie));
    }

    if ($registro['hora_inicial']) {
      $registro['hora_inicial'] = date('H:i', strtotime($registro['hora_inicial']));
      $this->addDetalhe(array('Hora Inicial', $registro['hora_inicial']));
    }

    if ($registro['hora_final']) {
      $registro['hora_final'] = date('H:i', strtotime( $registro['hora_final']));
      $this->addDetalhe(array('Hora Final', $registro['hora_final']));
    }

    if ($registro['hora_inicio_intervalo']) {
      $registro['hora_inicio_intervalo'] = date('H:i', strtotime($registro['hora_inicio_intervalo']));
      $this->addDetalhe(array('Hora In&iacute;cio Intervalo', $registro['hora_inicio_intervalo']));
    }

    if ($registro['hora_fim_intervalo']) {
      $registro['hora_fim_intervalo'] = date('H:i', strtotime($registro['hora_fim_intervalo']));
      $this->addDetalhe(array( 'Hora Fim Intervalo', $registro['hora_fim_intervalo']));
    }

    // Componentes da escola-série
    $componentes = array();
    try {
      $componentes = App_Model_IedFinder::getEscolaSerieDisciplina($this->ref_cod_serie, $this->ref_cod_escola);
    }
    catch (Exception $e) {
    }

    if (0 < count($componentes)) {
      $tabela = '
<table>
  <tr align="center">
    <td bgcolor="#A1B3BD"><b>Nome</b></td>
    <td bgcolor="#A1B3BD"><b>Carga horária</b></td>
  </tr>';

      $cont = 0;

      foreach ($componentes as $componente) {
        if (($cont % 2) == 0) {
          $color = ' bgcolor="#E4E9ED" ';
        }
        else {
          $color = ' bgcolor="#FFFFFF" ';
        }

        $tabela .= sprintf('
          <tr>
            <td %s align="left">%s</td>
            <td %s align="center">%.0f h</td>
          </tr>',
          $color, $componente, $color, $componente->cargaHoraria
        );

        $cont++;
      }

      $tabela .= '</table>';
    }

    if (isset($tabela)) {
      $this->addDetalhe(array('Componentes curriculares', $tabela));
    }

    if ($obj_permissao->permissao_cadastra(585, $this->pessoa_logada, 7)) {
      $this->url_novo = "educar_escola_serie_cad.php";
      $this->url_editar = "educar_escola_serie_cad.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}";
    }

    $this->url_cancelar = "educar_escola_serie_lst.php";
    $this->largura = "100%";
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();