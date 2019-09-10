<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Matricula Turma" );
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

    var $ref_cod_matricula;

    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $formando;

    function Inicializar()
    {
    //  print_r($_POST);die;
        $retorno = "Novo";


        //$this->ref_cod_turma=$_GET["ref_cod_turma"];


        foreach ($_GET as $key =>$value) {
            $this->$key = $value;
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php" );

        if( is_numeric( $this->ref_cod_matricula ) && is_numeric($this->formando))
        {

            $obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,null,null,null,null,null,null,null,$this->formando);
            $registro  = $obj->detalhe();
            if( $registro )
            {

                if(!$obj->edita())
                {
                    echo "erro ao cadastrar";
                    die;
                }
                $des = "";
                if(!$this->formando)
                    $des = "des";
                echo "<script>alert('Matrícula {$des}marcada como formando com sucesso!'); window.location='educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}';</script>";

            }

        }

        $this->simpleRedirect('educar_matricula_lst.php');
    }

    function Gerar()
    {

        die;

    }

    function Novo()
    {

    }

    function Editar()
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
