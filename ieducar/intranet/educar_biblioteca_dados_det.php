<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Dados Biblioteca" );
        $this->processoAp = "629";
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $cod_biblioteca;
    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $nm_biblioteca;
    var $valor_multa;
    var $max_emprestimo;
    var $valor_maximo_multa;
    var $data_cadastro;
    var $data_exclusao;
    var $requisita_senha;
    var $ativo;
    var $dias_espera;

    var $dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );

    function Gerar()
    {
        $this->titulo = "Dados Biblioteca - Detalhe";


        $this->cod_biblioteca=$_GET["cod_biblioteca"];

        $tmp_obj = new clsPmieducarBiblioteca( $this->cod_biblioteca );
        $registro = $tmp_obj->detalhe();

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if($nivel_usuario <= 3)
            $permitido = true;
        else{
            $obj_usuario_bib = new clsPmieducarBibliotecaUsuario();
            $lista_bib = $obj_usuario_bib->lista(null,$this->pessoa_logada);

            $permitido = false;
            if($lista_bib)
            {
                foreach ($lista_bib as $biblioteca)
                {
                    if($this->cod_biblioteca == $biblioteca['ref_cod_biblioteca'])
                        $permitido = true;
                }
            }
        }

        if( ! $registro || !$permitido)
        {
            $this->simpleRedirect('educar_biblioteca_dados_lst.php');
        }

        if( $registro["nm_biblioteca"] )
        {
            $this->addDetalhe( array( "Biblioteca", "{$registro["nm_biblioteca"]}") );
        }
        if( $registro["valor_multa"] )
        {
            $registro["valor_multa"] = number_format( $registro["valor_multa"], 2, ",", "." );
            $this->addDetalhe( array( "Valor Multa", "{$registro["valor_multa"]}") );
        }
        if( $registro["max_emprestimo"] )
        {
            $this->addDetalhe( array( "M&aacute;ximo Empr&eacute;stimo", "{$registro["max_emprestimo"]}") );
        }
        if( $registro["valor_maximo_multa"] )
        {
            $registro["valor_maximo_multa"] = number_format( $registro["valor_maximo_multa"], 2, ",", "." );
            $this->addDetalhe( array( "Valor M&aacute;ximo Multa", "{$registro["valor_maximo_multa"]}") );
        }
        if( $registro["requisita_senha"] )
        {
            if ($registro["requisita_senha"] == 0)
            {
                $registro["requisita_senha"] = "n&atilde;o";
            }
            else if ($registro["requisita_senha"] == 1)
            {
                $registro["requisita_senha"] = "sim";
            }
            $this->addDetalhe( array( "Requisita Senha", "{$registro["requisita_senha"]}") );
        }
        if( $registro["dias_espera"] )
        {
            $this->addDetalhe( array( "Dias Espera", "{$registro["dias_espera"]}") );
        }

        $obj = new clsPmieducarBibliotecaDia();
        $lst = $obj->lista( $this->cod_biblioteca );
        if ($lst)
        {
            $tabela = "<TABLE>
                           <TR align=center>
                               <TD bgcolor=#ccdce6><B>Nome</B></TD>
                           </TR>";
            $cont = 0;

            foreach ( $lst AS $valor )
            {
                if ( ($cont % 2) == 0 )
                {
                    $color = " bgcolor=#f5f9fd ";
                }
                else
                {
                    $color = " bgcolor=#FFFFFF ";
                }
                $tabela .= "<TR>
                                <TD {$color} align=left>{$this->dias_da_semana[$valor["dia"]]}</TD>
                            </TR>";
                $cont++;
            }
            $tabela .= "</TABLE>";
        }
        if( $tabela )
        {
            $this->addDetalhe( array( "Dia da Semana", "{$tabela}") );
        }

        $obj = new clsPmieducarBibliotecaFeriados();
        $obj->setOrderby("data_feriado ASC");
        $lst = $obj->lista( null, $this->cod_biblioteca );
        if ($lst)
        {
            $tabela1 = "<TABLE>
                           <TR align=center>
                               <TD bgcolor=#ccdce6><B>Nome</B></TD>
                               <TD bgcolor=#ccdce6><B>Data</B></TD>
                           </TR>";
            $cont = 0;

            foreach ( $lst AS $valor )
            {
                if ( ($cont % 2) == 0 )
                {
                    $color = " bgcolor=#f5f9fd ";
                }
                else
                {
                    $color = " bgcolor=#FFFFFF ";
                }

                $valor["data_feriado"] = dataFromPgToBr($valor["data_feriado"]);

                $tabela1 .= "<TR>
                                <TD {$color} align=left>{$valor["nm_feriado"]}</TD>
                                <TD {$color} align=left>{$valor["data_feriado"]}</TD>
                            </TR>";
                $cont++;
            }
            $tabela1 .= "</TABLE>";
        }
        if( $tabela1 )
        {
            $this->addDetalhe( array( "Data do Feriado", "{$tabela1}") );
        }


        if( $obj_permissoes->permissao_cadastra( 629, $this->pessoa_logada, 11 ) )
        {
            $this->url_editar = "educar_biblioteca_dados_cad.php?cod_biblioteca={$registro["cod_biblioteca"]}";
        }

        $this->url_cancelar = "educar_biblioteca_dados_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe dos dados da biblioteca', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
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
