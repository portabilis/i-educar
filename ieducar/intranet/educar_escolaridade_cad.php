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
 * @author      Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Escolaridade
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/String/Utils.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Servidores - Escolaridade');
    $this->processoAp = '632';
    $this->addEstilo("localizacaoSistema");
  }
}

class indice extends clsCadastro
{
  /**
   * Referência a usuário da sessão
   * @var int
   */
  var $pessoa_logada = NULL;

  var $idesco;
  var $descricao;
  var $escolaridade;

  function Inicializar()
  {
    $retorno = 'Novo';

    $this->idesco = $_GET['idesco'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 3, 'educar_escolaridade_lst.php');

    if (is_numeric($this->idesco)) {
      $obj = new clsCadastroEscolaridade($this->idesco);
      $registro = $obj->detalhe();

      if ($registro) {
        // Passa todos os valores obtidos no registro para atributos do objeto
        foreach($registro as $campo => $val) {
          $this->$campo = $val;
        }

        if ($obj_permissoes->permissao_excluir(632, $this->pessoa_logada, 3)) {
          $this->fexcluir = true;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      'educar_escolaridade_det.php?idesco=' . $registro['idesco'] :
      'educar_escolaridade_lst.php';

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_servidores_index.php"       => "Servidores",
         ""        => "{$nomeMenu} escolaridade"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    

    return $retorno;
  }

  function Gerar()
  {
    // Primary keys
    $this->campoOculto('idesco', $this->idesco);

    // Outros campos
    $this->campoTexto('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 30, 255, TRUE);

    $resources = array(1 => 'Fundamental incompleto',
                     2 => 'Fundamental completo',
                     3 => 'Ensino médio - Normal/Magistério',
                     4 => 'Ensino médio - Normal/Magistério Indígena',
                     5 => 'Ensino médio',
                     6 => 'Superior');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Escolaridade educacenso'), 'resources' => $resources, 'value' => $this->escolaridade);
    $this->inputsHelper()->select('escolaridade', $options);    
  }

  function Novo()
  {
    $tamanhoDesc = strlen($this->descricao);
    if($tamanhoDesc > 60){
      $this->mensagem = 'A descrição deve conter no máximo 60 caracteres.<br>';
      return FALSE;
    }

    $obj = new clsCadastroEscolaridade(NULL, $this->descricao, $this->escolaridade);
    $cadastrou = $obj->cadastra();

    if ($cadastrou) {

      $escolaridade = new clsCadastroEscolaridade($cadastrou);
      $escolaridade = $escolaridade->detalhe();

      $auditoria = new clsModulesAuditoriaGeral("escolaridade", $this->pessoa_logada, $cadastrou);
      $auditoria->inclusao($escolaridade);

      $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
      $this->simpleRedirect('educar_escolaridade_lst.php');
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
    return FALSE;
  }

  function Editar()
  {
    $escolaridade = new clsCadastroEscolaridade($this->idesco);
    $escolaridadeAntes = $escolaridade->detalhe();

    $obj = new clsCadastroEscolaridade($this->idesco, $this->descricao, $this->escolaridade);
    $editou = $obj->edita();
    if ($editou) {

      $escolaridadeDepois = $escolaridade->detalhe();

      $auditoria = new clsModulesAuditoriaGeral("escolaridade", $this->pessoa_logada, $this->idesco);
      $auditoria->alteracao($escolaridadeAntes, $escolaridadeDepois);

      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      $this->simpleRedirect('educar_escolaridade_lst.php');
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
    return FALSE;
  }

  function Excluir()
  {
    $obj = new clsCadastroEscolaridade($this->idesco, $this->descricao);
    $escolaridade = $obj->detalhe();
    $excluiu = $obj->excluir();
    if ($excluiu) {

      $auditoria = new clsModulesAuditoriaGeral("escolaridade", $this->pessoa_logada, $this->idesco);
      $auditoria->exclusao($escolaridade);

      $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
      $this->simpleRedirect('educar_escolaridade_lst.php');
    }

    $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';
    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
