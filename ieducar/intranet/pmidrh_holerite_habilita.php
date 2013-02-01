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
require_once ("include/clsBancoMS.inc.php");
require_once ("include/funcoes.inc.php");

/*$db=new clsBancoMS();
$db->Consulta("SELECT DateToStr(datnas, 'dd/mm/YYYY') FROM senior.r034fun where numcad=1206101");
while ($db->ProximoRegistro()) {
	list($dt_nasc) = $db->Tupla();
	echo "-->".$dt_nasc;
}
die();*/

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Holerite" );
		$this->processoAp = "480";
	}
}

class indice extends clsCadastro
{
	var $matricula,
		$data_nasc,
		$cpf,
		$senha_intranet;

	function Inicializar()
	{
		$retorno = "Novo";

		//$this->url_cancelar = ($retorno == "Editar") ? "secretarias_det.php?cod={$this->cod}" : "secretarias_lst.php";
		//$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoTexto( "matricula", "Matrícula",  $this->matricula, "16", "12", true );
		$this->campoSenha( "senha_intranet", "Senha",  $this->senha_intranet, true );
		$this->campoData( "data_nasc", "Data Nascimento",  $this->data_nasc, true );
		$this->campoCpf( "cpf", "CPF",  $this->cpf, true );		
		if ($_GET['erro']==1)
		{
			echo "<script>alert('Informações incorretas!');</script>";
		}
	}

	function Novo() 
	{
		$db = new clsBanco();
		
		
		/*
		 * INFORMAÇÕES DO BANCO DE DADOS
		 */
		$banco_matricula = $db->CampoUnico("SELECT matricula FROM portal.funcionario WHERE ref_cod_pessoa_fj='{$_SESSION['id_pessoa']}'");
		
		$banco_senha = $db->CampoUnico("SELECT senha FROM portal.funcionario WHERE ref_cod_pessoa_fj='{$_SESSION['id_pessoa']}'");
		
		$banco_data_nasc = $db->CampoUnico("SELECT data_nasc FROM cadastro.fisica WHERE idpes='{$_SESSION['id_pessoa']}'");
		
		$banco_cpf = $db->CampoUnico("SELECT cpf FROM cadastro.fisica_cpf WHERE idpes='{$_SESSION['id_pessoa']}'");
		
		/*
		 * COMPARA DADOS
		 */
		
		$this->senha_intranet = md5($this->senha_intranet);
		$banco_data_nasc = dataToBrasil($banco_data_nasc);
		$banco_cpf = int2CPF($banco_cpf);
		/*echo "Banco: {$banco_matricula} - {$banco_senha} - {$banco_cpf} - {$banco_data_nasc}<br>";
		echo "User: {$this->matricula} - {$this->senha_intranet} - {$this->cpf} - {$this->data_nasc}<br>";*/
		
		
		if ($banco_matricula == $this->matricula &&
			$banco_senha == $this->senha_intranet &&
			$banco_data_nasc == $this->data_nasc &&
			$banco_cpf == $this->cpf)
		{
			//$banco_data_nasc == $this->data_nasc &&
			$autorizado = true;
		}
		else 
		{
			$autorizado = false;
		}
		
		/*
		 * NEGA OU AUTORIZA VISUALIZAÇÃO
		 */
		
		
		if ($autorizado)
		{
			session_start();
			$_SESSION['autorizado_holerite'] = true;
			$_SESSION['matricula_user'] = $banco_matricula;
			
			header("Location: pmidrh_holerite_lst.php");
			die("era pra ter ido...");
		}
		header("Location: pmidrh_holerite_habilita.php?erro=1");
		
	}

	function Editar() 
	{
		return false;
	}

	function Excluir()
	{
		return false;
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>
