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
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
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
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Formação');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $cod_formacao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_servidor;
  var $nm_formacao;
  var $tipo;
  var $descricao;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_instituicao;

  function Gerar()
  {
    $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $this->titulo = 'Servidor Formacao - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach( $_GET AS $var => $val ) {
      $this->$var = ($val === '') ? NULL : $val;
    }



    $this->addCabecalhos(array(
      'Nome Formação',
      'Tipo'
    ));

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
    $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);

    // Filtros
    $this->campoTexto('nm_formacao', 'Nome da Formação', $this->nm_formacao,
      30, 255, FALSE);

    $opcoes = array(
      ''  => 'Selecione',
      'C' => 'Cursos',
      'T' => 'Títulos',
      'O' => 'Concursos'
    );

    $this->campoLista('tipo', 'Tipo de Formação', $opcoes, $this->tipo);

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_servidor_formacao = new clsPmieducarServidorFormacao();
    $obj_servidor_formacao->setOrderby('nm_formacao ASC');
    $obj_servidor_formacao->setLimite($this->limite, $this->offset);

    if (! isset($this->tipo)) {
      $this->tipo = NULL;
    }

    $lista = $obj_servidor_formacao->lista(
      NULL,
      NULL,
      NULL,
      $this->ref_cod_servidor,
      $this->nm_formacao,
      $this->tipo,
      NULL,
      NULL,
      NULL,
      1
    );

    $total = $obj_servidor_formacao->_total;

    // UrlHelper
    $url  = CoreExt_View_Helper_UrlHelper::getInstance();
    $path = 'educar_servidor_formacao_det.php';

    // Monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        // Pega detalhes de foreign_keys
          $obj_ref_usuario_exc = new clsPmieducarUsuario($registro['ref_usuario_exc']);
          $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();

          $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

          $obj_ref_cod_servidor = new clsPmieducarServidor($registro['ref_cod_servidor']);
          $det_ref_cod_servidor = $obj_ref_cod_servidor->detalhe();

          $registro['ref_cod_servidor'] = $det_ref_cod_servidor['cod_servidor'];

        if ($registro['tipo'] == 'C') {
          $registro['tipo'] = 'Curso';
        }
        elseif ($registro['tipo'] == 'T') {
          $registro['tipo'] = 'Título';
        }
        else {
          $registro['tipo'] = 'Concurso';
        }

        $options = array(
          'query' => array(
            'cod_formacao' => $registro['cod_formacao']
        ));

        $this->addLinhas(array(
          $url->l($registro['nm_formacao'], $path, $options),
          $url->l($registro['tipo'], $path, $options)
        ));

        $this->tipo = '';
      }
    }

    $this->addPaginador2('educar_servidor_formacao_lst.php', $total, $_GET, $this->nome, $this->limite);
    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
      $this->array_botao[]     = 'Novo';
      $this->array_botao_url[] = sprintf(
        'educar_servidor_formacao_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao
      );
    }

    $this->array_botao[]     = 'Voltar';
    $this->array_botao_url[] = sprintf(
      'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_cod_instituicao
    );

    $this->largura = '100%';
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
