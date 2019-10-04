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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'Portabilis/Date/Utils.php';

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
    $this->SetTitulo($this->_instituicao . ' Servidores - Falta Atraso');
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
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_falta_atraso;
  var $ref_cod_escola;
  var $ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_servidor;
  var $tipo;
  var $data_falta_atraso;
  var $qtd_horas;
  var $qtd_min;
  var $justificada;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  function Inicializar()
  {
    $retorno = 'Novo';


    $this->cod_falta_atraso    = $_GET['cod_falta_atraso'];
    $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
    $this->ref_cod_escola      = $_GET['ref_cod_escola'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_falta_atraso_lst.php');

    if (is_numeric($this->cod_falta_atraso)) {
      $obj = new clsPmieducarFaltaAtraso($this->cod_falta_atraso);
      $registro  = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $this->data_falta_atraso = dataFromPgToBr($this->data_falta_atraso);

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d', $this->ref_cod_servidor, $this->ref_cod_instituicao);

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

    $this->breadcrumb($nomeMenu . ' falta/atraso do servidor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);

    return $retorno;
  }

  function Gerar()
  {
    // Primary keys
    $this->campoOculto('cod_falta_atraso', $this->cod_falta_atraso);
    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

    $this->inputsHelper()->dynamic('instituicao', array('value' => $this->ref_cod_instituicao, 'disabled' => $desabilitado));
    $this->inputsHelper()->dynamic('escola', array('value' => $this->ref_cod_escola, 'disabled' => $desabilitado));

    // Text
    // @todo CoreExt_Enum
    $opcoes = array(
      '' => 'Selecione',
      1  => 'Atraso',
      2  => 'Falta'
    );

    $this->campoLista('tipo', 'Tipo', $opcoes, $this->tipo);

    $this->campoNumero('qtd_horas', 'Quantidade de Horas', $this->qtd_horas, 30, 255, FALSE);
    $this->campoNumero('qtd_min', 'Quantidade de Minutos', $this->qtd_min, 30, 255, FALSE);

    $opcoes = array(
      '' => 'Selecione',
      0  => 'Sim',
      1  => 'Não'
    );

    $this->campoLista('justificada', 'Justificada', $opcoes, $this->justificada);

    // Data
    $this->campoData('data_falta_atraso', 'Dia', $this->data_falta_atraso, TRUE);
  }

  function Novo()
  {



    $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao));

    if ($this->tipo == 1) {
      $obj = new clsPmieducarFaltaAtraso(NULL, $this->ref_cod_escola,
        $this->ref_cod_instituicao, NULL, $this->pessoa_logada,
        $this->ref_cod_servidor, $this->tipo, $this->data_falta_atraso,
        $this->qtd_horas, $this->qtd_min, $this->justificada, NULL, NULL, 1);
    }
    elseif ($this->tipo == 2) {
      $db = new clsBanco();
      $dia_semana = $db->CampoUnico(sprintf('(SELECT EXTRACT (DOW FROM date \'%s\') + 1 )', $this->data_falta_atraso));

      $obj_ser = new clsPmieducarServidor();
      $horas   = $obj_ser->qtdhoras( $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao, $dia_semana );

      if ($horas) {
        $obj = new clsPmieducarFaltaAtraso(NULL, $this->ref_cod_escola,
          $this->ref_cod_instituicao, NULL, $this->pessoa_logada,
          $this->ref_cod_servidor, $this->tipo, $this->data_falta_atraso,
          $horas['hora'], $horas['min'], $this->justificada, NULL, NULL, 1);
      }
    }

    $cadastrou = $obj->cadastra();

    if ($cadastrou) {
      $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
      $this->simpleRedirect(sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor, $this->ref_cod_instituicao));
    }

    $this->mensagem = 'Cadastro não realizado.<br />';

    return FALSE;
  }

  function Editar()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao));
    $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);
    if ($this->tipo == 1) {
      $obj = new clsPmieducarFaltaAtraso($this->cod_falta_atraso, $this->ref_cod_escola,
        $this->ref_cod_instituicao, $this->pessoa_logada, NULL,
        $this->ref_cod_servidor, $this->tipo, $this->data_falta_atraso,
        $this->qtd_horas, $this->qtd_min, $this->justificada, NULL, NULL, 1);
    }
    elseif ($this->tipo == 2) {
      $obj_ser = new clsPmieducarServidor($this->ref_cod_servidor, NULL, NULL,
        NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

      $det_ser = $obj_ser->detalhe();
      $horas   = floor($det_ser['carga_horaria']);
      $minutos = ($det_ser['carga_horaria'] - $horas) * 60;
      $obj = new clsPmieducarFaltaAtraso($this->cod_falta_atraso, $this->ref_cod_escola,
        $this->ref_cod_instituicao, $this->pessoa_logada, NULL,
        $this->ref_cod_servidor, $this->tipo, $this->data_falta_atraso, $horas,
        $minutos, $this->justificada, NULL, NULL, 1);
    }
    $editou = $obj->edita();
    if ($editou) {
      $this->mensagem .= 'Edição efetuada com sucesso.<br />';
      $this->simpleRedirect(sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor, $this->ref_cod_instituicao));
    }

    $this->mensagem = 'Edição não realizada.<br />';

    return FALSE;
  }

  function Excluir()
  {

    $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7,
      sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao));

    $obj = new clsPmieducarFaltaAtraso($this->cod_falta_atraso, $this->ref_cod_escola,
      $this->ref_ref_cod_instituicao, $this->pessoa_logada, $this->pessoa_logada,
      $this->ref_cod_servidor, $this->tipo, $this->data_falta_atraso, $this->qtd_horas,
      $this->qtd_min, $this->justificada, $this->data_cadastro, $this->data_exclusao, 0);
    $excluiu = $obj->excluir();
    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
      $this->simpleRedirect(sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor, $this->ref_cod_instituicao));
    }
    $this->mensagem = "Exclusão não realizada.<br>";

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
?>
<script type="text/javascript">
var obj_tipo = document.getElementById('tipo');

obj_tipo.onchange = function()
{
  if (document.getElementById('tipo').value == 1) {
    setVisibility('tr_qtd_horas', true);
    setVisibility('tr_qtd_min', true);
  }
  else if (document.getElementById( 'tipo' ).value == 2) {
    setVisibility('tr_qtd_horas', false);
    setVisibility('tr_qtd_min', false);
  }
}
</script>
