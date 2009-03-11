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
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Reserva Vaga" );
		$this->processoAp = "639";
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

	var $pessoa_logada;

	var $ref_cod_escola;
	var $ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Reserva Vaga - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->ref_cod_serie=$_GET["ref_cod_serie"];
		$this->ref_cod_escola=$_GET["ref_cod_escola"];

		$tmp_obj = new clsPmieducarEscolaSerie();
		$lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
		$registro = array_shift($lst_obj);

		if( ! $registro )
		{
			header( "location: educar_reserva_vaga_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}
		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$nm_escola = $det_ref_cod_escola["nome"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}
		if( class_exists( "clsPmieducarSerie" ) )
		{
			$obj_ref_cod_serie = new clsPmieducarSerie( $registro["ref_cod_serie"] );
			$det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
			$nm_serie = $det_ref_cod_serie["nm_serie"];
		}
		else
		{
			$registro["ref_cod_serie"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
		}
		if( class_exists( "clsPmieducarCurso" ) )
		{
			$obj_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_curso = $obj_curso->detalhe();
			$registro["ref_cod_curso"] = $det_curso["nm_curso"];
		}
		else
		{
			$registro["ref_cod_serie"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
		}
	//----------------------------------------------//
		if( class_exists( "clsPmieducarMatricula" ) )
		{
			$obj_matricula = new clsPmieducarMatricula();
			$lst_matricula = $obj_matricula->lista( null,null,$registro["ref_cod_escola"],$registro["ref_cod_serie"],null,null,null,3,null,null,null,null,1 );
			if (is_array($lst_matricula))
			{
				$matriculados = count($lst_matricula);
			}
		}
		else
		{
			$matriculados = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarMatricula\n-->";
		}

		if( class_exists( "clsPmieducarReservaVaga" ) )
		{
			$obj_reserva_vaga = new clsPmieducarReservaVaga();
			$lst_reserva_vaga = $obj_reserva_vaga->lista( null,$registro["ref_cod_escola"],$registro["ref_cod_serie"],null,null,null,null,null,null,null,1 );
			if (is_array($lst_reserva_vaga))
			{
				$reservados = count($lst_reserva_vaga);
			}
		}
		else
		{
			$reservados = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarReservaVaga\n-->";
		}

		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if( $registro["ref_cod_instituicao"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
			}
		}
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if( $nm_escola )
			{
				$this->addDetalhe( array( "Escola", "{$nm_escola}") );
			}
		}
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"]}") );
		}
		if( $nm_serie )
		{
			$this->addDetalhe( array( "S&eacute;rie", "{$nm_serie}") );
		}

		$obj_turmas = new clsPmieducarTurma();
		$lst_turmas = $obj_turmas->lista( null,null,null,$this->ref_cod_serie, $this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1 );

//		echo "<pre>"; print_r($lst_turmas);

		if ( is_array($lst_turmas) )
		{
			$cont = 0;
			$total_vagas = 0;
			$html = "<table width='50%' cellspacing='0' cellpadding='0' border='0'>
					<tr>
						<td bgcolor=#A1B3BD>Nome</td>
						<td bgcolor=#A1B3BD>N&uacute;mero Vagas</td>";
			foreach ( $lst_turmas AS $turmas )
			{
				$total_vagas += $turmas["max_aluno"];
				if ( ($cont % 2) == 0 )
				{
					$class = " formmdtd ";
				}
				else
				{
					$class = " formlttd ";
				}
				$cont++;

				$html .="<tr>
							<td class=$class width='35%'>{$turmas["nm_turma"]}</td>
							<td class=$class width='15%'>{$turmas["max_aluno"]}</td>
						</tr>";
			}
			$html .="</tr></table>";
			$this->addDetalhe( array( "Turma", $html) );

			if( $total_vagas )
			{
				$this->addDetalhe( array( "Total Vagas", "{$total_vagas}") );
			}
			if( $matriculados )
			{
				$this->addDetalhe( array( "Matriculados", "{$matriculados}") );
			}
			if( $reservados )
			{
				$this->addDetalhe( array( "Reservados", "{$reservados}") );
			}
			$vagas_restantes = $total_vagas - ($matriculados + $reservados);
			$this->addDetalhe( array( "Vagas Restantes", "{$vagas_restantes}") );
		}

		if( $obj_permissao->permissao_cadastra( 639, $this->pessoa_logada,7 ) )
		{
			$this->array_botao = array("Reservar Vaga", "Vagas Reservadas");
			$this->array_botao_url = array("educar_reserva_vaga_cad.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}", "educar_reservada_vaga_lst.php");
		}
		$this->url_cancelar = "educar_reserva_vaga_lst.php";
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