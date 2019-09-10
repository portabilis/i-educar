<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/clsPDF.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Quadro Curricular" );
        $this->processoAp = "696";
    }
}

class indice extends clsCadastro
{


    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;


    var $ref_cod_instituicao;
    var $ref_cod_escola;


    var $ano;

    var $nm_escola;
    var $nm_instituicao;

    var $pdf;


    var $page_y = 139;



    function Inicializar()
    {
        $retorno = "Novo";


        return $retorno;
    }

    function Gerar()
    {


        if($_POST){
            foreach ($_POST as $key => $value) {
                $this->$key = $value;

            }
        }

        $this->ano = $ano_atual = date("Y");

        $lim = 5;
        for($a = date('Y') ; $a < $ano_atual + $lim ; $a++ )
                $anos["{$a}"] = "{$a}";


        $this->campoLista( "ano", "Ano",$anos, $this->ano,"",false );


        $get_escola = true;
        $get_curso = true;
        $get_escola_curso_serie = true;
        $obrigatorio = false;
        $instituicao_obrigatorio = true;


        include("include/pmieducar/educar_campo_lista.php");

        if($this->ref_cod_escola)
            $this->ref_ref_cod_escola = $this->ref_cod_escola;

        $this->url_cancelar = "educar_index.php";
        $this->nome_url_cancelar = "Cancelar";

        $this->acao_enviar = 'acao2()';
        $this->acao_executa_submit = false;

    }



}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();


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
