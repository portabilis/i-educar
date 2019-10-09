<?php
// error_reporting(E_ALL);
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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';

/**
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Exporta&ccedil;&atilde;o Educacenso');
    $this->processoAp = ($_REQUEST['fase2'] == 1 ? 9998845 : 846);
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ano;
  var $ref_cod_instituicao;
  var $escola_em_andamento;
  var $segunda_fase = false;
  var $nome_url_sucesso = 'Analisar';

  function Inicializar()
  {


    $this->segunda_fase = ($_REQUEST['fase2'] == 1);

    $codigoMenu = ($this->segunda_fase ? 9998845 : 846);

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra($codigoMenu, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $nomeTela = $this->segunda_fase ? '2ª fase - Situação final' : '1ª fase - Matrícula inicial';

    $this->breadcrumb($nomeTela, [
        url('intranet/educar_educacenso_index.php') => 'Educacenso',
    ]);

    $exportacao = $_POST["exportacao"];

    if ($exportacao) {
      $converted_to_iso88591 = utf8_decode($exportacao);

      $inepEscola = DB::selectOne('SELECT cod_escola_inep FROM modules.educacenso_cod_escola WHERE cod_escola = ?', [$_POST["escola"]]);

      $nomeArquivo = $inepEscola->cod_escola_inep . '_' . date('dm_Hi') . '.txt';

      header('Content-type: text/plain');
      header('Content-Length: ' . strlen($converted_to_iso88591));
      header('Content-Disposition: attachment; filename=' . $nomeArquivo);
      echo $converted_to_iso88591;
      die();
    }

    $this->acao_enviar      = "acaoExportar();";

    return 'Nova exportação';
  }

  function Gerar()
  {
    $fase2 = $_REQUEST['fase2'];

    $dicaCampoData = 'dd/mm/aaaa';

    if ($fase2 == 1) {
      $dicaCampoData = 'A data informada neste campo, deverá ser a mesma informada na 1ª fase da exportação (Matrícula inicial).';
      $this->campoOculto("fase2", "true");
    }

    $this->campoOculto("enable_export", (int) config('legacy.educacenso.enable_export'));
    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola'));
    $this->inputsHelper()->hidden('escola_em_andamento', [ 'value' => $this->escola_em_andamento ]);

    if (!empty($this->ref_cod_escola)) {
        Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Educacenso/Assets/Javascripts/Educacenso.js');
    }

  }

  function Novo()
  {

    return false;
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

$j(function() {

    let checkIfSchoolIsActive = () => {
        let schoolId = $j("#ref_cod_escola").val();
        if (!schoolId) {
            return false;
        }

        let urlForGetSchoolActive = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'school-is-active', {
            school_id: schoolId
        });

        let options = {
            url: urlForGetSchoolActive,
            dataType: 'json',
            success: (data) => {
                $j('#escola_em_andamento').val(data['active'] ? '1' : '0');
                if (!data['active']) {
                    showNotActiveModal();
                }
            }
        };

        getResources(options);
    }

    $j('#ref_cod_escola').on('change', checkIfSchoolIsActive);

    let createNotActiveModal = () => {
        $j("body").append(`
<div id="not_active_modal" class="modal" style="display:none;">
   <p>Essa escola encontra-se paralisada ou extinta, portanto somente os dados do registro 00 serão analisados e exportados.</p>
</div>
        `);
    }
    createNotActiveModal();

    let showNotActiveModal = () => {
        $j("#not_active_modal").modal();
    }
});

function acaoExportar() {
    document.formcadastro.target='_blank';
    acao();
    document.getElementById( 'btn_enviar' ).disabled = false;
    document.getElementById( 'btn_enviar' ).value = 'Analisar';
}

function marcarCheck(idValue) {
    // testar com formcadastro
    var contaForm = document.formcadastro.elements.length;
    var campo = document.formcadastro;
    var i;

    for (i=0; i<contaForm; i++) {
        if (campo.elements[i].id == idValue) {

            campo.elements[i].checked = campo.CheckTodos.checked;
        }
    }
}
</script>
