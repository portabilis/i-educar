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
 * @author Adriano Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Turma" );
		$this->processoAp = "586";
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

	var $cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_ref_cod_serie;
	var $ref_ref_cod_escola;
	var $ref_cod_infra_predio_comodo;
	var $nm_turma;
	var $sgl_turma;
	var $max_aluno;
	var $multiseriada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_turma_tipo;
	var $hora_inicial;
	var $hora_final;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;

	var $ref_cod_instituicao;
	var $ref_cod_curso;

	var $ref_cod_instituicao_regente;
	var $ref_cod_regente;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Turma - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_turma=$_GET["cod_turma"];

		$tmp_obj = new clsPmieducarTurma();
		$lst_obj = $tmp_obj->lista( $this->cod_turma, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,
								null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, array("true", "false") );
		$registro = array_shift($lst_obj);

		foreach ($registro as $key => $value) {
			$this->$key = $value;
		}
		if( ! $registro )
		{
			header( "location: educar_turma_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarTurmaTipo" ) )
		{
			$obj_ref_cod_turma_tipo = new clsPmieducarTurmaTipo( $registro["ref_cod_turma_tipo"] );
			$det_ref_cod_turma_tipo = $obj_ref_cod_turma_tipo->detalhe();
			$registro["ref_cod_turma_tipo"] = $det_ref_cod_turma_tipo["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_turma_tipo"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarTurmaTipo\n-->";
		}

		if( class_exists( "clsPmieducarInfraPredioComodo" ) )
		{
			$obj_ref_cod_infra_predio_comodo = new clsPmieducarInfraPredioComodo( $registro["ref_cod_infra_predio_comodo"] );
			$det_ref_cod_infra_predio_comodo = $obj_ref_cod_infra_predio_comodo->detalhe();
			$registro["ref_cod_infra_predio_comodo"] = $det_ref_cod_infra_predio_comodo["nm_comodo"];
		}
		else
		{
			$registro["ref_cod_infra_predio_comodo"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarInfraPredioComodo\n-->";
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		if( class_exists( "clsPmieducarEscola" ) )
		{
			$this->ref_ref_cod_escola = $registro["ref_ref_cod_escola"];
			$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$registro["ref_ref_cod_escola"] = $det_ref_cod_escola["nome"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}

		if( class_exists( "clsPmieducarCurso" ) )
		{
			$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
			$padrao_ano_escolar = $det_ref_cod_curso["padrao_ano_escolar"];
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
		}

		if( class_exists( "clsPmieducarSerie" ) )
		{
			$this->ref_ref_cod_serie = $registro["ref_ref_cod_serie"];
			$obj_ser = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
			$det_ser = $obj_ser->detalhe();
			$registro["ref_ref_cod_serie"] = $det_ser["nm_serie"];
		}
		else
		{
			$registro["ref_ref_cod_serie"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarSerie\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if( $registro["ref_cod_instituicao"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
			}
		}
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if( $registro["ref_ref_cod_escola"] )
			{
				$this->addDetalhe( array( "Escola", "{$registro["ref_ref_cod_escola"]}") );
			}
		}
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"]}") );
		}
		if( $registro["ref_ref_cod_serie"] )
		{
			$this->addDetalhe( array( "S&eacute;rie", "{$registro["ref_ref_cod_serie"]}") );
		}
		if( $registro["ref_cod_regente"] )
		{
			$obj_pessoa = new clsPessoa_($registro["ref_cod_regente"]);
			$det = $obj_pessoa->detalhe();

			$this->addDetalhe( array( "Professor/Regente", "{$det["nome"]}") );
		}

		if( $registro["ref_cod_infra_predio_comodo"] )
		{
			$this->addDetalhe( array( "Sala", "{$registro["ref_cod_infra_predio_comodo"]}") );
		}
		if( $registro["ref_cod_turma_tipo"] )
		{
			$this->addDetalhe( array( "Tipo de Turma", "{$registro["ref_cod_turma_tipo"]}") );
		}
		if( $registro["nm_turma"] )
		{
			$this->addDetalhe( array( "Turma", "{$registro["nm_turma"]}") );
		}
		if( $registro["sgl_turma"] )
		{
			$this->addDetalhe( array( "Sigla", "{$registro["sgl_turma"]}") );
		}
		if( $registro["max_aluno"] )
		{
			$this->addDetalhe( array( "M&aacute;ximo de Alunos", "{$registro["max_aluno"]}") );
		}
		$this->addDetalhe(array("Situação", dbBool($registro["visivel"]) ? "Ativo" : "Desativo"));
		if( $registro["multiseriada"] == 1)
		{
			if ( $registro["multiseriada"] == 1 )
				$registro["multiseriada"] = "sim";
			else
				$registro["multiseriada"] = "n&atilde;o";

			$this->addDetalhe( array( "Multi-Seriada", "{$registro["multiseriada"]}") );
			$obj_serie_mult = new clsPmieducarSerie($registro['ref_ref_cod_serie_mult']);
			$det_serie_mult = $obj_serie_mult->detalhe();
			$this->addDetalhe( array( "Série Multi-Seriada", "{$det_serie_mult["nm_serie"]}") );

		}
		if ($padrao_ano_escolar == 1)
		{
			if( $registro["hora_inicial"] )
			{
				$registro["hora_inicial"] = date("H:i", strtotime( $registro["hora_inicial"]));
				$this->addDetalhe( array( "Hora Inicial", "{$registro["hora_inicial"]}") );
			}
			if( $registro["hora_final"] )
			{
				$registro["hora_final"] = date("H:i", strtotime( $registro["hora_final"]));
				$this->addDetalhe( array( "Hora Final", "{$registro["hora_final"]}") );
			}
			if( $registro["hora_inicio_intervalo"] )
			{
				$registro["hora_inicio_intervalo"] = date("H:i", strtotime( $registro["hora_inicio_intervalo"]));
				$this->addDetalhe( array( "Hora In&iacute;cio Intervalo", "{$registro["hora_inicio_intervalo"]}") );
			}
			if( $registro["hora_fim_intervalo"] )
			{
				$registro["hora_fim_intervalo"] = date("H:i", strtotime( $registro["hora_fim_intervalo"]));
				$this->addDetalhe( array( "Hora Fim Intervalo", "{$registro["hora_fim_intervalo"]}") );
			}

		}
		else if ($padrao_ano_escolar == 0)
		{
			$obj = new clsPmieducarTurmaModulo();
			$obj->setOrderby("data_inicio ASC");
			$lst = $obj->lista( $this->cod_turma );
			if ($lst)
			{
				$tabela = "<TABLE>
						       <TR align=center>
						           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
						           <TD bgcolor=#A1B3BD><B>Data In&iacute;cio</B></TD>
						           <TD bgcolor=#A1B3BD><B>Data Fim</B></TD>
						       </TR>";
				$cont = 0;

				foreach ( $lst AS $valor )
				{
					if ( ($cont % 2) == 0 )
					{
						$color = " bgcolor=#E4E9ED ";
					}
					else
					{
						$color = " bgcolor=#FFFFFF ";
					}
					$obj_modulo = new clsPmieducarModulo( $valor["ref_cod_modulo"] );
					$det_modulo = $obj_modulo->detalhe();
					$nm_modulo = $det_modulo["nm_tipo"];

					$valor["data_inicio"] = dataFromPgToBr($valor["data_inicio"]);
					$valor["data_fim"] = dataFromPgToBr($valor["data_fim"]);

					$tabela .= "<TR>
								    <TD {$color} align=left>{$nm_modulo}</TD>
								    <TD {$color} align=left>{$valor["data_inicio"]}</TD>
								    <TD {$color} align=left>{$valor["data_fim"]}</TD>
								</TR>";
					$cont++;
				}
				$tabela .= "</TABLE>";
			}
			if( $tabela )
			{
				$this->addDetalhe( array( "M&oacute;dulo", "{$tabela}") );
			}

			$dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );

			$obj = new clsPmieducarTurmaDiaSemana();
			$lst = $obj->lista( null, $this->cod_turma );
			if ($lst)
			{
				$tabela1 = "<TABLE>
						       <TR align=center>
						           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
						           <TD bgcolor=#A1B3BD><B>Hora Inicial</B></TD>
						           <TD bgcolor=#A1B3BD><B>Hora Final</B></TD>
						       </TR>";
				$cont = 0;

				foreach ( $lst AS $valor )
				{
					if ( ($cont % 2) == 0 )
					{
						$color = " bgcolor=#E4E9ED ";
					}
					else
					{
						$color = " bgcolor=#FFFFFF ";
					}

					$valor["hora_inicial"] = date("H:i", strtotime( $valor["hora_inicial"]));
					$valor["hora_final"] = date("H:i", strtotime( $valor["hora_final"]));

					$tabela1 .= "<TR>
								    <TD {$color} align=left>{$dias_da_semana[$valor["dia_semana"]]}</TD>
								    <TD {$color} align=left>{$valor["hora_inicial"]}</TD>
								    <TD {$color} align=left>{$valor["hora_final"]}</TD>
								</TR>";
					$cont++;
				}
				$tabela1 .= "</TABLE>";
			}
			if( $tabela1 )
			{
				$this->addDetalhe( array( "Dia da Semana", "{$tabela1}") );
			}

		}


		if($this->ref_ref_cod_escola && $this->ref_ref_cod_serie)
		{
			$obj = new clsPmieducarEscolaSerieDisciplina();
			$lst = $obj->lista( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola,null,1 );
			if ($lst)
			{
				$tabela3 = "<TABLE>
						       <TR align=center>
						           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
						       </TR>";
				$cont = 0;
				foreach ( $lst AS $valor )
				{
					if ( ($cont % 2) == 0 )
					{
						$color = " bgcolor=#E4E9ED ";
					}
					else
					{
						$color = " bgcolor=#FFFFFF ";
					}
					$obj_disciplina = new clsPmieducarDisciplina( $valor["ref_cod_disciplina"] );
					$obj_disciplina->setOrderby("nm_disciplina ASC");
					$obj_disciplina_det = $obj_disciplina->detalhe();
					$nm_disciplina = $obj_disciplina_det["nm_disciplina"];

					$tabela3 .= "<TR>
								    <TD {$color} align=left>{$nm_disciplina}</TD>
								</TR>";
					$cont++;
				}
				$tabela3 .= "</TABLE>";
			}
		}
		else
		{

			$obj = new clsPmieducarDisciplina();
			$lst = $obj->lista( null,null,null,null,null,null,null,null,null,null,null,null,null,null,$this->ref_cod_curso,$this->ref_cod_instituicao);
			if ($lst)
			{
				$tabela3 = "<TABLE>
						       <TR align=center>
						           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
						       </TR>";
				$cont = 0;

				foreach ( $lst AS $valor )
				{
					if ( ($cont % 2) == 0 )
					{
						$color = " bgcolor=#E4E9ED ";
					}
					else
					{
						$color = " bgcolor=#FFFFFF ";
					}

					$tabela3 .= "<TR>
								    <TD {$color} align=left>{$valor['nm_disciplina']}</TD>
								</TR>";
					$cont++;
				}
				$tabela3 .= "</TABLE>";
			}
		}

		if( $tabela3 )
		{
			$this->addDetalhe( array( "Disciplina", "{$tabela3}") );
		}

		if ( $obj_permissoes->permissao_cadastra( 586, $this->pessoa_logada, 7 ) )
		{
			$this->url_novo = "educar_turma_cad.php";
			$this->url_editar = "educar_turma_cad.php?cod_turma={$registro["cod_turma"]}";
		}
		$this->url_cancelar = "educar_turma_lst.php";
		$this->largura = "100%";
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