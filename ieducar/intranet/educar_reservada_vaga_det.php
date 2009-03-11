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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Vagas Reservadas" );
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

	var $cod_reserva_vaga;
	var $ref_ref_cod_escola;
	var $ref_ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_aluno;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_escola;
	var $ref_cod_curso;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Vagas Reservadas - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_reserva_vaga=$_GET["cod_reserva_vaga"];

		if ($_GET["desativa"] == true)
		{
			$obj = new clsPmieducarReservaVaga( $this->cod_reserva_vaga,null,null,$this->pessoa_logada,null,null,null,null,0 );
			$excluiu = $obj->excluir();
			if( $excluiu )
			{
				$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_reservada_vaga_lst.php" );
				die();
				return true;
			}
			$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
			return false;
		}

		$obj_reserva_vaga = new clsPmieducarReservaVaga();
		$lst_reserva_vaga = $obj_reserva_vaga->lista($this->cod_reserva_vaga);
		if(is_array($lst_reserva_vaga))
			$registro = array_shift($lst_reserva_vaga);

		if( !$registro )
		{
			header( "location: educar_reservada_vaga_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$det_instituicao = $obj_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_instituicao["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}
		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
			$det_escola = $obj_escola->detalhe();
			$registro["ref_ref_cod_escola"] = $det_escola["nome"];
		}
		else
		{
			$registro["ref_ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}
		if( class_exists( "clsPmieducarSerie" ) )
		{
			$obj_serie = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
			$det_serie = $obj_serie->detalhe();
			$registro["ref_ref_cod_serie"] = $det_serie["nm_serie"];
		}
		else
		{
			$registro["ref_ref_cod_serie"] = "Erro na gera&ccedil;&atilde;o";
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
			$registro["ref_cod_curso"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarCurso\n-->";
		}

		if( class_exists( "clsPmieducarAluno" ) )
		{
			$obj_aluno = new clsPmieducarAluno();
			$lst_aluno = $obj_aluno->lista(	$registro["ref_cod_aluno"],null,null,null,null,null,null,null,null,null,1 );
			if ( is_array($lst_aluno) )
			{
				$det_aluno = array_shift($lst_aluno);
				$nm_aluno = $det_aluno["nome_aluno"];
			}
		}
		else
		{
			$reservados = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarAluno\n-->";
		}

		if( $nm_aluno )
		{
			$this->addDetalhe( array( "Aluno", "{$nm_aluno}") );
		}
		if( $this->cod_reserva_vaga )
		{
			$this->addDetalhe( array( "N&uacute;mero Reserva Vaga", "{$this->cod_reserva_vaga}") );
		}

		$this->addDetalhe( array( "-", "Reserva Pretendida") );

		if( $registro["ref_cod_instituicao"] )
		{
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
		}
		if( $registro["ref_ref_cod_escola"] )
		{
			$this->addDetalhe( array( "Escola", "{$registro["ref_ref_cod_escola"]}") );
		}
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"]}") );
		}
		if( $registro["ref_ref_cod_serie"] )
		{
			$this->addDetalhe( array( "S&eacute;rie", "{$registro["ref_ref_cod_serie"]}") );
		}

		$obj_permissao = new clsPermissoes();
		if( $obj_permissao->permissao_cadastra( 639, $this->pessoa_logada,7 ) )
		{
			$this->array_botao = array("Emiss&atilde;o de Documento de Reserva de Vaga", "Desativar Reserva");
			//$this->array_botao_url = array("educar_relatorio_solicitacao_transferencia.php?cod_reserva_vaga={$this->cod_reserva_vaga}", "educar_reservada_vaga_det.php?cod_reserva_vaga={$this->cod_reserva_vaga}&desativa=true");
			$this->array_botao_url_script = array("showExpansivelImprimir(400, 200,  \"educar_relatorio_solicitacao_transferencia.php?cod_reserva_vaga={$this->cod_reserva_vaga}\",[], \"Relatório de Solicitação de transferência\")","go(\"educar_reservada_vaga_det.php?cod_reserva_vaga={$this->cod_reserva_vaga}&desativa=true\")");
		}
		$this->url_cancelar = "educar_reservada_vaga_lst.php";
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