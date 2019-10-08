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
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Disciplina de dependência');
    $this->processoAp = 578;
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

  var $ref_cod_matricula;
  var $ref_cod_serie;
  var $ref_cod_escola;
  var $ref_cod_disciplina;
  var $observacao;
  var $ref_sequencial;

  var $ref_cod_instituicao;
  var $ref_cod_turma;

  function Gerar()
  {
    // Helper para url
    $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

    $this->titulo = 'Disciplina de dependência - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    if (!$_GET['ref_cod_matricula']) {
        $this->simpleRedirect('educar_matricula_lst.php');
    }

    $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

    $obj_matricula = new clsPmieducarMatricula();
    $lst_matricula = $obj_matricula->lista($this->ref_cod_matricula);

    if (is_array($lst_matricula)) {
      $det_matricula             = array_shift($lst_matricula);
      $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
      $this->ref_cod_escola      = $det_matricula['ref_ref_cod_escola'];
      $this->ref_cod_serie       = $det_matricula['ref_ref_cod_serie'];

      $obj_matricula_turma = new clsPmieducarMatriculaTurma();
      $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie, NULL,
        $this->ref_cod_escola);

      if (is_array($lst_matricula_turma)) {
        $det                  = array_shift($lst_matricula_turma);
        $this->ref_cod_turma  = $det['ref_cod_turma'];
        $this->ref_sequencial = $det['sequencial'];
      }
    }

    $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);

    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $this->addCabecalhos(array(
      'Disciplina'
    ));

    $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

    // outros Filtros
    $opcoes = array('' => 'Selecione');

    // Escola série disciplina
    $componentes = App_Model_IedFinder::getComponentesTurma(
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_turma
    );

    foreach ($componentes as $componente) {
      $opcoes[$componente->id] = $componente->nome;
    }

    $this->campoLista('ref_cod_disciplina', 'Disciplina', $opcoes,
      $this->ref_cod_disciplina, '', FALSE, '', '', FALSE, FALSE);

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_disciplina_dependencia = new clsPmieducarDisciplinaDependencia();
    $obj_disciplina_dependencia->setLimite($this->limite, $this->offset);

    $lista = $obj_disciplina_dependencia->lista(
      $this->ref_cod_matricula,
      NULL,
      NULL,
      $this->ref_cod_disciplina
    );

    $total = $obj_disciplina_dependencia->_total;

    // Mapper de componente curricular
    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        // Componente curricular
        $componente = $componenteMapper->find($registro['ref_cod_disciplina']);

        // Dados para a url
        $url     = 'educar_disciplina_dependencia_det.php';
        $options = array('query' => array(
          'ref_cod_matricula'  => $registro['ref_cod_matricula'],
          'ref_cod_serie'      => $registro['ref_cod_serie'],
          'ref_cod_escola'     => $registro['ref_cod_escola'],
          'ref_cod_disciplina' => $registro['ref_cod_disciplina']
        ));

        $this->addLinhas(array(
          $urlHelper->l($componente->nome, $url, $options)
        ));
      }
    }

    $this->addPaginador2('educar_disciplina_dependencia_lst.php', $total, $_GET,
      $this->nome, $this->limite);

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
      $this->array_botao_url[] = 'educar_disciplina_dependencia_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
      $this->array_botao[]     = 'Novo';
    }

    $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
    $this->array_botao[]     = 'Voltar';

    $this->largura = '100%';

    $this->breadcrumb('Disciplinas de dependência', [
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
