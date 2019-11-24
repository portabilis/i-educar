<?php
//error_reporting(E_ERROR);
//ini_set("display_errors", 1);
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
 * @author    Lucas Schmoeller da Silva <lucas@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Vagas por série');
    $this->processoAp = 21253;
  }
}

/**
 * indice class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $ano;
  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $turno;

  function Gerar()
  {

    // Helper para url
    $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

    $this->titulo = 'Vagas por série - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $this->addCabecalhos(array(
      'Ano', 'Escola', 'Curso', 'Série', 'Turno', 'Vagas'
    ));


    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola', 'curso', 'serie'), array('required' => FALSE));

    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

    $get_escola = true;
//    $get_escola_curso = true;
    $get_escola_curso_serie = true;
    $sem_padrao = true;
    $get_curso = true;

    if ( $this->ref_cod_escola )
    {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }

    $turnos = array(
        0 => 'Selecione',
        1 => 'Matutino',
        2 => 'Vespertino',
        3 => 'Noturno',
        4 => 'Integral'
    );

    $options = array(
      'value'     => $this->turno,
      'resources' => $turnos,
      'required' => false
    );
    $this->inputsHelper()->select('turno', $options);

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_serie_vaga = new clsPmieducarSerieVaga();

    if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
      $obj_serie_vaga->codUsuario = $this->pessoa_logada;
    }

    $obj_serie_vaga->setLimite($this->limite, $this->offset);

    $lista = $obj_serie_vaga->lista(
      $this->ano,
      $this->ref_cod_escola,
      $this->ref_cod_curso,
      $this->ref_cod_serie
    );


    $total = $obj_serie_vaga->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
        $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
        $nm_serie = $det_ref_cod_serie['nm_serie'];

        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $nm_curso = $det_curso['nm_curso'];

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];


        // Dados para a url
        $url     = 'educar_serie_vaga_det.php';
        $options = array('query' => array(
          'cod_serie_vaga'  => $registro['cod_serie_vaga']
        ));

        $this->addLinhas(array(
          $urlHelper->l($registro['ano'], $url, $options),
          $urlHelper->l($nm_escola, $url, $options),
          $urlHelper->l($nm_curso, $url, $options),
          $urlHelper->l($nm_serie, $url, $options),
          $urlHelper->l($turnos[$registro['turno']], $url, $options),
          $urlHelper->l($registro['vagas'], $url, $options)
        ));
      }
    }

    $this->addPaginador2('educar_serie_vaga_lst.php', $total, $_GET,
      $this->nome, $this->limite);

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(21253, $this->pessoa_logada, 7)) {
      $this->array_botao_url[] = 'educar_serie_vaga_cad.php';
      $this->array_botao[]     = 'Novo';
    }

    $this->largura = '100%';

    $this->breadcrumb('Listagem de vagas por série/ano', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
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
