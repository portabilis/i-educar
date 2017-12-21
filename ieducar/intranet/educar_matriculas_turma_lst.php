<?php
// error_reporting(E_ERROR);
// ini_set("display_errors", 1);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Matr&iacute;culas Turmas" );
        $this->processoAp = "659";
        $this->addEstilo('localizacaoSistema');
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

    var $ref_cod_turma;
    var $ref_ref_cod_serie;
    var $ref_cod_escola;
    var $ref_ref_cod_escola;
    var $ref_cod_instituicao;
    var $ref_cod_curso;

    function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = "Matr&iacute;culas Turma - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Ano",
            "Turma",
            "S&eacute;rie",
            "Curso",
            "Escola"
        );

        $this->addCabecalhos($lista_busca);

        $this->inputsHelper()->dynamic(array('ano', 'instituicao'),array('required' => TRUE));
        $this->inputsHelper()->dynamic(array('escola', 'curso', 'serie', 'turma'),array('required' => FALSE));

        if ( $this->ref_cod_escola )
        {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby( "nm_turma ASC" );
        $obj_turma->setLimite( $this->limite, $this->offset );
        if (!$this->ano)
            $this->ano = date (Y);

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_turma->codUsuario = $this->pessoa_logada;
        }

        $lista = $obj_turma->lista3(
            $this->ref_cod_turma,
            null,
            null,
            $this->ref_cod_serie,
            $this->ref_ref_cod_escola,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_curso,
            $this->ref_cod_instituicao,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ano
        );

        $total = $obj_turma->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                if( class_exists( "clsPmieducarEscola" ) )
                {
                    $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
                    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                    $registro["nm_escola"] = $det_ref_cod_escola["nome"];
                }
                else
                {
                    $registro["ref_ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
                    echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
                }

                $lista_busca = array(
                    "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro["cod_turma"]}\">{$registro["ano"]}</a>",
                    "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro["cod_turma"]}\">{$registro["nm_turma"]}</a>"
                );

                if ($registro["ref_ref_cod_serie"])
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro["cod_turma"]}\">{$registro["nm_serie"]}</a>";
                else
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro["cod_turma"]}\">-</a>";

                $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro["cod_turma"]}\">{$registro["nm_curso"]}</a>";

                if ($registro["ref_ref_cod_escola"])
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro["cod_turma"]}\">{$registro["nm_escola"]}</a>";
                else
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro["cod_turma"]}\">-</a>";

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_matriculas_turma_lst.php", $total, $_GET, $this->nome, $this->limite );
        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Listagem de turmas para enturma&ccedil;&otilde;es"
    ));
    $this->enviaLocalizacao($localizacao->montar());
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

document.getElementById('ref_ref_cod_serie').onchange = function()
{
    getTurma();
}

</script>
