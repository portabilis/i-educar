<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

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

  function Gerar()
  {
    $this->titulo = 'Curso - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET AS $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $lista_busca = array(
      'Curso',
      'N&iacute;vel Ensino',
      'Tipo Ensino'
    );

    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
    if ($nivel_usuario == 1)
      $lista_busca[] = 'Institui&ccedil;&atilde;o';

    $this->addCabecalhos($lista_busca);

    include('include/pmieducar/educar_campo_lista.php');

    // outros Filtros
    $this->campoTexto('nm_curso', 'Curso', $this->nm_curso, 30, 255, FALSE);

    // outros de Foreign Keys
    $opcoes = array('' => 'Selecione');

    $todos_niveis_ensino = "nivel_ensino = new Array();\n";
    $objTemp = new clsPmieducarNivelEnsino();
    $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, 1);

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
          $todos_niveis_ensino .= "nivel_ensino[nivel_ensino.length] = new Array({$registro["cod_nivel_ensino"]},'{$registro["nm_nivel"]}', {$registro["ref_cod_instituicao"]});\n";
      }
    }
    echo "<script>{$todos_niveis_ensino}</script>";

    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarNivelEnsino();
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1, $this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_nivel_ensino']] = $registro['nm_nivel'];
        }
      }
    }

    $this->campoLista('ref_cod_nivel_ensino', 'N&iacute;vel Ensino', $opcoes,
      $this->ref_cod_nivel_ensino, NULL, NULL, NULL, NULL, NULL, FALSE);

    $opcoes = array('' => 'Selecione');

    $todos_tipos_ensino = "tipo_ensino = new Array();\n";
    $objTemp = new clsPmieducarTipoEnsino();
    $objTemp->setOrderby('nm_tipo');
    $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, 1);

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $todos_tipos_ensino .= "tipo_ensino[tipo_ensino.length] = new Array({$registro["cod_tipo_ensino"]},'{$registro["nm_tipo"]}', {$registro["ref_cod_instituicao"]});\n";
      }
    }
    echo "<script>{$todos_tipos_ensino}</script>";

    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarTipoEnsino();
      $objTemp->setOrderby("nm_tipo");

      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, 1,
        $this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes["{$registro['cod_tipo_ensino']}"] = $registro['nm_tipo'];
        }
      }
    }

    $this->campoLista('ref_cod_tipo_ensino', 'Tipo Ensino', $opcoes,
      $this->ref_cod_tipo_ensino, '', FALSE, '', '', '', FALSE);

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET["pagina_{$this->nome}"]) ?
      $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite : 0;

    $obj_curso = new clsPmieducarCurso();
    $obj_curso->setOrderby('nm_curso ASC');
    $obj_curso->setLimite($this->limite, $this->offset);

    $lista = $obj_curso->lista(
      NULL,
      NULL,
      NULL,
      $this->ref_cod_nivel_ensino,
      $this->ref_cod_tipo_ensino,
      NULL,
      $this->nm_curso,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      NULL,
      $this->ref_cod_instituicao
    );

    $total = $obj_curso->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino($registro['ref_cod_nivel_ensino']);
        $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
        $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

        $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino($registro['ref_cod_tipo_ensino']);
        $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
        $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

        $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

        $lista_busca = array(
          "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["nm_curso"]}</a>",
          "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_nivel_ensino"]}</a>",
          "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_tipo_ensino"]}</a>"
        );

        if ($nivel_usuario == 1) {
          $lista_busca[] = "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_instituicao"]}</a>";
        }

        $this->addLinhas($lista_busca);
      }
    }

    $this->addPaginador2("educar_curso_lst.php", $total, $_GET, $this->nome, $this->limite);

    $obj_permissoes = new clsPermissoes();
    if( $obj_permissoes->permissao_cadastra(566, $this->pessoa_logada, 3)) {
      $this->acao = "go(\"educar_curso_cad.php\")";
      $this->nome_acao = "Novo";
    }

    $this->largura = "100%";

    $this->breadcrumb('Listagem de cursos', [
        url('intranet/educar_index.php') => 'Escola',
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
?>
<script type="text/javascript">
function getNivelEnsino()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');

  campoNivelEnsino.length = 1;
  for (var j = 0; j < nivel_ensino.length; j++) {
    if (nivel_ensino[j][2] == campoInstituicao) {
      campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option(
        nivel_ensino[j][1], nivel_ensino[j][0], false, false
      );
    }
  }
}

function getTipoEnsino()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');

  campoTipoEnsino.length = 1;
  for (var j = 0; j < tipo_ensino.length; j++) {
    if (tipo_ensino[j][2] == campoInstituicao) {
      campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option(
        tipo_ensino[j][1], tipo_ensino[j][0], false, false
      );
    }
  }
}

document.getElementById('ref_cod_instituicao').onchange = function()
{
  getNivelEnsino();
  getTipoEnsino();
}
</script>
