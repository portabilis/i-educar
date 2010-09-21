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
	if(!isset($exibe_campo_lista_curso_escola))
	{
		$exibe_campo_lista_curso_escola = true;
	}

	if ($obrigatorio)
	{
		$instituicao_obrigatorio = $escola_obrigatorio = $curso_obrigatorio = $escola_curso_obrigatorio = $escola_curso_serie_obrigatorio = $serie_obrigatorio = $biblioteca_obrigatorio = $cliente_tipo_obrigatorio = $funcao_obrigatorio = $turma_obrigatorio = true;
	}
	else
	{
		$instituicao_obrigatorio = 			isset($instituicao_obrigatorio) 		? $instituicao_obrigatorio 			: false;
		$escola_obrigatorio = 				isset($escola_obrigatorio) 				? $escola_obrigatorio 				: false;
		$curso_obrigatorio = 				isset($curso_obrigatorio) 				? $curso_obrigatorio 				: false;
		$escola_curso_obrigatorio = 		isset($escola_curso_obrigatorio) 		? $escola_curso_obrigatorio 		: false;
		$escola_curso_serie_obrigatorio = 	isset($escola_curso_serie_obrigatorio) 	? $escola_curso_serie_obrigatorio 	: false;
		$serie_obrigatorio = 				isset($serie_obrigatorio) 				? $serie_obrigatorio 				: false;
		$biblioteca_obrigatorio = 			isset($biblioteca_obrigatorio) 			? $biblioteca_obrigatorio 			: false;
		$cliente_tipo_obrigatorio = 		isset( $cliente_tipo_obrigatorio ) 		? $cliente_tipo_obrigatorio 		: false;
		$funcao_obrigatorio = 				isset( $funcao_obrigatorio ) 			? $funcao_obrigatorio 				: false;
		$turma_obrigatorio = 				isset( $turma_obrigatorio ) 			? $turma_obrigatorio 				: false;
	}

	if ($desabilitado)
	{
		$instituicao_desabilitado = $escola_desabilitado = $curso_desabilitado = $escola_curso_desabilitado = $escola_curso_serie_desabilitado = $serie_desabilitado = $biblioteca_desabilitado = $cliente_tipo_desabilitado = $turma_desabilitado = true;
	}
	else
	{
		$instituicao_desabilitado = 		isset($instituicao_desabilitado) 		? $instituicao_desabilitado 		: false;
		$escola_desabilitado = 				isset($escola_desabilitado) 			? $escola_desabilitado 				: false;
		$curso_desabilitado = 				isset($curso_desabilitado) 				? $curso_desabilitado 				: false;
		$escola_curso_desabilitado = 		isset($escola_curso_desabilitado) 		? $escola_curso_desabilitado 		: false;
		$escola_curso_serie_desabilitado = 	isset($escola_curso_serie_desabilitado)	? $escola_curso_serie_desabilitado 	: false;
		$serie_desabilitado = 				isset($serie_desabilitado) 				? $serie_desabilitado 				: false;
		$biblioteca_desabilitado = 			isset($biblioteca_desabilitado) 		? $biblioteca_desabilitado 			: false;
		$cliente_tipo_desabilitado = 		isset( $cliente_tipo_desabilitado ) 	? $cliente_tipo_desabilitado 		: false;
		$funcao_desabilitado = 				isset( $funcao_desabilitado ) 			? $funcao_desabilitado 				: false;
		$turma_desabilitado = 				isset( $turma_desabilitado ) 			? $turma_desabilitado 				: false;
	}

 	$obj_permissoes = new clsPermissoes();
 	$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

	if( $nivel_usuario == 1 || $cad_usuario )
	{
		$opcoes = array( "" => "Selecione" );
		$obj_instituicao = new clsPmieducarInstituicao();
		$obj_instituicao->setCamposLista("cod_instituicao, nm_instituicao");
		$obj_instituicao->setOrderby("nm_instituicao ASC");
		$lista = $obj_instituicao->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,1);
		if ( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista as $registro )
			{
				$opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
			}
		}

		if ($get_escola && $get_biblioteca)
		{
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"getDuplo();",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
		}
		else if ($get_escola && $get_curso )
		{
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao, "getDuploEscolaCurso();", null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio );
		}
		else if ($get_escola)
		{
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"getEscola();",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
		}
		else if ($get_curso)
		{
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"getCurso();",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
		}
		else if ($get_biblioteca)
		{
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"getBiblioteca(1);",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
		}
		else if ( $get_cliente_tipo )
		{
			$this->campoLista( "ref_cod_cliente_tipo", "Tipo de Cliente", $opcoes, $this->ref_cod_cliente_tipo, "getCliente();", null, null, null, $cliente_tipo_desabilitado, $cliente_tipo_obrigatorio );
		}
		else
		{
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
		}
	}
	elseif ( $nivel_usuario != 1 )
	{
	 	$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
		$det_usuario = $obj_usuario->detalhe();
		$this->ref_cod_instituicao = $det_usuario["ref_cod_instituicao"];
		$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
		if ($nivel_usuario == 4 || $nivel_usuario == 8)
		{
			$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
			$det_usuario = $obj_usuario->detalhe();
			$this->ref_cod_escola = $det_usuario["ref_cod_escola"];
			$this->campoOculto( "ref_cod_escola", $this->ref_cod_escola );
			if($exibe_nm_escola == true)
			{
				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
				$det_escola = $obj_escola->detalhe();
				$nm_escola = $det_escola['nome'];
				$this->campoRotulo( "nm_escola","Escola", $nm_escola );
			}
			if ( $get_biblioteca )
			{
				$obj_per = new clsPermissoes();
				$ref_cod_biblioteca_ = $obj_per->getBiblioteca( $this->pessoa_logada );

			}
		}
	}

	if ( $get_escola && ( $nivel_usuario == 1 || $nivel_usuario == 2 || $cad_usuario ) )
	{
		$opcoes_escola = array( "" => "Selecione uma escola" );
		$todas_escolas = "escola = new Array();\n";
		$obj_escola = new clsPmieducarEscola();
		$obj_escola->setOrderby("nome ASC");
		$lista = $obj_escola->lista(null,null,null,null,null,null,null,null,null,null,1);
		if ( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista as $registro )
			{
				$todas_escolas .= "escola[escola.length] = new Array( {$registro["cod_escola"]}, '{$registro['nome']}', {$registro["ref_cod_instituicao"]} );\n";
			}
		}
		echo "<script>{$todas_escolas}</script>";

		// EDITAR
		if ($this->ref_cod_instituicao)
		{
			$obj_escola = new clsPmieducarEscola();
			$obj_escola->setOrderby("nome ASC");
			$lista = $obj_escola->lista(null,null,null,$this->ref_cod_instituicao,null,null,null,null,null,null,1);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes_escola["{$registro["cod_escola"]}"] = "{$registro['nome']}";
				}
			}
		}

		if ($get_biblioteca)
		{
			$this->campoLista( "ref_cod_escola", "Escola", $opcoes_escola, $this->ref_cod_escola,"getBiblioteca(2);",null,null,null,$escola_desabilitado,$escola_obrigatorio );
		}
		else
		{
			$this->campoLista( "ref_cod_escola", "Escola", $opcoes_escola, $this->ref_cod_escola,null,null,null,null,$escola_desabilitado,$escola_obrigatorio );
		}
	}

	if ( $get_escola_curso )
	{
		$todos_cursos = "curso = new Array();\n";
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



		$opcoes_cursos_escola = array( "" => "Selecione" );
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
		echo "<script>{$todos_cursos_escola}</script>";

		// EDITAR
		if ( $this->ref_cod_escola )
		{
			$obj_escola_curso = new clsPmieducarEscolaCurso();
//					$obj_escola_curso->setOrderby("ref_cod_curso ASC");

			$lst_escola_curso = $obj_escola_curso->lista( $this->ref_cod_escola,null,null,null,null,null,null,null,1 );

			if ( is_array( $lst_escola_curso ) && count( $lst_escola_curso ) )
			{
				foreach ( $lst_escola_curso as $escola_curso )
				{
					//$obj_curso = new clsPmieducarCurso( $escola_curso["ref_cod_curso"] );
					//$det_curso = $obj_curso->detalhe();
					$opcoes_cursos_escola["{$escola_curso["ref_cod_curso"]}"] = $escola_curso['nm_curso'];
				}
			}
		}

//		if(($exibe_campo_lista_curso_escola && !isset($sem_padrao)) || $this->ref_cod_escola)
			$this->campoLista( "ref_cod_curso", "Curso", $opcoes_cursos_escola, $this->ref_cod_curso, null, null, null, null, $escola_curso_desabilitado, $escola_curso_obrigatorio );
	}
	else if ($get_curso)
	{
		$opcoes_curso = array( "" => "Selecione" );
		$todos_cursos = "curso = new Array();\n";
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

		// EDITAR
		//														 -->!isset
		if ($this->ref_cod_instituicao && (!$this->ref_cod_escola /*&& isset($sem_padrao)*/))
		{
			$opcoes_curso = array( "" => "Selecione" );
			$obj_curso = new clsPmieducarCurso();
			$obj_curso->setOrderby("nm_curso ASC");

			if ($sem_padrao)
				$lista = $obj_curso->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao,0 );
			else
				$lista = $obj_curso->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao);

			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes_curso["{$registro['cod_curso']}"] = "{$registro['nm_curso']}";
				}
			}

		}
//		if(!$this->ref_cod_escola && isset($sem_padrao))
		$this->campoLista( "ref_cod_curso", "Curso", $opcoes_curso, $this->ref_cod_curso,null,null,null,null,false,$curso_obrigatorio );
	}

	if ( $get_escola_curso_serie )
	{

		$opcoes_series_curso_escola = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarEscolaSerie" ) && class_exists( "clsPmieducarSerie" ) )
		{
			$todas_series_curso_escola = "serie = new Array();\n";
			$obj_escola_serie = new clsPmieducarEscolaSerie();
			$obj_escola_serie->setOrderby("nm_serie ASC");
			$lst_escola_serie = $obj_escola_serie->lista( null,null,null,null,null,null,null,null,null,null,null,null,1 );
			if ( is_array( $lst_escola_serie ) && count( $lst_escola_serie ) )
			{
				foreach ( $lst_escola_serie as $escola_curso_serie )
				{

					$escola_curso_serie["hora_inicial"] = date("H:i", strtotime( $escola_curso_serie["hora_inicial"]));
					$escola_curso_serie["hora_final"] = date("H:i", strtotime( $escola_curso_serie["hora_final"]));
					$escola_curso_serie["hora_inicio_intervalo"] = date("H:i", strtotime( $escola_curso_serie["hora_inicio_intervalo"]));
					$escola_curso_serie["hora_fim_intervalo"] = date("H:i", strtotime( $escola_curso_serie["hora_fim_intervalo"]));
					//$obj_serie = new clsPmieducarSerie( $escola_curso_serie["ref_cod_serie"] );
					//$det_serie = $obj_serie->detalhe();
					$todas_series_curso_escola .= "serie[serie.length] = new Array( {$escola_curso_serie["ref_cod_serie"]}, '{$escola_curso_serie['nm_serie']}', {$escola_curso_serie["ref_cod_escola"]}, {$escola_curso_serie["ref_cod_curso"]}, '{$escola_curso_serie["hora_inicial"]}', '{$escola_curso_serie["hora_final"]}', '{$escola_curso_serie["hora_inicio_intervalo"]}', '{$escola_curso_serie["hora_fim_intervalo"]}' );\n";
				}
			}
			echo "<script>{$todas_series_curso_escola}</script>";

			// EDITAR
			if ( $this->ref_cod_escola && $this->ref_cod_curso )
			{
				$obj_escola_serie = new clsPmieducarEscolaSerie();
				$obj_escola_serie->setOrderby("nm_serie ASC");
				$lst_escola_serie = $obj_escola_serie->lista( $this->ref_cod_escola,null,null,null,null,null,null,null,null,null,null,null,1,null,null,null,null,null,$this->ref_cod_curso );
				if ( is_array( $lst_escola_serie ) && count( $lst_escola_serie ) )
				{
					foreach ( $lst_escola_serie as $escola_curso_serie )
					{
						//$obj_serie = new clsPmieducarSerie( $escola_curso_serie["ref_cod_serie"] );
						//$det_serie = $obj_serie->detalhe();
						$opcoes_series_curso_escola["{$escola_curso_serie["ref_cod_serie"]}"] = $escola_curso_serie['nm_serie'];
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse(s) clsPmieducarEscolaSerie / clsPmieducarSerie n&atilde;o encontrada(s)\n-->";
			$opcoes_series_curso_escola = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoLista( "ref_ref_cod_serie", "S&eacute;rie", $opcoes_series_curso_escola, $this->ref_ref_cod_serie, null, null, null, null, $escola_curso_serie_desabilitado, $escola_curso_serie_obrigatorio );

	}

	if ( $get_serie )
	{

		$opcoes_serie = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarSerie" ) )
		{
			$todas_series = "serie = new Array();\n";
			$obj_serie = new clsPmieducarSerie();
			$obj_serie->setOrderby("nm_serie ASC");
			$lst_serie = $obj_serie->lista( null,null,null,null,null,null,null,null,null,null,null,null,1 );

			if ( is_array( $lst_serie ) && count( $lst_serie ) )
			{
				foreach ( $lst_serie as $serie )
				{
					if ($get_escola_serie)
					{
						$obj_escola_serie = new clsPmieducarEscolaSerie();
						$obj_escola_serie->setOrderby("nm_serie ASC");
						$lst_escola_serie = $obj_escola_serie->lista( null,$serie["cod_serie"],null,null,null,null,null,null,null,null,null,null,1 );
						if ( is_array( $lst_escola_serie ) && count( $lst_escola_serie ) )
						{
							$arr = "new Array(";
							$conc = "";
							foreach ( $lst_escola_serie as $escola_serie )
							{
								$arr .= "{$conc}'{$escola_serie["ref_cod_escola"]}'";
								$conc = ",";
							}
							$arr .= ")";
						}else
						{
							$arr = "new Array()";
						}
						$todas_series .= "serie[serie.length] = new Array( {$serie["cod_serie"]}, '{$serie['nm_serie']}', {$serie["ref_cod_curso"]}, {$arr}, '{$serie["intervalo"]}' );\n";
					}
					else
					{
						$todas_series .= "serie[serie.length] = new Array( {$serie["cod_serie"]}, '{$serie['nm_serie']}', {$serie["ref_cod_curso"]} );\n";
					}
				}
			}
			echo "<script>{$todas_series}</script>";

			// EDITAR
			if ( $this->ref_cod_curso )
			{
				$obj_serie = new clsPmieducarSerie();
				$obj_serie->setOrderby("nm_serie ASC");
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
		$this->campoLista( "ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, null, null, null, null, $serie_desabilitado, $serie_obrigatorio );

	}

	if ( $get_biblioteca )
	{
		if ($ref_cod_biblioteca_ == 0 && $nivel_usuario != 1 && $nivel_usuario != 2 )
		{
		//	$this->ref_cod_biblioteca = $ref_cod_biblioteca_;

			$this->campoOculto( "ref_cod_biblioteca", $this->ref_cod_biblioteca );
		}
		else
		{
			$qtd_bibliotecas = count($ref_cod_biblioteca_);
			if ( $qtd_bibliotecas == 1 && ($nivel_usuario == 4 || $nivel_usuario == 8))
			{
				$det_unica_biblioteca = array_shift($ref_cod_biblioteca_);
				$this->ref_cod_biblioteca = $det_unica_biblioteca["ref_cod_biblioteca"];
				$this->campoOculto( "ref_cod_biblioteca", $this->ref_cod_biblioteca );
			}
			else if ( $qtd_bibliotecas > 1)
			{

				$opcoes_biblioteca = array( "" => "Selecione" );
				if ( is_array( $ref_cod_biblioteca_ ) && count( $ref_cod_biblioteca_ ) )
				{
					foreach ($ref_cod_biblioteca_ as $biblioteca)
					{
						$obj_biblioteca = new clsPmieducarBiblioteca($biblioteca["ref_cod_biblioteca"]);
						$det_biblioteca = $obj_biblioteca->detalhe();
						$opcoes_biblioteca["{$biblioteca["ref_cod_biblioteca"]}"] = "{$det_biblioteca['nm_biblioteca']}";
					}
				}
				$this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes_biblioteca, $this->ref_cod_biblioteca,null,null,null,null,$biblioteca_desabilitado,$biblioteca_obrigatorio );
			}
			else
			{
				$opcoes_biblioteca = array( "" => "Selecione" );
				if( class_exists( "clsPmieducarBiblioteca" ) )
				{
					$todas_bibliotecas = "biblioteca = new Array();\n";
					$obj_biblioteca = new clsPmieducarBiblioteca();
					$obj_biblioteca->setOrderby("nm_biblioteca ASC");
					$lista = $obj_biblioteca->lista(null,null,null,null,null,null,null,null,null,null,null,null,1);
					if ( is_array( $lista ) && count( $lista ) )
					{
						foreach ( $lista as $registro )
						{
							$todas_bibliotecas .= "biblioteca[biblioteca.length] = new Array( {$registro["cod_biblioteca"]}, '{$registro['nm_biblioteca']}', '{$registro['ref_cod_escola']}', {$registro['ref_cod_instituicao']});\n";
						}
					}
					echo "<script>{$todas_bibliotecas}</script>";

					// EDITAR
					if ($this->ref_cod_escola || $this->ref_cod_instituicao)
					{
						$objTemp = new clsPmieducarBiblioteca();
						$objTemp->setOrderby("nm_biblioteca ASC");
						$lista = $objTemp->lista(null,$this->ref_cod_instituicao,null,null,null,null,null,null,null,null,null,null,1);

						if ( is_array( $lista ) && count( $lista ) )
						{
							foreach ( $lista as $registro )
							{
								$opcoes_biblioteca["{$registro['cod_biblioteca']}"] = "{$registro['nm_biblioteca']}";
							}
						}
					}
				}
				else
				{
					echo "<!--\nErro\nClasse clsPmieducarBiblioteca n&atilde;o encontrada\n-->";
					$opcoes_biblioteca = array( "" => "Erro na gera&ccedil;&atilde;o" );
				}
				$this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes_biblioteca, $this->ref_cod_biblioteca,null,null,null,null,$biblioteca_desabilitado,$biblioteca_obrigatorio );
			}
		}

	}

	if ( $get_cliente_tipo )
	{
		if ( class_exists( "clsPmieducarClienteTipo" ) )
		{
			$todos_cli_tpo = "cliente_tipo = new Array();\n";
			$obj_cli_tpo = new clsPmieducarClienteTipo();
			$obj_cli_tpo->setOrderby("nm_tipo ASC");
			$lst_cli_tpo = $obj_cli_tpo->lista( null, null, null, null, null, null, null, null, null, null, 1 );
			if ( is_array( $lst_cli_tpo ) && count( $lst_cli_tpo ) )
			{
				foreach ( $lst_cli_tpo as $cli_tpo )
				{
					$todos_cli_tpo .= "cliente_tipo[cliente_tipo.length] = new Array( {$cli_tpo["cod_cliente_tipo"]}, '{$cli_tpo['nm_tipo']}', '{$cli_tpo['ref_cod_biblioteca']}' );\n";
				}
			}
			echo "<script>{$todos_cli_tpo}</script>";
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarClienteTipo n&atilde;o encontrada\n-->";
			$opcoes_cli_tpo = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$opcoes_cli_tpo = array( "" => "Selecione" );
		if ( $this->ref_cod_biblioteca )
		{
			if ( class_exists( "clsPmieducarClienteTipo" ) )
			{
				$obj_cli_tpo = new clsPmieducarClienteTipo();
				$obj_cli_tpo->setOrderby("nm_tipo ASC");
				$lst_cli_tpo = $obj_cli_tpo->lista( null, $this->ref_cod_biblioteca, null, null, null, null, null, null, null, null, 1 );
				if ( is_array( $lst_cli_tpo ) && count( $lst_cli_tpo ) )
				{
					foreach ( $lst_cli_tpo as $cli_tpo )
					{
						$opcoes_cli_tpo["{$cli_tpo['cod_cliente_tipo']}"] = "{$cli_tpo['nm_tipo']}";
					}
				}
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarClienteTipo n&atilde;o encontrada\n-->";
				$opcoes_cli_tpo = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
		}
		$this->campoLista( "ref_cod_cliente_tipo", "Tipo do Cliente", $opcoes_cli_tpo, $this->ref_cod_cliente_tipo, null, null, null, null, $cliente_tipo_desabilitado, $cliente_tipo_obrigatorio );
	}
	if ( $get_funcao )
	{
		if ( class_exists( "clsPmieducarFuncao" ) )
		{
			$todas_funcao = "funcao = new Array();\n";
			$obj_funcao = new clsPmieducarFuncao();
			$obj_funcao->setOrderby("nm_funcao ASC");
			$lst_funcao = $obj_funcao->lista( null, null, null, null, null, null, null, null, null, null, 1 );
			if ( is_array( $lst_funcao ) && count( $lst_funcao ) )
			{
				foreach ( $lst_funcao as $funcao )
				{
					$todas_funcao .= "funcao[funcao.length] = new Array( {$funcao["cod_funcao"]}, '{$funcao['nm_funcao']}', '{$funcao['ref_cod_instituicao']}' );\n";
				}
			}
			echo "<script>{$todas_funcao}</script>";
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarFuncao n&atilde;o encontrada\n-->";
			$opcoes_funcao = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$opcoes_funcao = array( "" => "Selecione" );
		if ( $this->ref_cod_instituicao )
		{
			if ( class_exists( "clsPmieducarFuncao" ) )
			{
				$obj_funcao = new clsPmieducarFuncao();
				$obj_funcao->setOrderby("nm_funcao ASC");
				$lst_funcao = $obj_funcao->lista( null, null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao );
				if ( is_array( $lst_funcao ) && count( $lst_funcao ) )
				{
					foreach ( $lst_funcao as $funcao )
					{
						$opcoes_funcao["{$funcao['cod_funcao']}"] = "{$funcao['nm_funcao']}";
					}
				}
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarFuncao n&atilde;o encontrada\n-->";
				$opcoes_funcao = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
		}
		$this->campoLista( "ref_cod_funcao", "Função", $opcoes_funcao, $this->ref_cod_funcao, null, null, null, null, $funcao_desabilitado, $funcao_obrigatorio );
	}

	if ( $get_turma )
	{
		$opcoes_turma = array( "" => "Selecione" );
		if ( class_exists( "clsPmieducarTurma" ) )
		{
			$todas_turma = "turma = new Array();\n";
			$obj_turma = new clsPmieducarTurma();
			$obj_turma->setOrderby("nm_turma ASC");
			$lst_turma = $obj_turma->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1 );
			if ( is_array( $lst_turma ) && count( $lst_turma ) )
			{
				foreach ( $lst_turma as $turma )
				{
					$todas_turma .= "turma[turma.length] = new Array( {$turma["cod_turma"]}, '{$turma['nm_turma']}', '{$turma['ref_ref_cod_serie']}', '{$turma["ref_ref_cod_escola"]}', '{$turma["ref_cod_curso"]}' );\n";
				}
			}
			echo "<script>{$todas_turma}</script>";

			// EDITAR
			if ( ($this->ref_ref_cod_serie && $this->ref_cod_escola) || $this->ref_cod_curso )
			{
				$obj_turma = new clsPmieducarTurma();
				$obj_turma->setOrderby("nm_turma ASC");
				$lst_turma = $obj_turma->lista( null, null, null, $this->ref_ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, $this->ref_cod_curso );
				if ( is_array( $lst_turma ) && count( $lst_turma ) )
				{
					foreach ( $lst_turma as $turma )
					{
						$opcoes_turma["{$turma['cod_turma']}"] = "{$turma['nm_turma']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurma n&atilde;o encontrada\n-->";
			$opcoes_turma = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoLista( "ref_cod_turma", "Turma", $opcoes_turma, $this->ref_cod_turma, null, null, null, null, $turma_desabilitado, $turma_obrigatorio );
	}
	if (isset($get_cabecalho))
	{
		if ( $qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8) )
			${$get_cabecalho}[] = "Biblioteca";
		else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)
			${$get_cabecalho}[] = "Biblioteca";
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
			${$get_cabecalho}[] = "Escola";
		if ($nivel_usuario == 1)
			${$get_cabecalho}[] = "Institui&ccedil;&atilde;o";
	}
?>
<script>
<?
if ( $nivel_usuario == 1 || $nivel_usuario == 2 || $cad_usuario )
{
?>
//caso seja preciso executar uma funcao no onchange da instituicao adicionar uma funcao as seguintes variaveis no arquivo
//que precisar , assim, toda vez que for chamada a funcao serao executadas estas funcoes
	var before_getEscola = function(){}
	var after_getEscola  = function(){}

	function getEscola()
	{
		before_getEscola();

		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola');
		}
		if ( document.getElementById('ref_ref_cod_escola') )
		{
			//document.getElementById('ref_ref_cod_escola').disabled = false;
			var campoEscola = document.getElementById('ref_ref_cod_escola');
		}

		campoEscola.length = 1;
		campoEscola.options[0].text = 'Selecione uma escola';
//		campoEscola.disabled = false;
		for (var j = 0; j < escola.length; j++)
		{
			if (escola[j][2] == campoInstituicao)
			{
				campoEscola.options[campoEscola.options.length] = new Option( escola[j][1], escola[j][0],false,false);
			}
		}
		if ( campoEscola.length == 1 && campoInstituicao != '' ) {
			campoEscola.options[0] = new Option( 'A instituição não possui nenhuma escola', '', false, false );
		}

		after_getEscola();
	}
<?
	if ($get_escola && $get_biblioteca)
	{
?>
		function getDuploEscolaBiblioteca()
		{
			getEscola();
			getBiblioteca(1);
		}
<?
	}
}
if ( $get_curso && $sem_padrao && !$get_matricula )
{
?>
	var before_getCurso = function(){}
	var after_getCurso  = function(){}

	function getCurso()
	{
		before_getCurso();

		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoCurso = document.getElementById('ref_cod_curso');
		campoCurso.length = 1;

		campoCurso.options[0].text = 'Selecione um curso';
		//campoCurso.disabled = false;
		for (var j = 0; j < curso.length; j++)
		{
			if ( (curso[j][3] == campoInstituicao) && (curso[j][4] == 0) )
			{
				campoCurso.options[campoCurso.options.length] = new Option( curso[j][1], curso[j][0],false,false);
			}
		}
		if ( campoCurso.length == 1 && campoInstituicao != '' )
		{
			campoCurso.options[0] = new Option( 'A instituição não possui nenhum curso', '', false, false );
		}
		after_getCurso();
	}
<?
}
elseif ( $get_curso && !$get_matricula )
{
?>
	var before_getCurso = function(){}
	var after_getCurso  = function(){}

	function getCurso()
	{
		before_getCurso();

		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoCurso = document.getElementById('ref_cod_curso');

		campoCurso.length = 1;
		campoCurso.options[0].text = 'Selecione um curso';
		for (var j = 0; j < curso.length; j++)
		{
			if (curso[j][3] == campoInstituicao)
			{
				campoCurso.options[campoCurso.options.length] = new Option( curso[j][1], curso[j][0],false,false);
			}
		}
		if ( campoCurso.length == 1 && campoInstituicao != '' ) {
			campoCurso.options[0] = new Option( 'A instituição não possui nenhum curso', '', false, false );
		}
		after_getCurso();
	}
<?
}
if ( $get_escola && $get_curso)
{
?>
	function getDuploEscolaCurso()
	{
		getEscola();
		getCurso();
	}
<?
}
if ( $get_escola_curso )
{
?>
	var before_getEscolaCurso = function(){}
	var after_getEscolaCurso  = function(){}

	function getEscolaCurso()
	{
		before_getEscolaCurso();

		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola').value;
		}
		else if ( document.getElementById('ref_ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_ref_cod_escola').value;
		}
		var campoCurso	= document.getElementById('ref_cod_curso');

		campoCurso.length = 1;
		campoCurso.options[0] = new Option( 'Selecione um curso', '', false, false );
		for (var j = 0; j < escola_curso.length; j++)
		{
			if (escola_curso[j][2] == campoEscola)
			{
				campoCurso.options[campoCurso.options.length] = new Option( escola_curso[j][1], escola_curso[j][0],false,false);
			}
		}
		if ( campoCurso.length == 1 && campoEscola != '' ) {
			campoCurso.options[0] = new Option( 'A escola não possui nenhum curso', '', false, false );
		}

		after_getEscolaCurso();
	}
<?
}
if ( $get_escola_curso_serie && $get_matricula )
{
?>
	var before_getEscolaCursoSerie= function(){}
	var after_getEscolaCursoSerie  = function(){}

	function getEscolaCursoSerie()
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoEscola = document.getElementById('ref_cod_escola').value;
		var campoCurso  = document.getElementById('ref_cod_curso').value;
		var campoSerie	= document.getElementById('ref_ref_cod_serie');

		before_getEscolaCursoSerie();

		campoSerie.length = 1;
		campoSerie.options[0] = new Option( 'Selecione uma série', '', false, false );
		if ( matriculas.length )
		{
			var instituicao_igual = false;
			for (var j = 0; j < matriculas.length; j++)
			{
				if ( matriculas[j][13] == campoInstituicao )
				{
					instituicao_igual = true;
					// caso a (matricula_escola seja == a escola_escolhida) e ( (matricula_escola siga o padrao_escola) e (escola_ano_letivo > ano_matricula) ou (matricula_escola NAO siga o padrao_escola)  )
					if ( (matriculas[j][1] == campoEscola) && ( ( ( (matriculas[j][11] == 1) && (matriculas[j][10] > matriculas[j][9]) ) ) || (matriculas[j][11] != 1) ) )
					{
						// caso curso_escolhido == curso_matricula
						if (matriculas[j][2] == campoCurso)
						{
							// foi reprovado na serie
							if (matriculas[j][5] == 2)
							{
								// exibe a mesma serie
								campoSerie.options[campoSerie.options.length] = new Option( matriculas[j][4], matriculas[j][3],false,false);
							}
							// foi aprovado na serie
							else if (matriculas[j][5] == 1)
							{
								for (var ct = 0; ct < matriculas[j][6].length; ct++)
								{
									if(matriculas[j][6][ct] == campoCurso)
									{
										campoSerie.options[campoSerie.options.length] = new Option( matriculas[j][8][ct], matriculas[j][7][ct],false,false);
									}
								}
							}
						} // caso curso_escolhido seja !
						else
						{
							// foi aprovado na serie
							if (matriculas[j][5] == 1)
							{
								// exibe somente a serie da sequencia do curso_escolhido
								for (var ct = 0; ct < matriculas[j][6].length; ct++)
								{
									if (matriculas[j][6][ct] == campoCurso)
									{
										campoSerie.options[campoSerie.options.length] = new Option( matriculas[j][8][ct], matriculas[j][7][ct],false,false);
									}
								}
							}
							// foi aprovado, reprovado ou esta em andamento na serie
							if ( (matriculas[j][5] == 1) || (matriculas[j][5] == 2) || (matriculas[j][5] == 3) )
							{
//								alert('aprovado == 1 || 2 || 3');
								var pertence_sequencia = false;
								var achou_serie_mat = false;
								var aux_serie;
								var ini_serie;

								for (var i = 0; i < ini_sequencia.length; i++)
								{
									ini_serie = ini_sequencia[i][0];
									var s = 0;

									while ( s < sequencia.length )
									{
										aux_serie = sequencia[s][1];

										if (ini_serie == aux_serie)
										{
											if (matriculas[j][3] == aux_serie)
											{
//												alert('achou serie matricula');
												achou_serie_mat = true;
											}
											if (sequencia[s][0] == campoCurso)
											{
//												alert('pertence sequencia ');
												pertence_sequencia = true;
											}
											else if (sequencia[s][2] == campoCurso)
											{
//												alert('pertence sequencia ');
												pertence_sequencia = true;
											}
											else
											{
												ini_serie = sequencia[s][3];
												s = -1;
											}
										}
										s++;
									}
									if (achou_serie_mat && pertence_sequencia)
									{
										break;
									}
								}
								if (achou_serie_mat && !pertence_sequencia)
								{
//									alert('nao pertence sequencia ');
									for (var i = 0; i < serie.length; i++)
									{
										if ( (serie[i][2] == campoEscola) && (serie[i][3] == campoCurso) )
										{
											campoSerie.options[campoSerie.options.length] = new Option( serie[i][1], serie[i][0],false,false);
										}
									}
//									if ( campoSerie.options.length == 1 )
//									{
//										campoSerie.options[0] = new Option( 'Nenhuma série disponível', '', false, false );
//									}
								}
							}
						}
					}
					// caso a (escola_matricula seja ! da escola_escolhida) e (exista uma solicitacao_transferencia)
					else if ( (matriculas[j][1] != campoEscola) && (matriculas[j][12] == 1) )
					{
//						alert('escola !');
						// caso curso_escolhido == curso_matricula
						if (matriculas[j][2] == campoCurso)
						{
							// foi (reprovado na serie) ou (esta em andamento)
							if ( (matriculas[j][5] == 2) || (matriculas[j][5] == 3) )
							{
								// exibe a mesma serie
								campoSerie.options[campoSerie.options.length] = new Option( matriculas[j][4], matriculas[j][3],false,false);
							}
							// foi aprovado na serie
							else if (matriculas[j][5] == 1)
							{
								for (var ct = 0; ct < matriculas[j][6].length; ct++)
								{
		//							alert(matriculas[j][6]);
									if(matriculas[j][6][ct] == campoCurso)
									{
										campoSerie.options[campoSerie.options.length] = new Option( matriculas[j][8][ct], matriculas[j][7][ct],false,false);
									}
								}
							}
						}// caso curso_escolhido seja !
						else
						{
							// foi aprovado na serie
							if (matriculas[j][5] == 1)
							{
								// exibe somente a serie da sequencia do curso_escolhido
								for (var ct = 0; ct < matriculas[j][6].length; ct++)
								{
									if (matriculas[j][6][ct] == campoCurso)
									{
										campoSerie.options[campoSerie.options.length] = new Option( matriculas[j][8][ct], matriculas[j][7][ct],false,false);
									}
								}
							}
							// foi aprovado, reprovado ou esta em andamento na serie
							if ( (matriculas[j][5] == 1) || (matriculas[j][5] == 2) || (matriculas[j][5] == 3) )
							{
//								alert('aprovado == 1 || 2 || 3');
								var pertence_sequencia = false;
								var achou_serie_mat = false;
								var aux_serie;
								var ini_serie;

								for (var i = 0; i < ini_sequencia.length; i++)
								{
									ini_serie = ini_sequencia[i][0];
									var s = 0;

									while ( s < sequencia.length )
									{
										aux_serie = sequencia[s][1];

										if (ini_serie == aux_serie)
										{
											if (matriculas[j][3] == aux_serie)
											{
//												alert('achou serie matricula');
												achou_serie_mat = true;
											}
											if (sequencia[s][0] == campoCurso)
											{
//												alert('pertence sequencia ');
												pertence_sequencia = true;
											}
											else if (sequencia[s][2] == campoCurso)
											{
//												alert('pertence sequencia ');
												pertence_sequencia = true;
											}
											else
											{
												ini_serie = sequencia[s][3];
												s = -1;
											}
										}
										s++;
									}
									if (achou_serie_mat && pertence_sequencia)
									{
										break;
									}
								}
								if (achou_serie_mat && !pertence_sequencia)
								{
//									alert('nao pertence sequencia ');
									for (var i = 0; i < serie.length; i++)
									{
										if ( (serie[i][2] == campoEscola) && (serie[i][3] == campoCurso) )
										{
											campoSerie.options[campoSerie.options.length] = new Option( serie[i][1], serie[i][0],false,false);
										}
									}
								}
							}
						}
					}
					// independente da escola
					else
					{
//						alert('independente da escola');
						// caso curso_escolhido seja ! curso_matricula
						if (matriculas[j][2] != campoCurso)
						{
							// foi aprovado, reprovado ou esta em andamento na serie
							if ( (matriculas[j][5] == 1) || (matriculas[j][5] == 2) || (matriculas[j][5] == 3) )
							{
//								alert('aprovado == 1 || 2 || 3');
								var pertence_sequencia = false;
								var achou_serie_mat = false;
								var aux_serie;
								var ini_serie;

								for (var i = 0; i < ini_sequencia.length; i++)
								{
									ini_serie = ini_sequencia[i][0];

									var s = 0;
									while ( s < sequencia.length )
									{
										aux_serie = sequencia[s][1];

										if (ini_serie == aux_serie)
										{
											if (matriculas[j][3] == aux_serie)
											{
//												alert('achou serie matricula');
												achou_serie_mat = true;
											}
											if (sequencia[s][0] == campoCurso)
											{
//												alert('pertence sequencia ');
												pertence_sequencia = true;
											}
											else if (sequencia[s][2] == campoCurso)
											{
//												alert('pertence sequencia ');
												pertence_sequencia = true;
											}
											else
											{
												ini_serie = sequencia[s][3];
												s = -1;
											}
										}
										s++;
									}
									if (achou_serie_mat && pertence_sequencia)
									{
										break;
									}
								}
								//modificado 190906
								if ((achou_serie_mat && !pertence_sequencia) || (!achou_serie_mat && !pertence_sequencia))
								{
//									alert('nao pertence sequencia ');
									for (var i = 0; i < serie.length; i++)
									{
										if ( (serie[i][2] == campoEscola) && (serie[i][3] == campoCurso) )
										{
											campoSerie.options[campoSerie.options.length] = new Option( serie[i][1], serie[i][0],false,false);
										}
									}
								}
							}
						}
					}
				}
			}
			// caso o aluno n tenha nenhuma matricula na instituicao escolhida, eh liberado para c matricular em qq serie
			if ( !instituicao_igual )
			{
				for (var j = 0; j < serie.length; j++)
				{
					if ( (serie[j][2] == campoEscola) && (serie[j][3] == campoCurso) )
					{
						campoSerie.options[campoSerie.options.length] = new Option( serie[j][1], serie[j][0],false,false);
					}
				}
				if ( campoSerie.options.length == 1 )
				{
					campoSerie.options[0] = new Option( 'Nenhuma série disponível', '', false, false );
				}
			}
		}
		else // caso o aluno ainda n possua nenhuma matricula, eh liberado para c matricular em qq serie
		{
			for (var j = 0; j < serie.length; j++)
			{
				if ( (serie[j][2] == campoEscola) && (serie[j][3] == campoCurso) )
				{
					campoSerie.options[campoSerie.options.length] = new Option( serie[j][1], serie[j][0],false,false);
				}
			}
			if ( campoSerie.options.length == 1 )
			{
				campoSerie.options[0] = new Option( 'Nenhuma série disponível', '', false, false );
			}
		}

		after_getEscolaCursoSerie();
	}
<?
}
if ( $get_escola_curso_serie  && !$get_matricula )
{
?>
	var before_getEscolaCursoSerie= function(){}
	var after_getEscolaCursoSerie  = function(){}

	function getEscolaCursoSerie()
	{
		var campoEscola = document.getElementById('ref_cod_escola').value;
		var campoCurso  = document.getElementById('ref_cod_curso').value;
		var campoSerie	= document.getElementById('ref_ref_cod_serie');

		before_getEscolaCursoSerie();

		campoSerie.length = 1;
		campoSerie.options[0] = new Option( 'Selecione uma série', '', false, false );
		for (var j = 0; j < serie.length; j++)
		{
			if ((serie[j][2] == campoEscola) && (serie[j][3] == campoCurso))
			{
				campoSerie.options[campoSerie.options.length] = new Option( serie[j][1], serie[j][0],false,false);
			}
		}
		if ( campoSerie.length == 1 && campoCurso != '' ) {
			campoSerie.options[0] = new Option( 'O curso não possui nenhuma série', '', false, false );
		}

		after_getEscolaCursoSerie();
	}
<?
}
if ( $get_serie && $get_escola_serie )
{
?>

	var before_getSerie= function(){}
	var after_getSerie  = function(){}

	function getSerie()
	{
		var campoEscola  = document.getElementById('ref_cod_escola').value;
		var campoCurso  = document.getElementById('ref_cod_curso').value;
		var campoSerie	= document.getElementById('ref_cod_serie');

		before_getSerie();

		campoSerie.length = 1;
		for (var j = 0; j < serie.length; j++)
		{
			if (serie[j][2] == campoCurso)
			{
				var achou = false;
				for(var ct = 0;ct < serie[j][3].length;ct++)
				{
					if(serie[j][3][ct] == campoEscola){
						achou = true;
						break;
					}
				}

				if(!achou)
					campoSerie.options[campoSerie.options.length] = new Option( serie[j][1], serie[j][0],false,false);
			}
		}

		after_getSerie();
	}
<?
}
if ( $get_serie && !$get_escola_serie )
{
?>
	function getSerie()
	{
		var campoCurso  = document.getElementById('ref_cod_curso').value;
		var campoSerie	= document.getElementById('ref_cod_serie');

		campoSerie.length = 1;
		for (var j = 0; j < serie.length; j++)
		{
			if (serie[j][2] == campoCurso)
			{
				campoSerie.options[campoSerie.options.length] = new Option( serie[j][1], serie[j][0],false,false);
			}
		}
	}
<?
}
if ( $get_biblioteca )
{
?>
	var before_getBiblioteca = function(){}
	var after_getBiblioteca  = function(){}

	function getBiblioteca(flag)
	{
		before_getBiblioteca();

		var campoBiblioteca = document.getElementById('ref_cod_biblioteca');

		campoBiblioteca.length = 1;
		campoBiblioteca.options[0].text = 'Selecione uma biblioteca';
		if (flag == 1)
		{
			var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
			for (var j = 0; j < biblioteca.length; j++)
			{
				if (biblioteca[j][3] == campoInstituicao)
				{
					campoBiblioteca.options[campoBiblioteca.options.length] = new Option( biblioteca[j][1], biblioteca[j][0],false,false);
				}
			}
			if ( campoBiblioteca.length == 1 && campoInstituicao != '' )
				campoBiblioteca.options[0] = new Option( 'A instituição não possui nenhuma biblioteca', '', false, false );
		}
		else if (flag == 2)
		{
			var campoEscola = document.getElementById('ref_cod_escola').value;
			for (var j = 0; j < biblioteca.length; j++)
			{
				if (biblioteca[j][2] == campoEscola)
				{
					campoBiblioteca.options[campoBiblioteca.options.length] = new Option( biblioteca[j][1], biblioteca[j][0],false,false);
				}
			}
			if ( campoBiblioteca.length == 1 && campoEscola != '' )
				campoBiblioteca.options[0] = new Option( 'A escola não possui nenhuma biblioteca', '', false, false );
		}

		after_getBiblioteca();
	}
<?
}
if ( $get_cliente_tipo )
{
?>
	var before_getClienteTipo = function(){}
	var after_getClienteTipo  = function(){}

	function getClienteTipo()
	{
		before_getClienteTipo();

		var campoBiblioteca  = document.getElementById( 'ref_cod_biblioteca' ).value;
		var campoClienteTipo = document.getElementById( 'ref_cod_cliente_tipo' );

		campoClienteTipo.length = 1;
		campoClienteTipo.options[0] = new Option( 'Selecione um tipo de cliente', '', false, false );
		for (var j = 0; j < cliente_tipo.length; j++)
		{
			if ( ( cliente_tipo[j][2] == campoBiblioteca ) )
			{
				campoClienteTipo.options[campoClienteTipo.options.length] = new Option( cliente_tipo[j][1], cliente_tipo[j][0], false, false );
			}
		}
		if ( campoClienteTipo.length == 1 && campoBiblioteca != '' ) {
			campoClienteTipo.options[0] = new Option( 'A biblioteca não possui nenhum tipo de cliente', '', false, false );
		}

		after_getClienteTipo();
	}
<?
}
if ( $get_funcao )
{
?>
	var before_getFuncao = function(){}
	var after_getFuncao  = function(){}

	function getFuncao()
	{
		before_getFuncao();

		var campoInstituicao = document.getElementById( 'ref_cod_instituicao' ).value;
		var campoFuncao      = document.getElementById( 'ref_cod_funcao' );

		campoFuncao.length = 1;
		campoFuncao.options[0] = new Option( 'Selecione uma Função', '', false, false );
		for (var j = 0; j < funcao.length; j++)
		{
			if ( ( funcao[j][2] == campoInstituicao ) )
			{
				campoFuncao.options[campoFuncao.options.length] = new Option( funcao[j][1], funcao[j][0], false, false );
			}
		}
		if ( campoFuncao.length == 1 && campoInstituicao != '' ) {
			campoFuncao.options[0] = new Option( 'A instituição não possui nenhuma função', '', false, false );
		}

		after_getFuncao();
	}
<?
}
if ( $get_turma )
{
?>
	var before_getTurma = function(){}
	var after_getTurma  = function(){}

	function getTurma()
	{
		before_getTurma();

		var campoEscola = document.getElementById( 'ref_cod_escola' ).value;
		var campoSerie = document.getElementById( 'ref_ref_cod_serie' ).value;
		var campoTurma = document.getElementById( 'ref_cod_turma' );

		campoTurma.length = 1;
		campoTurma.options[0] = new Option( 'Selecione uma Turma', '', false, false );
		for ( var j = 0; j < turma.length; j++ )
		{
			if ( ( turma[j][2] == campoSerie ) && ( turma[j][3] == campoEscola ) )
			{
				campoTurma.options[campoTurma.options.length] = new Option( turma[j][1], turma[j][0], false, false );
			}
		}
		if ( campoTurma.length == 1 && campoSerie != '' ) {
			campoTurma.options[0] = new Option( 'A série não possui nenhuma turma', '', false, false );
		}

		after_getTurma();
	}
<?
}

?>

</script>