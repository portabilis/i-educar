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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - S&eacute;rie" );
		$this->processoAp = "583";
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

	var $cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_curso;
	var $nm_serie;
	var $etapa_curso;
	var $concluinte;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $intervalo;
	var $media_especial;

	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "S&eacute;rie - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_serie=$_GET["cod_serie"];

		$tmp_obj = new clsPmieducarSerie( $this->cod_serie );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_serie_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarCurso" ) )
		{
			$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];

			if (class_exists("clsPmieducarInstituicao"))
			{
				$registro["ref_cod_instituicao"] = $det_ref_cod_curso["ref_cod_instituicao"];
				$obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
				$obj_instituicao_det = $obj_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $obj_instituicao_det["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
				echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
			}
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
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
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"]}") );
		}
		if( $registro["nm_serie"] )
		{
			$this->addDetalhe( array( "S&eacute;rie", "{$registro["nm_serie"]}") );
		}
		if( $registro["etapa_curso"] )
		{
			$this->addDetalhe( array( "Etapa Curso", "{$registro["etapa_curso"]}") );
		}
		if( $registro["concluinte"] )
		{
			if ($registro["concluinte"] == 1) {
				$registro["concluinte"] = 'n&atilde;o';
			}
			else if ($registro["concluinte"] == 2) {
				$registro["concluinte"] = 'sim';
			}
			$this->addDetalhe( array( "Concluinte", "{$registro["concluinte"]}") );
		}
		if( $registro["carga_horaria"] )
		{
			$this->addDetalhe( array( "Carga Hor&aacute;ria", "{$registro["carga_horaria"]}") );
		}
		if( $registro["intervalo"] )
		{
			$this->addDetalhe( array( "Intervalo", "{$registro["intervalo"]}") );
		}

		$this->addDetalhe( array( "Média Especial", (dbBool($registro['media_especial']) == true ? 'Sim' : 'N&atilde;o') ) );
		$this->addDetalhe( array( "Última Nota Define Situação", (dbBool($registro['ultima_nota_define']) == true ? 'Sim' : 'N&atilde;o') ) );

		$obj = new clsPmieducarDisciplinaSerie();
		$lst = $obj->lista( null, $this->cod_serie,1 );
		if ($lst) {
			$tabela = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
					       </TR>";
			$cont = 0;

			foreach ( $lst AS $valor ) {
				if ( ($cont % 2) == 0 ) {
					$color = " bgcolor=#E4E9ED ";
				}
				else {
					$color = " bgcolor=#FFFFFF ";
				}
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
		if( $nm_disciplina )
		{
			$this->addDetalhe( array( "Disciplina", "{$tabela}") );
		}

		if( $obj_permissoes->permissao_cadastra( 583, $this->pessoa_logada,3 ) ) {
			$this->url_novo = "educar_serie_cad.php";
			$this->url_editar = "educar_serie_cad.php?cod_serie={$registro["cod_serie"]}";
		}
		$this->url_cancelar = "educar_serie_lst.php";
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