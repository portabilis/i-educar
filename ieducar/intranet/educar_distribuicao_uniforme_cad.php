<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Distribui&ccedil;&atilde;o de uniforme");
        $this->processoAp = 578;
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_distribuicao_uniforme;

    public $ref_cod_aluno;

    public $ano;

    public $agasalho_qtd;

    public $camiseta_curta_qtd;

    public $camiseta_longa_qtd;

    public $meias_qtd;

    public $bermudas_tectels_qtd;

    public $bermudas_coton_qtd;

    public $tenis_qtd;

    public $data;

    public $agasalho_tm;

    public $camiseta_curta_tm;

    public $camiseta_longa_tm;

    public $meias_tm;

    public $bermudas_tectels_tm;

    public $bermudas_coton_tm;

    public $tenis_tm;

    public $ref_cod_escola;

    public $kit_completo;

    public $camiseta_infantil_qtd;

    public $camiseta_infantil_tm;

    public $calca_jeans_qtd;

    public $calca_jeans_tm;

    public $saia_qtd;

    public $saia_tm;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_distribuicao_uniforme=$_GET['cod_distribuicao_uniforme'];
        $this->ref_cod_aluno=$_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->cod_distribuicao_uniforme)) {

            $obj = new clsPmieducarDistribuicaoUniforme($this->cod_distribuicao_uniforme);

            $registro  = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->data = Portabilis_Date_Utils::pgSqlToBr($this->data);

                $this->kit_completo = dbBool($this->kit_completo);

                if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? "educar_distribuicao_uniforme_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&cod_distribuicao_uniforme={$registro['cod_distribuicao_uniforme']}"
            : "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}";

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Distribuições de uniforme escolar', [
            'educar_index.php' => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = !$this->$campo ?  $val : $this->$campo;
            }
        }

        $objEscola = new clsPmieducarEscola();
        $lista = $objEscola->lista();

        $escolaOpcoes = ['' => 'Selecione'];

        foreach ($lista as $escola) {
            $escolaOpcoes["{$escola['cod_escola']}"] = "{$escola['nome']}";
        }

        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

        $this->campoOculto('cod_distribuicao_uniforme', $this->cod_distribuicao_uniforme);

        $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, true);

        $this->inputsHelper()->date('data', [
            'label' => 'Data da distribuição',
            'value' => $this->data,
            'placeholder' => '',
            'size' => 10
        ]);

        $this->campoLista(
            'ref_cod_escola',
            'Escola',
            $escolaOpcoes,
            $this->ref_cod_escola,
            '',
            false,
            '(Responsável pela distribuição do uniforme)',
            '',
            false,
            true
        );

        $this->inputsHelper()->checkbox('kit_completo', [
            'label' => 'Kit completo', 'value' => $this->kit_completo
        ]);

        $this->inputsHelper()->integer('agasalho_qtd', [
            'required' => false,
            'label' => 'Quantidade de agasalhos (jaqueta e calça)',
            'value' => $this->agasalho_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline'  => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('agasalho_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->agasalho_tm,
            'max_length'  => 10,
            'size' => 10
        ]);


        $this->inputsHelper()->integer('camiseta_curta_qtd', [
            'required' => false,
            'label' => 'Quantidade de camisetas (manga curta)',
            'value' => $this->camiseta_curta_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('camiseta_curta_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->camiseta_curta_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('camiseta_longa_qtd', [
            'required' => false,
            'label' => 'Quantidade de camisetas (manga longa)',
            'value' => $this->camiseta_longa_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('camiseta_longa_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->camiseta_longa_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('camiseta_infantil_qtd', [
            'required' => false,
            'label' => 'Quantidade de camisetas infantis (sem manga)',
            'value' => $this->camiseta_infantil_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('camiseta_infantil_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->camiseta_infantil_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('calca_jeans_qtd', [
            'required' => false,
            'label' => 'Quantidade de calças jeans',
            'value' => $this->calca_jeans_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('calca_jeans_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->calca_jeans_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('meias_qtd', [
            'required' => false,
            'label' => 'Quantidade de meias',
            'value' => $this->meias_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('meias_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->meias_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('saia_qtd', [
            'required' => false,
            'label' => 'Quantidade de saias',
            'value' => $this->saia_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('saia_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->saia_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('bermudas_tectels_qtd', [
            'required' => false,
            'label' => 'Bermudas tectels (masculino)',
            'value' => $this->bermudas_tectels_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('bermudas_tectels_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->bermudas_tectels_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('bermudas_coton_qtd', [
            'required' => false,
            'label' => 'Bermudas coton (feminino)',
            'value' => $this->bermudas_coton_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('bermudas_coton_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->bermudas_coton_tm,
            'max_length' => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer('tenis_qtd', [
            'required' => false,
            'label' => 'Tênis',
            'value' => $this->tenis_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text('tenis_tm', [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->tenis_tm,
            'max_length'  => 10,
            'size' => 10
        ]);
    }

    public function Novo()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj_tmp = $obj = new clsPmieducarDistribuicaoUniforme();
        $lista_tmp = $obj_tmp->lista($this->ref_cod_aluno, $this->ano);

        if ($lista_tmp) {
            $this->mensagem = 'Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>';

            return false;
        }

        $obj = new clsPmieducarDistribuicaoUniforme(
            null,
            $this->ref_cod_aluno,
            $this->ano,
            !is_null($this->kit_completo),
            $this->agasalho_qtd,
            $this->camiseta_curta_qtd,
            $this->camiseta_longa_qtd,
            $this->meias_qtd,
            $this->bermudas_tectels_qtd,
            $this->bermudas_coton_qtd,
            $this->tenis_qtd,
            $this->data,
            $this->agasalho_tm,
            $this->camiseta_curta_tm,
            $this->camiseta_longa_tm,
            $this->meias_tm,
            $this->bermudas_tectels_tm,
            $this->bermudas_coton_tm,
            $this->tenis_tm,
            $this->ref_cod_escola,
            $this->camiseta_infantil_qtd,
            $this->camiseta_infantil_tm,
            $this->calca_jeans_qtd,
            $this->calca_jeans_tm,
            $this->saia_qtd,
            $this->saia_tm
        );

        $this->cod_distribuicao_uniforme = $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $distribuicao = new clsPmieducarDistribuicaoUniforme($this->cod_distribuicao_uniforme);
            $distribuicao = $distribuicao->detalhe();

            $auditoria = new clsModulesAuditoriaGeral('distribuicao_uniforme', $this->pessoa_logada, $this->cod_distribuicao_uniforme);
            $auditoria->inclusao($distribuicao);

            $this->redirectIf(true, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj_tmp = $obj = new clsPmieducarDistribuicaoUniforme();
        $lista_tmp = $obj_tmp->lista($this->ref_cod_aluno, $this->ano);

        if ($lista_tmp) {
            foreach ($lista_tmp as $reg) {
                if ($reg['cod_distribuicao_uniforme'] != $this->cod_distribuicao_uniforme) {
                    $this->mensagem = 'Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>';

                    return false;
                }
            }
        }

        $obj = new clsPmieducarDistribuicaoUniforme(
            $this->cod_distribuicao_uniforme,
            $this->ref_cod_aluno,
            $this->ano,
            !is_null($this->kit_completo),
            $this->agasalho_qtd,
            $this->camiseta_curta_qtd,
            $this->camiseta_longa_qtd,
            $this->meias_qtd,
            $this->bermudas_tectels_qtd,
            $this->bermudas_coton_qtd,
            $this->tenis_qtd,
            $this->data,
            $this->agasalho_tm,
            $this->camiseta_curta_tm,
            $this->camiseta_longa_tm,
            $this->meias_tm,
            $this->bermudas_tectels_tm,
            $this->bermudas_coton_tm,
            $this->tenis_tm,
            $this->ref_cod_escola,
            $this->camiseta_infantil_qtd,
            $this->camiseta_infantil_tm,
            $this->calca_jeans_qtd,
            $this->calca_jeans_tm,
            $this->saia_qtd,
            $this->saia_tm
        );

        $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();

        if ($editou) {
            $auditoria = new clsModulesAuditoriaGeral('distribuicao_uniforme', $this->pessoa_logada, $this->cod_distribuicao_uniforme);
            $auditoria->alteracao($detalheAntigo, $obj->detalhe());

            $this->redirectIf(true, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj = new clsPmieducarDistribuicaoUniforme($this->cod_distribuicao_uniforme);
        $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $auditoria = new clsModulesAuditoriaGeral('distribuicao_uniforme', $this->pessoa_logada, $this->cod_distribuicao_uniforme);
            $auditoria->exclusao($detalhe);

            $this->redirectIf(true, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }
}

$pagina = new clsIndexBase();
$pagina->addForm(new indice());
$pagina->MakeAll();

?>

<script type="text/javascript">
    function bloqueiaCamposQuantidade(){
        $j('#agasalho_qtd').val('').attr('disabled', 'disabled');
        $j('#camiseta_curta_qtd').val('').attr('disabled', 'disabled');
        $j('#camiseta_longa_qtd').val('').attr('disabled', 'disabled');
        $j('#camiseta_infantil_qtd').val('').attr('disabled', 'disabled');
        $j('#calca_jeans_qtd').val('').attr('disabled', 'disabled');
        $j('#meias_qtd').val('').attr('disabled', 'disabled');
        $j('#saia_qtd').val('').attr('disabled', 'disabled');
        $j('#bermudas_tectels_qtd').val('').attr('disabled', 'disabled');
        $j('#bermudas_coton_qtd').val('').attr('disabled', 'disabled');
        $j('#tenis_qtd').val('').attr('disabled', 'disabled');
        return true;
    }

    function liberaCamposQuantidade(){
        $j('#agasalho_qtd').removeAttr('disabled');
        $j('#camiseta_curta_qtd').removeAttr('disabled');
        $j('#camiseta_longa_qtd').removeAttr('disabled');
        $j('#camiseta_infantil_qtd').removeAttr('disabled');
        $j('#calca_jeans_qtd').removeAttr('disabled');
        $j('#meias_qtd').removeAttr('disabled');
        $j('#saia_qtd').removeAttr('disabled');
        $j('#bermudas_tectels_qtd').removeAttr('disabled');
        $j('#bermudas_coton_qtd').removeAttr('disabled');
        $j('#tenis_qtd').removeAttr('disabled');
    }

    $j(document).ready(function(){
        if($j('#kit_completo').is(':checked'))
            bloqueiaCamposQuantidade();

        $j('#kit_completo').on('change', function(){
            if($j('#kit_completo').is(':checked'))
                bloqueiaCamposQuantidade();
            else
                liberaCamposQuantidade();
        });
    })
</script>
