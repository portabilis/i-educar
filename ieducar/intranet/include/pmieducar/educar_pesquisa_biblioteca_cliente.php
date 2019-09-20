<?php

/**
 * Ao incluir este arquivo, adicionar a variável $obrigatorio = true ou $obrigatorio = false para
 * definir se os campos são obrigatórios ou não. Adicionar também a variável editar para informar
 * se está sendo editados os itens ou não.
 *
 * Ex.:
 * $obrigatorio = true;
 * $editar      = true;
 * include("include/pmieducar/educar_pesquisa_biblioteca_cliente.php");
 *
 * @author Adriano Erik Weiguert Nagasava
 */

$permissoes = new clsPermissoes();
$privilegio = $permissoes->nivel_acesso( $this->pessoa_logada );

$this->campoOculto( "ref_cod_instituicao", $permissoes->getInstituicao( $this->pessoa_logada ) );
$this->campoOculto( "ref_cod_escola", $permissoes->getEscola( $this->pessoa_logada ) );

if ( $editar )
    echo $script = "<script> var editar = false; \n {$series}</script>\n";
else
    echo $script = "<script> var editar = true; \n {$series}</script>\n";

if ( $privilegio == 4 ) {

    // foreign keys
    $opcoes  = array( "" => "Selecione" );
    $opcoes2 = array( "" => "Selecione" );
        $objTemp = new clsPmieducarBiblioteca();
        $lista   = $objTemp->lista( null, $permissoes->getInstituicao( $this->pessoa_logada ), $permissoes->getEscola( $this->pessoa_logada ), null, null, null, null, null, null, null, null, null, 1 );
        if ( $lista ) {
            $tipos = '';
            foreach ( $lista as $registro ) {
                if ( $editar )
                    $opcoes["{$registro["cod_biblioteca"]}"] = "{$registro["nm_biblioteca"]}";
                else {
                    $opcoes[""] = "Selecione uma biblioteca";
                    $opcoes["{$registro["cod_biblioteca"]}"] = "{$registro["nm_biblioteca"]}";
                }
                $tipos .= " tipo['_{$registro["cod_biblioteca"]}'] = new Array();\n";

                    $obj_tipo = new clsPmieducarClienteTipo();
                    $lst_tipo = $obj_tipo->lista( null, $registro["cod_biblioteca"], nul, null, null, null, null, null, null, null, 1 );
                    if ( $lst_tipo ) {
                        foreach ( $lst_tipo as $tipo ) {
                            $tipos .= " tipo['_{$registro["cod_biblioteca"]}'][tipo['_{$registro["cod_biblioteca"]}'].length] = new Array( {$tipo["cod_cliente_tipo"]}, '{$tipo["nm_tipo"]}' );\n";
                            if ( $editar )
                                $opcoes2["{$tipo['cod_cliente_tipo']}"] = "{$tipo['nm_tipo']}";
                            else
                                $opcoes2[""] = "Selecione um tipo de cliente";
                        }
                    }
            }
            echo $script = "<script> var tipo = new Array(); \n {$tipos} </script>\n";
        }
    $this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes, $this->ref_cod_biblioteca, "BibliotecaTipo();", false, "", "", false, $obrigatorio );
    $this->campoLista( "ref_cod_cliente_tipo", "Tipo do Cliente", $opcoes2, $this->ref_cod_cliente_tipo, "", false, "", "", false, $obrigatorio );
}
elseif ( $privilegio == 2 ) {

    include("include/pmieducar/educar_pesquisa_instituicao_escola.php");
    $opcoes = array( "" => "Selecione" );
    $opcoes2 = array( "" => "Selecione" );
        $obj_escola = new clsPmieducarEscola( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        $lst_escola = $obj_escola->lista( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        if ( $lst_escola ) {
            $tipos = '';
            $bibliotecas = '';
            foreach ( $lst_escola as $escola ) {
                    $objTemp = new clsPmieducarBiblioteca();
                    $lista   = $objTemp->lista( null, $escola["ref_cod_escola_instituicao"], $escola["cod_escola"], null, null, null, null, null, null, null, null, null, 1 );
                    $bibliotecas .= " escola['_{$escola["cod_escola"]}'] = new Array();\n";
                    if ( $lista ) {
                        foreach ( $lista as $registro ) {
                            if ( $editar )
                                $opcoes["{$registro["cod_biblioteca"]}"] = "{$detalhe["nm_biblioteca"]}";
                            else
                                $opcoes[""] = "Selecione uma biblioteca";
                            $bibliotecas .= " escola['_{$escola["cod_escola"]}'][escola['_{$escola["cod_escola"]}'].length] = new Array( {$registro["cod_biblioteca"]}, '{$registro["nm_biblioteca"]}' );\n";
                            $tipos .= " tipo['_{$registro["cod_biblioteca"]}'] = new Array();\n";
                                $obj_tipo = new clsPmieducarClienteTipo();
                                $lst_tipo = $obj_tipo->lista( null, $registro["cod_biblioteca"], nul, null, null, null, null, null, null, null, 1 );
                                if ( $lst_tipo ) {
                                    foreach ( $lst_tipo as $tipo ) {
                                        $tipos .= " tipo['_{$registro["cod_biblioteca"]}'][tipo['_{$registro["cod_biblioteca"]}'].length] = new Array( {$tipo["cod_cliente_tipo"]}, '{$tipo["nm_tipo"]}' );\n";
                                        if ( $editar )
                                            $opcoes2["{$tipo['cod_cliente_tipo']}"] = "{$tipo['nm_tipo']}";
                                        else
                                            $opcoes2[""] = "Selecione um tipo de cliente";
                                    }
                                }
                        }
                    }
            }
            echo $script = "<script> var tipo = new Array(); \n {$tipos} </script>\n";
            echo $script = "<script> var escola = new Array(); \n {$bibliotecas}</script>\n";
        }
    $this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes, $this->ref_cod_biblioteca, "BibliotecaTipo();", false, "", "", false, $obrigatorio );
    $this->campoLista( "ref_cod_cliente_tipo", "Tipo do Cliente", $opcoes2, $this->ref_cod_cliente_tipo, "", false, "", "", false, $obrigatorio );
}
elseif ( $privilegio == 1 ) {

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    include("include/pmieducar/educar_pesquisa_instituicao_escola.php");

        $obj_ins = new clsPmieducarInstituicao( $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1 );
        $lst_ins = $obj_ins->lista( $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, null, null, null, null, null, null, 1 );
        if ( $lst_ins ) {
            $instituicoes = '';
            $tipos_extra = '';
            foreach ( $lst_ins as $instituicao ) {
                $instituicoes .= " biblioteca['_{$instituicao["cod_instituicao"]}'] = new Array();\n";
                    $obj_bib = new clsPmieducarBiblioteca( null, $instituicao["cod_instituicao"], null, null, null, null, null, null, null, null, 1 );
                    $lst_bib = $obj_bib->lista( null, $instituicao["cod_instituicao"], null, null, null, null, null, null, null, null, null, null, 1 );
                    if ( $lst_bib ) {
                        foreach ( $lst_bib as $biblioteca ) {
                            if ( !$biblioteca["ref_cod_escola"] ) {
                                $tipos_extra  .= " tipo['_{$biblioteca["cod_biblioteca"]}'] = new Array();\n";
                                $instituicoes .= " biblioteca['_{$instituicao["cod_instituicao"]}'][biblioteca['_{$instituicao["cod_instituicao"]}'].length] = new Array( {$biblioteca["cod_biblioteca"]}, '{$biblioteca["nm_biblioteca"]}' );\n";
                                    $obj_tipo = new clsPmieducarClienteTipo( null, $biblioteca["cod_biblioteca"], null, null, null, null, null, null, 1 );
                                    $lst_tipo = $obj_tipo->lista( null, $biblioteca["cod_biblioteca"], null, null, null, null, null, null, null, null, 1 );
                                    if ( $lst_tipo ) {
                                        foreach ( $lst_tipo as $tipo ) {
                                            $tipos_extra .= " tipo['_{$biblioteca["cod_biblioteca"]}'][tipo['_{$biblioteca["cod_biblioteca"]}'].length] = new Array( {$tipo["cod_cliente_tipo"]}, '{$tipo["nm_tipo"]}' );\n";
                                        }
                                    }
                            }
                        }
                    }
            }
            echo $script = "<script> var biblioteca = new Array(); \n {$instituicoes}</script>\n";
        }

    $opcoes = array( "" => "Selecione" );
    $opcoes2 = array( "" => "Selecione" );

        $obj_escola = new clsPmieducarEscola( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        $lst_escola = $obj_escola->lista( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        if ( $lst_escola ) {
            $tipos = '';
            $bibliotecas = '';
            foreach ( $lst_escola as $escola ) {
                $bibliotecas .= " escola['_{$escola["cod_escola"]}'] = new Array();\n";

                    $objTemp = new clsPmieducarBiblioteca();
                    $lista   = $objTemp->lista( null, $escola["ref_cod_escola_instituicao"], $escola["cod_escola"], null, null, null, null, null, null, null, null, null, 1 );
                    if ( $lista ) {
                        foreach ( $lista as $registro ) {
                            $tipos .= " tipo['_{$registro["cod_biblioteca"]}'] = new Array();\n";
                            if ( $editar )
                                $opcoes["{$registro["cod_biblioteca"]}"] = "{$detalhe["nm_biblioteca"]}";
                            else
                                $opcoes[""] = "Selecione uma biblioteca";
                            $bibliotecas .= " escola['_{$escola["cod_escola"]}'][escola['_{$escola["cod_escola"]}'].length] = new Array( {$registro["cod_biblioteca"]}, '{$registro["nm_biblioteca"]}' );\n";

                                $obj_tipo = new clsPmieducarClienteTipo();
                                $lst_tipo = $obj_tipo->lista( null, $registro["cod_biblioteca"], nul, null, null, null, null, null, null, null, 1 );
                                if ( $lst_tipo ) {
                                    foreach ( $lst_tipo as $tipo ) {
                                        $tipos .= " tipo['_{$registro["cod_biblioteca"]}'][tipo['_{$registro["cod_biblioteca"]}'].length] = new Array( {$tipo["cod_cliente_tipo"]}, '{$tipo["nm_tipo"]}' );\n";
                                        if ( $editar )
                                            $opcoes2["{$tipo['cod_cliente_tipo']}"] = "{$tipo['nm_tipo']}";
                                        else
                                            $opcoes2[""] = "Selecione um tipo de cliente";
                                    }
                                }
                        }
                    }
            }
            echo $script = "<script> var tipo = new Array(); \n {$tipos}{$tipos_extra} </script>\n";
            echo $script = "<script> var escola = new Array(); \n {$bibliotecas}</script>\n";
        }

    $this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes, $this->ref_cod_biblioteca, "BibliotecaTipo();", false, "", "", false, $obrigatorio );
    $this->campoLista( "ref_cod_cliente_tipo", "Tipo do Cliente", $opcoes2, $this->ref_cod_cliente_tipo, "", false, "", "", false, $obrigatorio );
}

?>
<?php $scripts_js = "
<script>

var ref_cod_escola = document.getElementById('ref_cod_escola');

ref_cod_escola.onchange = function() { EscolaBiblioteca(); };

function EscolaBiblioteca()
{
    alert( \"teste\" );
    var codEscola  = document.getElementById( 'ref_cod_escola' ).value;
    var campoBiblioteca = document.getElementById( 'ref_cod_biblioteca' );

    campoCurso.length = 1;
    if ( !codEscola ) {
        campoBiblioteca.options[0].text = \"Selecione um Biblioteca\";
        return;
    }

    campoBiblioteca.length = 1;

    try {
        var tamanho = eval( \"escola['_\" + codEscola + \"'].length\" );
        for ( var ct = 0 ; ct < tamanho ; ct++ ){
            campoBiblioteca.options[ct + 1] = new Option( eval(\"escola['_\" + codEscola + \"'][\" + ct + \"][1]\" ), eval( \"escola['_\" + codEscola + \"'][\" + ct + \"][0]\" ), false, false );
        }
        if ( tamanho == 0 ) {
            campoBiblioteca.options[0].text = \"Escola sem biblioteca\";
        }
        else {
            campoBiblioteca.options[0].text = \"Selecione um biblioteca\";
        }
    }
    catch ( e ) {
    }
}

function BibliotecaTipo()
{
    alert( \"teste\" );
    var codBiblioteca   = document.getElementById( 'ref_cod_biblioteca' ).value;
    var campoTipo = document.getElementById( 'ref_cod_cliente_tipo' );

    campoTipo.length = 1;
    if ( !codBiblioteca ) {
        campoTipo.options[0].text = \"Selecione uma tipo\";
        return;
    }

    campoTipo.length = 1;

    try {
        var tamanho = eval( \"tipo['_\" + codBiblioteca + \"'].length\" );;
        for ( var ct = 0 ; ct < tamanho ; ct++ ){
            campoTipo.options[ct + 1] = new Option( eval(\"tipo['_\" + codBiblioteca + \"'][\" + ct + \"][1]\" ), eval( \"tipo['_\" + codBiblioteca + \"'][\" + ct + \"][0]\" ), false, false );
        }
        if ( tamanho == 0 ) {
            campoTipo.options[0].text = \"Biblioteca sem tipo de cliente\";
        }
        else {
            campoTipo.options[0].text = \"Selecione um tipo de cliente\";
        }
    }
    catch ( e ) {
    }
}

ref_cod_biblioteca.onchange = function(){BibliotecaTipo()};
</script>";
?>
