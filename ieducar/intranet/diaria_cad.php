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
		$this->SetTitulo( "{$this->_instituicao} Diaria" );
		$this->processoAp = "293";
	}
}

class indice extends clsCadastro
{
	var $cod_diaria,
		$ref_funcionario_cadastro,
		$ref_cod_diaria_grupo,
		$ref_funcionario,
		$conta_corrente,
		$agencia,
		$banco,
		$dotacao_orcamentaria,
		$objetivo,
		$data_partida,
		$data_chegada,
		$hora_partida,
		$hora_chegada,
		$estadual,
		$destino,
		$data_pedido,
		$vl100,
		$vl75,
		$vl50,
		$vl25,
		$ref_cod_setor,
		$num_diaria;
		

	function Inicializar()
	{
		$retorno = "Novo";

		if ( isset( $_GET['cod_diaria'] ) )
		{
			$this->cod_diaria = $_GET['cod_diaria'];
			$db = new clsBanco();
			$db->Consulta( "SELECT ref_funcionario_cadastro, ref_cod_diaria_grupo, ref_funcionario, conta_corrente, agencia, banco,  dotacao_orcamentaria,  objetivo, data_partida, data_chegada, estadual, destino, data_pedido, vl100,  vl75, vl50, vl25, ref_cod_setor, num_diaria FROM pmidrh.diaria WHERE cod_diaria='{$this->cod_diaria}'" );
			if ($db->ProximoRegistro())
			{
				list( $this->ref_funcionario_cadastro, $this->ref_cod_diaria_grupo, $this->ref_funcionario, $this->conta_corrente, $this->agencia, $this->banco, $this->dotacao_orcamentaria, $this->objetivo, $this->data_partida, $this->data_chegada, $this->estadual, $this->destino, $this->data_pedido, $this->vl100, $this->vl75, $this->vl50, $this->vl25, $this->ref_cod_setor, $this->num_diaria ) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}

		if( $retorno == "Editar" )
		{
			$this->url_cancelar = "diaria_det.php?cod_diaria={$this->cod_diaria}";
		}
		else
		{
			$this->url_cancelar = "diaria_lst.php";
		}
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		@session_start();
		$this->pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();

		$this->campoOculto( "pessoaFj", $this->pessoaFj );
		$this->campoOculto( "cod_diaria", $this->cod_diaria );

		if( $this->num_diaria ) 
		{
			$this->campoOculto( "num_diaria", $this->num_diaria );
		}
		
		// Campo listaPesq de Funcionario
		$lista = array();
		if( $this->ref_funcionario )
		{
			$objPessoa = new clsPessoaFJ( $this->ref_funcionario );
			$detalhe = $objPessoa->detalhe();
			if( $detalhe )
			{
				$lista[$detalhe["idpes"]] = $detalhe["nome"];
				$lista[""] = "Pesquise outras pessoas clicando no botao ao lado";
			}
			else
			{
				$lista[""] = "Pesquise a pessoa clicando no botao ao lado";
			}
		}
		else
		{
			$lista[""] = "Pesquise a pessoa clicando no botao ao lado";
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "ref_funcionario", "ref_cod_pessoa_fj", "nome" );
		$this->campoListaPesq( "ref_funcionario", "Funcion&aacute;rio", $lista, $this->ref_funcionario, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );

		$grupo = array();
		$grupo[""] = "Selecione o grupo";
		$db = new clsBanco();
		$db->Consulta( "SELECT cod_diaria_grupo, desc_grupo FROM pmidrh.diaria_grupo ORDER BY cod_diaria_grupo ASC" );
		while ( $db->ProximoRegistro() )
		{
			list( $cod_grupo, $nome ) = $db->Tupla();
			$grupo[$cod_grupo] = $nome;
		}
		$this->campoLista( "ref_cod_diaria_grupo", "Grupo de di&aacute;ria", $grupo, $this->ref_cod_diaria_grupo, "diaria_carrega_valores();" );

		$lst_setores = array( "" => "Selecione" );
		$obj_setor = new clsSetor();
		$lst_setor = $obj_setor->lista( null, null, null, null, null, null, null, null, null, null, 0 );
		if( is_array( $lst_setor ) && count( $lst_setor ) )
		{
			foreach ( $lst_setor AS $linha )
			{
				$lst_setores[$linha["cod_setor"]] = $linha["nm_setor"];
			}
		}
		$this->campoLista( "ref_cod_setor", "Secretaria", $lst_setores, $this->ref_cod_setor, "diaria_carrega_valores();" );

		$this->campoTexto( "conta_corrente", "Conta corrente", $this->conta_corrente, 10, 10 );
		if( ! $this->banco ) $this->banco = "001";
		if( ! $this->agencia ) $this->agencia = "03050";
		$this->campoTexto( "agencia", "Ag&ecirc;ncia", $this->agencia, 10, 10 );
		$this->campoTexto( "banco", "Banco", $this->banco, 10, 10 );
		$this->campoTexto( "dotacao_orcamentaria", "Dota&ccedil;&atilde;o Or&ccedil;ament&aacute;ria", $this->dotacao_orcamentaria, 50, 50 );

		$this->campoMemo( "objetivo", "Objetivo", $this->objetivo, 48, 2 );

		if( $this->data_pedido ) $this->data_pedido = date( "d/m/Y", strtotime( substr( $this->data_pedido, 0, 16 ) ) );
		$this->campoData( "data_pedido", "Data para Empenho", $this->data_pedido, true );

		if( $this->data_partida )
		{
			$this->hora_partida = date( "H:i", strtotime( $this->data_partida ) );
			$this->data_partida = date( "d/m/Y", strtotime( $this->data_partida ) );
		}
		$this->campoData( "data_partida", "Data de Partida", $this->data_partida, false, false, false, "onBlur=\"diaria_carrega_valores();\"" );
		$this->campoHora( "hora_partida", "Hora de Partida", $this->hora_partida, false, false, "onBlur=\"diaria_carrega_valores();\"" );

		if( $this->data_chegada )
		{
			$this->hora_chegada = date( "H:i", strtotime( $this->data_chegada ) );
			$this->data_chegada = date( "d/m/Y", strtotime( $this->data_chegada ) );
		}
		$this->campoData( "data_chegada", "Data de Chegada", $this->data_chegada, false, false, false, "onBlur=\"diaria_carrega_valores();\"" );
		$this->campoHora( "hora_chegada", "Hora de Chegada", $this->hora_chegada, false, false, "onBlur=\"diaria_carrega_valores();\"" );

		if( ! is_numeric( $this->estadual ) )
		{
			$this->estadual = 1;
		}
		$this->campoLista( "estadual", "Estadual", array( "N&atilde;o", "Sim" ), $this->estadual, "diaria_carrega_valores();" );
		$this->campoTexto( "destino", "Destino", $this->destino, 50, 100 );
		$this->campoTexto( "vl100", "Valor 100%", number_format($this->vl100, 2, ',', '.'), 20, 20, false, false, true );
		$this->campoTextoInv( "sug100", " &nbsp; &nbsp; Valor sugerido", "", 10, 10 );
		$this->campoTexto( "vl75", "Valor 75%", number_format($this->vl75, 2, ',', '.'), 20, 20, false, false, true );
		$this->campoTextoInv( "sug75", " &nbsp; &nbsp; Valor sugerido", "", 10, 10 );
		$this->campoTexto( "vl50", "Valor 50%", number_format($this->vl50, 2, ',', '.'), 20, 20, false, false, true );
		$this->campoTextoInv( "sug50", " &nbsp; &nbsp; Valor sugerido", "", 10, 10 );
		$this->campoTexto( "vl25", "Valor 25%", number_format($this->vl25, 2, ',', '.'), 20, 20, false, false, true );
		$this->campoTextoInv( "sug25", " &nbsp; &nbsp; Valor sugerido", "", 10, 10 );
		$this->campoRotulo( "copia", "Copiar", "<a href=\"#bottom\" onclick=\"diaria_copia_valores();\">Clique aqui para copiar os valores sugeridos.</a>", false, "<script type=\"text/javascript\">diaria_carrega_valores();</script>" );
	}

	function Novo()
	{
		@session_start();
		$this->ref_funcionario_cadastro = $_SESSION['id_pessoa'];
		session_write_close();

//		echo "<pre>"; print_r($this);die;
//		echo "funcionario: ".$this->ref_funcionario;die;
		
		$campos = "";
		$values = "";
		$db = new clsBanco();

		if( is_numeric( $this->ref_funcionario_cadastro ) )
		{
			if( is_numeric( $this->ref_cod_diaria_grupo ) )
			{
				if( is_numeric( $this->ref_funcionario ) )
				{
					$this->conta_corrente = idFederal2Int( $this->conta_corrente );
					if( is_numeric( $this->conta_corrente ) )
					{
						$campos .= ", conta_corrente";
						$values .= ", '{$this->conta_corrente}'";
					}

					$this->agencia = idFederal2Int( $this->agencia );
					if( is_numeric( $this->agencia ) )
					{
						$campos .= ", agencia";
						$values .= ", '{$this->agencia}'";
					}

					$this->banco = idFederal2Int( $this->banco );
					if( is_numeric( $this->banco ) )
					{
						$campos .= ", banco";
						$values .= ", '{$this->banco}'";
					}

					if( $this->dotacao_orcamentaria )
					{
						$campos .= ", dotacao_orcamentaria";
						$values .= ", '{$this->dotacao_orcamentaria}'";
					}

					if( $this->objetivo )
					{
						$campos .= ", objetivo";
						$values .= ", '{$this->objetivo}'";
					}

					if( $this->data_chegada )
					{
						$data = explode( "/", $this->data_chegada );
						$this->data_chegada = "{$data[2]}-{$data[1]}-{$data[0]}";

						if( $this->hora_chegada )
						{
							$this->data_chegada .= " $this->hora_chegada";
						}

						$campos .= ", data_chegada";
						$values .= ", '{$this->data_chegada}'";
					}

					if( $this->data_partida )
					{
						$data = explode( "/", $this->data_partida );
						$this->data_partida = "{$data[2]}-{$data[1]}-{$data[0]}";

						if( $this->hora_partida )
						{
							$this->data_partida .= " $this->hora_partida";
						}

						$campos .= ", data_partida";
						$values .= ", '{$this->data_partida}'";
					}

					if( $this->data_pedido )
					{
						$data = explode( "/", $this->data_pedido );
						$this->data_pedido = "{$data[2]}-{$data[1]}-{$data[0]}";
						
						$ano = $data[2];

						$campos .= ", data_pedido";
						$values .= ", '{$this->data_pedido}'";
					}

					/*$campos .= ", data_pedido";
					$values .= ", NOW()";*/

					if( is_numeric( $this->estadual ) && $this->estadual < 2 )
					{
						$campos .= ", estadual";
						$values .= ", '{$this->estadual}'";
					}

					if( $this->destino )
					{
						$campos .= ", destino";
						$values .= ", '{$this->destino}'";
					}

					$this->vl100 = str_replace( ".", "", $this->vl100 );
					$this->vl100 = str_replace( ",", ".", $this->vl100 );
					if( is_numeric( str_replace( ".", "", $this->vl100 ) ) )
					{
						$campos .= ", vl100";
						$values .= ", '{$this->vl100}'";
					}

					$this->vl75 = str_replace( ".", "", $this->vl75 );
					$this->vl75 = str_replace( ",", ".", $this->vl75 );
					if( is_numeric( str_replace( ".", "", $this->vl75 ) ) )
					{
						$campos .= ", vl75";
						$values .= ", '{$this->vl75}'";
					}

					$this->vl50 = str_replace( ".", "", $this->vl50 );
					$this->vl50 = str_replace( ",", ".", $this->vl50 );
					if( is_numeric( str_replace( ".", "", $this->vl50 ) ) )
					{
						$campos .= ", vl50";
						$values .= ", '{$this->vl50}'";
					}

					$this->vl25 = str_replace( ".", "", $this->vl25 );
					$this->vl25 = str_replace( ",", ".", $this->vl25 );
					if( is_numeric( str_replace( ".", "", $this->vl25 ) ) )
					{
						$campos .= ", vl25";
						$values .= ", '{$this->vl25}'";
					}
										if( $this->ref_cod_setor )
					{
						$campos .= ", ref_cod_setor";
						$values .= ", '{$this->ref_cod_setor}'";
					}

					$sql = "SELECT 
								count( cod_diaria )
							FROM
								pmidrh.diaria
							WHERE
								data_pedido >= '{$ano}-01-01 00:00:00'
								AND data_pedido <= '{$ano}-12-31 23:59:59'";
					
					$db->CampoUnico( $sql );
					$resultado = $db->Tupla();
					$num_diaria = $resultado['count'] + 1;
					
					$db->Consulta( "INSERT INTO pmidrh.diaria( ref_funcionario_cadastro, ref_cod_diaria_grupo, ref_funcionario $campos, num_diaria ) VALUES( '{$this->ref_funcionario_cadastro}', '{$this->ref_cod_diaria_grupo}', '{$this->ref_funcionario}' $values, '{$num_diaria}' )" );

					$cod_diaria = $db->InsertId( "pmidrh.diaria_cod_diaria_seq" );

					if( $cod_diaria )
					{
						header( "location: diaria_det.php?cod_diaria={$cod_diaria}" );
					}
					else
					{
						header( "location: diaria_lst.php" );
					}
					die();
					return true;
				}
				else
				{
					$this->mensagem = "Preencha corretamente o campo Funcionario";
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
				if( is_numeric( $this->ref_funcionario ) )
				{
					if( is_numeric( $this->cod_diaria ) )
					{
						$this->conta_corrente = idFederal2Int( $this->conta_corrente );
						if( is_numeric( $this->conta_corrente ) )
						{
							$set .= ", conta_corrente = '{$this->conta_corrente}'";
						}

						$this->agencia = idFederal2Int( $this->agencia );
						if( is_numeric( $this->agencia ) )
						{
							$set .= ", agencia = '{$this->agencia}'";
						}

						$this->banco = idFederal2Int( $this->banco );
						if( is_numeric( $this->banco ) )
						{
							$set .= ", banco = '{$this->banco}'";
						}

						if( $this->dotacao_orcamentaria )
						{
							$set .= ", dotacao_orcamentaria = '{$this->dotacao_orcamentaria}'";
						}

						if( $this->objetivo )
						{
							$set .= ", objetivo = '{$this->objetivo}'";
						}

						if( $this->data_chegada )
						{
							$data = explode( "/", $this->data_chegada );
							$this->data_chegada = "{$data[2]}-{$data[1]}-{$data[0]}";

							if( $this->hora_chegada )
							{
								$this->data_chegada .= " {$this->hora_chegada}:00";
							}

							$set .= ", data_chegada = '{$this->data_chegada}'";
						}

						if( $this->data_partida )
						{
							$data = explode( "/", $this->data_partida );
							
							$ano = $data[2];
							
							$this->data_partida = "{$data[2]}-{$data[1]}-{$data[0]}";

							if( $this->hora_partida )
							{
								$this->data_partida .= " {$this->hora_partida}:00";
							}

							$set .= ", data_partida = '{$this->data_partida}'";
						}

						if( $this->data_pedido )
						{
							$data = explode( "/", $this->data_pedido );
							$this->data_pedido = "{$data[2]}-{$data[1]}-{$data[0]}";

							$set .= ", data_pedido = '{$this->data_pedido}'";
						}

						if( is_numeric( $this->estadual ) && $this->estadual < 2 )
						{
							$set .= ", estadual = '{$this->estadual}'";
						}

						if( $this->destino )
						{
							$set .= ", destino = '{$this->destino}'";
						}

						$this->vl100 = str_replace( ".", "", $this->vl100 );
						$this->vl100 = str_replace( ",", ".", $this->vl100 );
						if( is_numeric( str_replace( ".", "", $this->vl100 ) ) )
						{
							$set .= ", vl100 = '{$this->vl100}'";
						}

						$this->vl75 = str_replace( ".", "", $this->vl75 );
						$this->vl75 = str_replace( ",", ".", $this->vl75 );
						if( is_numeric( str_replace( ".", "", $this->vl75 ) ) )
						{
							$set .= ", vl75 = '{$this->vl75}'";
						}

						$this->vl50 = str_replace( ".", "", $this->vl50 );
						$this->vl50 = str_replace( ",", ".", $this->vl50 );
						if( is_numeric( str_replace( ".", "", $this->vl50 ) ) )
						{
							$set .= ", vl50 = '{$this->vl50}'";
						}

						$this->vl25 = str_replace( ".", "", $this->vl25 );
						$this->vl25 = str_replace( ",", ".", $this->vl25 );
						if( is_numeric( str_replace( ".", "", $this->vl25 ) ) )
						{
							$set .= ", vl25 = '{$this->vl25}'";
						}

						
						if( $this->ref_cod_setor )
						{
							$set .= ", ref_cod_setor= '{$this->ref_cod_setor}'";
						}
					
						$db->Consulta( "UPDATE pmidrh.diaria SET ref_funcionario_cadastro = '{$this->ref_funcionario_cadastro}', ref_cod_diaria_grupo = '{$this->ref_cod_diaria_grupo}', ref_funcionario = '{$this->ref_funcionario}' $set WHERE cod_diaria = '{$this->cod_diaria}'" );
						header( "location: diaria_lst.php" );
						die();
						return true;
					}
					else
					{
						$this->mensagem = "Codigo de DIaria invalido!";
					}
				}
				else
				{
					$this->mensagem = "Preencha corretamente o campo Funcionario";
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
		if( is_numeric( $this->cod_diaria ) )
		{
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM pmidrh.diaria WHERE cod_diaria={$this->cod_diaria}" );
			header( "location: diaria_lst.php" );
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