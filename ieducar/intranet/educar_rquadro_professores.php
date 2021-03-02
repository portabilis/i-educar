<?php


return new class extends clsCadastro
{

    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $ano;

    public $nm_escola;
    public $nm_instituicao;

    public $pdf;

    public $page_y = 139;

    public function Inicializar()
    {
        $retorno = 'Novo';

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $key => $value) {
                $this->$key = $value;
            }
        }

        $this->ano = $ano_atual = date('Y');

        $lim = 5;
        for ($a = date('Y') ; $a < $ano_atual + $lim ; $a++) {
            $anos["{$a}"] = "{$a}";
        }

        $this->campoLista('ano', 'Ano', $anos, $this->ano, '', false);

        $get_escola = true;
        $get_curso = true;
        $get_escola_curso_serie = true;
        $obrigatorio = false;
        $instituicao_obrigatorio = true;

        include('include/pmieducar/educar_campo_lista.php');

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        $this->url_cancelar = 'educar_index.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->acao_enviar = 'acao2()';
        $this->acao_executa_submit = false;

    }

    public function Formular()
    {
        $this->title = "i-Educar - Quadro Curricular";
        $this->processoAp = '696';
    }
};



?>
<script>

function acao2()
{

    if(!acao())
        return false;

    showExpansivelImprimir(400, 200,'',[], "Quadro Curricular");

    document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

    document.formcadastro.submit();
}

document.formcadastro.action = 'educar_relatorio_quadro_curricular_proc.php';

document.getElementById('ref_cod_escola').onchange = function()
{
    getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
    getEscolaCursoSerie();
}

</script>
