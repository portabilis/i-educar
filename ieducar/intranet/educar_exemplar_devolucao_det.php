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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar Devolu&ccedil;&atilde;o" );
		$this->processoAp = "628";
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

	var $cod_emprestimo;
	var $ref_usuario_devolucao;
	var $ref_usuario_cad;
	var $ref_cod_cliente;
	var $ref_cod_exemplar;
	var $data_retirada;
	var $data_devolucao;
	var $valor_multa;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
			unset($_SESSION['reload']);
		session_write_close();

		$this->titulo = "Exemplar Devolu&ccedil;&atilde;o - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_emprestimo=$_GET["cod_emprestimo"];

		if(!$this->cod_emprestimo)
			header("Location: educar_exemplar_devolucao_lst.php");

		$obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
		$lista = $obj_exemplar_emprestimo->lista($this->cod_emprestimo);
		if( is_array( $lista ) && count( $lista ) )
		{
			$registro = array_shift($lista);

			if( ! $registro )
			{
				header( "location: educar_exemplar_devolucao_lst.php" );
				die();
			}

			if( class_exists( "clsPmieducarBiblioteca" ) )
			{
				$obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
				$det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
				$registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
			}
			else
			{
				$registro["ref_cod_biblioteca"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarBiblioteca\n-->";
			}
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
				$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
			}
			if( class_exists( "clsPmieducarEscola" ) )
			{
				$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
				$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
				$idpes = $det_ref_cod_escola["ref_idpes"];
				if ($idpes)
				{
					$obj_escola = new clsPessoaJuridica( $idpes );
					$obj_escola_det = $obj_escola->detalhe();
					$registro["ref_cod_escola"] = $obj_escola_det["fantasia"];
				}
				else
				{
					$obj_escola = new clsPmieducarEscolaComplemento( $registro["ref_cod_escola"] );
					$obj_escola_det = $obj_escola->detalhe();
					$registro["ref_cod_escola"] = $obj_escola_det["nm_escola"];
				}
			}

			if( class_exists( "clsPmieducarExemplar" ) )
			{
				$obj_ref_cod_exemplar = new clsPmieducarExemplar( $registro["ref_cod_exemplar"] );
				$det_ref_cod_exemplar = $obj_ref_cod_exemplar->detalhe();

				if ( class_exists( "clsPmieducarAcervo" ) )
				{
					$acervo = $det_ref_cod_exemplar["ref_cod_acervo"];
					$obj_acervo = new clsPmieducarAcervo($acervo);
					$det_acervo = $obj_acervo->detalhe();
					$titulo_exemplar = $det_acervo["titulo"];
				}
			}
			else
			{
				$registro["ref_cod_exemplar"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarExemplar\n-->";
			}

			if( class_exists( "clsPmieducarCliente" ) )
			{
				$obj_cliente = new clsPmieducarCliente( $registro["ref_cod_cliente"] );
				$det_cliente = $obj_cliente->detalhe();
				$ref_idpes = $det_cliente["ref_idpes"];
				$obj_pessoa = new clsPessoa_($ref_idpes);
				$det_pessoa = $obj_pessoa->detalhe();
				$registro["ref_cod_cliente"] = $det_pessoa["nome"];
			}
			else
			{
				$registro["ref_cod_cliente"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarCliente\n-->";
			}
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
			if( $registro["ref_cod_escola"] )
			{
				$this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
			}
		}
		if( $registro["ref_cod_biblioteca"] )
		{
				$this->addDetalhe( array( "Biblioteca", "{$registro["ref_cod_biblioteca"]}") );
		}
		if( $registro["ref_cod_cliente"] )
		{
			$this->addDetalhe( array( "Cliente", "{$registro["ref_cod_cliente"]}") );
		}
		if( $titulo_exemplar )
		{
			$this->addDetalhe( array( "Obra", "{$titulo_exemplar}") );
		}

  	$this->addDetalhe( array( "Código exemplar", "{$registro["ref_cod_exemplar"]}") );
    $this->addDetalhe( array( "Tombo", "{$det_ref_cod_exemplar["tombo"]}") );

		if( $registro["data_retirada"] )
		{
			$this->addDetalhe( array( "Data Retirada", dataFromPgToBr( $registro["data_retirada"], "d/m/Y" ) ) );
		}
		if( $registro["valor_multa"] )
		{
			$this->addDetalhe( array( "Valor Multa", "{$registro["valor_multa"]}") );
		}

		if( $obj_permissoes->permissao_cadastra( 628, $this->pessoa_logada, 11 ) )
		{
			$this->caption_novo = "Devolu&ccedil;&atilde;o";
			$this->url_novo = "educar_exemplar_devolucao_cad.php?cod_emprestimo={$registro["cod_emprestimo"]}";
		}

		$this->url_cancelar = "educar_exemplar_devolucao_lst.php";
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
