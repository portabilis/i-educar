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
require_once 'include/clsPDF.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Diário de Classe');
    $this->processoAp = 664;
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

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_cod_curso;
  var $ref_cod_modulo;

  var $sequencial;
  var $ano;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    return $retorno;
  }

  function Gerar()
  {
    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    $this->ano = $ano_atual = date('Y');

    $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, TRUE);

    $this->campoCheck('em_branco', 'Relatório em branco', '');
    $this->campoNumero('numero_registros', 'Número de linhas', '', 3, 3);
    $this->campoCheck('temporario', 'Gerar lista temporária?', '',
      'Gera lista de alunos mesmo para os componentes curriculares ' .
      'não definidos no quadro de horário da turma.');

    $get_escola             = TRUE;
    $obrigatorio            = TRUE;
    $exibe_nm_escola        = TRUE;
    $get_curso              = TRUE;
    $get_escola_curso_serie = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    $opcoes_turma = array('' => 'Selecione');

    if (($this->ref_ref_cod_serie && $this->ref_cod_escola) || $this->ref_cod_curso) {
      $obj_turma = new clsPmieducarTurma();
      $obj_turma->setOrderby('nm_turma ASC');
      $lst_turma = $obj_turma->lista(NULL, NULL, NULL, $this->ref_ref_cod_serie,
        $this->ref_cod_escola, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_cod_curso);

      if (is_array($lst_turma) && count($lst_turma)) {
        foreach ($lst_turma as $turma) {
          $opcoes_turma[$turma['cod_turma']] = $turma['nm_turma'];
        }
      }
    }

    $this->campoLista('ref_cod_turma', 'Turma', $opcoes_turma, $this->ref_cod_turma);

    $this->campoLista('ref_cod_modulo', 'Módulo', array('' => 'Selecione'), '');

    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }

    if ($this->get_link) {
      $this->campoRotulo('rotulo11', '-',
        sprintf('<a href="%s" target="_blank">Baixar Relatório</a>', $this->get_link)
      );
    }

    $this->url_cancelar = 'educar_index.php';
    $this->nome_url_cancelar = 'Cancelar';

    $this->acao_enviar = 'acao2()';
    $this->acao_executa_submit = FALSE;
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
function acao2()
{
  if (!acao()) {
    return false;
  }

  showExpansivelImprimir(400, 200,'',[], 'Diário de Classe');

  document.formcadastro.target = 'miolo_' + (DOM_divs.length - 1);

  document.formcadastro.submit();
}

function getModulos()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoCurso  = document.getElementById('ref_cod_curso').value;
  var campoAno    = document.getElementById('ano').value;
  var campoTurma  = document.getElementById('ref_cod_turma').value;

  var xml1 = new ajax(getModulos_XML);

  strURL = 'educar_modulo_xml.php?esc=' + campoEscola + '&ano=' + campoAno +
           '&curso=' + campoCurso + '&turma=' + campoTurma;

  xml1.envia(strURL);
}

function getModulos_XML(xml)
{
  var modulos     = xml.getElementsByTagName('ano_letivo_modulo');
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoCurso  = document.getElementById('ref_cod_curso').value;
  var campoModulo = document.getElementById('ref_cod_modulo');
  var campoAno    = document.getElementById('ano').value;

  campoModulo.length = 1;
  campoModulo.options[0] = new Option('Selecione um módulo', '', false, false);

  for (var j = 0; j < modulos.length; j++) {
    campoModulo.options[campoModulo.options.length] = new Option(
      modulos[j].firstChild.nodeValue, modulos[j].getAttribute('cod_modulo') + '-' + modulos[j].getAttribute('sequencial'),
      false, false
    );
  }

  if (campoModulo.length == 1) {
    campoModulo.options[0] = new Option('O curso não possui nenhum módulo', '', false, false);
  }
}

document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
  document.getElementById('ref_cod_curso').onchange();
}

document.getElementById('ano').onchange = function()
{
  getModulos();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
  document.getElementById('ref_cod_modulo').length = 1;
  getModulos();
}

document.getElementById('ref_cod_turma').onchange = function()
{
  document.getElementById('ref_cod_modulo').length = 1;
  getModulos();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoSerie  = document.getElementById('ref_ref_cod_serie').value;

  var xml1 = new ajax(getTurma_XML);
  strURL   = 'educar_turma_xml.php?esc=' + campoEscola + '&ser=' + campoSerie;

  xml1.envia(strURL);
}

function getTurma_XML(xml)
{
  var campoSerie = document.getElementById('ref_ref_cod_serie').value;

  var campoTurma = document.getElementById('ref_cod_turma');

  var turma = xml.getElementsByTagName('turma');

  campoTurma.length = 1;
  campoTurma.options[0] = new Option('Selecione uma Turma', '', false, false);

  for (var j = 0; j < turma.length; j++) {
    campoTurma.options[campoTurma.options.length] = new Option(
      turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false
    );
  }

  if (campoTurma.length == 1 && campoSerie != '') {
    campoTurma.options[0] = new Option('A série não possui nenhuma turma', '', false, false);
  }
}

document.formcadastro.action = 'educar_relatorio_diario_classe_proc.php';
</script>