<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
 * @author Adriano Erik Weiguert Nagasava
 */
	 	$obj_permissoes = new clsPermissoes();
	 	$nivel_usuario  = $obj_permissoes->nivel_acesso( $this->pessoa_logada );
		$retorno .= '<tr>
					 <td height="24" colspan="2" class="formdktd">
					 <span class="form">
					 <b>Filtros de busca</b>
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
			$escola_obrigatorio 	 = isset( $escola_obrigatorio ) ? $escola_obrigatorio : $obrigatorio;
			$curso_obrigatorio 		 = isset( $curso_obrigatorio ) ? $curso_obrigatorio : $obrigatorio;
			$serie_obrigatorio 		 = isset( $serie_obrigatorio ) ? $serie_obrigatorio : $obrigatorio;
			$turma_obrigatorio 		 = isset( $turma_obrigatorio ) ? $turma_obrigatorio : $obrigatorio;
		}

		if ( $desabilitado )
		{
			$instituicao_desabilitado = $escola_desabilitado = $curso_desabilitado = $serie_desabilitado = $turma_desabilitado = true;
		}
		else
		{
			$instituicao_desabilitado = isset( $instituicao_desabilitado ) ? $instituicao_desabilitado : $desabilitado;
			$escola_desabilitado 	  = isset( $escola_desabilitado ) ? $escola_desabilitado : $desabilitado;
			$curso_desabilitado 	  = isset( $curso_desabilitado ) ? $curso_desabilitado : $desabilitado;
			$serie_desabilitado 	  = isset( $serie_desabilitado ) ? $serie_desabilitado : $desabilitado;
			$turma_desabilitado 	  = isset( $turma_desabilitado ) ? $turma_desabilitado : $desabilitado;
		}

	 	$obj_permissoes = new clsPermissoes();
	 	$nivel_usuario = $obj_permissoes->nivel_acesso( $this->pessoa_logada );

		if ( $nivel_usuario == 1 )
		{
			if ( class_exists( "clsPmieducarInstituicao" ) )
			{
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
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarInstituicao n&atilde;o encontrada\n-->";
				$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
			if ( $get_escola && $get_curso )
			{
				$retorno .= '<tr id="tr_status">
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
				$retorno .= '<tr id="tr_status">
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
		}
		elseif ( $nivel_usuario != 1 )
		{
		 	$obj_usuario = new clsPmieducarUsuario( $this->pessoa_logada );
			$det_usuario = $obj_usuario->detalhe();
			$this->ref_cod_instituicao = $det_usuario["ref_cod_instituicao"];
			if ( $nivel_usuario == 4 )
			{
				$obj_usuario = new clsPmieducarUsuario( $this->pessoa_logada );
				$det_usuario = $obj_usuario->detalhe();
				$this->ref_cod_escola = $det_usuario["ref_cod_escola"];
				if ( $get_escola )
				{
					$retorno .= '<td valign="top" class="formmdtd"><span class="form">';
					$retorno .= "<input name='ref_cod_escola' id='ref_cod_escola' type='hidden' value='{$this->ref_cod_escola}'>";
					$retorno .= '</span>
									</td>
									</tr>';
				}
			}
			$instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
			$retorno .= "<input type='hidden' name='ref_cod_instituicao' id='ref_cod_instituicao' value='$instituicao'";
		}

		if ( $nivel_usuario == 1 || $nivel_usuario == 2 )
		{
			if ( $get_escola )
			{
				if ( class_exists( "clsPmieducarEscola" ) )
				{
					$opcoes_escola = array( "" => "Selecione" );
					//$todas_escolas = "escola = new Array();\n";
					$obj_escola = new clsPmieducarEscola();
					$lista = $obj_escola->lista( null, null, null, null, null, null, null, null, null, null, 1 );
					/*if ( is_array( $lista ) && count( $lista ) )
					{
						foreach ( $lista as $registro )
						{
							$todas_escolas .= "escola[escola.length] = new Array( {$registro["cod_escola"]}, '{$registro['nome']}', {$registro["ref_cod_instituicao"]} );\n";
						}
					}
					echo "<script>{$todas_escolas}</script>";*/
				}
				else
				{
					echo "<!--\nErro\nClasse clsPmieducarEscola n&atilde;o encontrada\n-->";
					$opcoes_escola = array( "" => "Erro na gera&ccedil;&atilde;o" );
				}
				if ( $this->ref_cod_instituicao )
				{
					if ( class_exists( "clsPmieducarEscola" ) )
					{
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
					else
					{
						echo "<!--\nErro\nClasse clsPmieducarEscola n&atilde;o encontrada\n-->";
						$opcoes_escola = array( "" => "Erro na gera&ccedil;&atilde;o" );
					}
				}
				if ( $get_escola )
				{
					$retorno .= '<tr id="tr_escola">
								 <td valign="top" class="formmdtd">
								 <span class="form">Escola</span>
								 <span class="campo_obrigatorio">*</span>
								 <br/>
								 <sub style="vertical-align: top;"/>
								 </td>';
					$retorno .= '<td valign="top" class="formmdtd"><span class="form">';

					$disabled = !$this->ref_cod_escola && $nivel_usuario == 1 /*&& !$this->ref_cod_curso */?  "disabled='true' " : "" ;
					$retorno .=  " <select onchange=\"getCurso();\" class='geral' name='ref_cod_escola' {$disabled} id='ref_cod_escola'>";

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
		}
		if ( $get_curso )
		{
			if( class_exists( "clsPmieducarCurso" ) )
			{
				/*$todos_cursos = "curso = new Array();\n";
				$obj_curso = new clsPmieducarCurso();
				$obj_curso->setOrderby("nm_curso ASC");
				$lista = $obj_curso->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1);
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$todos_cursos .= "curso[curso.length] = new Array( {$registro["cod_curso"]}, '{$registro['nm_curso']}', {$registro['qtd_etapas']}, {$registro["ref_cod_instituicao"]}, {$registro["padrao_ano_escolar"]} );\n";
					}
				}
				echo "<script>{$todos_cursos}</script>";
*/
				/*if( class_exists( "clsPmieducarEscolaCurso" ) && class_exists( "clsPmieducarCurso" ) )
				{
					$todos_cursos_escola  = "escola_curso = new Array();\n";
					$obj_escola_curso = new clsPmieducarEscolaCurso();
	//				$obj_escola_curso->setOrderby("ref_cod_curso ASC");
					$lst_escola_curso = $obj_escola_curso->lista( null,null,null,null,null,null,null,null,1 );
					if ( is_array( $lst_escola_curso ) && count( $lst_escola_curso ) )
					{
						foreach ( $lst_escola_curso as $escola_curso )
						{
							//$obj_curso = new clsPmieducarCurso( $escola_curso["ref_cod_curso"] );
						//$det_curso = $obj_curso->he();
							if(isset($curso_padrao_ano_escolar) && $det_curso['padrao_ano_escolar'] != $curso_padrao_ano_escolar)
								continue;
							$todos_cursos_escola .= "escola_curso[escola_curso.length] = new Array( {$escola_curso["ref_cod_curso"]}, '{$escola_curso['nm_curso']}', {$escola_curso["ref_cod_escola"]}, {$escola_curso["padrao_ano_escolar"]} );\n";
						}
					}
				}
				echo "<script>{$todos_cursos_escola}</script>";*/
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
//							$obj_curso = new clsPmieducarCurso( $detalhe["ref_cod_curso"] );
//							$det_curso = $obj_curso->detalhe();
//							if ( is_array( $det_curso ) && count( $det_curso ) )
//							{
								$opcoes_curso["{$detalhe['ref_cod_curso']}"] = "{$detalhe['nm_curso']}";
//							}
						}
					}
				}/*elseif($this->ref_cod_curso)
				{
					$obj_curso = new clsPmieducarCurso();
					$obj_curso->setOrderby("nm_curso ASC");
					$lista = $obj_curso->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao);

					if ( is_array( $lista ) && count( $lista ) )
					{
						foreach ( $lista as $registro )
						{
							$opcoes_curso["{$registro['cod_curso']}"] = "{$registro['nm_curso']}";
						}
					}
				}*/
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarCurso n&atilde;o encontrada\n-->";
				$opcoes_curso = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
			$retorno .= '<tr id="tr_curso">
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
			if( class_exists( "clsPmieducarSerie" ) )
			{
			/*	$todas_series = "serie = new Array();\n";
				$obj_serie = new clsPmieducarSerie();
				$obj_serie->setOrderby( "nm_serie ASC" );
				$lst_serie = $obj_serie->lista( null,null,null,null,null,null,null,null,null,null,null,null,1 );

				if ( is_array( $lst_serie ) && count( $lst_serie ) )
				{
					foreach ( $lst_serie as $serie )
					{
						if ( $get_escola_serie )
						{
							$obj_escola_serie = new clsPmieducarEscolaSerie();
							$lst_escola_serie = $obj_escola_serie->lista( null,$serie["cod_serie"],null,null,null,null,null,null,null,null,null,null,1 );
							if ( is_array( $lst_escola_serie ) && count( $lst_escola_serie ) )
							{	$arr = "new Array(";
								$conc = "";
								foreach ( $lst_escola_serie as $escola_serie )
								{
									$arr .= "{$conc}'{$escola_serie["ref_cod_escola"]}'";
									$conc = ",";
								}
								$arr .= ")";
							}else{
								$arr = "new Array()";
							}

							$todas_series .= "serie[serie.length] = new Array( {$serie["cod_serie"]}, '{$serie['nm_serie']}', {$serie["ref_cod_curso"]}, {$arr} );\n";
						}
						else
						{
							$todas_series .= "serie[serie.length] = new Array( {$serie["cod_serie"]}, '{$serie['nm_serie']}', {$serie["ref_cod_curso"]} );\n";
						}
					}
				}
				echo "<script>{$todas_series}</script>";
*/
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
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarSerie n&atilde;o encontrada\n-->";
				$todas_series = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
			$retorno .= '<tr id="tr_curso">
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
			if( class_exists( "clsPmieducarTurma" ) )
			{
				/*$todas_turmas = "turma = new Array();\n";
				$obj_turma = new clsPmieducarTurma();
				$obj_turma->setOrderby("nm_turma ASC");
				$lst_turma = $obj_turma->lista( null, null, null, null, null, null, null, null, null, null, null, null, null,null, 1, null );

				if ( is_array( $lst_turma ) && count( $lst_turma ) )
				{
					foreach ( $lst_turma as $turma )
					{
						$todas_turmas .= "turma[turma.length] = new Array( {$turma["cod_turma"]}, '{$turma['nm_turma']}', '{$turma["ref_ref_cod_serie"]}', '{$turma["ref_ref_cod_escola"]}','{$turma["ref_cod_curso"]}', '{$turma["ref_cod_instituicao"]}' );\n";
					}
				}
				echo "<script>{$todas_turmas}</script>";*/

				// EDITAR
				if ( $this->ref_cod_serie /*|| $this->ref_cod_curso*/)
				{
					$obj_turma = new clsPmieducarTurma();
					$obj_turma->setOrderby("nm_turma ASC");
					$lst_turma = $obj_turma->lista( null, null, null, $this->ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, null, $this->ref_cod_curso, $this->ref_cod_instituicao );
					if ( is_array( $lst_turma ) && count( $lst_turma ) )
					{
						foreach ( $lst_turma as $turma )
						{
							$opcoes_turma["{$turma["cod_turma"]}"] = $turma['nm_turma'];
						}
					}
				}
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarTurma n&atilde;o encontrada\n-->";
				$todas_turmas = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
			$retorno .= '<tr id="tr_turma">
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

		$validacao = "";
		if ( $nivel_usuario == 1 )
		{
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
					if ( !document.getElementById( "ref_cod_curso" ).value ) {
					alert( "Por favor, selecione um curso" );
					return false;
					}
					if ( !document.getElementById( "ref_cod_serie" ).value) {
						//if( document.getElementById( "ref_cod_escola" ).value){
							alert( "Por favor, selecione uma série" );
							return false;
					//	}else{
						//	alert( "Por favor, selecione uma turma" );
					//		return false;
					//	}
					}
					if ( !document.getElementById( "ref_cod_turma" ).value ) {
					alert( "Por favor, selecione uma turma" );
					return false;
					} ';
		}
		elseif ( $nivel_usuario == 2 )
		{
			$validacao = '
					if ( !document.getElementById( "ref_cod_escola" ).value /*&&  !document.getElementById( "ref_cod_curso" ).value*/) {
					alert( "Por favor, selecione uma escola" );
					return false;
					}
					if ( !document.getElementById( "ref_cod_curso" ).value ) {
					alert( "Por favor, selecione um curso" );
					return false;
					}
					if ( !document.getElementById( "ref_cod_serie" ).value ) {
					alert( "Por favor, selecione uma série" );
					return false;
					}
					if ( !document.getElementById( "ref_cod_turma" ).value ) {
					alert( "Por favor, selecione uma turma" );
					return false;
					} ';
		}
		elseif ( $nivel_usuario == 4 )
		{
			$validacao = '
					if ( !document.getElementById( "ref_cod_curso" ).value ) {
					alert( "Por favor, selecione um curso" );
					return false;
					}
					if ( !document.getElementById( "ref_cod_serie" ).value ) {
					alert( "Por favor, selecione uma série" );
					return false;
					}
					if ( !document.getElementById( "ref_cod_turma" ).value ) {
					alert( "Por favor, selecione uma turma" );
					return false;
					} ';
		}
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
					 <input type='button' id='botao_busca' value='busca' onclick='javascript:acao();' class='botaolistagem'/>
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
		obj_instituicao 		 = document.getElementById( 'ref_cod_instituicao' );
		obj_instituicao.disabled = false;
	}

	if ( document.getElementById( 'ref_cod_escola' ) )
	{
		obj_escola 			= document.getElementById('ref_cod_escola');
		obj_escola.disabled = false;
	}

	if ( document.getElementById('ref_cod_curso') ) {
		obj_curso 		   = document.getElementById('ref_cod_curso');
		obj_curso.disabled = false;
	}

	if ( document.getElementById('ref_cod_serie') )
	{
		obj_serie 		   = document.getElementById('ref_cod_serie');
		obj_serie.disabled = false;
	}

	if ( document.getElementById('ref_cod_turma') )
	{
		obj_turma 		   = document.getElementById('ref_cod_turma');
		obj_turma.disabled = false;
	}
}
*/

<?
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
<?
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