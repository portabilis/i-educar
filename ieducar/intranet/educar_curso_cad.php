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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Curso');
    $this->processoAp = '566';
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

  var $cod_curso;
  var $ref_usuario_cad;
  var $ref_cod_tipo_regime;
  var $ref_cod_nivel_ensino;
  var $ref_cod_tipo_ensino;
  var $nm_curso;
  var $sgl_curso;
  var $qtd_etapas;
  var $carga_horaria;
  var $ato_poder_publico;
  var $habilitacao;
  var $objetivo_curso;
  var $publico_alvo;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_usuario_exc;
  var $ref_cod_instituicao;
  var $padrao_ano_escolar;
  var $hora_falta;

  var $incluir;
  var $excluir_;
  var $habilitacao_curso;
  var $curso_sem_avaliacao  = true;

  var $multi_seriado;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_curso = $_GET['cod_curso'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(566, $this->pessoa_logada, 3,
      'educar_curso_lst.php');

    if (is_numeric($this->cod_curso)) {
      $obj = new clsPmieducarCurso( $this->cod_curso );
      $registro  = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $this->fexcluir = $obj_permissoes->permissao_excluir(566,
          $this->pessoa_logada, 3);

        $retorno = 'Editar';
      }
    }
    $this->url_cancelar = ($retorno == 'Editar') ?
      "educar_curso_det.php?cod_curso={$registro["cod_curso"]}" : 'educar_curso_lst.php';

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        $this->$campo = ($this->$campo) ? $this->$campo : $val;
      }
    }

    if ($_POST['habilitacao_curso']) {
      $this->habilitacao_curso = unserialize(urldecode($_POST['habilitacao_curso']));
    }

    $qtd_habilitacao = (count($this->habilitacao_curso) == 0) ?
      1 : (count($this->habilitacao_curso) + 1);

    if (is_numeric($this->cod_curso) && $_POST['incluir'] != 'S' &&
      empty($_POST['excluir_'])) {

      $obj = new clsPmieducarHabilitacaoCurso(NULL, $this->cod_curso);
      $registros = $obj->lista(NULL, $this->cod_curso);

      if ($registros) {
        foreach ($registros as $campo) {
          $this->habilitacao_curso[$campo[$qtd_habilitacao]]['ref_cod_habilitacao_'] =
            $campo['ref_cod_habilitacao'];

          $qtd_habilitacao++;
        }
      }
    }

    if ($_POST['habilitacao']) {
      $this->habilitacao_curso[$qtd_habilitacao]['ref_cod_habilitacao_'] =
        $_POST['habilitacao'];

      $qtd_habilitacao++;
      unset($this->habilitacao);
    }

    // primary keys
    $this->campoOculto('cod_curso', $this->cod_curso);

    $obrigatorio = true;
    include('include/pmieducar/educar_campo_lista.php');

    // Nível ensino
    $opcoes = array( '' => 'Selecione' );

    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarNivelEnsino();
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL,1,$this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_nivel_ensino']] = $registro['nm_nivel'];
        }
      }
    }

    $script = "javascript:showExpansivelIframe(520, 230, 'educar_nivel_ensino_cad_pop.php');";
    if ($this->ref_cod_instituicao) {
      $script = "<img id='img_nivel_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
    }
    else {
      $script = "<img id='img_nivel_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
    }

    $this->campoLista('ref_cod_nivel_ensino', 'N&iacute;vel Ensino', $opcoes,
      $this->ref_cod_nivel_ensino, '', FALSE, '', $script);

    // Tipo ensino
    $opcoes = array('' => 'Selecione');

    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarTipoEnsino();
      $objTemp->setOrderby("nm_tipo");
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, 1,
        $this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_tipo_ensino']] = $registro['nm_tipo'];
        }
      }
    }

    $script = "javascript:showExpansivelIframe(520, 150, 'educar_tipo_ensino_cad_pop.php');";
    if ($this->ref_cod_instituicao) {
      $script = "<img id='img_tipo_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
    }
    else {
      $script = "<img id='img_tipo_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
    }

    $this->campoLista('ref_cod_tipo_ensino', 'Tipo Ensino', $opcoes,
      $this->ref_cod_tipo_ensino, '', FALSE, '', $script);


    // Tipo regime
    $opcoes = array('' => 'Selecione');

    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarTipoRegime();
      $objTemp->setOrderby('nm_tipo');

      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        1, $this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_tipo_regime']] = $registro['nm_tipo'];
        }
      }
    }

    $script = "javascript:showExpansivelIframe(520, 120, 'educar_tipo_regime_cad_pop.php');";

    if ($this->ref_cod_instituicao) {
      $script = "<img id='img_tipo_regime' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
    }
    else {
      $script = "<img id='img_tipo_regime' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
    }

    $this->campoLista('ref_cod_tipo_regime', 'Tipo Regime', $opcoes,
      $this->ref_cod_tipo_regime, '', FALSE, '', $script, FALSE, FALSE);

    // Outros campos
    $this->campoTexto('nm_curso', 'Curso', $this->nm_curso, 30, 255, TRUE);

    $this->campoTexto('sgl_curso', 'Sigla Curso', $this->sgl_curso, 15, 15, TRUE);

    $this->campoNumero('qtd_etapas', 'Quantidade Etapas', $this->qtd_etapas, 2, 2, TRUE);

    if (is_numeric($this->hora_falta)) {
      $this->campoMonetario('hora_falta', 'Hora Falta',
        number_format($this->hora_falta, 2, ',', ''), 5, 5, FALSE, '', '', '');
    }
    else {
      $this->campoMonetario('hora_falta', 'Hora Falta', $this->hora_falta, 5, 5,
        FALSE, '', '', '');
    }

    $this->campoMonetario('carga_horaria', 'Carga Hor&aacute;ria',
      $this->carga_horaria, 7, 7, TRUE);

    $this->campoTexto('ato_poder_publico', 'Ato Poder P&uacute;blico',
      $this->ato_poder_publico, 30, 255, FALSE);

    $this->campoOculto('excluir_', '');
    $qtd_habilitacao = 1;
    $aux;

    $this->campoQuebra();
    if ($this->habilitacao_curso) {
      foreach ($this->habilitacao_curso as $campo) {
        if ($this->excluir_ == $campo["ref_cod_habilitacao_"]) {
          $this->habilitacao_curso[$campo["ref_cod_habilitacao"]] = NULL;
          $this->excluir_ = NULL;
        }
        else
        {
          $obj_habilitacao = new clsPmieducarHabilitacao($campo["ref_cod_habilitacao_"]);
          $obj_habilitacao_det = $obj_habilitacao->detalhe();
          $nm_habilitacao = $obj_habilitacao_det["nm_tipo"];

          $this->campoTextoInv("ref_cod_habilitacao_{$campo["ref_cod_habilitacao_"]}",
            '', $nm_habilitacao, 30, 255, FALSE, FALSE, FALSE, '',
            "<a href='#' onclick=\"getElementById('excluir_').value = '{$campo["ref_cod_habilitacao_"]}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );

          $aux[$qtd_habilitacao]["ref_cod_habilitacao_"] = $campo["ref_cod_habilitacao_"];

          $qtd_habilitacao++;
        }
      }

      unset($this->habilitacao_curso);
      $this->habilitacao_curso = $aux;
    }

    $this->campoOculto('habilitacao_curso', serialize($this->habilitacao_curso));

    // Habilitação
    $opcoes = array('' => 'Selecione');

    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarHabilitacao();
      $objTemp->setOrderby('nm_tipo');

      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1, $this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_habilitacao']] = $registro['nm_tipo'];
        }
      }
    }

    $script = "javascript:showExpansivelIframe(520, 225, 'educar_habilitacao_cad_pop.php');";
    $script = "<img id='img_habilitacao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";

    $this->campoLista('habilitacao', 'Habilita&ccedil;&atilde;o', $opcoes,
      $this->habilitacao, '', FALSE, '',
      "<a href='#' onclick=\"getElementById('incluir').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>{$script}",
      FALSE, FALSE);
    $this->campoOculto('incluir', '');
    $this->campoQuebra();

    // Padrão ano escolar
    $this->campoCheck('padrao_ano_escolar', 'Padr&atilde;o Ano Escolar', $this->padrao_ano_escolar);

    $this->campoCheck('multi_seriado', 'Multi seriado', $this->multi_seriado);

    // Objetivo do curso
    $this->campoMemo('objetivo_curso', 'Objetivo Curso', $this->objetivo_curso,
      60, 5, FALSE);

    // Público alvo
    $this->campoMemo('publico_alvo', 'P&uacute;blico Alvo', $this->publico_alvo,
      60, 5, FALSE);
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($this->habilitacao_curso && $this->incluir != 'S' && empty($this->excluir_)) {
      $this->carga_horaria     = str_replace('.', '', $this->carga_horaria);
      $this->carga_horaria     = str_replace(',', '.', $this->carga_horaria);
      $this->hora_falta        = str_replace('.', '', $this->hora_falta);
      $this->hora_falta        = str_replace(',', '.', $this->hora_falta);

      $this->padrao_ano_escolar = is_null($this->padrao_ano_escolar) ? 0 : 1;
      $this->multi_seriado = is_null($this->multi_seriado) ? 0 : 1;

      $obj = new clsPmieducarCurso(NULL, $this->pessoa_logada,
        $this->ref_cod_tipo_regime, $this->ref_cod_nivel_ensino,
        $this->ref_cod_tipo_ensino, NULL, $this->nm_curso, $this->sgl_curso,
        $this->qtd_etapas, NULL, NULL, NULL, NULL, $this->carga_horaria,
        $this->ato_poder_publico, NULL, $this->objetivo_curso,
        $this->publico_alvo, NULL, NULL, 1, NULL, $this->ref_cod_instituicao,
        $this->padrao_ano_escolar, $this->hora_falta, NULL, $this->multi_seriado);

      $cadastrou = $obj->cadastra();
      if ($cadastrou) {

        $this->habilitacao_curso = unserialize(urldecode($this->habilitacao_curso));

        if ($this->habilitacao_curso) {
          foreach ($this->habilitacao_curso as $campo) {
            $obj = new clsPmieducarHabilitacaoCurso($campo["ref_cod_habilitacao_"],
             $cadastrou);

            $cadastrou2  = $obj->cadastra();

            if (!$cadastrou2) {
              $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
              echo "<!--\nErro ao cadastrar clsPmieducarHabilitacaoCurso\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$campo["ref_cod_habilitacao_"]} ) )\n-->";
              return FALSE;
            }
          }
        }

        $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
        header("Location: educar_curso_lst.php");
        die();
      }

      $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
      echo "<!--\nErro ao cadastrar clsPmieducarCurso\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_tipo_regime ) && is_numeric( $this->ref_cod_nivel_ensino ) && is_numeric( $this->ref_cod_tipo_ensino ) && is_string( $this->nm_curso ) && is_string( $this->sgl_curso ) && is_numeric( $this->qtd_etapas ) && is_numeric( $this->frequencia_minima ) && is_numeric( $this->media ) && is_numeric( $this->falta_ch_globalizada ) && is_numeric( $this->edicao_final ) && is_string( $this->data_inicio ) && is_string( $this->data_fim )\n-->";
      return FALSE;
    }

    return TRUE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($this->habilitacao_curso && $this->incluir != 'S' && empty($this->excluir_)) {
      $this->carga_horaria     = str_replace('.', '', $this->carga_horaria);
      $this->carga_horaria     = str_replace(',', '.', $this->carga_horaria);
      $this->hora_falta        = str_replace('.', '', $this->hora_falta);
      $this->hora_falta        = str_replace(',', '.', $this->hora_falta);

      $this->padrao_ano_escolar = is_null($this->padrao_ano_escolar) ? 0 : 1;
      $this->multi_seriado = is_null($this->multi_seriado) ? 0 : 1;

      $obj = new clsPmieducarCurso($this->cod_curso, NULL, $this->ref_cod_tipo_regime,
        $this->ref_cod_nivel_ensino, $this->ref_cod_tipo_ensino, NULL,
        $this->nm_curso, $this->sgl_curso, $this->qtd_etapas, NULL, NULL, NULL,
        NULL, $this->carga_horaria, $this->ato_poder_publico, NULL,
        $this->objetivo_curso, $this->publico_alvo, NULL, NULL, 1,
        $this->pessoa_logada, $this->ref_cod_instituicao,
        $this->padrao_ano_escolar, $this->hora_falta, NULL, $this->multi_seriado);

      $editou = $obj->edita();
      if ($editou) {
        $this->habilitacao_curso = unserialize(urldecode($this->habilitacao_curso));
        $obj  = new clsPmieducarHabilitacaoCurso(NULL, $this->cod_curso);
        $excluiu = $obj->excluirTodos();

        if ($excluiu) {
          if ($this->habilitacao_curso) {
            foreach ($this->habilitacao_curso as $campo) {
              $obj = new clsPmieducarHabilitacaoCurso(
                $campo["ref_cod_habilitacao_"], $this->cod_curso
              );

              $cadastrou2  = $obj->cadastra();

              if (!$cadastrou2) {
                $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
                echo "<!--\nErro ao editar clsPmieducarHabilitacaoCurso\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_curso ) && is_numeric( {$campo["ref_cod_habilitacao_"]} ) )\n-->";
                return FALSE;
              }
            }
          }
        }

        $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
        header("Location: educar_curso_lst.php");
        die();
      }

      $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
      echo "<!--\nErro ao editar clsPmieducarCurso\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_curso ) && is_numeric( $this->pessoa_logada ) )\n-->";
      return FALSE;
    }

    return TRUE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj = new clsPmieducarCurso($this->cod_curso, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, 0, $this->pessoa_logada);

    $excluiu = $obj->excluir();
    if ($excluiu) {
      $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
      header("Location: educar_curso_lst.php");
      die();
    }

    $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao excluir clsPmieducarCurso\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_curso ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
function getNivelEnsino(xml_nivel_ensino)
{
  var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
  var DOM_array = xml_nivel_ensino.getElementsByTagName('nivel_ensino');

  if (DOM_array.length) {
    campoNivelEnsino.length = 1;
    campoNivelEnsino.options[0].text = 'Selecione um nível de ensino';
    campoNivelEnsino.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_nivel_ensino"),
        false, false
      );
    }
  }
  else {
    campoNivelEnsino.options[0].text = 'A instituição não possui nenhum nível de ensino';
  }
}

function getTipoEnsino(xml_tipo_ensino)
{
  var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
  var DOM_array = xml_tipo_ensino.getElementsByTagName('tipo_ensino');

  if (DOM_array.length) {
    campoTipoEnsino.length = 1;
    campoTipoEnsino.options[0].text = 'Selecione um tipo de ensino';
    campoTipoEnsino.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('cod_tipo_ensino'),
        false, false
      );
    }
  }
  else {
    campoTipoEnsino.options[0].text = 'A instituição não possui nenhum tipo de ensino';
  }
}

function getTipoRegime(xml_tipo_regime)
{
  var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
  var DOM_array = xml_tipo_regime.getElementsByTagName( "tipo_regime" );

  if(DOM_array.length)
  {
    campoTipoRegime.length = 1;
    campoTipoRegime.options[0].text = 'Selecione um tipo de regime';
    campoTipoRegime.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoTipoRegime.options[campoTipoRegime.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_tipo_regime"),
        false, false
      );
    }
  }
  else {
    campoTipoRegime.options[0].text = 'A instituição não possui nenhum tipo de regime';
  }
}

function getHabilitacao(xml_habilitacao)
{
  var campoHabilitacao = document.getElementById('habilitacao');
  var DOM_array = xml_habilitacao.getElementsByTagName( "habilitacao" );

  if (DOM_array.length) {
    campoHabilitacao.length = 1;
    campoHabilitacao.options[0].text = 'Selecione uma habilitação';
    campoHabilitacao.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoHabilitacao.options[campoHabilitacao.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_habilitacao"),
        false, false
      );
    }
  }
  else {
    campoHabilitacao.options[0].text = 'A instituição não possui nenhuma habilitação';
  }
}

document.getElementById('ref_cod_instituicao').onchange = function()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
  campoNivelEnsino.length = 1;
  campoNivelEnsino.disabled = true;
  campoNivelEnsino.options[0].text = 'Carregando nível de ensino';

  var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
  campoTipoEnsino.length = 1;
  campoTipoEnsino.disabled = true;
  campoTipoEnsino.options[0].text = 'Carregando tipo de ensino';

  var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
  campoTipoRegime.length = 1;
  campoTipoRegime.disabled = true;
  campoTipoRegime.options[0].text = 'Carregando tipo de regime';

  var campoHabilitacao = document.getElementById('habilitacao');
  campoHabilitacao.length = 1;
  campoHabilitacao.disabled = true;
  campoHabilitacao.options[0].text = 'Carregando habilitação';

  var xml_nivel_ensino = new ajax(getNivelEnsino);
  xml_nivel_ensino.envia("educar_nivel_ensino_xml.php?ins="+campoInstituicao);

  var xml_tipo_ensino = new ajax(getTipoEnsino);
  xml_tipo_ensino.envia("educar_tipo_ensino_xml.php?ins="+campoInstituicao);

  var xml_tipo_regime = new ajax(getTipoRegime);
  xml_tipo_regime.envia("educar_tipo_regime_xml.php?ins="+campoInstituicao);

  var xml_habilitacao = new ajax(getHabilitacao);
  xml_habilitacao.envia("educar_habilitacao_xml.php?ins="+campoInstituicao);

  if (this.value == '') {
    $('img_nivel_ensino').style.display   = 'none;';
    $('img_tipo_regime').style.display    = 'none;';
    $('img_tipo_ensino').style.display    = 'none;';
  }
  else {
    $('img_nivel_ensino').style.display   = '';
    $('img_tipo_regime').style.display    = '';
    $('img_tipo_ensino').style.display    = '';
  }
}
</script>
