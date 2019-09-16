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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Movimentação Mensal de Alunos" );
        $this->processoAp = "944";
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
    var $mes;

    var $nm_escola;
    var $nm_instituicao;
    var $ref_cod_curso;
    var $sequencial;

    var $pdf;
    var $pagina_atual = 1;
    var $total_paginas = 1;

    var $page_y = 125;

    var $cursos = array();

    var $array_disciplinas = array();

    var $get_link;

    var $ref_cod_modulo;

    var $meses_do_ano = array(
                             "1" => "JANEIRO"
                            ,"2" => "FEVEREIRO"
                            ,"3" => "MAR&Ccedil;O"
                            ,"4" => "ABRIL"
                            ,"5" => "MAIO"
                            ,"6" => "JUNHO"
                            ,"7" => "JULHO"
                            ,"8" => "AGOSTO"
                            ,"9" => "SETEMBRO"
                            ,"10" => "OUTUBRO"
                            ,"11" => "NOVEMBRO"
                            ,"12" => "DEZEMBRO"
                        );

    var $total_dias_uteis;


    function Inicializar()
    {

        $retorno = "Novo";


        $obj_permissoes = new clsPermissoes();
        if($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7){
            $this->simpleRedirect('index.php');
        }

        return $retorno;
    }

    function Gerar()
    {

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if($_POST){
            foreach ($_POST as $key => $value) {
                $this->$key = $value;

            }
        }



        $this->ano = $ano_atual = date("Y");
        $this->mes = $mes_atual = date("n");

        $this->campoLista( "mes", "M&ecirc;s",$this->meses_do_ano, $this->mes,"",false );
//      $this->campoLista( "ano", "Ano",$anos, $this->ano,"",false );

        $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true );

        $get_escola = true;
        $obrigatorio = true;
        $exibe_nm_escola = true;

        $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        include("include/pmieducar/educar_campo_lista.php");
        $this->campoRotulo("cursos_","Cursos","<div id='cursos'>Selecione uma escola</div>");

        if($nivel_usuario <=3)
        {

            echo "<script>
                    window.onload = function(){document.getElementById('ref_cod_escola').onchange = changeCurso};
                  </script>";

        }
        else
        {

            echo "<script>
                    window.onload = function(){ changeCurso() };
                  </script>";
        }

    //  if($this->get_link)
        //  $this->campoRotulo("rotulo11", "-", "<a href='$this->get_link' target='_blank'>Baixar Relatório</a>");

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

changeCurso =
function(){
    var campoEscola = document.getElementById('ref_cod_escola').value;
    var xml1 = new ajax(getCurso_XML);
    strURL = "educar_curso_serie_xml.php?esc="+campoEscola+"&cur=1";
    xml1.envia(strURL);


}

after_getEscola = changeCurso;

function getCurso_XML(xml)
{

    var escola = document.getElementById('ref_cod_escola');
    var cursos = document.getElementById('cursos');
    var conteudo = '';
    var achou = false;
    var escola_curso = xml.getElementsByTagName( "item" );

    cursos.innerHTML = 'Selecione uma escola';
    if(escola.value == '')
        return;

    for(var ct = 0; ct < escola_curso.length;ct+=2)
    {

        achou = true;
        conteudo += '<input type="checkbox" checked="checked" name="cursos[]" id="cursos[]" value="'+ escola_curso[ct].firstChild.nodeValue +'"><label for="cursos[]">' + escola_curso[ct+1].firstChild.nodeValue +'</label> <br />';

    }
    if( !achou ){
        cursos.innerHTML = 'Escola sem cursos';
        return;
    }
    cursos.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
    cursos.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
    cursos.innerHTML += '</table>';

}

function acao2()
{

    if(!acao())
        return false;

    showExpansivelImprimir(400, 200,'',[], "Movimentação Mensal de Alunos");

    document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

    document.formcadastro.submit();
}

document.formcadastro.action = 'educar_alunos_defasados_nominal_proc.php';

</script>
