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
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  ReservaVaga
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase {
  public function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Vagas Reservadas');
    $this->processoAp = '639';
  }
}

class indice extends clsListagem
{
  /**
   * Referência a usuário da sessão
   * @var int
   */
  var $pessoa_logada = NULL;

  /**
   * Título no topo da página
   * @var string
   */
  var $titulo = '';

  /**
   * Limite de registros por página
   * @var int
   */
  var $limite = 0;

  /**
   * Início dos registros a serem exibidos (limit)
   * @var int
   */
  var $offset = 0;

  // Atributos de mapeamento da tabela pmieducar.reserva_vaga
  var
    $cod_reserva_vaga   = NULL,
    $ref_ref_cod_escola = NULL,
    $ref_ref_cod_serie  = NULL,
    $ref_usuario_exc    = NULL,
    $ref_usuario_cad    = NULL,
    $ref_cod_aluno      = NULL,
    $data_cadastro      = NULL,
    $data_exclusao      = NULL,
    $ativo              = NULL;

  /**
   * Atributos para apresentação
   * @var mixed
   */
  var
    $ref_cod_escola      = NULL,
    $ref_cod_curso       = NULL,
    $ref_cod_instituicao = NULL,
    $nm_aluno            = NULL;

  /**
   * Sobrescreve clsListagem::Gerar().
   * @see clsListagem::Gerar()
   */
  function Gerar()
  {
    $this->titulo = 'Vagas Reservadas - Listagem';

    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }



    $lista_busca = array(
      'Aluno',
      'S&eacute;rie',
      'Curso'
    );

    // Recupera ní­vel de acesso do usuário logado
    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      $lista_busca[] = 'Escola';
      $lista_busca[] = 'Institui&ccedil;&atilde;o';
    }
    elseif ($nivel_usuario == 2) {
      $lista_busca[] = 'Escola';
    }
    $this->addCabecalhos($lista_busca);

    // Lista de opçõees para o formulário de pesquisa rápida
    $get_escola = TRUE;
    $get_curso  = TRUE;
    $get_escola_curso_serie = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    // Referência de escola
    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }
    elseif (isset($_GET['ref_cod_escola'])) {
      $this->ref_ref_cod_escola = intval($_GET['ref_cod_escola']);
    }

    // Referência de série
    if ($this->ref_cod_serie) {
      $this->ref_ref_cod_serie = $this->ref_cod_serie;
    }
    elseif (isset($_GET['ref_cod_serie'])) {
      $this->ref_ref_cod_serie = intval($_GET['ref_cod_serie']);
    }

    // Campos do formulário
    $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, FALSE, FALSE,
      FALSE, '', '<img border="0" onclick="pesquisa_aluno();" id="ref_cod_aluno_lupa" name="ref_cod_aluno_lupa" src="imagens/lupa.png" />');

    // Código do aluno (retornado de pop-up de busca da pesquisa de alunos - lupa)
    $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET["pagina_{$this->nome}"] ?
      ($_GET["pagina_{$this->nome}"] * $this->limite - $this->limite)
      : 0;

    // Instância objeto de mapeamento relacional com o tabela pmieducar.reserva_vaga
    $obj_reserva_vaga = new clsPmieducarReservaVaga();
    $obj_reserva_vaga->setOrderby('data_cadastro ASC');
    $obj_reserva_vaga->setLimite($this->limite, $this->offset);

    // Lista os registros usando os valores passados pelos filtros
    $lista = $obj_reserva_vaga->lista(
      $this->cod_reserva_vaga,
      $this->ref_ref_cod_escola,
      $this->ref_ref_cod_serie,
      NULL,
      NULL,
      $this->ref_cod_aluno,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $this->ref_cod_instituicao,
      $this->ref_cod_curso
    );

    // Pega o total de registros encontrados
    $total = $obj_reserva_vaga->_total;

    // Itera sobre resultados montando a lista de apresentação
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        // Recupera nome da série da reserva de vaga
        $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
        $det_serie = $obj_serie->detalhe();
        $nm_serie  = $det_serie['nm_serie'];

        // Recupera o nome do curso da reserva de vaga
        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $registro['ref_cod_curso'] = $det_curso['nm_curso'];

        // Recupera o nome da escola da reserva de vaga
        $obj_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
        $det_escola = $obj_escola->detalhe();
        $nm_escola = $det_escola['nome'];

        // Recupera o nome da instituição da reserva de vaga
        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        /*
         * Se for um aluno previamente cadastrado, procuramos seu nome, primeiro
         * buscando a referência de Pessoa e depois pesquisando a tabela para
         * carregar o nome
         */
        if ($registro['ref_cod_aluno']) {
          // Pesquisa por aluno para pegar o identificador de Pessoa
          $obj_aluno = new clsPmieducarAluno($registro['ref_cod_aluno']);
          $det_aluno = $obj_aluno->detalhe();
          $ref_idpes = $det_aluno['ref_idpes'];

          // Pesquisa a tabela de pessoa para recuperar o nome
          $obj_pessoa = new clsPessoa_($ref_idpes);
          $det_pessoa = $obj_pessoa->detalhe();
          $registro['ref_cod_aluno'] = $det_pessoa['nome'];
        }
        else {
          $registro['ref_cod_aluno'] = $registro['nm_aluno'] . ' (aluno externo)';
        }

        // Array de dados formatados para apresentação
        $lista_busca = array(
          "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_aluno"]}</a>",
          "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_serie}</a>",
          "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_curso"]}</a>"
        );

        // Verifica por permissões
        if ($nivel_usuario == 1) {
          $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_escola}</a>";
          $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_instituicao"]}</a>";
        }
        elseif ($nivel_usuario == 2) {
          $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_escola}</a>";
        }

        $this->addLinhas($lista_busca);
      }
    }

    $this->addPaginador2('educar_reservada_vaga_lst.php', $total, $_GET,
      $this->nome, $this->limite);

    $this->largura = '100%';

    $this->breadcrumb('Listagem de vagas reservadas', [
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
document.getElementById('ref_cod_escola').onchange = function() {
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function() {
  getEscolaCursoSerie();
}

function pesquisa_aluno() {
  pesquisa_valores_popless('educar_pesquisa_aluno.php')
}
</script>
