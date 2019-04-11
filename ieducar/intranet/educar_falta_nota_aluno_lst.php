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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Faltas/Notas Aluno');
    $this->processoAp = "642";
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

  var $ref_ref_cod_escola;
  var $ref_cod_matricula;
  var $ref_cod_turma;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_ref_cod_serie;

  var $ref_cod_aluno;
  var $nm_aluno;
  var $aprovado;

  function Gerar()
  {
    $this->titulo = "Faltas/Notas Aluno - Listagem";

    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = $val === '' ? NULL : $val;
    }

    

    $lista_busca = array(
      'Aluno',
      'Matrícula',
      'Turma',
      'Série',
      'Curso'
    );

    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      $lista_busca[] = 'Escola';
      $lista_busca[] = 'Institui&ccedil;&atilde;o';
    }
    else if ($nivel_usuario == 2) {
      $lista_busca[] = 'Escola';
    }

    $this->addCabecalhos($lista_busca);

    $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, FALSE,
      FALSE, FALSE, '', "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/>", '', '', TRUE);
    $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

    $get_escola             = TRUE;
    $get_curso              = TRUE;
    $sem_padrao             = TRUE;
    $get_escola_curso_serie = TRUE;
    $get_turma              = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }

    $opcoes = array(
      '' => 'Selecione',
      1  => 'Aprovado',
      2  => 'Reprovado',
      3  => 'Cursando'
    );

    $this->campoLista('aprovado', 'Situa&ccedil;&atilde;o', $opcoes,
      $this->aprovado, '', '', '', '', FALSE, FALSE);

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite-$this->limite : 0;

    $obj_nota_aluno = new clsPmieducarMatriculaTurma();
    $obj_nota_aluno->setOrderby('ref_cod_matricula ASC');
    $obj_nota_aluno->setLimite($this->limite, $this->offset);

    $aparece = TRUE;

    $lista = $obj_nota_aluno->lista(
      $this->ref_cod_matricula,
      $this->ref_cod_turma,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $this->ref_ref_cod_serie,
      $this->ref_cod_curso,
      $this->ref_ref_cod_escola,
      $this->ref_cod_instituicao,
      $this->ref_cod_aluno,
      NULL,
      $this->aprovado,
      NULL,
      NULL,
      NULL,
      TRUE,
      FALSE,
      NULL,
      1,
      TRUE,
      TRUE,
      NULL,
      NULL,
      NULL,
      NULL,
      $aparece
    );

    $total = $obj_nota_aluno->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      $ref_cod_serie  = '';
      $nm_serie       = '';
      $ref_cod_escola = '';
      $nm_escola      = '';

      foreach ($lista as $registro) {
        if ($registro['ref_ref_cod_serie']  != '' &&
          $ref_cod_serie !=  $registro['ref_ref_cod_serie']
        ) {
          $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
          $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
          $ref_cod_serie = $registro['ref_ref_cod_serie'];
          $nm_serie = $det_ref_cod_serie['nm_serie'];
        }
        elseif ($registro['ref_ref_cod_serie'] == '') {
          $ref_cod_serie = '';
          $nm_serie = '';
        }

        if ($registro['ref_ref_cod_escola']  != '' &&
          $ref_cod_escola !=  $registro['ref_ref_cod_escola']
        ) {
          $obj_ref_cod_escola = new clsPmieducarEscola( $registro['ref_ref_cod_escola']);
          $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
          $ref_cod_escola = $registro['ref_ref_cod_escola'];
          $nm_escola = $det_ref_cod_escola['nome'];
        }
        elseif ($registro['ref_ref_cod_escola']  == '') {
          $ref_cod_escola = '';
          $nm_escola = '';
        }

        // Itens a mostrar na listagem de alunos
        $lista_busca = array();

        // Variáveis para a geração do link
        $path = '/module/Avaliacao/boletim';
        $params = array('query' => array('matricula' => $registro['ref_cod_matricula']));

        // Instância de UrlHelper
        $url = CoreExt_View_Helper_UrlHelper::getInstance();

        $lista_busca[] = $url->l($registro['nome'], $path, $params);
        $lista_busca[] = $url->l($registro['ref_cod_matricula'], $path, $params);
        $lista_busca[] = $url->l($registro['nm_turma'], $path, $params);

        $lista_busca[] = $url->l(
          $registro['ref_ref_cod_serie'] ? $registro['ref_ref_cod_serie'] : '',
          $path, $params
        );

        $lista_busca[] = $url->l($registro['nm_curso'], $path, $params);

        if ($nivel_usuario == 1) {
          $lista_busca[] = $url->l(
            $registro['ref_ref_cod_escola'] ? $nm_escola : '-',
            $path, $params
          );

          $lista_busca[] = $url->l(
            $registro['nm_instituicao'], $path, $params
          );
        }
        elseif ($nivel_usuario == 2) {
          $lista_busca[] = $url->l(
            $registro['ref_ref_cod_escola'] ? $registro['ref_ref_cod_escola'] : '-',
            $path, $params
          );
        }

        $this->addLinhas($lista_busca);
      }
    }

    $this->addPaginador2('educar_falta_nota_aluno_lst.php', $total, $_GET,
      $this->nome, $this->limite);

    $this->largura = '100%';
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
function pesquisa_aluno()
{
  pesquisa_valores_popless('educar_pesquisa_aluno.php')
}


document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
  getTurma();
}
</script>
