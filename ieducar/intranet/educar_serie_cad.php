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
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - S&eacute;rie');
    $this->processoAp = '583';
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

  var $cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_curso;
  var $nm_serie;
  var $etapa_curso;
  var $concluinte;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $intervalo;

  var $ref_cod_instituicao;

  var $disciplina_serie;
  var $ref_cod_disciplina;
  var $incluir_disciplina;
  var $excluir_disciplina;

  var $idade_inicial;
  var $idade_final;

  var $regra_avaliacao_id;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_serie=$_GET['cod_serie'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3,
      'educar_serie_lst.php');

    if (is_numeric($this->cod_serie)) {
      $obj = new clsPmieducarSerie($this->cod_serie);
      $registro  = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $obj_curso_det = $obj_curso->detalhe();
        $this->ref_cod_instituicao = $obj_curso_det['ref_cod_instituicao'];
        $this->fexcluir = $obj_permissoes->permissao_excluir(583,
          $this->pessoa_logada,3);

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == "Editar") ?
      "educar_serie_det.php?cod_serie={$registro["cod_serie"]}" :
      "educar_serie_lst.php";

    $this->nome_url_cancelar = "Cancelar";

    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach($_POST as $campo => $val) {
        $this->$campo = ($this->$campo) ? $this->$campo : $val;
      }
    }

    // primary keys
    $this->campoOculto("cod_serie", $this->cod_serie);

    $obrigatorio = TRUE;
    $get_curso = TRUE;
    include('include/pmieducar/educar_campo_lista.php');

    $this->campoTexto("nm_serie", "S&eacute;rie", $this->nm_serie, 30, 255, TRUE);

    $opcoes = array("" => "Selecione");

    if ($this->ref_cod_curso) {
      $objTemp = new clsPmieducarCurso();
      $lista = $objTemp->lista($this->ref_cod_curso, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, 1);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes_["{$registro['cod_curso']}"] = "{$registro['qtd_etapas']}";
        }
      }

      for ($i=1; $i <= $opcoes_["{$registro['cod_curso']}"]; $i++) {
        $opcoes[$i] = "Etapa {$i}";
      }
    }

    $this->campoLista('etapa_curso', 'Etapa Curso', $opcoes, $this->etapa_curso);

    // Regra de avaliação
    $mapper = new RegraAvaliacao_Model_RegraDataMapper();
    $regras = array();
    if (!is_null($this->ref_cod_instituicao)) {
      $regras = $mapper->findAll(array(),
        array('instituicao' => $this->ref_cod_instituicao)
      );
      $regras = CoreExt_Entity::entityFilterAttr($regras, 'id', 'nome');
    }

    $this->campoLista('regra_avaliacao_id', 'Regra Avaliação', $regras, $this->regra_avaliacao_id);

    $opcoes = array('' => 'Selecione', 1 => 'n&atilde;o', 2 => 'sim');

    $this->campoLista('concluinte', 'Concluinte', $opcoes, $this->concluinte);

    $this->campoMonetario('carga_horaria', 'Carga Hor&aacute;ria', $this->carga_horaria, 7, 7, TRUE);

    $this->campoNumero('dias_letivos', 'Dias letivos', $this->dias_letivos, 3, 3, TRUE);

    $this->campoNumero('intervalo', 'Intervalo', $this->intervalo, 2, 2, TRUE);

    $this->campoNumero('idade_inicial', 'Faixa et&aacute;ria', $this->idade_inicial,
      2, 2, FALSE, '', '', FALSE, FALSE, TRUE);

    $this->campoNumero('idade_final', '&nbsp;até', $this->idade_final, 2, 2, FALSE);

		$this->campoMemo( "observacao_historico", "Observa&ccedil;&atilde;o histórico", $this->observacao_historico, 60, 5, false );
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->carga_horaria = str_replace(".", "", $this->carga_horaria);
    $this->carga_horaria = str_replace(",", ".", $this->carga_horaria);

    $obj = new clsPmieducarSerie(NULL, NULL, $this->pessoa_logada, $this->ref_cod_curso,
      $this->nm_serie, $this->etapa_curso, $this->concluinte, $this->carga_horaria,
      NULL, NULL, 1, $this->intervalo, $this->idade_inicial, $this->idade_final,
      $this->regra_avaliacao_id, $this->observacao_historico, $this->dias_letivos);

    $cadastrou = $obj->cadastra();

    if ($cadastrou) {
      $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
      header("Location: educar_serie_lst.php");
      die();
    }

    $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
    echo "<!--\nErro ao cadastrar clsPmieducarSerie\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_curso ) && is_string( $this->nm_serie ) && is_numeric( $this->etapa_curso ) && is_numeric( $this->concluinte ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->intervalo )\n-->";
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->carga_horaria = str_replace(".", "", $this->carga_horaria);
    $this->carga_horaria = str_replace(",", ".", $this->carga_horaria);

    $obj = new clsPmieducarSerie($this->cod_serie, $this->pessoa_logada, NULL,
      $this->ref_cod_curso, $this->nm_serie, $this->etapa_curso, $this->concluinte,
      $this->carga_horaria, NULL, NULL, 1, $this->intervalo, $this->idade_inicial,
      $this->idade_final, $this->regra_avaliacao_id, $this->observacao_historico, $this->dias_letivos);

    $editou = $obj->edita();
    if ($editou) {
      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      header("Location: educar_serie_lst.php");
      die();
    }

    $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao editar clsPmieducarSerie\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_serie ) && is_numeric( $this->pessoa_logada ) )\n-->";
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj = new clsPmieducarSerie($this->cod_serie, $this->pessoa_logada, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
      header( "Location: educar_serie_lst.php" );
      die();
    }

    $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao excluir clsPmieducarSerie\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_serie ) && is_numeric( $this->pessoa_logada ) )\n-->";
    return FALSE;
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
?>
<script type="text/javascript">
function getRegra()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  var campoRegras = document.getElementById('regra_avaliacao_id');
  campoRegras.length = 1;
  campoRegras.disabled = true;
  campoRegras.options[0].text = 'Carregando regras';

  var xml_qtd_etapas = new ajax(RegrasInstituicao);
  xml_qtd_etapas.envia("educar_serie_regra_xml.php?ins=" + campoInstituicao);
}

function EtapasCurso(xml_qtd_etapas)
{
  var campoEtapas = document.getElementById('etapa_curso');
  var DOM_array = xml_qtd_etapas.getElementsByTagName('curso');

  if (DOM_array.length) {
    campoEtapas.length = 1;
    campoEtapas.options[0].text = 'Selecione uma etapa';
    campoEtapas.disabled = false;

    var etapas;
    etapas = DOM_array[0].getAttribute("qtd_etapas");

    for (var i = 1; i<=etapas;i++) {
      campoEtapas.options[i] = new Option("Etapa "+i , i, false, false);
    }
  }
  else {
    campoEtapas.options[0].text = 'O curso não possui nenhuma etapa';
  }
}

function RegrasInstituicao(xml_qtd_regras)
{
  var campoRegras = document.getElementById('regra_avaliacao_id');
  var DOM_array = xml_qtd_regras.getElementsByTagName('regra');

  if (DOM_array.length) {
    campoRegras.length = 1;
    campoRegras.options[0].text = 'Selecione uma regra';
    campoRegras.disabled = false;

    var loop = DOM_array.length;

    for (var i = 0; i < loop;i++) {
      campoRegras.options[i] = new Option(DOM_array[i].firstChild.data, i, false, false);
    }
  }
  else {
	  campoRegras.options[0].text = 'A instituição não possui uma Regra de Avaliação';
  }
}

document.getElementById('ref_cod_curso').onchange = function()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;

  var campoEtapas = document.getElementById('etapa_curso');
  campoEtapas.length = 1;
  campoEtapas.disabled = true;
  campoEtapas.options[0].text = 'Carregando etapas';

  var xml_qtd_etapas = new ajax(EtapasCurso);
  xml_qtd_etapas.envia("educar_curso_xml2.php?cur=" + campoCurso);
}

/**
 * Dispara eventos durante onchange da select ref_cod_instituicao.
 */
document.getElementById('ref_cod_instituicao').onchange = function()
{
  // Essa ação é a padrão do item, via include
  getCurso();

  // Requisição Ajax para as Regras de Avaliação
  getRegra();
}
</script>
