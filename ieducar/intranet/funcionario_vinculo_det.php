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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/time.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Vínculo Funcionários" );
		$this->processoAp = "190";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsDetalhe
{
	var $cod_usuario;
	
	function Gerar()
	{
				
		@session_start();
		$this->cod_usuario = $_SESSION['id_pessoa'];
		session_write_close();
		$this->titulo = "Detalhe do Vínculo";
		

		// Pega o codigo do chamado
		$cod_func = @$_GET['cod_func'];
		$db = new clsBanco();
		$db->Consulta( "SELECT nm_vinculo FROM funcionario_vinculo WHERE cod_funcionario_vinculo= '$cod_func' " );
		if( $db->ProximoRegistro() )
		{
			list($nm_vinculo) = $db->Tupla();
			$this->addDetalhe( array("Nome", $nm_vinculo ) );
		}	
		$this->url_novo = "funcionario_vinculo_cad.php";
		$this->url_editar = "funcionario_vinculo_cad.php?cod_funcionario_vinculo={$cod_func}";
		$this->url_cancelar = "funcionario_vinculo_lst.php";
		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""                                  => "Detalhe do v&iacute;nculo"
    ));
    $this->enviaLocalizacao($localizacao->montar());		
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>