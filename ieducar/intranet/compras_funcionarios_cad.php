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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Licita&ccedil;&otilde;es!" );
		$this->processoAp = "137";
	}
}

class indice extends clsCadastro
{
	var $cod_pessoa_fj;
	var $nm_pessoa;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		
		if (@$_GET['cod_pessoa_fj'])
		{
			$this->fexcluir = true;
			$this->cod_pessoa_fj = @$_GET['cod_pessoa_fj'];
			
			$objPessoa = new clsPessoaFisica();
			
			$db = new clsBanco();
			list( $this->nome_pessoa ) = $objPessoa->queryRapida( $this->cod_pessoa_fj, "nome" );
			$retorno = "Editar";
		}
		if( isset( $_POST["cod_pessoa_fj"] ) )
		{
			$this->cod_pessoa_fj = $_POST["cod_pessoa_fj"];
			$this->nm_pessoa = $_POST["nm_pessoa"];
		}

		$this->url_cancelar = "compras_funcionarios_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$lista = array();
		if( $this->nome_pessoa )
		{
			$lista[$this->cod_pessoa_fj] = $this->nome_pessoa;
		}
		else 
		{
			$lista[""] = "Pesquise a pessoa clicando no botão ao lado";
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "ref_ref_cod_pessoa_fj", "ref_cod_pessoa_fj", "nome" );
		$this->campoListaPesq( "ref_ref_cod_pessoa_fj", "Funcionario", $lista, $this->ref_ref_cod_pessoa_fj, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
	}

	function Novo() 
	{
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO compras_funcionarios (ref_ref_cod_pessoa_fj) VALUES ('{$this->ref_ref_cod_pessoa_fj}')" );
		echo "<script>document.location.href='compras_funcionarios_lst.php'</script>";
		return true;
	}

	function Editar() 
	{
		return false;
	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM compras_funcionarios WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}'" );
		echo "<script>document.location.href='compras_funcionarios_lst.php'</script>";
		return false;
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
