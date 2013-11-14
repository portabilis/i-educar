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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matricula Turma');
    $this->processoAp = 578;
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

  var $ref_cod_matricula;
  var $ref_cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  var $ref_cod_serie;
  var $ref_cod_escola;
  var $ref_cod_turma_origem;
  var $ref_cod_curso;
  var $ref_cod_instituicao;

  var $sequencial;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Selecione uma turma para enturmar ou remover a enturmação';

    $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

    if (!$this->ref_cod_matricula) {
      header('Location: educar_matricula_lst.php');
      die;
    }

    $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
    $det_matricula = $obj_matricula->detalhe();
    $this->ref_cod_curso = $det_matricula['ref_cod_curso'];

    $this->ref_cod_serie  = $det_matricula['ref_ref_cod_serie'];
    $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
    $this->ref_cod_turma = $_GET['ref_cod_turma'];

    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->addCabecalhos(array(
      'Turma',
      'Enturmado'
    ));

    // Busca dados da matricula
    $obj_ref_cod_matricula = new clsPmieducarMatricula();
    $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

    $obj_aluno = new clsPmieducarAluno();
    $det_aluno = array_shift($obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1));

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
      $det_escola = $obj_escola->detalhe();

    if ($det_escola['nome']) {
      $this->campoRotulo('nm_escola', 'Escola', $det_escola['nome']);
    }

    $this->campoRotulo('nm_pessoa', 'Nome do Aluno', $det_aluno['nome_aluno']);

    // Filtros de foreign keys
    $opcoes = array('' => 'Selecione');

    // Opções de turma
    $objTemp = new clsPmieducarTurma();
    $lista = $objTemp->lista3(NULL, NULL, NULL, $this->ref_cod_serie,
      $this->ref_cod_escola, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      $this->ref_cod_curso);

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $opcoes[$registro['cod_turma']] = $registro['nm_turma'];
      }

      $this->exibirBotaoSubmit = false;

    }

    #$this->campoLista('ref_cod_turma_', 'Turma', $opcoes, $this->ref_cod_turma);

    // outros filtros
    $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
    $this->campoOculto('ref_cod_serie', '');
    $this->campoOculto('ref_cod_turma', '');
    $this->campoOculto('ref_cod_escola', '');

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_matricula_turma = new clsPmieducarTurma();
    $obj_matricula_turma->setOrderby('data_cadastro ASC');
    $obj_matricula_turma->setLimite($this->limite, $this->offset);

    $lista = $obj_matricula_turma->lista3($this->ref_cod_turma, NULL, NULL,
      $this->ref_cod_serie,$this->ref_cod_escola, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL,1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL,$this->ref_cod_curso, NULL, NULL, NULL, NULL, NULL, NULL, TRUE);

    if (is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_curso) &&
      is_numeric($this->ref_cod_escola)) {
      $sql = "
SELECT
  t.cod_turma, t.ref_usuario_exc, t.ref_usuario_cad, t.ref_ref_cod_serie,
  t.ref_ref_cod_escola, t.ref_cod_infra_predio_comodo, t.nm_turma, t.sgl_turma,
  t.max_aluno, t.multiseriada, t.data_cadastro, t.data_exclusao, t.ativo,
  t.ref_cod_turma_tipo, t.hora_inicial, t.hora_final, t.hora_inicio_intervalo,
  t.hora_fim_intervalo, t.ref_cod_regente, t.ref_cod_instituicao_regente,
  t.ref_cod_instituicao, t.ref_cod_curso, t.ref_ref_cod_serie_mult,
  t.ref_ref_cod_escola_mult
FROM
  pmieducar.turma t
WHERE
  t.ref_ref_cod_serie_mult = {$this->ref_cod_serie}
  AND t.ref_ref_cod_escola={$this->ref_cod_escola}
  AND t.ativo = '1'
  AND t.ref_ref_cod_escola = '{$this->ref_cod_escola}'";

      $db = new clsBanco();
      $db->Consulta($sql);

      $lista_aux = array();
      while ($db->ProximoRegistro()) {
        $lista_aux[] = $db->Tupla();
      }

      if (is_array($lista_aux) && count($lista_aux)) {
        if (is_array($lista) && count($lista)) {
          $lista = array_merge($lista, $lista_aux);
        } else {
          $lista = $lista_aux;
        }
      }

      $total = count($lista);
    }
    else {
      $total = $obj_matricula_turma->_total;
    }

    $enturmacoesMatricula = new clsPmieducarMatriculaTurma();
    $enturmacoesMatricula = $enturmacoesMatricula->lista3($this->ref_cod_matricula, NULL, NULL,
                                                         NULL, NULL, NULL, NULL, NULL, 1);

    $turmasThisSerie = $lista;
    // lista turmas disponiveis para enturmacao, somente lista as turmas sem enturmacao
    foreach ($turmasThisSerie as $turma) {

      $turmaHasEnturmacao = false;
      foreach ($enturmacoesMatricula as $enturmacao) {
        if(! $turmaHasEnturmacao && $turma['cod_turma'] == $enturmacao['ref_cod_turma'])
          $turmaHasEnturmacao = true;
      }

      if($turmaHasEnturmacao) 
        $enturmado = "Sim";
      else
        $enturmado = "Não";

      $script = sprintf('onclick="enturmar(\'%s\',\'%s\',\'%s\',\'%s\');"',
                        $this->ref_cod_escola, $turma['ref_ref_cod_serie'],
                        $this->ref_cod_matricula, $turma['cod_turma']);

      $this->addLinhas(array(sprintf('<a href="#" %s>%s</a>', $script, $turma['nm_turma']), $enturmado));
    }

    $this->addPaginador2("educar_matricula_turma_lst.php", $total, $_GET,
        $this->nome, $this->limite);

    $obj_permissoes = new clsPermissoes();

    $this->array_botao[] = 'Voltar';
    $this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

    $this->largura = '100%';
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
function enturmar(ref_cod_escola, ref_cod_serie, ref_cod_matricula, ref_cod_turma,
  ref_cod_turma_origem)
{
  document.formcadastro.method = 'post';
  document.formcadastro.action = 'educar_matricula_turma_det.php';

  document.formcadastro.ref_cod_escola.value    = ref_cod_escola;
  document.formcadastro.ref_cod_serie.value     = ref_cod_serie;
  document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
  document.formcadastro.ref_cod_turma.value     = ref_cod_turma;

  document.formcadastro.submit();
}
</script>
