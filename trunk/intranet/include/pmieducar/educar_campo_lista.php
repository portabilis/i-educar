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
?>
<?
  require_once 'include/portabilis_utils.php';

	$pessoa_logada = $_SESSION['id_pessoa'];

  if (! isset($listar_escolas_alocacao_professor))
    $listar_escolas_alocacao_professor = false;

  if (! isset($listar_somente_cursos_funcao_professor))
    $listar_somente_cursos_funcao_professor = false;

  if (! isset($listar_turmas_periodo_alocacao_professor))
    $listar_turmas_periodo_alocacao_professor = false;

  if (! isset($listar_componentes_curriculares_professor))
    $listar_componentes_curriculares_professor = false;

  if (! isset($exibe_nm_escola))
    $exibe_nm_escola = true;

	if (! isset($exibe_campo_lista_curso_escola))
		$exibe_campo_lista_curso_escola = true;

	if ($obrigatorio)
	{
		$instituicao_obrigatorio = $escola_obrigatorio = $curso_obrigatorio = $escola_curso_obrigatorio = $escola_curso_serie_obrigatorio = $serie_obrigatorio = $biblioteca_obrigatorio = $cliente_tipo_obrigatorio = $funcao_obrigatorio = $turma_obrigatorio = $componente_curricular_obrigatorio = $etapa_obrigatorio = true;
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
    $componente_curricular_obrigatorio = isset( $componente_curricular_obrigatorio ) ? $componente_curricular_obrigatorio           : false;
    $etapa_obrigatorio =                isset( $etapa_obrigatorio ) 	        ? $etapa_obrigatorio                : false;
    $ano_escolar_obrigatorio =                isset( $ano_escolar_obrigatorio ) 	        ? $ano_escolar_obrigatorio                : false;
	}

	if ($desabilitado)
	{
		$instituicao_desabilitado = $escola_desabilitado = $curso_desabilitado = $escola_curso_desabilitado = $escola_curso_serie_desabilitado = $serie_desabilitado = $biblioteca_desabilitado = $cliente_tipo_desabilitado = $turma_desabilitado = $componente_curricular_obrigatorio = $etapa_obrigatorio = true;
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
		$componente_curricular_desabilitado = 			isset( $componente_curricular_desabilitado ) 		? $displina_desabilitado     		: false;
    $etapa_desabilitado =               isset( $etapa_desabilitado ) 	        ? $etapa_desabilitado                : false;
    $ano_escolar_desabilitado =               isset( $ano_escolar_desabilitado ) 	        ? $ano_escolar_desabilitado                : false;
	}

 	$obj_permissoes = new clsPermissoes();
 	$nivel_usuario = $obj_permissoes->nivel_acesso($pessoa_logada);

 	//Se administrador
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
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"getDuploEscolaBiblioteca();",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
		}
		else if ($get_escola && $get_curso && $get_matricula)
		{
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao, "getMatricula();", null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio );
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
	//se nao eh administrador
	elseif ( $nivel_usuario != 1 )
	{
	 	$obj_usuario = new clsPmieducarUsuario($pessoa_logada);
		$det_usuario = $obj_usuario->detalhe();
		$this->ref_cod_instituicao = $det_usuario["ref_cod_instituicao"];
		$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
		//se eh institucional - admin
		if ($nivel_usuario == 4 || $nivel_usuario == 8)
		{
      if (! $listar_escolas_alocacao_professor)
      {
			  $this->ref_cod_escola = $det_usuario["ref_cod_escola"];
			  $this->campoOculto( "ref_cod_escola", $this->ref_cod_escola );
			  if($exibe_nm_escola == true)
			  {
				  $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
				  $det_escola = $obj_escola->detalhe();
				  $nm_escola = $det_escola['nome'];
				  $this->campoRotulo( "nm_escola","Escola", $nm_escola );
			  }
      }
			if ( $get_biblioteca )
			{
				$obj_per = new clsPermissoes();
				$ref_cod_biblioteca_ = $obj_per->getBiblioteca( $pessoa_logada );
			}
		}
	}

	//                    administrador          institucional - CPD
	if ( $get_escola && ( $nivel_usuario == 1 || $nivel_usuario == 2 || $cad_usuario || $listar_escolas_alocacao_professor))
	{
		$opcoes_escola = array( "" => "Selecione uma escola" );
		// EDITAR
		if ($this->ref_cod_instituicao)
		{
      $user = isset($user) ? $user : new User();
      if ($listar_escolas_alocacao_professor && $user->isProfessor())
      {
        $_professor = isset($_professor) ? $_professor : new Professor($user->userId);
        $_escolas = $_professor->getEscolasByInstituicao($this->ref_cod_instituicao);
			  foreach ($_escolas as $e)
				  $opcoes_escola[$e["escola_id"]] = $e['escola_nome'];
      }
      else
      {
			  $obj_escola = new clsPmieducarEscola();
			  $obj_escola->setOrderby("nome ASC");
			  $lista = $obj_escola->lista(null,null,null,$this->ref_cod_instituicao,null,null,null,null,null,null,1);
			  if ( is_array( $lista ) && count( $lista ) )
			  {
				  foreach ( $lista as $registro )
					  $opcoes_escola["{$registro["cod_escola"]}"] = "{$registro['nome']}";
			  }
      }
		}

		if ($get_biblioteca)
      $_js = 'getBiblioteca(2)';
    else
      $_js = null;

		$this->campoLista( "ref_cod_escola", "Escola", $opcoes_escola, $this->ref_cod_escola, $_js, null, null ,null, $escola_desabilitado,$escola_obrigatorio );
	}

	if ($get_curso)
	{
		$opcoes_curso = array( "" => "Selecione" );
    $opcoes_curso[''] = 'Selecione';

    $user = isset($user) ? $user : new User();
    if ($this->ref_cod_escola  && $listar_somente_cursos_funcao_professor && $user->isProfessor())
    {
      $_professor = isset($_professor) ? $_professor : new Professor($user->userId);
      $_cursos = $_professor->getCursosByInstituicaoEscola($this->ref_cod_instituicao, $this->ref_cod_escola);
		  foreach ($_cursos as $c)
			  $opcoes_curso[$c["curso_id"]] = $c['curso_nome'];
    }
		else
    {
  		// EDITAR
      if( $this->ref_cod_escola )
		  {
			  $obj_escola_curso = new clsPmieducarEscolaCurso();
			  $lst_escola_curso = $obj_escola_curso->lista( $this->ref_cod_escola,null,null,null,null,null,null,null,1 );

			  if ( is_array( $lst_escola_curso ) && count( $lst_escola_curso ) )
			  {
				  foreach ( $lst_escola_curso as $escola_curso )
					  $opcoes_curso["{$escola_curso["ref_cod_curso"]}"] = $escola_curso['nm_curso'];
			  }
		  }
		  else if( $this->ref_cod_instituicao )
		  {
			  $opcoes_curso = array( "" => "Selecione" );
			  $obj_curso = new clsPmieducarCurso();
			  $obj_curso->setOrderby("nm_curso ASC");

			  if ($sem_padrao)
			  {
				  $lista = $obj_curso->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao,0 );
        }
			  else
        {
				  $lista = $obj_curso->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao);
        }

			  if ( is_array( $lista ) && count( $lista ) )
			  {
				  foreach ( $lista as $registro )
					  $opcoes_curso["{$registro['cod_curso']}"] = "{$registro['nm_curso']}";
			  }
      }
		}
		$this->campoLista( "ref_cod_curso", "Curso", $opcoes_curso, $this->ref_cod_curso,null,null,null,null,$curso_desabilitado,$curso_obrigatorio );

		if ($get_semestre)
		{
			$this->campoRotulo("semestres", "Semestre", "<div id='div_semestre'>Selecione um Curso</div>");
			$this->campoOculto("is_padrao", 1);
		}
	}

  if ($get_ano_escolar)
  {
    $this->campoLista( "ano_escolar", "Ano escolar", array("" => "Selecione"), null, null, null, null, null, $ano_escolar_desabilitado, $ano_escolar_obrigatorio );
//    if ($this->ano_escolar)
//      $this->appendOutput("<script type='text/javascript'>getAnoEscolar(defaultId=$this->ano_escolar);</script>");
  }

	if ( $get_escola_curso_serie )
	{

		$opcoes_series_curso_escola = array( "" => "Selecione" );
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
					$opcoes_series_curso_escola["{$escola_curso_serie["ref_cod_serie"]}"] = $escola_curso_serie['nm_serie'];
				}
			}
		}
		$this->campoLista( "ref_ref_cod_serie", "S&eacute;rie", $opcoes_series_curso_escola, $this->ref_ref_cod_serie, null, null, null, null, $escola_curso_serie_desabilitado, $escola_curso_serie_obrigatorio );
	}

	if ( $get_serie )
	{
		$opcoes_serie = array( "" => "Selecione" );
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
		$this->campoLista( "ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, null, null, null, null, $serie_desabilitado, $serie_obrigatorio );

	}

	if ( $get_biblioteca )
	{
		if ($ref_cod_biblioteca_ == 0 && $nivel_usuario != 1 && $nivel_usuario != 2 )
		{
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
				$getCliente = '';
				if ($get_cliente_tipo) {
		      $getCliente = "getClienteTipo()";
				}
				$this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes_biblioteca, $this->ref_cod_biblioteca,$getCliente,null,null,null,$biblioteca_desabilitado,$biblioteca_obrigatorio );
			}
			else
			{
				$opcoes_biblioteca = array( "" => "Selecione" );
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
			  $getCliente = '';
        if ($get_cliente_tipo) {
          $getCliente = "getClienteTipo()";
        }
				$this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes_biblioteca, $this->ref_cod_biblioteca,$getCliente,null,null,null,$biblioteca_desabilitado,$biblioteca_obrigatorio );
			}
		}

	}

	if ( $get_cliente_tipo )
	{
		$opcoes_cli_tpo = array( "" => "Selecione" );
		if ( $this->ref_cod_biblioteca )
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
		$this->campoLista( "ref_cod_cliente_tipo", "Tipo do Cliente", $opcoes_cli_tpo, $this->ref_cod_cliente_tipo, null, null, null, null, $cliente_tipo_desabilitado, $cliente_tipo_obrigatorio );
	}
	if ( $get_funcao )
	{
		$opcoes_funcao = array( "" => "Selecione" );
		if ( $this->ref_cod_instituicao )
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
		$this->campoLista( "ref_cod_funcao", "Função", $opcoes_funcao, $this->ref_cod_funcao, null, null, null, null, $funcao_desabilitado, $funcao_obrigatorio );
	}
	if ( $get_turma )
	{
		$opcoes_turma = array( "" => "Selecione" );
	  // EDITAR
	  if ( ($this->ref_ref_cod_serie && $this->ref_cod_escola) || $this->ref_cod_curso )
	  {

      $user = isset($user) ? $user : new User();
      $turnos = array();
      if ($this->ref_cod_instituicao && $this->ref_cod_escola && $listar_turmas_periodo_alocacao_professor && $user->isProfessor())
      {
        require_once 'include/pmieducar/clsPmieducarServidorAlocacao.inc.php';
        $aloc = new ClsPmieducarServidorAlocacao();
        $aloc = $aloc->lista(null, $this->ref_cod_instituicao, null, null, $this->ref_cod_escola, $user->userId);
        foreach($aloc as $a)
          $turnos[] = $a['periodo'];
      }

		  $obj_turma = new clsPmieducarTurma();
		  $obj_turma->setOrderby("nm_turma ASC");

		  $lst_turma = $obj_turma->lista( null, null, null, $this->ref_ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, $this->ref_cod_curso, $this->ref_cod_instituicao);
		  if ( is_array( $lst_turma ) && count( $lst_turma ) )
		  {
			  foreach ( $lst_turma as $turma )
			  {
          if ($listar_turmas_periodo_alocacao_professor && $user->isProfessor())
          {
            if (in_array($turma['turma_turno_id'], $turnos))
  				    $opcoes_turma["{$turma['cod_turma']}"] = "{$turma['nm_turma']}";
          }
          else
				    $opcoes_turma["{$turma['cod_turma']}"] = "{$turma['nm_turma']}";
			  }
		  }
	  }

		$this->campoLista( "ref_cod_turma", "Turma", $opcoes_turma, $this->ref_cod_turma, null, null, null, null, $turma_desabilitado, $turma_obrigatorio );
	}

	if ($get_etapa)
  {
    $this->campoLista( "etapa", "Etapa", array("" => "Selecione"), null, null, null, null, null, $etapa_desabilitado, $etapa_obrigatorio );
  }

	if ($get_componente_curricular)
  {
		$this->campoLista( "ref_cod_componente_curricular", "Componente curricular", array("" => "Selecione"), null, null, null, null, null, $componente_curricular_desabilitado, $componente_curricular_obrigatorio );
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
<script type='text/javascript'>

/*
lista de funcoes a serem executadas apos atualizar um select
entity: nome da entidade do select alvo
_functions: funcoes e argumentos a serem executados ao atualizar o select (definido na entity)

Ex: afterUpdateSelect.push({entity:'ano_escolar', _functions:[{_function:getEtapa, _args:[$this->etapa]}]}
*/
var afterUpdateSelect = [];

/*
lista de strings a serem incluidas no final da url a ser enviada na chamada ajax,
ex: appendToUrl.push(['nome_entidade', 'var=valor']);
*/
var appendToUrl = [];

/* adiciona a url o conteudo incluido na lista appendToUrl, quando a entidade da lista é a mesma entidade repasada a função,
ex appendToUrl.push(['nome_entidade', 'var=valor']);
var url = prepareUrl('nome_entidade', 'http://teste.com');  

resultado http://teste.com?var=valor

*/
function prepareUrl(entity, url)
{
  for (var i=0; i<appendToUrl.length; i++)
  {
    if (appendToUrl[i][0] == entity && appendToUrl[i].length > 1)
    {
      if (url[url.length-1] != '&' && url[url.length-1] != '?')
      {
        if (url.indexOf('?') > -1)
          var _div = '&';
        else
          var _div = '?';
      }
      else
        var _div = '';
      url += _div + appendToUrl[i][1];
    }
  }
  return url;
}

<?
//   administrador          institucional = cpd
if ( $nivel_usuario == 1 || $nivel_usuario == 2 || $cad_usuario )
{
?>
	var before_getEscola;
	var after_getEscola;

	function getEscola()
	{
		if( typeof before_getEscola == 'function' )
		{
			before_getEscola();
		}

		limpaCampos(2);
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola');
		}
		if ( document.getElementById('ref_ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_ref_cod_escola');
		}
		campoEscola.disabled = true;
		campoEscola.options[0].text = 'Carregando escolas';

		var xml = new ajax( atualizaLstEscola );
		xml.envia( "educar_escola_xml2.php?ins="+campoInstituicao );
	}

	function atualizaLstEscola(xml)
	{
		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola');
		}
		if ( document.getElementById('ref_ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_ref_cod_escola');
		}
		campoEscola.length = 1;
		campoEscola.options[0].text = 'Selecione uma escola';
		campoEscola.disabled = false;

		var escolas = xml.getElementsByTagName( "escola" );
		if(escolas.length)
		{
			for( var i = 0; i < escolas.length; i++ )
			{
				campoEscola.options[campoEscola.options.length] = new Option( escolas[i].firstChild.data, escolas[i].getAttribute("cod_escola"),false,false);
			}
		}
		else
		{
			campoEscola.options[0].text = 'A instituição não possui nenhuma escola';
		}

		if( typeof after_getEscola == 'function' )
		{
			after_getEscola();
		}
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
	function getCurso()
	{
		var campoCurso = document.getElementById('ref_cod_curso');
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		campoCurso.length = 1;

		limpaCampos(3);
		if( campoInstituicao )
		{
			campoCurso.disabled = true;
			campoCurso.options[0].text = 'Carregando cursos';

			var xml = new ajax( atualizaLstCurso );
			xml.envia( "educar_curso_xml.php?ins="+campoInstituicao+"&sem=true" );
		}
		else
		{
			campoCurso.options[0].text = 'Selecione';
		}
	}
	function atualizaLstCurso(xml)
	{
		var campoCurso = document.getElementById('ref_cod_curso');
		campoCurso.length = 1;
		campoCurso.options[0].text = 'Selecione um curso';
		campoCurso.disabled = false;

		var cursos = xml.getElementsByTagName( "curso" );
		if(cursos.length)
		{
			for( var i = 0; i < cursos.length; i++ )
			{
				campoCurso.options[campoCurso.options.length] = new Option( cursos[i].firstChild.data, cursos[i].getAttribute('cod_curso'),false,false);
			}
		}
		else
		{
			campoCurso.options[0].text = 'A instituição não possui nenhum curso';



		}
	}
<?
}
elseif ( $get_curso && !$get_matricula )
{
?>
	function getCurso()
	{
		var campoCurso = document.getElementById('ref_cod_curso');
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		campoCurso.length = 1;

		limpaCampos(3);
		if( campoInstituicao )
		{
			campoCurso.disabled = true;
			campoCurso.options[0].text = 'Carregando cursos';

			var xml = new ajax( atualizaLstCurso );
			xml.envia( "educar_curso_xml.php?ins="+campoInstituicao );
		}
		else
		{
			campoCurso.options[0].text = 'Selecione';
		}
	}
	function atualizaLstCurso(xml)
	{
		var campoCurso = document.getElementById('ref_cod_curso');
		campoCurso.length = 1;
		campoCurso.options[0].text = 'Selecione um curso';
		campoCurso.disabled = false;

		var cursos = xml.getElementsByTagName( "curso" );
		if(cursos.length)
		{
			for( var i = 0; i < cursos.length; i++ )
			{
				campoCurso.options[campoCurso.options.length] = new Option( cursos[i].firstChild.data, cursos[i].getAttribute('cod_curso'),false,false);
			}
		}
		else
		{
			campoCurso.options[0].text = 'A instituição não possui nenhum curso';
		}
	}
<?
}
if ( $get_escola && $get_curso && $get_matricula)
{
?>
	function getMatricula()
	{
		getEscola();
		getCursoMatricula();
	}
<?
}
if ( $get_escola && $get_curso && !$get_matricula)
{
?>
	function getDuploEscolaCurso()
	{
		getEscola();
		getCurso();
	}
<?
}
//if ( $get_escola_curso )
if ( $get_curso )
{
  $user = isset($user) ? $user : new User();
  if ($listar_somente_cursos_funcao_professor && $user->isProfessor())
  {
    ?>
    function getEscolaCurso()
	  {
		  var escolaId = document.getElementById('ref_cod_escola').value;
		  var instituicaoId = document.getElementById('ref_cod_instituicao').value;
      if (escolaId)
      {
    		clearSelect(entity = 'curso', disable = true, text = 'Carregando cursos...', multipleId = true);

        var ajaxReq = new ajax(updateSelect);
        ajaxReq.envia("portabilis_curso_funcao_professor_xml.php?instituicao_id="+instituicaoId+"&escola_id="+escolaId);
      }
      else
    		clearSelect(entity = 'curso', disable = false, text = '', multipleId = true);
	  }
<?php
  }
  else
  {
?>
	function getEscolaCurso()
	{
		var campoCurso = document.getElementById('ref_cod_curso');
		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola').value;
		}
		else if ( document.getElementById('ref_ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_ref_cod_escola').value;
		}
		campoCurso.length = 1;

		limpaCampos(3);
		if( campoEscola )
		{
			campoCurso.disabled = true;
			campoCurso.options[0].text = 'Carregando cursos';

			var xml = new ajax( atualizaLstEscolaCurso );
			<? if ($get_cursos_nao_padrao) {?>
				xml.envia( "educar_curso_xml.php?esc="+campoEscola+"&padrao_ano_escolar=nao" );
			<?} else {?>
				xml.envia( "educar_curso_xml.php?esc="+campoEscola );
			<?}?>
		}
		else
		{
			campoCurso.options[0].text = 'Selecione';
		}
	}

	function atualizaLstEscolaCurso(xml)
	{
		var campoCurso = document.getElementById('ref_cod_curso');
		campoCurso.length = 1;
		campoCurso.options[0].text = 'Selecione um curso';
		campoCurso.disabled = false;

		var cursos = xml.getElementsByTagName( "curso" );
		if(cursos.length)
		{
			for( var i = 0; i < cursos.length; i++ )
			{
				campoCurso.options[campoCurso.options.length] = new Option( cursos[i].firstChild.data, cursos[i].getAttribute('cod_curso'),false,false);
			}
		}
		else
		{
			campoCurso.options[0].text = 'A escola não possui nenhum curso';
		}
	}
<?
  }
}
if ( $get_escola_curso_serie && $get_matricula && $_GET["ref_cod_aluno"] )
{
	// tah matriculando o aluno, seleciona as series que ele pode se matricular?
?>
	function getEscolaCursoSerie()
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoEscola = document.getElementById('ref_cod_escola').value;
		var campoCursoValue  = document.getElementById('ref_cod_curso').value;
		var campoCurso  = document.getElementById('ref_cod_curso');
		var campoSerie	= document.getElementById('ref_ref_cod_serie');
		var cod_aluno = <?=$_GET["ref_cod_aluno"]?>;

		campoSerie.length = 1;

		limpaCampos(4);
		if( campoInstituicao && campoCursoValue && campoEscola && cod_aluno )
		{
			campoSerie.disabled = true;
			campoSerie.options[0].text = 'Carregando séries';

			var xml = new ajax(atualizaLstSerieMatricula);
			xml.envia("educar_serie_matricula_xml.php?ins="+campoInstituicao+"&cur="+campoCursoValue+"&esc="+campoEscola+"&alu="+cod_aluno);
		}
		else
		{
			campoSerie.options[0].text = 'Selecione';
		}
	}

	function atualizaLstSerieMatricula(xml)
	{
		var campoSerie = document.getElementById('ref_ref_cod_serie');
		campoSerie.length = 1;
		campoSerie.options[0].text = 'Selecione uma série';
		campoSerie.disabled = false;

		series = xml.getElementsByTagName('serie');
		if(series.length)
		{
			for( var i = 0; i < xml.length; i++ )
			{
				campoSerie.options[campoSerie.options.length] = new Option( series[i].firstChild.data, series[i].getAttribute('cod_serie'),false,false);
			}
		}
		else
		{
			campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
		}
	}
<?
}
if ( $get_escola_curso_serie  && !$get_matricula )
{
?>
	function getEscolaCursoSerie()
	{
		var campoCurso = document.getElementById('ref_cod_curso').value;
		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola').value;
		}
		else if ( document.getElementById('ref_ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_ref_cod_escola').value;
		}
		var campoSerie	= document.getElementById('ref_ref_cod_serie');
		campoSerie.length = 1;

		limpaCampos(4);
		if( campoEscola && campoCurso )
		{
			campoSerie.disabled = true;
			campoSerie.options[0].text = 'Carregando séries';
			var xml = new ajax(atualizaLstEscolaCursoSerie);
			xml.envia("educar_escola_curso_serie_xml.php?esc="+campoEscola+"&cur="+campoCurso);
		}
		else
		{
			campoSerie.options[0].text = 'Selecione';
		}
	}

	function atualizaLstEscolaCursoSerie(xml)
	{
		var campoSerie = document.getElementById('ref_ref_cod_serie');
		campoSerie.length = 1;
		campoSerie.options[0].text = 'Selecione uma série';
		campoSerie.disabled = false;

		series = xml.getElementsByTagName('serie');
		if(series.length)
		{
			for( var i = 0; i < series.length; i++ )
			{
				campoSerie.options[campoSerie.options.length] = new Option( series[i].firstChild.data, series[i].getAttribute('cod_serie'),false,false);
			}
		}
		else
		{
			campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
		}
	}
<?
}
if ( $get_serie && $get_escola_serie)
{
	// lista todas as series que nao estao associadas a essa escola
?>
	function getSerie()
	{
		var campoCurso = document.getElementById('ref_cod_curso').value;
		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola').value;
		}
		else if ( document.getElementById('ref_ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_ref_cod_escola').value;
		}

		var campoSerie	= document.getElementById('ref_cod_serie');

		campoSerie.length = 1;

		limpaCampos(4);
		if( campoEscola && campoCurso )
		{
			campoSerie.disabled = true;
			campoSerie.options[0].text = 'Carregando séries';

			var xml = new ajax(atualizaLstSerie);
			xml.envia("educar_serie_not_escola_xml.php?esc="+campoEscola+"&cur="+campoCurso);
		}
		else
		{
			campoSerie.options[0].text = 'Selecione';
		}
	}

	function atualizaLstSerie(xml)
	{

		var campoSerie = document.getElementById('ref_cod_serie');
		campoSerie.length = 1;
		campoSerie.options[0].text = 'Selecione uma série';
		campoSerie.disabled = false;

		series = xml.getElementsByTagName('serie');
		if(series.length)
		{
			for( var i = 0; i < series.length; i++ )
			{

				campoSerie.options[campoSerie.options.length] = new Option( series[i].firstChild.data, series[i].getAttribute('cod_serie'),false,false);
			}
		}
		else
		{
			campoSerie.options[0].text = 'O curso não possui nenhuma série ou todas as séries já estã associadas a essa escola';
		}
	}
<?
}
if ( $get_serie && !$get_escola_serie  || $exibe_get_serie)
{
?>
	function getSerie()
	{
		var campoCurso = document.getElementById('ref_cod_curso').value;
		var campoSerie	= document.getElementById('ref_cod_serie');
		if(!campoSerie)
			campoSerie = document.getElementById('ref_ref_cod_serie');
		campoSerie.length = 1;

		limpaCampos(4);
		if( campoCurso )
		{
			campoSerie.disabled = true;
			campoSerie.options[0].text = 'Carregando séries';

			var xml = new ajax(atualizaLstSerie);
			xml.envia("educar_serie_xml.php?cur="+campoCurso);
		}
		else
		{
			campoSerie.options[0].text = 'Selecione';
		}
	}

	function atualizaLstSerie(xml)
	{
		var campoSerie = document.getElementById('ref_cod_serie');
		if(!campoSerie)
			campoSerie = document.getElementById('ref_ref_cod_serie');
		campoSerie.length = 1;
		campoSerie.options[0].text = 'Selecione uma série';
		campoSerie.disabled = false;

		series = xml.getElementsByTagName('serie');
		if(series.length)
		{
			for( var i = 0; i < series.length; i++ )
			{
				campoSerie.options[campoSerie.options.length] = new Option( series[i].firstChild.data, series[i].getAttribute('cod_serie'),false,false);
			}
		}
		else
		{
			campoSerie.options[0].text = 'O curso não possui nenhuma série';
		}
	}
<?
}
if ( $get_biblioteca )
{
?>
	function getBiblioteca(flag)
	{
		var campoBiblioteca	= document.getElementById('ref_cod_biblioteca');
		campoBiblioteca.length = 1;

		campoBiblioteca.disabled = true;
		campoBiblioteca.options[0].text = 'Carregando bibliotecas';

		var xml = new ajax(atualizaLstBiblioteca);
		if( flag == 1 )
		{
			xml.envia('educar_biblioteca_xml.php?ins=' + document.getElementById('ref_cod_instituicao').value);
		}
		else if( flag == 2 )
		{
			xml.envia('educar_biblioteca_xml.php?esc=' + document.getElementById('ref_cod_escola').value);
		}
	}

	function atualizaLstBiblioteca(xml)
	{
		var campoBiblioteca = document.getElementById('ref_cod_biblioteca');
		campoBiblioteca.length = 1;
		campoBiblioteca.options[0].text = 'Selecione uma biblioteca';
		campoBiblioteca.disabled = false;

		bibliotecas = xml.getElementsByTagName('biblioteca');
		if(bibliotecas.length)
		{
			for( var i = 0; i < bibliotecas.length; i++ )
			{
				campoBiblioteca.options[campoBiblioteca.options.length] = new Option( bibliotecas[i].firstChild.data, bibliotecas[i].getAttribute('cod_biblioteca'),false,false);
			}
		}
		else
		{
			campoBiblioteca.options[0].text = 'Nenhuma biblioteca';
		}
	}
<?
}
if ( $get_cliente_tipo )
{
?>
	function getClienteTipo()
	{
		var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
		var campoClienteTipo	= document.getElementById('ref_cod_cliente_tipo');
		campoClienteTipo.length = 1;

		if( campoClienteTipo )
		{
			campoClienteTipo.disabled = true;
			campoClienteTipo.options[0].text = 'Carregando tipos de cliente';

			var xml = new ajax(atualizaLstClienteTipo);
			xml.envia("educar_cliente_tipo_xml.php?bib="+campoBiblioteca);
//			educar_cliente_tipo_xml = function() { atualizaLstClienteTipo(); };
//			strURL = "educar_cliente_tipo_xml.php?bib="+campoBiblioteca;
//			DOM_loadXMLDoc( strURL );
		}
		else
		{
			campoClienteTipo.options[0].text = 'Selecione';
		}
	}

	function atualizaLstClienteTipo(xml)
	{
		var campoClienteTipo = document.getElementById('ref_cod_cliente_tipo');
		campoClienteTipo.length = 1;
		campoClienteTipo.options[0].text = 'Selecione um tipo de cliente';
		campoClienteTipo.disabled = false;

		var tipos = xml.getElementsByTagName('cliente_tipo');
		if(tipos.length)
		{
			for( var i = 0; i < tipos.length; i++ )
			{
				campoClienteTipo.options[campoClienteTipo.options.length] = new Option( tipos[i].firstChild.data, tipos[i].getAttribute('cod_cliente_tipo'),false,false);
			}
		}
		else
		{
			campoClienteTipo.options[0].text = 'A biblioteca não possui nenhum tipo de cliente';
		}
	}
<?
}
if ( $get_funcao )
{
?>
	function getFuncao()
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoFuncao	= document.getElementById('ref_cod_funcao');
		campoFuncao.length = 1;

		if( campoFuncao )
		{
			campoFuncao.disabled = true;
			campoFuncao.options[0].text = 'Carregando funções';

			var xml = new ajax(atualizaLstFuncao);
			xml.envia("educar_funcao_xml.php?ins="+campoInstituicao);
		}
		else
		{
			campoFuncao.options[0].text = 'Selecione';
		}
	}

	function atualizaLstFuncao(xml)
	{
		var campoFuncao = document.getElementById('ref_cod_funcao');
		campoFuncao.length = 1;
		campoFuncao.options[0].text = 'Selecione uma função';
		campoFuncao.disabled = false;

		var funcoes = xml.getElementsByTagName('funcao');
		if(funcoes.length)
		{
			for( var i = 0; i < funcoes.length; i++ )
			{
				campoFuncao.options[campoFuncao.options.length] = new Option( funcoes[i].firstChild.data, funcoes[i].getAttribute('cod_funcao'),false,false);
			}
		}
		else
		{
			campoFuncao.options[0].text = 'A instituição não possui nenhuma função';
		}
	}
<?
}
if ( $get_turma )
{

  $user = isset($user) ? $user : new User();
  if ($listar_turmas_periodo_alocacao_professor && $user->isProfessor())
  {
    $this->appendOutput("<script type='text/javascript'>appendToUrl.push(['turma', 'somente_turno_alocado_professor=true']);</script>");
  }

?>
	function getTurma()
	{
    var instituicaoId = document.getElementById('ref_cod_instituicao').value;
		var campoEscola = document.getElementById('ref_cod_escola').value;
		var campoSerie = document.getElementById('ref_ref_cod_serie').value;
		var campoTurma	= document.getElementById('ref_cod_turma');
		campoTurma.length = 1;

		limpaCampos(5);
		if( campoTurma )
		{
			campoTurma.disabled = true;
			campoTurma.options[0].text = 'Carregando turmas';

			var xml = new ajax(atualizaLstTurma);
		  xml.envia(prepareUrl('turma', "educar_turma_xml.php?inst="+instituicaoId+"&esc="+campoEscola+"&ser="+campoSerie));
		}
		else
		{
			campoTurma.options[0].text = 'Selecione';
		}
	}

	var after_getTurma= function(){};

	function atualizaLstTurma(xml)
	{
		var campoTurma = document.getElementById('ref_cod_turma');
		campoTurma.length = 1;
		campoTurma.options[0].text = 'Selecione uma turma';
		campoTurma.disabled = false;

		var turmas = xml.getElementsByTagName('turma');
		if(turmas.length)
		{
			for( var i = 0; i < turmas.length; i++ )
			{
				campoTurma.options[campoTurma.options.length] = new Option( turmas[i].firstChild.data, turmas[i].getAttribute('cod_turma'),false,false);
			}
		}
		else
		{
			campoTurma.options[0].text = 'A série não possui nenhuma turma';
		}

		after_getTurma();
	}
<?
}
?>


function limpaCampos(nivel)
{
  //for backward compatibility
  if (typeof(nivel) != 'string')
  {
    switch(nivel)
    {
      case 1:
        nivel = 'instituicao';
        break;
      case 2:
        nivel = 'escola';
        break;
      case 3:
        nivel = 'curso';
        break;
      case 4:
        nivel = 'serie';
        break;
      case 5:
        nivel = 'turma';
        break;
      default:
        nivel = '';
    }
  clearSelect(entity = nivel, disable = false, text = '', multipleId = true);
  }
}

<?
if ($get_semestre)
{?>

	function verifica_curso()
	{
		var ref_cod_curso = document.getElementById('ref_cod_curso').value;
		if (ref_cod_curso != '')
		{
			var pars = "ref_cod_curso="+ref_cod_curso;
			new Ajax.Request("educar_matricula_cad_curso_segue_padrao.php", {
									method: 'post',
									parameters: pars,
									onComplete: function (resp) {
										if (resp.responseText == 0) {
											var radios = "<input type='radio' id='sem1' name='semestre' value='1'>1º Semestre<br>"+
														  "<input type='radio' id='sem2' name='semestre' value='2'>2º Semestre<br>";
											$('div_semestre').innerHTML = radios;
											$('is_padrao').value = 0;
										} else {
											$('div_semestre').innerHTML = 'Selecione um Curso';
											$('is_padrao').value = 1;
										}
									}
								}
							);
		}
		else
		{
			$('div_semestre').innerHTML = 'Selecione um Curso';
			$('is_padrao').value = 1;
		}
	}

<?}?>

function clearSelect(entity, disable, text, multipleId)
{
  if (multipleId)
    var ids = new Array("cod_"+entity, "ref_cod_"+entity, "ref_ref_cod_"+entity);
  else
    var ids = new Array(entity);

  for (var i=0; i<ids.length; i++)
  {
    var e = document.getElementById(ids[i]);
    if (e)
    {
      e.disabled = disable;
      if (e.type == 'select-one')
      {
        e.length = 1;
        e.selectedIndex = 0;
        if (!text)
          text = 'Selecione';
        e.options[0].text = text;
      }
    }
  }
}

function updateSelect(xml)
{
	var att = xml.documentElement.getAttribute('entity');
  var attElementId = xml.documentElement.getAttribute('element_id');

  if (! attElementId)
    attElementId = att;
  var att_name = att.replace('_', ' ');

  var attElement = document.getElementById(attElementId);
	attElement.length = 1;
  attElement.selectedIndex = 0;

	var atts = xml.getElementsByTagName(att);
  if (atts.length)
  {
	  attElement.options[0] = new Option('Selecione um(a) ' + att_name,'');
    var _index = 0;
	  for(var i=0; i<atts.length; i++)
	  {
      var _selected = atts[i].getAttribute('selected') == 'selected';
      if (_selected)
        var _index = i+1;
		  attElement.options[i+1] = new Option(atts[i].getAttribute('value'), atts[i].getAttribute('id'), _selected,true);
    }
    attElement.selectedIndex = _index;
		attElement.disabled = false;

    var _actions = [];
    var __list = [];
    for (var i = 0; i< afterUpdateSelect.length; i++)
    {
      if (afterUpdateSelect[i].entity == att)
        _actions.push(afterUpdateSelect[i]);
      else
        __list.push(afterUpdateSelect[i]);
    }
    afterUpdateSelect = __list;

    for (var i = 0; i<_actions.length; i++)
    {
      for (var j = 0; j<_actions[i]._functions.length; j++)
      {
        var _function = _actions[i]._functions[j]._function;
        var _args = _actions[i]._functions[j]._args;
        _function(_args);
      }
    }
  }
	else
		attElement.options[0].text = 'Nenhum(a) '+ att_name +' encontrado(a)';
}

<?php if ($get_ano_escolar) { ?>
	function getAnoEscolar(defaultId)
	{
		var escolaId = document.getElementById('ref_cod_escola').value;
    if (escolaId)
    {
      clearSelect(entity = 'ano_escolar', disable = true, text = 'Carregando anos escolares...', multipleId=false);
      var ajaxReq = new ajax( updateSelect );
      ajaxReq.envia("portabilis_ano_escolar_xml.php?escola_id="+escolaId+"&default_id="+defaultId);
    }
    else
      clearSelect(entity = 'ano_escolar', disable = false, text = '', multipleId=false);
  }

<?php
    if ($this->ano_escolar)
    {
      $this->appendOutput("<script type='text/javascript'>getAnoEscolar(defaultId=$this->ano_escolar);</script>");
    }
} ?>

<?php if ($get_etapa) { ?>
	function getEtapa(defaultId)
	{
		var escolaId = document.getElementById('ref_cod_escola').value;
    var anoEscolar = document.getElementById('ano_escolar');
    if (! anoEscolar)
        var anoEscolar = document.getElementById('ano');
    var anoEscolar = anoEscolar.value;   
		var cursoId = document.getElementById('ref_cod_curso').value;
		var turmaId = document.getElementById('ref_cod_turma').value;
    if (escolaId && anoEscolar && cursoId && turmaId)
    {
		  clearSelect(entity = 'etapa', disable = true, text = 'Carregando etapas...', multipleId=false);

      var ajaxReq = new ajax( updateSelect );
      ajaxReq.envia("portabilis_etapa_xml.php?escola_id="+escolaId+"&ano_escolar="+anoEscolar+"&curso_id="+cursoId+"&turma_id="+turmaId+"&default_id="+defaultId);  
    }
    else
      clearSelect(entity = 'etapa', disable = false, text = '', multipleId=false);
  }
<?php
    if ($this->etapa)
    {
      $this->appendOutput("<script type='text/javascript'>afterUpdateSelect.push({entity:'ano_escolar', _functions:[{_function:getEtapa, _args:[$this->etapa]}]});</script>");
    }
} ?>

<?php if ($get_componente_curricular) { 

  $user = isset($user) ? $user : new User();
  if ($listar_componentes_curriculares_professor && $user->isProfessor())
  {
    $this->appendOutput("<script type='text/javascript'>appendToUrl.push(['componente_curricular', 'somente_funcao_professor=true']);</script>");
  }
?>

	function getComponenteCurricular(defaultId)
	{
   	var instituicaoId = document.getElementById('ref_cod_instituicao').value;
    var escolaId = document.getElementById('ref_cod_escola').value;
		var cursoId = document.getElementById('ref_cod_curso').value;
    var anoEscolar = document.getElementById('ano_escolar').value;
		var turmaId = document.getElementById('ref_cod_turma').value;
    if (escolaId && anoEscolar && turmaId)
    {
  		clearSelect(entity = 'componente_curricular', disable = true, text = 'Carregando componentes curriculares...', multipleId = true);

      var ajaxReq = new ajax( updateSelect );

      //ajaxReq.envia("portabilis_componente_curricular_xml.php?escola_id="+escolaId+"&turma_id="+turmaId+"&ano_escolar="+anoEscolar+"&default_id="+defaultId);

      ajaxReq.envia(prepareUrl('componente_curricular', "portabilis_componente_curricular_xml.php?instituicao_id="+instituicaoId+"&escola_id="+escolaId+"&curso_id="+cursoId+"&turma_id="+turmaId+"&ano_escolar="+anoEscolar+"&default_id="+defaultId));
    }
    else
  		clearSelect(entity = 'componente_curricular', disable = false, text = '', multipleId = true);
	}
<?php
    if ($this->ref_cod_componente_curricular)
    {
      $this->appendOutput("<script type='text/javascript'>afterUpdateSelect.push({entity:'ano_escolar', _functions:[{_function:getComponenteCurricular, _args:[$this->ref_cod_componente_curricular]}]});</script>");
    }
} ?>

</script>

