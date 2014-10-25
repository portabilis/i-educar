<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");

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
			$lista[""] = "Pesquise a pessoa clicando no bot�o ao lado";
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


$pagina = new clsBase();

$pagina->SetTitulo( "{$pagina->_instituicao} Licita&ccedil;&otilde;es!" );
$pagina->processoAp = "137";
	
$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
