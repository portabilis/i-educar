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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Public
 * @since     ?
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicSetorBai.inc.php';
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Public
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Setor');
    $this->processoAp = 759;
    $this->addEstilo('localizacaoSistema');
  }
}

/**
 * indice class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Public
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  /**
   * Referência a usuário da sessão.
   * @var int
   */
  var $pessoa_logada;

  var $idsetorbai;
  var $nome;

  function Inicializar()
  {
    $retorno = 'Novo';
    $this->idsetorbai = $_GET['idsetorbai'];

    if (is_numeric($this->idsetorbai)) {
      $obj_setor_bai = new clsPublicSetorBai($this->idsetorbai);
      $det_setor_bai = $obj_setor_bai->detalhe();

      if ($det_setor_bai) {
        $registro = $det_setor_bai;
      }

      if ($registro) {
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      'public_setor_det.php?idsetorbai=' . $registro['idsetorbai'] :
      'public_setor_lst.php';

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_enderecamento_index.php"    => "Endereçamento",
         ""        => "{$nomeMenu} setor"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    

    return $retorno;
  }

  function Gerar()
  {
    // primary keys
    $this->campoOculto('idsetorbai', $this->idsetorbai);

    $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, TRUE);
  
  }

  function Novo()
  {
    $obj = new clsPublicSetorBai(NULL, $this->nome);

    $cadastrou = $obj->cadastra();
    if ($cadastrou) {

      $enderecamento = new clsPublicSetorBai($cadastrou);
      $enderecamento = $enderecamento->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("Endereçamento de Setor", $this->pessoa_logada, $cadastrou);
      $auditoria->inclusao($enderecamento);

      $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
      $this->simpleRedirect('public_setor_lst.php');
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

    return FALSE;
  }

  function Editar()
  {
    $enderecamentoDetalhe = new clsPublicSetorBai($this->idsetorbai);
    $enderecamentoDetalhe->cadastrou = $this->idsetorbai;
    $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

    $obj = new clsPublicSetorBai($this->idsetorbai, $this->nome);

    $editou = $obj->edita();
    if ($editou) {

      $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("Endereçamento de Setor", $this->pessoa_logada, $this->idsetorbai);
      $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      $this->simpleRedirect('public_setor_lst.php');
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

    return FALSE;
  }

  function Excluir()
  {
    $obj = new clsPublicSetorBai($this->idsetorbai);
    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
      $this->simpleRedirect('public_setor_lst.php');
    }

    $this->mensagem = 'Exclusão não realizada.<br>';

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
?>
