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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Falta Atraso');
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
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

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

    $this->url_cancelar = $retorno == 'Editar' ?
      sprintf('educar_falta_atraso_det.php?cod_falta_atraso=%d', $registro['cod_falta_atraso']) :
      sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d', $this->ref_cod_servidor, $this->ref_cod_instituicao);

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    // Primary keys
    $this->campoOculto('cod_falta_atraso', $this->cod_falta_atraso);
    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

    // Foreign keys
    $obrigatorio     = TRUE;
    $get_instituicao = TRUE;
    $get_escola      = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

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
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

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
      $dia_semana = $db->CampoUnico(sprintf('(SELECT EXTRACT (DOW FROM date \'%s\') + 1 )', dataToBanco($this->data_falta_atraso)));

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
      header('Location: ' . sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao));
      die();
    }

    $this->mensagem = 'Cadastro não realizado.<br />';
    echo "<!--\nErro ao cadastrar clsPmieducarFaltaAtraso\nvalores obrigatórios\nis_numeric( $this->ref_cod_escola ) && is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_servidor) && is_numeric($this->tipo) && is_string($this->data_falta_atraso) && is_numeric($this->justificada)\n-->";
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao));

    if ($this->tipo == 1) {
      $obj = new clsPmieducarFaltaAtraso(NULL, $this->ref_cod_escola,
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
      $obj = new clsPmieducarFaltaAtraso(NULL, $this->ref_cod_escola,
        $this->ref_cod_instituicao, $this->pessoa_logada, NULL,
        $this->ref_cod_servidor, $this->tipo, $this->data_falta_atraso, $horas,
        $minutos, $this->justificada, NULL, NULL, 1);
    }

    $editou = $obj->edita();
    if ($editou) {
      $this->mensagem .= 'Edição efetuada com sucesso.<br />';
      header('Location: ' . sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao));
      die();
    }

    $this->mensagem = 'Edição não realizada.<br />';
    echo "<!--\nErro ao editar clsPmieducarFaltaAtraso\nvalores obrigatórios\nif(is_numeric($this->cod_falta_atraso) && is_numeric($this->ref_usuario_exc))\n-->";
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

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
      header('Location: ' . sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao));
      die();
    }

    $this->mensagem = "Exclusão não realizada.<br>";
    echo "<!--\nErro ao excluir clsPmieducarFaltaAtraso\nvalores obrigatórios\nif( is_numeric( $this->cod_falta_atraso ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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