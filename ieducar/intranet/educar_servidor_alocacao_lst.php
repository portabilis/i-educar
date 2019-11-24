<?php
// error_reporting(E_ERROR);
// ini_set("display_errors", 1);
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
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'CoreExt/View/Helper/UrlHelper.php';

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
    $this->SetTitulo($this->_instituicao . ' Servidores - Servidor');
    $this->processoAp = 635;
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
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $ref_cod_servidor;
  var $ref_cod_funcao;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ref_cod_escola;
  var $ref_cod_instituicao;
  var $ano_letivo;

  function Gerar()
  {
    $this->titulo = 'Alocação servidor - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET AS $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $tmp_obj = new clsPmieducarServidor($this->ref_cod_servidor, NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_cod_instituicao);
    $registro = $tmp_obj->detalhe();

    if (!$registro) {
        $this->simpleRedirect('educar_servidor_lst.php');
    }

    $this->addCabecalhos( array(
      'Escola',
      'Função',
      'Ano',
      'Período',
      'Carga horária',
      'Vínculo'
    ));

    $fisica = new clsPessoaFisica($this->ref_cod_servidor);
    $fisica = $fisica->detalhe();

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
    $this->campoRotulo('nm_servidor', 'Servidor', $fisica['nome']);

    $this->inputsHelper()->dynamic('instituicao', array('required' => false, 'show-select' => true, 'value' => $this->ref_cod_instituicao));
    $this->inputsHelper()->dynamic('escola', array('required' => false, 'show-select' => true, 'value' => $this->ref_cod_escola));
    $this->inputsHelper()->dynamic('anoLetivo', array('required' => false, 'show-select' => true, 'value' => $this->ano_letivo));

    $parametros = new clsParametrosPesquisas();
    $parametros->setSubmit(0);

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_servidor_alocacao = new clsPmieducarServidorAlocacao();

    if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
      $obj_servidor_alocacao->codUsuario = $this->pessoa_logada;
    }

    $obj_servidor_alocacao->setOrderby('ano ASC');
    $obj_servidor_alocacao->setLimite($this->limite, $this->offset);

    $lista = $obj_servidor_alocacao->lista(
      null,
      $this->ref_cod_instituicao,
      null,
      null,
      $this->ref_cod_escola,
      $this->ref_cod_servidor,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      $this->ano_letivo
    );
    $total = $obj_servidor_alocacao->_total;

    // UrlHelper
    $url = CoreExt_View_Helper_UrlHelper::getInstance();

    // Monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        $path = 'educar_servidor_alocacao_det.php';
        $options = array(
          'query' => array(
            'cod_servidor_alocacao' => $registro['cod_servidor_alocacao'],
        ));

        //Escola
        $escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $escola = $escola->detalhe();

        //Periodo
        $periodo = array(
          1  => 'Matutino',
          2  => 'Vespertino',
          3  => 'Noturno'
        );

        //Função
        $funcaoServidor = new clsPmieducarServidorFuncao(null, null, null, null, $registro['ref_cod_servidor_funcao']);
        $funcaoServidor = $funcaoServidor->detalhe();

        $funcao = new clsPmieducarFuncao($funcaoServidor['ref_cod_funcao']);
        $funcao = $funcao->detalhe();

        //Vinculo
        $funcionarioVinculo = new clsPortalFuncionario();
        $funcionarioVinculo = $funcionarioVinculo->getNomeVinculo($registro['ref_cod_funcionario_vinculo']);

        $this->addLinhas(array(
          $url->l($escola['nome'], $path, $options),
          $url->l($funcao['nm_funcao'], $path, $options),
          $url->l($registro['ano'], $path, $options),
          $url->l($periodo[$registro['periodo']], $path, $options),
          $url->l($registro['carga_horaria'], $path, $options),
          $url->l($funcionarioVinculo, $path, $options),
        ));
      }
    }

    $this->addPaginador2('educar_servidor_alocacao_lst.php', $total, $_GET, $this->nome, $this->limite);

    $obj_permissoes = new clsPermissoes();

    $this->array_botao = array();
    $this->array_botao_url = array();
    if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7 ) )
    {
      $this->array_botao_url[]= "educar_servidor_alocacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
      $this->array_botao[]= "Novo";
    }

    $this->array_botao[] = "Voltar";
    $this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";

    $this->largura = '100%';

    $this->breadcrumb('Listagem de alocações', [
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
