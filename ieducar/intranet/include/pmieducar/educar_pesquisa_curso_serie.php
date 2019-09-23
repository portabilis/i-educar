<?php

/**
 * Ao incluir este arquivo, adicionar a variável $obrigatorio = true ou $obrigatorio = false para
 * definir se os campos são obrigatórios ou não. Adicionar também a variável editar para informar
 * se está sendo editados os itens ou não.
 *
 * Ex.:
 * $obrigatorio = true;
 * $editar      = true;
 * include("include/pmieducar/educar_pesquisa_curso_serie.php");
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

    $opcoes = array( "" => "Selecione" );
        $objTemp = new clsPmieducarEscolaCurso();
        $lista   = $objTemp->lista( $permissoes->getEscola( $this->pessoa_logada ), null, null, null, null, null, null, null, 1 );
        if ( $lista ) {
            $series = '';
            $ESeries = '';
            foreach ( $lista as $registro ) {
                    $objTemp = new clsPmieducarCurso( $registro["ref_cod_curso"] );
                    $detalhe = $objTemp->detalhe();
                    if ( $editar )
                        $opcoes["{$registro["ref_cod_curso"]}"] = "{$detalhe["nm_curso"]}";
                    else {
                        $opcoes[""] = "Selecione um curso";
                        $opcoes["{$registro["ref_cod_curso"]}"] = "{$detalhe["nm_curso"]}";
                    }
                    $series .= " curso['_{$registro["ref_cod_curso"]}'] = new Array();\n";
                        $obj_esc_ser = new clsPmieducarEscolaSerie( $permissoes->getEscola( $this->pessoa_logada ), null, null, null, null, null, null, null, null, 1 );
                        $lst_esc_ser = $obj_esc_ser->lista( $permissoes->getEscola( $this->pessoa_logada ), null, null, null, null, null, null, null, null, null, null, null, null, 1, $permissoes->getInstituicao( $this->pessoa_logada ), $registro["ref_cod_curso"] );
                        if ( $lst_esc_ser ) {
                            foreach ( $lst_esc_ser as $esc_ser ) {

                            }
                        }
            }
            echo $script = "<script> var curso = new Array(); \n {$series}</script>\n";
        }
    $this->campoLista( "ref_cod_curso", "Curso", $opcoes, $this->ref_cod_curso, "CursoSerie();", false, "", "", false, $obrigatorio );

    $opcoes = array( "" => "Selecione" );
        $objTemp = new clsPmieducarEscolaSerie( $permissoes->getEscola( $this->pessoa_logada ) );
        $lista = $objTemp->lista( $permissoes->getEscola( $this->pessoa_logada ) );
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $obj_ser = new clsPmieducarSerie( $registro["ref_cod_serie"] );
                $lst_ser = $obj_ser->lista( null, null, null, $this->ref_cod_curso, null, null, null, null, null, null, null, null, 1 );
                if ( $lst_ser ) {
                    foreach ( $lst_ser as $serie ) {
                        if ( $editar )
                            $opcoes["{$serie['cod_serie']}"] = "{$serie['nm_serie']}";
                        else
                            $opcoes[""] = "Selecione uma série";
                    }
                }
            }
        }
    $this->campoLista( "ref_ref_cod_serie", "Série", $opcoes, $this->ref_ref_cod_serie, "", false, "", "", false, $obrigatorio );
}
elseif ( $privilegio == 2 ) {

    // foreign keys

    include("include/pmieducar/educar_pesquisa_instituicao_escola.php");
    $opcoes = array( "" => "Selecione" );
        $obj_escola = new clsPmieducarEscola( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        $lst_escola = $obj_escola->lista( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        if ( $lst_escola ) {
            $cursos = '';
            $series = '';
            foreach ( $lst_escola as $escola ) {
                    $objTemp = new clsPmieducarEscolaCurso();
                    $lista   = $objTemp->lista( $escola["cod_escola"], null, null, null, null, null, null, null, 1 );
                    $cursos .= " escola['_{$escola["cod_escola"]}'] = new Array();\n";
                    if ( $lista ) {
                        foreach ( $lista as $registro ) {
                                $objTemp = new clsPmieducarCurso( $registro["ref_cod_curso"] );
                                $detalhe = $objTemp->detalhe();
                                if ( $editar )
                                    $opcoes["{$registro["ref_cod_curso"]}"] = "{$detalhe["nm_curso"]}";
                                else
                                    $opcoes[""] = "Selecione um curso";
                                $series .= " curso['_{$registro["ref_cod_curso"]}'] = new Array();\n";
                                $cursos .= " escola['_{$registro["ref_cod_escola"]}'][escola['_{$registro["ref_cod_escola"]}'].length] = new Array( {$detalhe["cod_curso"]}, '{$detalhe["nm_curso"]}' );\n";
                                    $objSe = new clsPmieducarSerie( null, null, null, $registro["ref_cod_curso"], null, null, null, null, null, null, 1 );
                                    $listaSe = $objSe->lista( null, null, null, $registro["ref_cod_curso"], null, null, null, null, null, null, null, null, 1 );
                                    if ( $listaSe ) {
                                        foreach ( $listaSe as $registroSe ) {
                                            $series .= " curso['_{$registro["ref_cod_curso"]}'][curso['_{$registro["ref_cod_curso"]}'].length] = new Array( {$registroSe["cod_serie"]}, '{$registroSe["nm_serie"]}' );\n";
                                        }
                                    }
                        }
                        echo $script = "<script> var curso = new Array(); \n {$series}</script>\n";
                    }
            }
            echo "<script> var escola = new Array(); \n {$cursos}</script>\n";
        }
    $this->campoLista( "ref_cod_curso", "Curso", $opcoes, $this->ref_cod_curso, "CursoSerie();", false, "", "", false, $obrigatorio );

    $opcoes = array( "" => "Selecione" );
        $objTemp = new clsPmieducarEscolaSerie( $permissoes->getEscola( $this->pessoa_logada ) );
        $lista = $objTemp->lista( $permissoes->getEscola( $this->pessoa_logada ) );
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $obj_ser = new clsPmieducarSerie( $registro["ref_cod_serie"] );
                $lst_ser = $obj_ser->lista( null, null, null, $this->ref_cod_curso, null, null, null, null, null, null, null, null, 1 );
                if ( $lst_ser ) {
                    foreach ( $lst_ser as $serie ) {
                        if ( $editar )
                            $opcoes["{$serie['cod_serie']}"] = "{$serie['nm_serie']}";
                        else
                            $opcoes[""] = "Selecione uma série";
                    }
                }
            }
        }
    $this->campoLista( "ref_ref_cod_serie", "Série", $opcoes, $this->ref_ref_cod_serie, "", false, "", "", false, $obrigatorio );
}
elseif ( $privilegio == 1 ) {

    // foreign keys

    include("include/pmieducar/educar_pesquisa_instituicao_escola.php");
    $opcoes = array( "" => "Selecione" );
        $obj_escola = new clsPmieducarEscola( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        $lst_escola = $obj_escola->lista( null, null, null, $permissoes->getInstituicao( $this->pessoa_logada ), null, null, null, null, null, null, 1 );
        if ( $lst_escola ) {
            $cursos = '';
            $series = '';
            foreach ( $lst_escola as $escola ) {
                    $objTemp = new clsPmieducarEscolaCurso();
                    $lista   = $objTemp->lista( $escola["cod_escola"], null, null, null, null, null, null, null, 1 );
                    $cursos .= " escola['_{$escola["cod_escola"]}'] = new Array();\n";
                    if ( $lista ) {
                        foreach ( $lista as $registro ) {
                                $objTemp = new clsPmieducarCurso( $registro["ref_cod_curso"] );
                                $detalhe = $objTemp->detalhe();
                                $series .= " curso['_{$registro["ref_cod_curso"]}'] = new Array();\n";
                                $cursos .= " escola['_{$registro["ref_cod_escola"]}'][escola['_{$registro["ref_cod_escola"]}'].length] = new Array( {$detalhe["cod_curso"]}, '{$detalhe["nm_curso"]}' );\n";
                                if ( $editar )
                                    $opcoes["{$registro["ref_cod_curso"]}"] = "{$detalhe["nm_curso"]}";
                                else
                                    $opcoes[""] = "Selecione um curso";
                                    $objSe = new clsPmieducarSerie( null, null, null, $registro["ref_cod_curso"], null, null, null, null, null, null, 1 );
                                    $listaSe = $objSe->lista( null, null, null, $registro["ref_cod_curso"], null, null, null, null, null, null, null, null, 1 );
                                    if ( $listaSe ) {
                                        foreach ( $listaSe as $registroSe ) {
                                            $series .= " curso['_{$registro["ref_cod_curso"]}'][curso['_{$registro["ref_cod_curso"]}'].length] = new Array( {$registroSe["cod_serie"]}, '{$registroSe["nm_serie"]}' );\n";
                                        }
                                    }
                        }
                    }
            }
            echo "<script> var escola = new Array(); \n {$cursos}</script>\n";
            echo "<script> var curso = new Array(); \n {$series}</script>\n";
        }
    $this->campoLista( "ref_cod_curso", "Curso", $opcoes, $this->ref_cod_curso, "CursoSerie();", false, "", "", false, $obrigatorio );

    $opcoes = array( "" => "Selecione" );
        $objTemp = new clsPmieducarEscolaSerie( $permissoes->getEscola( $this->pessoa_logada ) );
        $lista = $objTemp->lista( $permissoes->getEscola( $this->pessoa_logada ) );
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $obj_ser = new clsPmieducarSerie( $registro["ref_cod_serie"] );
                $lst_ser = $obj_ser->lista( null, null, null, $this->ref_cod_curso, null, null, null, null, null, null, null, null, 1 );
                if ( $lst_ser ) {
                    foreach ( $lst_ser as $serie ) {
                        if ( $editar )
                            $opcoes["{$serie['cod_serie']}"] = "{$serie['nm_serie']}";
                        else
                            $opcoes[""] = "Selecione uma série";
                    }
                }
            }
        }
    $this->campoLista( "ref_ref_cod_serie", "Série", $opcoes, $this->ref_ref_cod_serie, "", false, "", "", false, $obrigatorio );
}

?>
<?php $scripts_js = "
<script>

var ref_cod_escola = document.getElementById('ref_cod_escola');
//var ref_curso        = document.getElementById('ref_cod_curso');

ref_cod_escola.onchange = function() { EscolaCurso();};

function EscolaCurso()
{
    var codEscola  = document.getElementById( 'ref_cod_escola' ).value;
    var campoCurso = document.getElementById( 'ref_cod_curso' );

    campoCurso.length = 1;
    if ( !codEscola ) {
        campoCurso.options[0].text = \"Selecione um Curso\";
        return;
    }

    campoCurso.length = 1;

    try {
        var tamanho = eval( \"escola['_\" + codEscola + \"'].length\" );
        for ( var ct = 0 ; ct < tamanho ; ct++ ){
            campoCurso.options[ct + 1] = new Option( eval(\"escola['_\" + codEscola + \"'][\" + ct + \"][1]\" ), eval( \"escola['_\" + codEscola + \"'][\" + ct + \"][0]\" ), false, false );
        }
        if ( tamanho == 0 ) {
            campoCurso.options[0].text = \"Escola sem curso\";
        }
        else {
            campoCurso.options[0].text = \"Selecione um curso\";
        }
    }
    catch ( e ) {
    }
}

function CursoSerie()
{
    var codCurso   = document.getElementById( 'ref_cod_curso' ).value;
    var campoSerie = document.getElementById( 'ref_ref_cod_serie' );

    campoSerie.length = 1;
    if ( !codCurso ) {
        campoSerie.options[0].text = \"Selecione uma série\";
        return;
    }

    campoSerie.length = 1;

    try {
        var tamanho = eval( \"curso['_\" + codCurso + \"'].length\" );;
        for ( var ct = 0 ; ct < tamanho ; ct++ ){
            campoSerie.options[ct + 1] = new Option( eval(\"curso['_\" + codCurso + \"'][\" + ct + \"][1]\" ), eval( \"curso['_\" + codCurso + \"'][\" + ct + \"][0]\" ), false, false );
        }
        if ( tamanho == 0 ) {
            campoSerie.options[0].text = \"Curso sem série\";
        }
        else {
            campoSerie.options[0].text = \"Selecione uma serie\";
        }
    }
    catch ( e ) {
    }
}

ref_curso.onchange = function(){CursoSerie()};
</script>";
?>
