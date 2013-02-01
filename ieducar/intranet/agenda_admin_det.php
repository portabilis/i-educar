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

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Agenda" );
		$this->processoAp = "343";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Agendas";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_agenda = @$_GET['cod_agenda'];
		
		$db = new clsBanco();
		$db2 = new clsBanco();
		$db->Consulta( "SELECT cod_agenda, nm_agenda, publica, envia_alerta, ref_ref_cod_pessoa_cad, data_cad, ref_ref_cod_pessoa_own FROM portal.agenda WHERE cod_agenda = '{$cod_agenda}'" );
		if( $db->ProximoRegistro() )
		{
			list( $cod_agenda, $nm_agenda, $publica, $envia_alerta, $pessoa_cad, $data_cad, $pessoa_own ) = $db->Tupla();

			$objPessoa = new clsPessoaFisica();
			list( $nome ) = $objPessoa->queryRapida( $pessoa_cad, "nome" );
			
			$objPessoa_ = new clsPessoaFisica();
			list( $nm_pessoa_own) = $objPessoa_->queryRapida( $pessoa_own, "nome" );

			$this->addDetalhe( array("Código da Agenda", $cod_agenda) );
			$this->addDetalhe( array("Agenda", $nm_agenda) );
			$this->addDetalhe( array("Pública", ($publica==0) ? $publica='Não' : $pubica = 'Sim' ) );
			$this->addDetalhe( array("Envia Alerta", ($envia_alerta==0) ? $envia_alerta='Não' : $envia_alerta= 'Sim' ) );
			$this->addDetalhe( array("Quem Cadastrou", $nome) );
			$this->addDetalhe( array("Data do Cadastro", date("d/m/Y H:m:s", strtotime(substr($data_cad,0,19))) ) );
			$this->addDetalhe( array("Dono da Agenda", $nm_pessoa_own) );
			
			$editores = "";
			if( $nm_pessoa_own )
			{
				$editores .= "<b>$nm_pessoa_own</b><br>";
			}
			
			$edit_array = array();
			$db2->Consulta( "SELECT ref_ref_cod_pessoa_fj FROM agenda_responsavel WHERE ref_cod_agenda = '{$cod_agenda}'" );
			while ( $db2->ProximoRegistro() )
			{
				list( $nome ) = $objPessoa->queryRapida( $db2->Campo( "ref_ref_cod_pessoa_fj" ), "nome" );
				$edit_array[] = $nome;
			}
			
			if( ! count( $edit_array ) )
			{
				if( ! $nm_pessoa_own )
				{
					$editores .= "Nenhum editor cadastrado";
				}
			}
			else 
			{
				asort( $edit_array );
				reset( $edit_array );
				$editores .= implode( "<br>", $edit_array );
			}
			$this->addDetalhe( array("Editores autorizados", $editores ) );
		}
		else 
		{
			$this->addDetalhe( array( "Erro", "Codigo de agenda inválido" ) );
		}
		$this->url_editar = "agenda_admin_cad.php?cod_agenda={$cod_agenda}";
		$this->url_novo = "agenda_admin_cad.php";
		$this->url_cancelar = "agenda_admin_lst.php";

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>