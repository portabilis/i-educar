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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Editora" );
		$this->processoAp = "595";
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

	var $cod_acervo_editora;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $nm_editora;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $telefone;
	var $ddd_telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Editora - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_acervo_editora=$_GET["cod_acervo_editora"];

		$tmp_obj = new clsPmieducarAcervoEditora( $this->cod_acervo_editora );
		$registro = $tmp_obj->detalhe();
                
                if( class_exists( "clsPmieducarBiblioteca" ) )
		{
			$obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
			$det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
			$registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$registro["ref_cod_instituicao"] = $det_ref_cod_biblioteca["ref_cod_instituicao"];
				$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
				$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
			}
                }
                
               if( class_exists( "clsPmieducarEscola" ) )
	       {
				$registro["ref_cod_escola"] = $det_ref_cod_biblioteca["ref_cod_escola"];
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
                
                $obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		

		if( ! $registro )
		{
			header( "location: educar_acervo_editora_lst.php" );
			die();
		}

		if( class_exists( "clsTipoLogradouro" ) )
		{
			$obj_ref_idtlog = new clsTipoLogradouro( $registro["ref_idtlog"] );
			$det_ref_idtlog = $obj_ref_idtlog->detalhe();
			$registro["ref_idtlog"] = $det_ref_idtlog["descricao"];
		}
		else
		{
			$registro["ref_idtlog"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsUrbanoTipoLogradouro\n-->";
		}

		if( class_exists( "clsUf" ) )
		{
			$obj_ref_sigla_uf = new clsUf( $registro["ref_sigla_uf"] );
			$det_ref_sigla_uf = $obj_ref_sigla_uf->detalhe();
			$registro["ref_sigla_uf"] = $det_ref_sigla_uf["nome"];
		}
		else
		{
			$registro["ref_sigla_uf"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsUf\n-->";
		}

		if( $registro["nm_editora"] )
		{
			$this->addDetalhe( array( "Editora", "{$registro["nm_editora"]}") );
		}
                
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
                
		if( $registro["cep"] )
		{
			$registro["cep"] = int2CEP($registro["cep"]);
			$this->addDetalhe( array( "CEP", "{$registro["cep"]}") );
		}
		if( $registro["ref_sigla_uf"] )
		{
			$this->addDetalhe( array( "Estado", "{$registro["ref_sigla_uf"]}") );
		}
		if( $registro["cidade"] )
		{
			$this->addDetalhe( array( "Cidade", "{$registro["cidade"]}") );
		}
		if( $registro["bairro"] )
		{
			$this->addDetalhe( array( "Bairro", "{$registro["bairro"]}") );
		}
		if( $registro["ref_idtlog"] )
		{
			$this->addDetalhe( array( "Tipo Logradouro", "{$registro["ref_idtlog"]}") );
		}
		if( $registro["logradouro"] )
		{
			$this->addDetalhe( array( "Logradouro", "{$registro["logradouro"]}") );
		}
		if( $registro["numero"] )
		{
			$this->addDetalhe( array( "N&uacute;mero", "{$registro["numero"]}") );
		}
		if( $registro["ddd_telefone"] )
		{
			$this->addDetalhe( array( "DDD Telefone", "{$registro["ddd_telefone"]}") );
		}
		if( $registro["telefone"] )
		{
			$this->addDetalhe( array( "Telefone", "{$registro["telefone"]}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 595, $this->pessoa_logada, 11 ) )
		{
			$this->url_novo = "educar_acervo_editora_cad.php";
			$this->url_editar = "educar_acervo_editora_cad.php?cod_acervo_editora={$registro["cod_acervo_editora"]}";
		}

		$this->url_cancelar = "educar_acervo_editora_lst.php";
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