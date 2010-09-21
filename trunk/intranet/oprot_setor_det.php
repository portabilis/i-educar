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
require_once ("include/pessoa/clsPessoaFj.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Setor" );
		$this->processoAp = "375";
	}
}

class indice extends clsDetalhe
{
	var $cod_setor;
	
	function Gerar()
	{
		$this->titulo = "Detalhe do Setor";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_setor = @$_GET['cod_setor'];
		$obj_setor = new clsSetor( $this->cod_setor);
		$detalhe = $obj_setor->detalhe();
		
		if( ! $detalhe )
		{
			$this->addDetalhe( array( "Erro", "Setor Inexistente" ) );
		}
		else 
		{
			$objSetor = new clsSetor( $detalhe["cod_setor"] );
			$parentes = $objSetor->getNiveis( $detalhe["cod_setor"] );
			$strParentes = "";
			$gruda = "";
			for ( $i = 0; $i < count( $parentes ); $i++ )
			{
				$objSetor = new clsSetor( $parentes[$i] );
				$detalheSetor = $objSetor->detalhe();
				$strParentes = " {$detalheSetor["nm_setor"]} - {$detalheSetor["sgl_setor"]}";
				//$gruda = " &gt; ";
				$gruda .= "&nbsp&nbsp&nbsp ";
				if($i == 0)
				{
					$this->addDetalhe( array( "Setor", $strParentes ) );
				}
				else 
				{
					$this->addDetalhe( array( "Setor", "$gruda<img src=\"imagens/nvp_setal.gif\">$strParentes" ) );
				}
			}
			
			$ref_cod_pessoa_cad = $detalhe["ref_cod_pessoa_cad"];
			$obj_pessoa_fj = new clsPessoaFj($ref_cod_pessoa_cad);
			$det = $obj_pessoa_fj->detalhe();
			
			$this->addDetalhe( array( "Responsável pelo cadastro", $det["nome"] ) );
			
			$ativo = $detalhe["ativo"] == 1 ? "Sim" : "Não";
			$this->addDetalhe( array( "Ativo", $ativo ) );
			
			$no_paco = $detalhe["no_paco"] ? "Sim" : "Não";
			$this->addDetalhe( array( "No Paço", $no_paco ) );
			
			if($detalhe["endereco"])
			{
				$this->addDetalhe( array( "Endereço", $detalhe["endereco"] ) );
			}
			
			if ($detalhe["tipo"]) 
			{
				switch ($detalhe["tipo"])
				{
					case "s":
						$this->addDetalhe( array( "Tipo", "Secretaria" ) );
						break;
					case "a":
						$this->addDetalhe( array( "Tipo", "Altarquia" ) );
						break;
					case "f":
						$this->addDetalhe( array( "Tipo", "Fundação" ) );
						break;
				}
			}
			
			if($detalhe["refIdpesResp"])
			{
				$obj_pessoa = new clsPessoa($detalhe["refIdpesResp"]);
				$det_pessoa = $obj_pessoa->detalhe();
				
				$this->addDetalhe( array( "Secretario", $det_pessoa["nome"] ) );
			}
		}
		
		if(!is_null($detalhe["ref_cod_setor"]))
		{
			$this->url_editar = "oprot_setor_cad.php?cod_setor={$this->cod_setor}&setor_atual=$detalhe[ref_cod_setor]";
		}
		else 
		{
			$this->url_editar = "oprot_setor_cad.php?cod_setor={$this->cod_setor}";
		}
		$this->url_novo = "oprot_setor_cad.php";
		$this->url_cancelar = "oprot_setor_lst.php";

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>