<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Abandono Matrícula" );
        $this->processoAp = "578";
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

    var $cod_matricula;
    var $ref_cod_reserva_vaga;
    var $ref_ref_cod_escola;
    var $ref_ref_cod_serie;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_aluno;
    var $aprovado;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ano;

    var $ref_cod_instituicao;
    var $ref_cod_curso;
    var $ref_cod_escola;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_matricula=$_GET["ref_cod_matricula"];
        $this->ref_cod_aluno=$_GET["ref_cod_aluno"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

        $obj_matricula = new clsPmieducarMatricula( $this->cod_matricula,null,null,null,$this->pessoa_logada,null,null,6 );

        $det_matricula = $obj_matricula->detalhe();

        if(!$det_matricula) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        if($obj_matricula->edita())
        {

            echo "<script>
                alert('Abandono realizado com sucesso');
                window.location='educar_matricula_det.php?cod_matricula={$this->cod_matricula}';
                </script>";
        }


        die();
        return;
    }

    function Gerar()
    {

    }

    function Novo()
    {

    }

    function Excluir()
    {

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

document.getElementById('ref_cod_escola').onchange = function()
{
    getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
    getEscolaCursoSerie();
}

</script>
