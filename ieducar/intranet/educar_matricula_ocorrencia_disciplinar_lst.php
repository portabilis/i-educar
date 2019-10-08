<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Ocorr&ecirc;ncia Disciplinar" );
        $this->processoAp = "578";
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    var $ref_cod_matricula;
    var $ref_cod_tipo_ocorrencia_disciplinar;
    var $sequencial;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $observacao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $ref_cod_curso;
    var $ref_ref_cod_serie;
    var $ref_cod_turma;

    function Gerar()
    {
        $this->titulo = "Matricula Ocorr&ecirc;ncia Disciplinar - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        if(!$this->ref_cod_matricula)
            $this->simpleRedirect('educar_matricula_lst.php');

        $this->campoOculto("ref_cod_matricula",$this->ref_cod_matricula);

        $this->addCabecalhos( array(
            "Tipo Ocorr&ecirc;ncia Disciplinar",
            "Série ",
            "Turma"
        ) );

            $obj_ref_cod_matricula = new clsPmieducarMatricula();
            $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));
            $obj_escola = new clsPmieducarEscola( $detalhe_aluno['ref_ref_cod_escola'] );
            $det_escola = $obj_escola->detalhe();

            $obj_aluno = new clsPmieducarAluno();
            $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],null,null,null,null,null,null,null,null,null,1));

            $this->campoRotulo("nm_pessoa","Nome do Aluno",$det_aluno['nome_aluno']);

        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsPmieducarTipoOcorrenciaDisciplinar();
        $lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1,$det_escola['ref_cod_instituicao']);
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_tipo_ocorrencia_disciplinar']}"] = "{$registro['nm_tipo']}";
            }
        }

        $this->campoLista( "ref_cod_tipo_ocorrencia_disciplinar", "Tipo Ocorr&ecirc;ncia Disciplinar", $opcoes, $this->ref_cod_tipo_ocorrencia_disciplinar );

        if ( $this->ref_cod_escola )
        {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        // outros Filtros

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_matricula_ocorrencia_disciplinar = new clsPmieducarMatriculaOcorrenciaDisciplinar();
        $obj_matricula_ocorrencia_disciplinar->setOrderby( "observacao ASC" );
        $obj_matricula_ocorrencia_disciplinar->setLimite( $this->limite, $this->offset );

        $lista = $obj_matricula_ocorrencia_disciplinar->lista(
            $this->ref_cod_matricula,
            $this->ref_cod_tipo_ocorrencia_disciplinar,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $total = $obj_matricula_ocorrencia_disciplinar->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_ref_cod_matricula = new clsPmieducarMatricula( $registro["ref_cod_matricula"] );
                $det_ref_cod_matricula = $obj_ref_cod_matricula->detalhe();
                //$registro["ref_cod_matricula"] = $det_ref_cod_matricula["ref_cod_matricula"];

                $obj_serie = new clsPmieducarSerie( $det_ref_cod_matricula["ref_ref_cod_serie"] );
                $det_serie = $obj_serie->detalhe();
                $registro["ref_ref_cod_serie"] = $det_serie["nm_serie"];

                $obj_ref_cod_tipo_ocorrencia_disciplinar = new clsPmieducarTipoOcorrenciaDisciplinar( $registro["ref_cod_tipo_ocorrencia_disciplinar"] );
                $det_ref_cod_tipo_ocorrencia_disciplinar = $obj_ref_cod_tipo_ocorrencia_disciplinar->detalhe();
                $registro["nm_tipo"] = $det_ref_cod_tipo_ocorrencia_disciplinar["nm_tipo"];

                $obj_mat_turma = new clsPmieducarMatriculaTurma();

                $det_mat_turma = $obj_mat_turma->lista($registro["ref_cod_matricula"],null,null,null,null,null,null,null,1);

                if($det_mat_turma){
                    $det_mat_turma = array_shift($det_mat_turma);
                    $obj_turma = new clsPmieducarTurma($det_mat_turma['ref_cod_turma']);
                    $det_turma = $obj_turma->detalhe();
                }


                $this->addLinhas( array(
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_tipo_ocorrencia_disciplinar={$registro["ref_cod_tipo_ocorrencia_disciplinar"]}&sequencial={$registro["sequencial"]}\">{$registro["nm_tipo"]}</a>",
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_tipo_ocorrencia_disciplinar={$registro["ref_cod_tipo_ocorrencia_disciplinar"]}&sequencial={$registro["sequencial"]}\">{$registro["ref_ref_cod_serie"]}</a>",
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_tipo_ocorrencia_disciplinar={$registro["ref_cod_tipo_ocorrencia_disciplinar"]}&sequencial={$registro["sequencial"]}\">{$det_turma["nm_turma"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_matricula_ocorrencia_disciplinar_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();

        $this->array_botao = array();
        $this->array_botao_url = array();
        if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
        {
            $this->array_botao_url[]= "educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula={$this->ref_cod_matricula}";
            $this->array_botao[]= "Novo";
        }

        $this->array_botao[] = "Voltar";
        $this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

        $this->largura = "100%";

        $this->breadcrumb('Ocorrências disciplinares da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
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
