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

require_once 'App/Date/Utils.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Ano Letivo Módulo');
    $this->processoAp = 561;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_ano;
  var $ref_ref_cod_escola;
  var $sequencial;
  var $ref_cod_modulo;
  var $data_inicio;
  var $data_fim;

  var $ano_letivo_modulo;
  var $incluir_modulo;
  var $excluir_modulo;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_modulo     = $_GET['ref_cod_modulo'];
    $this->ref_ref_cod_escola = $_GET['ref_cod_escola'];
    $this->ref_ano            = $_GET['ano'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola)) {
      $obj = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola, $this->ref_ano);
      $registro  = $obj->detalhe();

      if ($registro) {
        if ($obj_permissoes->permissao_excluir(561, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = $_GET['referrer'] ?
      $_GET['referrer'] . '?cod_escola=' . $this->ref_ref_cod_escola:
      'educar_escola_lst.php';

    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        $this->$campo = $this->$campo ? $this->$campo : $val;
      }
    }

    // Primary keys
    $this->campoOculto('ref_ano', $this->ref_ano);
    $this->campoOculto('ref_ref_cod_escola', $this->ref_ref_cod_escola);

    $obj_escola = new clsPmieducarEscola($this->ref_ref_cod_escola);
    $det_escola = $obj_escola->detalhe();
    $ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

    $ref_ano_ = $this->ref_ano;
    $this->campoTexto('ref_ano_', 'Ano', $ref_ano_, 4, 4, FALSE, FALSE, FALSE,
      '', '', '', '', TRUE);

    $this->campoQuebra();

    // Módulos do ano letivo
    if ($_POST['ano_letivo_modulo']) {
      $this->ano_letivo_modulo = unserialize(urldecode($_POST['ano_letivo_modulo']));
    }

    $qtd_modulo = count($this->ano_letivo_modulo) == 0 ?
      1 : count($this->ano_letivo_modulo) + 1;

    if (is_numeric($this->ref_ano) &&
      is_numeric($this->ref_ref_cod_escola) &&
      !$_POST
    ) {
      $obj = new clsPmieducarAnoLetivoModulo();
      $obj->setOrderBy('sequencial ASC');
      $registros = $obj->lista($this->ref_ano, $this->ref_ref_cod_escola);

      if ($registros) {
        foreach ($registros as $campo) {
          $this->ano_letivo_modulo[$campo[$qtd_modulo]]['sequencial_']     = $campo['sequencial'];
          $this->ano_letivo_modulo[$campo[$qtd_modulo]]['ref_cod_modulo_'] = $campo['ref_cod_modulo'];
          $this->ano_letivo_modulo[$campo[$qtd_modulo]]['data_inicio_']    = dataFromPgToBr($campo['data_inicio']);
          $this->ano_letivo_modulo[$campo[$qtd_modulo]]['data_fim_']       = dataFromPgToBr($campo['data_fim']);
          $qtd_modulo++;
        }
      }
    }

    if ($_POST['ref_cod_modulo'] && $_POST['data_inicio'] && $_POST['data_fim']) {
      $this->ano_letivo_modulo[$qtd_modulo]['sequencial_']     = $qtd_modulo;
      $this->ano_letivo_modulo[$qtd_modulo]['ref_cod_modulo_'] = $_POST['ref_cod_modulo'];
      $this->ano_letivo_modulo[$qtd_modulo]['data_inicio_']    = $_POST['data_inicio'];
      $this->ano_letivo_modulo[$qtd_modulo]['data_fim_']       = $_POST['data_fim'];

      $qtd_modulo++;

      unset($this->ref_cod_modulo);
      unset($this->data_inicio);
      unset($this->data_fim);
    }

    $this->campoOculto('excluir_modulo', '');
    $qtd_modulo = 1;
    unset($aux);

    if ($this->ano_letivo_modulo) {
      foreach ($this->ano_letivo_modulo as $campo) {
        if ($this->excluir_modulo == $campo['sequencial_']) {
          $this->ano_letivo_modulo[$campo['sequencial']] = NULL;
          $this->excluir_modulo = NULL;
        }
        else {
          $obj_modulo = new clsPmieducarModulo($campo['ref_cod_modulo_']);
          $det_modulo = $obj_modulo->detalhe();
          $nm_tipo_modulo = $det_modulo['nm_tipo'];

          $url = sprintf('
            <a href="#" onclick="getElementById(\'excluir_modulo\').value = \'%s\'; getElementById(\'tipoacao\').value = \'\'; %s.submit();">
              <img src="imagens/nvp_bola_xis.gif" title="Excluir" border="0" />
            </a>',
            $campo['sequencial_'], $this->__nome
          );

          $this->campoTextoInv('ref_cod_modulo_' . $campo['sequencial_'], '',
            $nm_tipo_modulo, 30, 255, FALSE, FALSE, TRUE);

          $this->campoTextoInv('data_inicio_' . $campo['sequencial_'], '',
            $campo['data_inicio_'], 10, 10, FALSE, FALSE, TRUE);

          $this->campoTextoInv('data_fim_' . $campo['sequencial_'], '',
            $campo['data_fim_'], 10, 10, FALSE, FALSE, FALSE, '', $url
          );

          $aux[$qtd_modulo]['sequencial_']     = $qtd_modulo;
          $aux[$qtd_modulo]['ref_cod_modulo_'] = $campo['ref_cod_modulo_'];
          $aux[$qtd_modulo]['data_inicio_']    = $campo['data_inicio_'];
          $aux[$qtd_modulo]['data_fim_']       = $campo['data_fim_'];

          $qtd_modulo++;
        }
      }

      unset($this->ano_letivo_modulo);
      $this->ano_letivo_modulo = $aux;
    }

    $this->campoOculto('ano_letivo_modulo', serialize($this->ano_letivo_modulo));

    // Foreign keys
    $opcoes = array('' => 'Selecione');
    if (class_exists("clsPmieducarModulo")) {
      $objTemp = new clsPmieducarModulo();
      $objTemp->setOrderby('nm_tipo ASC');

      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, 1, $ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_modulo']] = $registro['nm_tipo'];
        }
      }
    }
    else {
      $opcoes = array('' => 'Erro na geração');
    }

    // data
    if ($qtd_modulo > 1) {
      $this->campoLista('ref_cod_modulo', 'Módulo', $opcoes,
        $this->ref_cod_modulo, NULL, NULL, NULL, NULL, NULL, FALSE);

      $this->campoData('data_inicio', 'Data Início', $this->data_inicio);

      $this->campoData('data_fim', 'Data Fim', $this->data_fim);
    }
    else {
      $this->campoLista('ref_cod_modulo', 'Módulo', $opcoes, $this->ref_cod_modulo);
      $this->campoData('data_inicio', 'Data Início', $this->data_inicio, TRUE);
      $this->campoData('data_fim', 'Data Fim', $this->data_fim, TRUE);
    }

    $this->campoOculto('incluir_modulo', '');
    $this->campoRotulo('bt_incluir_modulo', 'Módulo',
     '<a href="#" onclick="incluir();"><img src="imagens/nvp_bot_adiciona.gif" title="Incluir" border="0" /></a>'
    );

    $this->campoQuebra();
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    $this->ano_letivo_modulo = unserialize(urldecode($this->ano_letivo_modulo));

    if ($this->ano_letivo_modulo) {
      $obj = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola,
        $this->ref_ano, $this->pessoa_logada, NULL, 0, NULL, NULL, 1
      );

      $cadastrou = $obj->cadastra();

      if ($cadastrou) {
        if (FALSE == $this->_verificaModuloDatas($this->ano_letivo_modulo)) {
          return FALSE;
        }

        foreach ($this->ano_letivo_modulo as $campo) {
          $campo['data_inicio_'] = dataToBanco($campo['data_inicio_']);
          $campo['data_fim_']    = dataToBanco($campo['data_fim_']);

          $obj = new clsPmieducarAnoLetivoModulo($this->ref_ano,
            $this->ref_ref_cod_escola, $campo['sequencial_'],
            $campo['ref_cod_modulo_'], $campo['data_inicio_'],
            $campo['data_fim_']
          );

          $cadastrou1 = $obj->cadastra();

          if (! $cadastrou1) {
            $this->mensagem = 'Cadastro não realizado.<br />';
            return FALSE;
          }
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        header('Location: educar_escola_lst.php');

        die();
      }

      $this->mensagem = 'Cadastro não realizado. <br />';
      return FALSE;
    }

    echo '<script>alert("É necessário adicionar pelo menos um módulo!")</script>';
    $this->mensagem = 'Cadastro não realizado.<br />';
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    $this->ano_letivo_modulo = unserialize(urldecode($this->ano_letivo_modulo));

    if ($this->ano_letivo_modulo) {
      $obj  = new clsPmieducarAnoLetivoModulo($this->ref_ano, $this->ref_ref_cod_escola);
      $excluiu = $obj->excluirTodos();

      if ($excluiu) {
        if (FALSE == $this->_verificaModuloDatas($this->ano_letivo_modulo)) {
          return FALSE;
        }

        foreach ($this->ano_letivo_modulo as $campo) {
          $campo['data_inicio_'] = dataToBanco($campo['data_inicio_']);
          $campo['data_fim_']    = dataToBanco($campo['data_fim_']);

          $obj = new clsPmieducarAnoLetivoModulo($this->ref_ano,
            $this->ref_ref_cod_escola, $campo['sequencial_'],
            $campo['ref_cod_modulo_'], $campo['data_inicio_'],
            $campo['data_fim_']
          );

          $cadastrou = $obj->cadastra();

          if (! $cadastrou) {
            $this->mensagem = 'Edição não realizada.<br />';
            return FALSE;
          }
        }

        $this->mensagem .= 'Edição efetuada com sucesso.<br />';
        header('Location: educar_escola_lst.php');
        die();
      }
    }

    echo "<script>alert('É necessário adicionar pelo menos um módulo!')</script>";
    $this->mensagem = 'Edição não realizada.<br />';
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    $obj = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola,
      $this->ref_ano, NULL, $this->pessoa_logada, NULL, NULL, NULL, 0);

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $obj  = new clsPmieducarAnoLetivoModulo($this->ref_ano, $this->ref_ref_cod_escola);
      $excluiu1 = $obj->excluirTodos();

      if ($excluiu1) {
        $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
        header('Location: educar_escola_lst.php');
        die();
      }

      $this->mensagem = 'Exclusão não realizada.<br />';
      return FALSE;
    }

    $this->mensagem = 'Exclusão não realizada.<br />';
    return FALSE;
  }

  /**
   * Verifica se ao menos uma das datas as datas de início dos módulos é do
   * mesmo ano letivo da escola. Em caso de erro, configura a mensagem de
   * erro que é retornado pelo formulário.
   *
   * @access private
   * @param  array $modulos O array associativo recebido via POST pelo formulário.
   * @return bool  FALSE caso nenhuma das datas esteja no mesmo ano letivo da escola.
   */
  function _verificaModuloDatas(array $modulos)
  {
    $dates = array();
    foreach ($modulos as $modulo) {
      $dates[] = $modulo['data_inicio_'];
    }

    try {
      App_Date_Utils::datesYearAtLeast($dates, $this->ref_ano, 1);
    }
    catch (App_Date_Exception $e) {
      $this->mensagem = $e->getMessage();
      return FALSE;
    }

    return TRUE;
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
/**
 * Realiza validação client-side do formulário.
 */
function incluir()
{
  var phpjs     = ied_phpjs.getInstance();
  var startDate = null;
  var endDate   = null;

  startDate = document.getElementById('data_inicio').value.split('/');
  endDate   = document.getElementById('data_fim').value.split('/');

  if ('' === document.getElementById('ref_cod_modulo').value) {
    alert('É necessário selecionar um "módulo".');
    return false;
  }

  if (!phpjs.checkdate(startDate[1], startDate[0], startDate[2])) {
    document.getElementById('data_inicio').className = 'formdestaque';
    alert('Preencha o campo "Data Início" corretamente!');
    document.getElementById('data_inicio').focus();
    return false;
  }

  if (!phpjs.checkdate(endDate[1], endDate[0], endDate[2])) {
    document.getElementById('data_fim').className = 'formdestaque';
    alert('Preencha o campo "Data Fim" corretamente!');
    document.getElementById('data_fim').focus();
    return false;
  }

  startDate = new Date(parseInt(startDate[2], 10), parseInt(startDate[1], 10) - 1, parseInt(startDate[0], 10));
  endDate   = new Date(parseInt(endDate[2], 10), parseInt(endDate[1], 10) - 1, parseInt(endDate[0], 10));

  if (endDate < startDate) {
    alert('"Data Início" não pode ser posterior a "Data Fim".');
    return false;
  }

  document.getElementById('incluir_modulo').value = 'S';
  document.getElementById('tipoacao').value = '';
  acao();
}
</script>