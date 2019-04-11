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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Falta Atraso Compensado');
    $this->processoAp = 635;
  }
}

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
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_compensado;
  var $ref_cod_escola;
  var $ref_cod_instituicao;
  var $ref_cod_servidor;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_inicio;
  var $data_fim;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  function Inicializar()
  {
    $retorno = 'Novo';
    

    $this->cod_compensado      = $_GET['cod_compensado'];
    $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
    $this->ref_cod_escola      = $_GET['ref_cod_escola'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      sprintf('educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao));

    if (is_numeric($this->cod_compensado)) {
      $obj = new clsPmieducarFaltaAtrasoCompensado($this->cod_compensado);
      $registro = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $this->data_inicio   = dataFromPgToBr($this->data_inicio);
        $this->data_fim      = dataFromPgToBr($this->data_fim);
        $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
        $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = sprintf('educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao);

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    // Primary keys
    $this->campoOculto('cod_compensado', $this->cod_compensado);
    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

    // Foreign keys
    $obrigatorio     = TRUE;
    $get_instituicao = TRUE;
    $get_escola      = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    // Data
    $this->campoData('data_inicio', 'Data Inicio', $this->data_inicio, TRUE);
    $this->campoData('data_fim', 'Data Fim', $this->data_fim, TRUE);
  }

  function Novo()
  {
    

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}");

    // Transforma a data para o formato aceito pelo banco
    $this->data_inicio = dataToBanco($this->data_inicio);
    $this->data_fim    = dataToBanco($this->data_fim);

    $obj = new clsPmieducarFaltaAtrasoCompensado(NULL, $this->ref_cod_escola,
      $this->ref_cod_instituicao, $this->ref_cod_servidor, $this->pessoa_logada,
      $this->pessoa_logada, $this->data_inicio, $this->data_fim, NULL, NULL, 1);

    $cadastrou = $obj->cadastra();

    if ($cadastrou) {
      $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
      $this->simpleRedirect(sprintf('educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao));
    }

    $this->mensagem = 'Cadastro não realizado.<br />';
    return FALSE;
  }

  function Editar()
  {
    

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      sprintf('educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao));

    // Transforma a data para o formato aceito pelo banco
    $this->data_inicio = dataToBanco($this->data_inicio);
    $this->data_fim    = dataToBanco($this->data_fim);

    $obj = new clsPmieducarFaltaAtrasoCompensado($this->cod_compensado,
      $this->ref_cod_escola, $this->ref_cod_instituicao, $this->ref_cod_servidor,
      $this->pessoa_logada, $this->pessoa_logada, $this->data_inicio,
      $this->data_fim, $this->data_cadastro, $this->data_exclusao, $this->ativo);

    $editou = $obj->edita();

    if ($editou) {
      $this->mensagem .= 'Edição efetuada com sucesso.<br />';
      $this->simpleRedirect(sprintf('educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao));
    }

    $this->mensagem = 'Edição não realizada.<br />';
    return FALSE;
  }

  function Excluir()
  {
    

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7,
      sprintf('educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao));

    // Transforma a data para o formato aceito pelo banco
    $this->data_inicio = dataToBanco($this->data_inicio);
    $this->data_fim    = dataToBanco($this->data_fim);

    $obj = new clsPmieducarFaltaAtrasoCompensado($this->cod_compensado,
      $this->ref_cod_escola, $this->ref_cod_instituicao, $this->ref_cod_servidor,
      $this->pessoa_logada, $this->pessoa_logada, $this->data_inicio,
      $this->data_fim, $this->data_cadastro, $this->data_exclusao, 0);

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
      $this->simpleRedirect("educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}");
    }

    $this->mensagem = 'Exclusão não realizada.<br />';
    return FALSE;
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
