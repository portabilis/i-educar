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
require_once( "include/urbano/geral.inc.php" );
require_once( "include/public/clsPublicBairro.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Cep Logradouro" );
		$this->processoAp = "758";
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
	
	var $cep;
	var $idlog;
	var $nroini;
	var $nrofin;
	var $idpes_rev;
	var $data_rev;
	var $origem_gravacao;
	var $idpes_cad;
	var $data_cad;
	var $operacao;
	var $idsis_rev;
	var $idsis_cad;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Cep Logradouro - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->idlog=$_GET["idlog"];

		
		$obj_cep_logradouro = new clsUrbanoCepLogradouro();
		$lst_cep_logradouro = $obj_cep_logradouro->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idlog);
		
		if( ! $lst_cep_logradouro )
		{
			header( "location: urbano_cep_logradouro_lst.php" );
			die();
		}
		else 
		{
			$registro = $lst_cep_logradouro[0];
		}
		
		
		if( $registro["nm_pais"] )
		{
			$this->addDetalhe( array( "Pais", "{$registro["nm_pais"]}") );
		}
		if( $registro["nm_estado"] )
		{
			$this->addDetalhe( array( "Estado", "{$registro["nm_estado"]}") );
		}
		if( $registro["nm_municipio"] )
		{
			$this->addDetalhe( array( "Município", "{$registro["nm_municipio"]}") );
		}
		if( $registro["nm_logradouro"] )
		{
			$this->addDetalhe( array( "Logradouro", "{$registro["nm_logradouro"]}") );
		}
		
		$obj_cep_log_bairro = new clsUrbanoCepLogradouroBairro();
		$lst_cep_log_bairro = $obj_cep_log_bairro->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idlog );
		if( $lst_cep_log_bairro )
		{
			$tab_endereco = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>CEP</B></TD>
					           <TD bgcolor=#A1B3BD><B>Bairro</B></TD>
					       </TR>";
			$cont = 0;
			foreach ( $lst_cep_log_bairro AS $endereco )
			{
				if ( ($cont % 2) == 0 )
				{
					$color = " bgcolor=#E4E9ED ";
				}
				else
				{
					$color = " bgcolor=#FFFFFF ";
				}
				
				$obj_bairro = new clsPublicBairro( null, null, $endereco['idbai'] );
				$det_bairro = $obj_bairro->detalhe();
				
				$endereco['cep'] = int2CEP($endereco['cep']);
				
				$tab_endereco .= "<TR>
									<TD {$color} align=center>{$endereco['cep']}</TD>
									<TD {$color} align=center>{$det_bairro['nome']}</TD>
								</TR>";
				$cont++;
			}
			$tab_endereco .= "</TABLE>";
		}
		if( $tab_endereco )
		{
			$this->addDetalhe( array( "Tabela de CEP-Bairro", "{$tab_endereco}") );
		}


		$this->url_novo = "urbano_cep_logradouro_cad.php";
		$this->url_editar = "urbano_cep_logradouro_cad.php?cep={$registro["cep"]}&idlog={$registro["idlog"]}";

		$this->url_cancelar = "urbano_cep_logradouro_lst.php";
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