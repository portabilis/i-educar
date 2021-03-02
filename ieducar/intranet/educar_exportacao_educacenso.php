<?php

use Illuminate\Support\Facades\DB;


return new class extends clsCadastro
{
    public $pessoa_logada;

    public $ano;
    public $ref_cod_instituicao;
    public $escola_em_andamento;
    public $segunda_fase = false;
    public $nome_url_sucesso = 'Analisar';

    public function Inicializar()
    {
        $this->segunda_fase = ($_REQUEST['fase2'] == 1);

        $codigoMenu = ($this->segunda_fase ? 9998845 : 846);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            $codigoMenu,
            $this->pessoa_logada,
            7,
            'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $nomeTela = $this->segunda_fase ? '2ª fase - Situação final' : '1ª fase - Matrícula inicial';

        $this->breadcrumb($nomeTela, [
        url('intranet/educar_educacenso_index.php') => 'Educacenso',
    ]);

        $exportacao = $_POST['exportacao'];

        if ($exportacao) {
            $converted_to_iso88591 = utf8_decode($exportacao);

            $inepEscola = DB::selectOne('SELECT cod_escola_inep FROM modules.educacenso_cod_escola WHERE cod_escola = ?', [$_POST['escola']]);

            $nomeArquivo = $inepEscola->cod_escola_inep . '_' . date('dm_Hi') . '.txt';

            header('Content-type: text/plain');
            header('Content-Length: ' . strlen($converted_to_iso88591));
            header('Content-Disposition: attachment; filename=' . $nomeArquivo);
            echo $converted_to_iso88591;
            die();
        }

        $this->acao_enviar      = 'acaoExportar();';

        return 'Nova exportação';
    }

    public function Gerar()
    {
        $fase2 = $_REQUEST['fase2'];

        $dicaCampoData = 'dd/mm/aaaa';

        if ($fase2 == 1) {
            $dicaCampoData = 'A data informada neste campo, deverá ser a mesma informada na 1ª fase da exportação (Matrícula inicial).';
            $this->campoOculto('fase2', 'true');
        }

        $this->campoOculto('enable_export', (int) config('legacy.educacenso.enable_export'));
        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola']);
        $this->inputsHelper()->hidden('escola_em_andamento', [ 'value' => $this->escola_em_andamento ]);

        if (!empty($this->ref_cod_escola)) {
            Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Educacenso/Assets/Javascripts/Educacenso.js');
        }
    }

    public function Novo()
    {
        return false;
    }
}

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
   <p>Essa escola encontra-se paralisada ou extinta, portanto somente os dados dos registros 00, 30 e 40 serão analisados e exportados.</p>
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

    public function Formular()
    {
        $this->title = "i-Educar - Exporta&ccedil;&atilde;o Educacenso";
        $this->processoAp = ($_REQUEST['fase2'] == 1 ? 9998845 : 846);
    }
};
</script>
