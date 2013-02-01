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
		$this->SetTitulo( "{$this->_instituicao} Diaria Valores" );
		$this->processoAp = "295";
	}
}

class indice extends clsCadastro
{
	var $cod_diaria_valores, 
		$ref_funcionario_cadastro,
		$ref_cod_diaria_grupo,
		$estadual,
		$p100, 
		$p75,
		$p50,
		$p25,
		$data_vigencia;

	function Inicializar()
	{
		$retorno = "Novo";
		
		if ( isset( $_GET['cod_diaria_valores'] ) )
		{
			$this->cod_diaria_valores = $_GET['cod_diaria_valores'];
			$db = new clsBanco();
			$db->Consulta( "SELECT cod_diaria_valores, ref_funcionario_cadastro, ref_cod_diaria_grupo, estadual, p100, p75, p50, p25, data_vigencia FROM pmidrh.diaria_valores WHERE cod_diaria_valores='{$this->cod_diaria_valores}'" );
			if ($db->ProximoRegistro())
			{
				list( $this->cod_diaria_valores, $this->ref_funcionario_cadastro, $this->ref_cod_diaria_grupo, $this->estadual, $this->p100, $this->p75, $this->p50, $this->p25, $this->data_vigencia ) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ( $retorno == "Editar" ) ? "diaria_valores_det.php?cod_diaria_valores={$this->cod_diaria_valores}" : "diaria_valores_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		@session_start();
		$this->pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->campoOculto( "pessoaFj", $this->pessoaFj );
		$this->campoOculto( "cod_diaria_valores", $this->cod_diaria_valores );
		
		$grupo = array();
		$grupo[""] = "Selecione o grupo";
		$db = new clsBanco();
		$db->Consulta( "SELECT cod_diaria_grupo, desc_grupo FROM pmidrh.diaria_grupo ORDER BY desc_grupo ASC" );
		while ( $db->ProximoRegistro() ) 
		{
			list( $cod_grupo, $nome ) = $db->Tupla();
			$grupo[$cod_grupo] = $nome;
		}
		$this->campoLista( "ref_cod_diaria_grupo", "Grupo de di&aacute;ria", $grupo, $this->ref_cod_diaria_grupo );
		
		if( ! is_numeric( $this->estadual ) )
		{
			$this->estadual = 1;
		}
		$this->campoLista( "estadual", "Estadual", array( "N&atilde;o", "Sim" ), $this->estadual );
		
		if( $this->data_vigencia ) $this->data_vigencia = date( "d/m/Y", strtotime( $this->data_vigencia ) );
		$this->campoData( "data_vigencia", "Data de Vig&ecirc;ncia", $this->data_vigencia );
		
		$this->campoTexto( "p100", "Valor 100%", $this->p100, 20, 20 );
		$this->campoTexto( "p75", "Valor 75%", $this->p75, 20, 20 );
		$this->campoTexto( "p50", "Valor 50%", $this->p50, 20, 20 );
		$this->campoTexto( "p25", "Valor 25%", $this->p25, 20, 20 );
	}

	function Novo() 
	{
		@session_start();
		$this->ref_funcionario_cadastro = $_SESSION['id_pessoa'];
		session_write_close();
		
		$campos = "";
		$values = "";
		$db = new clsBanco();
		
		if( is_numeric( $this->ref_funcionario_cadastro ) )
		{
			if( is_numeric( $this->ref_cod_diaria_grupo ) )
			{
				if( is_numeric( $this->estadual ) && $this->estadual < 2 )
				{
					if( $this->data_vigencia )
					{
						$data = explode( "/", $this->data_vigencia );
						$this->data_vigencia = "{$data[2]}-{$data[1]}-{$data[0]}";
					
						$this->p100 = str_replace( ".", "", $this->p100 );
						$this->p100 = str_replace( ",", ".", $this->p100 );
						if( is_numeric( str_replace( ".", "", $this->p100 ) ) )
						{
							$campos .= ", p100";
							$values .= ", '{$this->p100}'";
						}
						
						$this->p75 = str_replace( ".", "", $this->p75 );
						$this->p75 = str_replace( ",", ".", $this->p75 );
						if( is_numeric( str_replace( ".", "", $this->p75 ) ) )
						{
							$campos .= ", p75";
							$values .= ", '{$this->p75}'";
						}
						
						$this->p50 = str_replace( ".", "", $this->p50 );
						$this->p50 = str_replace( ",", ".", $this->p50 );
						if( is_numeric( str_replace( ".", "", $this->p50 ) ) )
						{
							$campos .= ", p50";
							$values .= ", '{$this->p50}'";
						}
						
						$this->p25 = str_replace( ".", "", $this->p25 );
						$this->p25 = str_replace( ",", ".", $this->p25 );
						if( is_numeric( str_replace( ".", "", $this->p25 ) ) )
						{
							$campos .= ", p25";
							$values .= ", '{$this->p25}'";
						}
						
						$db->Consulta( "INSERT INTO pmidrh.diaria_valores( ref_funcionario_cadastro, ref_cod_diaria_grupo, estadual, data_vigencia $campos) VALUES( '{$this->ref_funcionario_cadastro}', '{$this->ref_cod_diaria_grupo}', '{$this->estadual}', '{$this->data_vigencia}' $values)" );
						header( "location: diaria_valores_lst.php" );
						die();
						return true;
					}
					else 
					{
						$this->mensagem = "Preencha corretamente o campo Data Vigencia";
					}
				}
				else 
				{
					$this->mensagem = "Preencha corretamente o campo Estadual";
				}
			}
			else 
			{
				$this->mensagem = "Preencha corretamente o campo Grupo de Diaria";
			}
		}
		else 
		{
			$this->mensagem = "Logue-se novamente para realizar esta operacao";
		}
		return false;
	}

	function Editar() 
	{
		@session_start();
		$this->ref_funcionario_cadastro = $_SESSION['id_pessoa'];
		session_write_close();
		
		$set = "";
		$db = new clsBanco();
		
		if( is_numeric( $this->ref_funcionario_cadastro ) )
		{
			if( is_numeric( $this->ref_cod_diaria_grupo ) )
			{
				if( is_numeric( $this->estadual ) && $this->estadual < 2 )
				{
					if( $this->data_vigencia )
					{
						if(is_numeric( $this->cod_diaria_valores ) )
						{
							$data = explode( "/", $this->data_vigencia );
							$this->data_vigencia = "{$data[2]}-{$data[1]}-{$data[0]}";
							
							$campos .= ", data_vigencia";
							$values .= ", '{$this->data_vigencia}'";
	
							$this->p100 = str_replace( ".", "", $this->p100 );
							$this->p100 = str_replace( ",", ".", $this->p100 );
							if( is_numeric( str_replace( ".", "", $this->p100 ) ) )
							{
								$campos .= ", p100 = '{$this->p100}'";
							}
	
							$this->p75 = str_replace( ".", "", $this->p75 );
							$this->p75 = str_replace( ",", ".", $this->p75 );
							if( is_numeric( str_replace( ".", "", $this->p75 ) ) )
							{
								$campos .= ", p75 = '{$this->p75}'";
							}
	
							$this->p50 = str_replace( ".", "", $this->p50 );
							$this->p50 = str_replace( ",", ".", $this->p50 );
							if( is_numeric( str_replace( ".", "", $this->p50 ) ) )
							{
								$campos .= ", p50 = '{$this->p50}'";
							}
	
							$this->p25 = str_replace( ".", "", $this->p25 );
							$this->p25 = str_replace( ",", ".", $this->p25 );
							if( is_numeric( str_replace( ".", "", $this->p25 ) ) )
							{
								$campos .= ", p25 = '{$this->p25}'";
							}
	
							$db->Consulta( "UPDATE pmidrh.diaria_valores SET ref_funcionario_cadastro = '{$this->ref_funcionario_cadastro}', ref_cod_diaria_grupo = '{$this->ref_cod_diaria_grupo}', estadual = '{$this->estadual}', data_vigencia='{$this->data_vigencia}' $set WHERE cod_diaria_valores = '{$this->cod_diaria_valores}'" );
							header( "location: diaria_valores_lst.php" );
							die();
							return true;
						}
						else 
						{
							$this->mensagem = "Codigo de DIaria-Valor invalido!";
						}
					}
					else 
					{
						$this->mensagem = "Preencha corretamente o campo Data de Vigencia";
					}
				}
				else 
				{
					$this->mensagem = "Preencha corretamente o campo Estadual";
				}
			}
			else 
			{
				$this->mensagem = "Preencha corretamente o campo Grupo de Diaria";
			}
		}
		else 
		{
			$this->mensagem = "Logue-se novamente pra realizar esta operacao";
		}
		return true;
	}

	function Excluir()
	{
		if( is_numeric( $this->cod_diaria_valores ) )
		{
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM pmidrh.diaria_valores WHERE cod_diaria_valores={$this->cod_diaria_valores}" );
			header( "location: diaria_valores_lst.php" );
			die();
			return true;
		}
		$this->mensagem = "Codigo de DIaria invalido!";
		return false;
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>