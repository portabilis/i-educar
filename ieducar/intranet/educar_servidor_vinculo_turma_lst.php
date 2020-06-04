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
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   $Id$
 */

use iEducar\Support\View\SelectOptions;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesProfessorTurma.inc.php';
require_once 'lib/Portabilis/String/Utils.php';

require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
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
    $this->SetTitulo($this->_instituicao . ' Servidores - Servidor Vínculo Turma');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
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

  var $id;
  var $ano;
  var $servidor_id;
  var $funcao_exercida;
  var $tipo_vinculo;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $ref_cod_turma;

  function Gerar()
  {
    $this->servidor_id    = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $this->titulo = 'Servidor Vínculo Turma - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach( $_GET AS $var => $val ) {
      $this->$var = ($val === '') ? NULL : $val;
    }



    $this->addCabecalhos(array(
      'Ano',
      'Escola',
      'Curso',
      'Série',
      'Turma',
      'Função exercida',
      'Tipo de vínculo'
    ));

    $this->campoOculto('ref_cod_servidor', $this->servidor_id);

    $this->inputsHelper()->dynamic(array('ano', 'instituicao','escola','curso','serie', 'turma'), array('required' => false));

    $resources_funcao = SelectOptions::funcoesExercidaServidor();
    $options = array('label' => Portabilis_String_Utils::toLatin1('Função exercida'), 'resources' => $resources_funcao, 'value' => $this->funcao_exercida);
    $this->inputsHelper()->select('funcao_exercida', $options);

    $resources_tipo = SelectOptions::tiposVinculoServidor();
    $options = array('label' => Portabilis_String_Utils::toLatin1('Tipo do vínculo'), 'resources' => $resources_tipo, 'value' => $this->tipo_vinculo);
    $this->inputsHelper()->select('tipo_vinculo', $options);

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_vinculo = new clsModulesProfessorTurma();

    if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
      $obj_vinculo->codUsuario = $this->pessoa_logada;
    }

    $obj_vinculo->setOrderby(' nm_escola, nm_curso, nm_serie, nm_turma ASC');
    $obj_vinculo->setLimite($this->limite, $this->offset);

    if (! isset($this->tipo)) {
      $this->tipo = NULL;
    }

    $lista = $obj_vinculo->lista(
      $this->servidor_id,
      $this->ref_cod_instituicao,
      $this->ano,
      $this->ref_cod_escola,
      $this->ref_cod_curso,
      $this->ref_cod_serie,
      $this->ref_cod_turma,
      $this->funcao_exercida,
      $this->tipo_vinculo
    );

    $total = $obj_vinculo->_total;

    // UrlHelper
    $url  = CoreExt_View_Helper_UrlHelper::getInstance();
    $path = 'educar_servidor_vinculo_turma_det.php';

    // Monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        $options = array(
          'query' => array(
            'id' => $registro['id']
        ));

        $this->addLinhas(array(
          $url->l($registro['ano'], $path, $options),
          $url->l($registro['nm_escola'], $path, $options),
          $url->l($registro['nm_curso'], $path, $options),
          $url->l($registro['nm_serie'], $path, $options),
          $url->l($registro['nm_turma'], $path, $options),
          $url->l($resources_funcao[$registro['funcao_exercida']], $path, $options),
          $url->l($resources_tipo[$registro['tipo_vinculo']], $path, $options)
        ));
      }
    }

    $this->addPaginador2('educar_servidor_vinculo_turma_lst.php', $total, $_GET, $this->nome, $this->limite);
    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
      $this->array_botao[]     = 'Novo';
      $this->array_botao_url[] = sprintf(
        'educar_servidor_vinculo_turma_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->servidor_id, $this->ref_cod_instituicao
      );
    }

    $this->array_botao[]     = 'Voltar';
    $this->array_botao_url[] = sprintf(
      'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->servidor_id, $this->ref_cod_instituicao
    );

    $this->largura = '100%';

    $this->breadcrumb('Registro de vínculos do professor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
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
