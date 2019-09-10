<?php

/**
 * @author Adriano Erik Weiguert Nagasava
 */
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario  = $obj_permissoes->nivel_acesso( $this->pessoa_logada );
        $retorno .= '<tr>
                     <td height="24" colspan="2" class="formdktd">
                     <span class="form">
                     <b style="font-size: 16px;">Filtros de busca</b>
                     </span>
                     </td>
                     </tr>';

        $retorno .= '<form action="" method="post" id="formcadastro" name="formcadastro">';
        if ( $obrigatorio )
        {
            $instituicao_obrigatorio = $escola_obrigatorio = $curso_obrigatorio = $serie_obrigatorio = $turma_obrigatorio = true;
        }
        else
        {
            $instituicao_obrigatorio = isset( $instituicao_obrigatorio ) ? $instituicao_obrigatorio : $obrigatorio;
            $escola_obrigatorio      = isset( $escola_obrigatorio ) ? $escola_obrigatorio : $obrigatorio;
            $curso_obrigatorio       = isset( $curso_obrigatorio ) ? $curso_obrigatorio : $obrigatorio;
            $serie_obrigatorio       = isset( $serie_obrigatorio ) ? $serie_obrigatorio : $obrigatorio;
            $turma_obrigatorio       = isset( $turma_obrigatorio ) ? $turma_obrigatorio : $obrigatorio;
        }

        if ( $desabilitado )
        {
            $instituicao_desabilitado = $escola_desabilitado = $curso_desabilitado = $serie_desabilitado = $turma_desabilitado = true;
        }
        else
        {
            $instituicao_desabilitado = isset( $instituicao_desabilitado ) ? $instituicao_desabilitado : $desabilitado;
            $escola_desabilitado      = isset( $escola_desabilitado ) ? $escola_desabilitado : $desabilitado;
            $curso_desabilitado       = isset( $curso_desabilitado ) ? $curso_desabilitado : $desabilitado;
            $serie_desabilitado       = isset( $serie_desabilitado ) ? $serie_desabilitado : $desabilitado;
            $turma_desabilitado       = isset( $turma_desabilitado ) ? $turma_desabilitado : $desabilitado;
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso( $this->pessoa_logada );

            $opcoes = array( "" => "Selecione" );
            $obj_instituicao = new clsPmieducarInstituicao();
            $obj_instituicao->setCamposLista( "cod_instituicao, nm_instituicao" );
            $obj_instituicao->setOrderby( "nm_instituicao ASC" );
            $lista = $obj_instituicao->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, 1 );
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
                }
            }
        if ( $get_escola && $get_curso )
        {
            $retorno .= '<tr id="tr_status" class="input_quadro_horario">
                         <td valign="top" class="formlttd">
                         <span class="form">Institui&ccedil;&atilde;o</span>
                         <span class="campo_obrigatorio">*</span>
                         <br/>
                         <sub style="vertical-align: top;"/>
                         </td>';
            $retorno .= '<td valign="top" class="formlttd"><span class="form">';
            $retorno .= "<select onchange=\"getEscola();\" class='geral' name='ref_cod_instituicao' id='ref_cod_instituicao'>";
            reset( $opcoes );
            while ( list( $chave, $texto ) = each( $opcoes ) )
            {
                $retorno .=  "<option id=\"ref_cod_instituicao_".urlencode( $chave )."\" value=\"".urlencode( $chave )."\"";

                if ( $chave == $this->ref_cod_instituicao )
                {
                    $retorno .= " selected";
                }
                $retorno .=  ">$texto</option>";
            }
            $retorno .= "</select>";
            $retorno .= '</span>
                            </td>
                            </tr>';
        }
        else
        {
            $retorno .= '<tr id="tr_status" class="input_quadro_horario">
                         <td valign="top" class="formlttd">
                         <span class="form">Institui&ccedil;&atilde;o</span>
                         <span class="campo_obrigatorio">*</span>
                         <br/>
                         <sub style="vertical-align: top;"/>
                         </td>';
            $retorno .= '<td valign="top" class="formlttd"><span class="form">';
            $retorno .= "<select class='geral' name='ref_cod_instituicao' id='ref_cod_instituicao'>";
            reset( $opcoes );
            while ( list( $chave, $texto ) = each( $opcoes ) )
            {
                $retorno .=  "<option id=\"ref_cod_instituicao_".urlencode( $chave )."\" value=\"".urlencode( $chave )."\"";

                if ( $chave==$this->ref_cod_instituicao )
                {
                    $retorno .= " selected";
                }
                $retorno .=  ">$texto</option>";
            }
            $retorno .= "</select>";
            $retorno .= '</span>
                            </td>
                            </tr>';
        }

        if ( $get_escola )
        {
                $opcoes_escola = array( "" => "Selecione" );
                $obj_escola = new clsPmieducarEscola();
                $lista = $obj_escola->lista( null, null, null, null, null, null, null, null, null, null, 1 );
            if ($nivel_usuario == 4 || $nivel_usuario == 8) {
              $opcoes_escola = array('' => 'Selecione');
              $obj_escola = new clsPmieducarEscolaUsuario();
              $lista = $obj_escola->lista($this->pessoa_logada);

              if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                  $codEscola = $registro['ref_cod_escola'];

                  $escola = new clsPmieducarEscola($codEscola);
                  $escola = $escola->detalhe();

                  $opcoes_escola[$codEscola] = $escola['nome'];
                }
              }
            } else if ($this->ref_cod_instituicao) {
                    $opcoes_escola = array( "" => "Selecione" );
                    $obj_escola = new clsPmieducarEscola();
                    $lista = $obj_escola->lista( null, null, null, $this->ref_cod_instituicao, null, null, null, null, null, null, 1 );
                    if ( is_array( $lista ) && count( $lista ) )
                    {
                        foreach ( $lista as $registro )
                        {
                            $opcoes_escola["{$registro["cod_escola"]}"] = "{$registro['nome']}";
                        }
                    }
            }
            if ( $get_escola )
            {
                $retorno .= '<tr id="tr_escola" class="input_quadro_horario">
                             <td valign="top" class="formmdtd">
                             <span class="form">Escola</span>
                             <span class="campo_obrigatorio">*</span>
                             <br/>
                             <sub style="vertical-align: top;"/>
                             </td>';
                $retorno .= '<td valign="top" class="formmdtd"><span class="form">';

                $disabled = !$this->ref_cod_escola && $nivel_usuario == 1 /*&& !$this->ref_cod_curso */?  "disabled='true' " : "" ;
                $retorno .=  " <select onchange=\"getCurso();getAnoLetivo();\" class='geral' name='ref_cod_escola' {$disabled} id='ref_cod_escola'>";

                reset( $opcoes_escola );
                while ( list( $chave, $texto ) = each( $opcoes_escola ) )
                {
                    $retorno .=  "<option id=\"ref_cod_escola_".urlencode( $chave )."\" value=\"".urlencode( $chave )."\"";

                    if ( $chave == $this->ref_cod_escola )
                    {
                        $retorno .= " selected";
                    }
                    $retorno .=  ">$texto</option>";
                }
                $retorno .=  "</select>";
                $retorno .= '</span>
                                </td>
                                </tr>';
            }
        }
        if ( $get_ano )
        {
                $opcoes_ano = array( "" => "Selecione" );

                // EDITAR
                if ( $this->ref_cod_escola )
                {
                    $obj_esc_ano = new clsPmieducarEscolaAnoLetivo();
                    $lst_esc_ano = $obj_esc_ano->lista($this->ref_cod_escola);
                    if ( is_array( $lst_esc_ano ) && count( $lst_esc_ano ) )
                    {
                        foreach ( $lst_esc_ano as $detalhe )
                        {
                            $opcoes_ano["{$detalhe['ano']}"] = "{$detalhe['ano']}";
                        }
                    }
                }
            $retorno .= '<tr id="tr_ano" class="input_quadro_horario">
                         <td valign="top" class="formlttd">
                         <span class="form">Ano</span>
                         <span class="campo_obrigatorio">*</span>
                         <br/>
                         <sub style="vertical-align: top;"/>
                         </td>';
            $retorno .= '<td valign="top" class="formlttd"><span class="form">';

            $disabled = !$this->ano && $nivel_usuario == 1 ?  "disabled='true' " : "" ;
            $retorno .=  " <select onchange=\"getSerie();\" class='geral' name='ano' {$disabled} id='ano'>";

            if ( is_array( $opcoes_ano ) )
                reset( $opcoes_ano );
            while ( list( $chave, $texto ) = each( $opcoes_ano ) )
            {
                $retorno .=  "<option id=\"ano".urlencode( $chave )."\" value=\"".urlencode( $chave )."\"";

                if ( $chave == $this->ano )
                {
                    $retorno .= " selected";
                }
                $retorno .=  ">$texto</option>";
            }
            $retorno .=  "</select>";
            $retorno .= '</span>
                            </td>
                            </tr>';
        }
        if ( $get_curso )
        {
                $opcoes_curso = array( "" => "Selecione" );

                // EDITAR
                if ( $this->ref_cod_escola )
                {
                    $obj_esc_cur = new clsPmieducarEscolaCurso();
                    $lst_esc_cur = $obj_esc_cur->lista( $this->ref_cod_escola, null, null, null, null, null, null, null, 1 );
                    if ( is_array( $lst_esc_cur ) && count( $lst_esc_cur ) )
                    {
                        foreach ( $lst_esc_cur as $detalhe )
                        {
                            $opcoes_curso["{$detalhe['ref_cod_curso']}"] = "{$detalhe['nm_curso']}";
                        }
                    }
                }
            $retorno .= '<tr id="tr_curso" class="input_quadro_horario">
                         <td valign="top" class="formlttd">
                         <span class="form">Curso</span>
                         <span class="campo_obrigatorio">*</span>
                         <br/>
                         <sub style="vertical-align: top;"/>
                         </td>';
            $retorno .= '<td valign="top" class="formlttd"><span class="form">';

            $disabled = !$this->ref_cod_curso && $nivel_usuario == 1 /*&& !$this->ref_cod_curso*/ ?  "disabled='true' " : "" ;
            $retorno .=  " <select onchange=\"getSerie();\" class='geral' name='ref_cod_curso' {$disabled} id='ref_cod_curso'>";

            if ( is_array( $opcoes_curso ) )
                reset( $opcoes_curso );
            while ( list( $chave, $texto ) = each( $opcoes_curso ) )
            {
                $retorno .=  "<option id=\"ref_cod_curso_".urlencode( $chave )."\" value=\"".urlencode( $chave )."\"";

                if ( $chave == $this->ref_cod_curso )
                {
                    $retorno .= " selected";
                }
                $retorno .=  ">$texto</option>";
            }
            $retorno .=  "</select>";
            $retorno .= '</span>
                            </td>
                            </tr>';
        }
        if ( $get_serie )
        {
            $opcoes_serie = array( "" => "Selecione" );
                // EDITAR
                if ( $this->ref_cod_curso && $this->ref_cod_escola)
                {
                    $obj_serie = new clsPmieducarSerie();
                    $obj_serie->setOrderby( "nm_serie ASC" );
                    $lst_serie = $obj_serie->lista( null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1);
                    if ( is_array( $lst_serie ) && count( $lst_serie ) )
                    {
                        foreach ( $lst_serie as $serie )
                        {
                            $opcoes_serie["{$serie["cod_serie"]}"] = $serie['nm_serie'];
                        }
                    }
                }
            $retorno .= '<tr id="tr_curso" class="input_quadro_horario">
                         <td valign="top" class="formmdtd">
                         <span class="form">S&eacute;rie</span>
                         <span class="campo_obrigatorio">*</span>
                         <br/>
                         <sub style="vertical-align: top;"/>
                         </td>';
            $retorno .= '<td valign="top" class="formmdtd"><span class="form">';

            $disabled = !$this->ref_cod_serie && $nivel_usuario == 1 /*&& !$this->ref_cod_curso*/ ?  "disabled='true' " : "" ;
            $retorno .=  " <select onchange=\"getTurma();\" class='geral' name='ref_cod_serie' {$disabled} id='ref_cod_serie'>";

            if ( is_array( $opcoes_serie ) )
                reset( $opcoes_serie );
            while ( list( $chave, $texto ) = each( $opcoes_serie ) )
            {
                $retorno .=  "<option id=\"ref_cod_serie_".urlencode( $chave )."\" value=\"".urlencode( $chave )."\"";

                if ( $chave == $this->ref_cod_serie )
                {
                    $retorno .= " selected";
                }
                $retorno .=  ">$texto</option>";
            }
            $retorno .=  "</select>";
            $retorno .= '</span>
                            </td>
                            </tr>';
        }
        if ( $get_turma )
        {
            $opcoes_turma = array( "" => "Selecione" );
                // EDITAR
                if ( $this->ref_cod_serie /*|| $this->ref_cod_curso*/)
                {
                    $obj_turma = new clsPmieducarTurma();
                    $obj_turma->setOrderby("nm_turma ASC");
                    $lst_turma = $obj_turma->lista( null, null, null, $this->ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, null, $this->ref_cod_curso, $this->ref_cod_instituicao, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $this->ano );
                    if ( is_array( $lst_turma ) && count( $lst_turma ) )
                    {
                        foreach ( $lst_turma as $turma )
                        {
                            $opcoes_turma["{$turma["cod_turma"]}"] = $turma['nm_turma'];
                        }
                    }
                }
            $retorno .= '<tr id="tr_turma" class="input_quadro_horario">
                         <td valign="top" class="formlttd">
                         <span class="form">Turma</span>
                         <span class="campo_obrigatorio">*</span>
                         <br/>
                         <sub style="vertical-align: top;"/>
                         </td>';
            $retorno .= '<td valign="top" class="formlttd"><span class="form">';

            $disabled = ( !$this->ref_cod_turma && $nivel_usuario == 1 ) ?  "disabled='true' " : "" ;
            $retorno .=  " <select onchange=\"\" class='geral' name='ref_cod_turma' {$disabled} id='ref_cod_turma'>";

            if ( is_array( $opcoes_turma ) )
            {
                reset( $opcoes_turma );
            }
            while ( list( $chave, $texto ) = each( $opcoes_turma ) )
            {
                $retorno .=  "<option id=\"ref_cod_turma_".urlencode( $chave )."\" value=\"".urlencode( $chave )."\"";

                if ( $chave == $this->ref_cod_turma )
                {
                    $retorno .= " selected";
                }
                $retorno .=  ">$texto</option>";
            }
            $retorno .=  "</select>";
            $retorno .= '</span>
                            </td>
                            </tr>';
        }
        if ( isset( $get_cabecalho ) )
        {
            if ( $nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4 ) {
                ${$get_cabecalho}[] = "Curso";
                ${$get_cabecalho}[] = "S&eacute;rie";
                ${$get_cabecalho}[] = "Turma";
            }
            if ( $nivel_usuario == 1 || $nivel_usuario == 2 )
                ${$get_cabecalho}[] = "Escola";
            if ( $nivel_usuario == 1 )
                ${$get_cabecalho}[] = "Institui&ccedil;&atilde;o";
        }

        $validacao = 'if ( !document.getElementById( "ref_cod_instituicao" ).value ) {
                alert( "Por favor, selecione uma instituição" );
                return false;
                }
                if ( !document.getElementById( "ref_cod_escola" ).value) {
                    //if( !document.getElementById( "ref_cod_curso" ).value){
                        alert( "Por favor, selecione uma escola" );
                        return false;
                    //}
                }
                if ( !document.getElementById( "ano" ).value ) {
                alert( "Por favor, selecione um ano" );
                return false;
                }
                if ( !document.getElementById( "ref_cod_curso" ).value ) {
                alert( "Por favor, selecione um curso" );
                return false;
                }
                if ( !document.getElementById( "ref_cod_serie" ).value) {
                    //if( document.getElementById( "ref_cod_escola" ).value){
                        alert( "Por favor, selecione uma série" );
                        return false;
                //  }else{
                    //  alert( "Por favor, selecione uma turma" );
                //      return false;
                //  }
                }
                if ( !document.getElementById( "ref_cod_turma" ).value ) {
                alert( "Por favor, selecione uma turma" );
                return false;
                } ';
        $retorno .= '</form>';
        $retorno .= "<tr>
                     <td colspan='2' class='formdktd'/>
                     </tr>
                     <tr>
                     <td align='center' colspan='2'>
                     <script language='javascript'>
                     function acao() {
                     {$validacao}
                     document.formcadastro.submit();
                     }
                     </script>
                     <input type='button' class='btn-green' id='botao_busca' value='Buscar' onclick='javascript:acao();' class='botaolistagem'/>
                     </td>
                     </tr><tr><td>&nbsp;</td></tr>";
?>
<script>
/*
function desabilitaCampos()
{
    var obj_instituicao;
    var obj_escola;
    var obj_curso;
    var obj_serie;
    var obj_turma;

    if ( document.getElementById('ref_cod_instituicao') )
    {
        obj_instituicao          = document.getElementById( 'ref_cod_instituicao' );
        obj_instituicao.disabled = false;
    }

    if ( document.getElementById( 'ref_cod_escola' ) )
    {
        obj_escola          = document.getElementById('ref_cod_escola');
        obj_escola.disabled = false;
    }

    if ( document.getElementById('ref_cod_curso') ) {
        obj_curso          = document.getElementById('ref_cod_curso');
        obj_curso.disabled = false;
    }

    if ( document.getElementById('ref_cod_serie') )
    {
        obj_serie          = document.getElementById('ref_cod_serie');
        obj_serie.disabled = false;
    }

    if ( document.getElementById('ref_cod_turma') )
    {
        obj_turma          = document.getElementById('ref_cod_turma');
        obj_turma.disabled = false;
    }
}
*/

<?php
if ( $nivel_usuario == 1 || $nivel_usuario == 2 )
{
?>
    function getEscola( xml_escola )
    {
        var DOM_array = xml_escola.getElementsByTagName( "escola" );

        if(DOM_array.length)
        {
            campoEscola.length = 1;
            campoEscola.options[0].text = 'Selecione uma escola';
            campoEscola.disabled = false;

            for( var i = 0; i < DOM_array.length; i++ )
            {
                campoEscola.options[campoEscola.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_escola"),false,false);
            }
        }
        else
            campoEscola.options[0].text = 'A instituição não possui nenhuma escola';
    }
<?php
}
?>

function getCurso( xml_curso )
{
    var DOM_array = xml_curso.getElementsByTagName( "curso" );

    if(DOM_array.length)
    {
        campoCurso.length = 1;
        campoCurso.options[0].text = 'Selecione um curso';
        campoCurso.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoCurso.options[campoCurso.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
        }
    }
    else
        campoCurso.options[0].text = 'A escola não possui nenhum curso';
}

function getAnoLetivo( xml_ano )
{
    var DOM_array = xml_ano.getElementsByTagName( "ano" );

    if(DOM_array.length)
    {
        campoAno.length = 1;
        campoAno.options[0].text = 'Selecione um ano';
        campoAno.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoAno.options[campoAno.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].firstChild.data,false,false);
        }
    }
    else
        campoAno.options[0].text = 'A escola não possui nenhum ano';
}

function getSerie( xml_serie )
{
    var DOM_array = xml_serie.getElementsByTagName( "serie" );

    if(DOM_array.length)
    {
        campoSerie.length = 1;
        campoSerie.options[0].text = 'Selecione uma série';
        campoSerie.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoSerie.options[campoSerie.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
        }
    }
    else
        campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
}

function getTurma( xml_turma )
{

    console.log(xml_turma);
    var DOM_array = xml_turma.getElementsByTagName( "turma" );

    if(DOM_array.length)
    {
        campoTurma.length = 1;
        campoTurma.options[0].text = 'Selecione uma turma';
        campoTurma.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoTurma.options[campoTurma.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_turma"),false,false);
        }
    }
    else
        campoTurma.options[0].text = 'A escola/série não possui nenhuma turma';
}
</script>
