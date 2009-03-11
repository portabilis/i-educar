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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Escola S&eacute;rie" );
		$this->processoAp = "585";
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

	var $ref_cod_escola;
	var $ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $hora_inicial;
	var $hora_final;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Escola S&eacute;rie - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->ref_cod_serie=$_GET["ref_cod_serie"];
		$this->ref_cod_escola=$_GET["ref_cod_escola"];

		$tmp_obj = new clsPmieducarEscolaSerie();
		$lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
		$registro = array_shift($lst_obj);

		if( ! $registro )
		{
			header( "location: educar_escola_serie_lst.php" );
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
		$obj = new clsPmieducarEscolaSerieDisciplina();
		$lst = $obj->lista( $this->ref_cod_serie, $this->ref_cod_escola,null,1 );
		if ($lst)
		{
			$tabela = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
					       </TR>";
			$cont = 0;

			foreach ( $lst AS $valor )
			{
				if ( ($cont % 2) == 0 )
					$color = " bgcolor=#E4E9ED ";
				else
					$color = " bgcolor=#FFFFFF ";

				$obj_disciplina = new clsPmieducarDisciplina( $valor["ref_cod_disciplina"] );
				$obj_disciplina->setOrderby("nm_disciplina ASC");
				$obj_disciplina_det = $obj_disciplina->detalhe();
				$nm_disciplina = $obj_disciplina_det["nm_disciplina"];

				$tabela .= "<TR>
							    <TD {$color} align=left>{$nm_disciplina}</TD>
							</TR>";
				$cont++;
			}
			$tabela .= "</TABLE>";
		}
		if( $tabela )
			$this->addDetalhe( array( "Disciplina", "{$tabela}") );

		if( $obj_permissao->permissao_cadastra( 585, $this->pessoa_logada,7 ) )
		{
			$this->url_novo = "educar_escola_serie_cad.php";
			$this->url_editar = "educar_escola_serie_cad.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}";
		}
		$this->url_cancelar = "educar_escola_serie_lst.php";
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