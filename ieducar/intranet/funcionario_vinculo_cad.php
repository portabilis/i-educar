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
		$this->SetTitulo( "{$this->_instituicao} Vínculo Funcionários!" );
		$this->processoAp = "190";
	}
}

class indice extends clsCadastro
{
	var $nm_vinculo;
	var $cod_vinculo;

	function Inicializar()
	{
		$retorno = "Novo";
		if($_GET['cod_funcionario_vinculo'])
		{
			$this->cod_vinculo = $_GET['cod_funcionario_vinculo'];
			$db =new clsBanco();
			$db->Consulta( "SELECT nm_vinculo FROM funcionario_vinculo WHERE cod_funcionario_vinculo = $this->cod_vinculo" );
			if($db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->nm_vinculo = $tupla[0];
				$retorno = "Editar";
				$this->fexcluir = true;
			}
		}
		$this->nome_url_cancelar = "Cancelar";
		$this->url_cancelar = "funcionario_vinculo_lst.php";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto("cod_vinculo",$this->cod_vinculo);
		$this->campoTexto("nm_vinculo","Nome",$this->nm_vinculo,30,250,true);
	}

	function Novo() 
	{
		$db = new clsBanco();
		$db->Consulta("INSERT INTO funcionario_vinculo ( nm_vinculo ) VALUES ( '$this->nm_vinculo' )");
		echo "<script>document.location='funcionario_vinculo_lst.php';</script>";
		return true;
	}

	function Editar() 
	{
		$db = new clsBanco();
		$db->Consulta( "UPDATE funcionario_vinculo SET nm_vinculo = '$this->nm_vinculo' WHERE cod_funcionario_vinculo=$this->cod_vinculo" );
		echo "<script>document.location='funcionario_vinculo_lst.php';</script>";
		return true;
	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM funcionario_vinculo WHERE cod_funcionario_vinculo=$this->cod_vinculo" );
		echo "<script>document.location='funcionario_vinculo_lst.php';</script>";
		return true;
	}


}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
