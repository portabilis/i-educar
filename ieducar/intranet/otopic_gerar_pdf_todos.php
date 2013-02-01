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
require_once ("include/relatorio.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");
require_once ("include/relatorio.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Imprimir" );
		$this->processoAp = "396";
	}
}

class indice extends clsCadastro
{
	function Inicializar()
	{
		@session_start();
		$this->cod_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		
		return $retorno;
	}

	function Gerar()
	{
		if($_SESSION["arr_pessoas"])
		{
			$i = count($_SESSION["arr_pessoas"][0]) == 4 ? 1 : 0;
			
			if($_GET["tipo"] == "end")
			{
				$obj_relatorios = new relatorios("Todas as ocorrências");
				
				foreach ($_SESSION["arr_pessoas"] as $indice=>$valor)
				{
					//pessoa
					if($valor[3] == 1)
					{
						$obj_pessoa = new clsPessoa_($valor[2]);
						$det_pessoa = $obj_pessoa->detalhe();
						if($det_pessoa)
						{
							$obj_relatorios->novalinha(array("Nome", $det_pessoa["nome"]));
							
							if($det_pessoa["tipo"] == "F")
							{
								$objPessoa = new clsPessoaFisica();
								$detalhe = $objPessoa->queryRapida($valor[2], "idpes", "complemento","nome", "cpf", "logradouro", "bairro", "idtlog", "numero", "apartamento","cidade","sigla_uf", "cep", "ddd_1", "fone_1", "ddd_2", "fone_2", "ddd_mov", "fone_mov", "ddd_fax", "fone_fax", "email", "url", "tipo", "sexo", "data_nasc");		
								if($detalhe)
								{
									$obj_relatorios->novalinha(array("CEP", $detalhe["cep"]));	
									
									if($detalhe['logradouro'])
									{
										if($detalhe['numero'])
										{
											$end = " nº {$detalhe['numero']}";
										}
										if($detalhe['apartamento'])
										{
											$end .= " apto {$detalhe['apartamento']}";
										}
										$obj_relatorios->novalinha(array("Endereço", strtolower($detalhe['idtlog']).": {$detalhe['logradouro']} $end") );
										
									}
									
									if($detalhe['complemento'])
									{
										$obj_relatorios->novalinha(array("Complemento", $detalhe['complemento']) );
									}
									
									$obj_relatorios->novalinha(array("Cidade", strtolower($detalhe['cidade'])." ".strtolower($detalhe['sigla_uf'])) );

									//* 20-06-2006
									if($detalhe["bairro"])
										$obj_relatorios->novalinha(array("Bairro:", $detalhe["bairro"]));
									//*
																			
									if($detalhe['data_nasc'])
									{
										
										$obj_relatorios->novalinha(array("Data Nasc", date("d/m/Y",strtotime(substr($detalhe['data_nasc'],0,19)))));
									}
								}
							}
							elseif($det_pessoa["tipo"] == "J")
							{
								$objPessoaJuridica = new clsPessoaJuridica();
								list ($cod_pessoa_fj, $nm_pessoa, $id_federal, $endereco, $cep, $nm_bairro, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $http, $tipo_pessoa, $razao_social, $ins_est, $ins_mun, $cidade, $idtlog) = $objPessoaJuridica->queryRapida($idpes, "idpes","fantasia","cnpj","logradouro","cep","bairro","ddd_1","fone_1","ddd_2","fone_2","ddd_mov","fone_mov","ddd_fax","fone_fax","email","url","tipo","nome","insc_estadual","insc_municipal","cidade", "idtlog");
								$endereco = "$idtlog $endereco";
								
								$obj_relatorios->novalinha( array("CEP", $cep) );
		
								$obj_relatorios->novalinha( array("Endereço", $endereco) );
								
								if($nm_bairro)
								{
									$this->addDetalhe( array("Bairro", $nm_bairro) );
									$obj_relatorios->novalinha( array("Bairro", $nm_bairro) );
								}
								
								$obj_relatorios->novalinha( array("Cidade", $cidade) );
							}	
							
							$obj_relatorios->novalinha(array("", ""));
						}
					}
					else
					{
						
						//pessoa AUXILIAR 
						$obj_pessoa_auxiliar = new clsPessoaAuxiliar($valor[2]);
						$det_pessoa_auxiliar = $obj_pessoa_auxiliar->detalhe();
						if($det_pessoa_auxiliar)
						{
							$obj_relatorios->novalinha(array("Nome:", $valor[1]));
							$obj_relatorios->novalinha(array("CEP:", $det_pessoa_auxiliar["cep"]));
							$obj_relatorios->novalinha(array("Endereço:", "{$det_pessoa_auxiliar["logradouro"]} {$det_pessoa_auxiliar["numero"]}"));
							
							if($det_pessoa_auxiliar["numero_ap"])
							{
								$obj_relatorios->novalinha(array("Apartamento:", $det_pessoa_auxiliar["numero_ap"]));	
							}
							if($det_pessoa_auxiliar["andar"])
							{
								$obj_relatorios->novalinha(array("Andar:", $det_pessoa_auxiliar["andar"]));	
							}
							if($det_pessoa_auxiliar["bloco"])
							{
								$obj_relatorios->novalinha(array("Bloco:", $det_pessoa_auxiliar["bloco"]));	
							}
							if($det_pessoa_auxiliar["letra"])
							{
								$obj_relatorios->novalinha(array("Letra:", $det_pessoa_auxiliar["letra"]));	
							}
							
							$obj_relatorios->novalinha(array("Bairro:", $det_pessoa_auxiliar["bairro"]));
							$obj_relatorios->novalinha(array("Cidade:", "{$det_pessoa_auxiliar["cidade"]} {$det_pessoa_auxiliar["estado"]}"));
							if($det_pessoa_auxiliar['data_nasc'])
							{
								
								$obj_relatorios->novalinha(array("Data Nasc", date("d/m/Y",strtotime(substr($det_pessoa_auxiliar['data_nasc'],0,19)))));
							}
							$obj_relatorios->novalinha(array("", ""));
						}
					}
				}
				
				@session_start();
				unset($_SESSION["arr_pessoas"]);
				@session_write_close();
				$this->campoRotulo("imprimir", "Imprimir", "<a href=".$obj_relatorios->fechaPdf().">Clique aqui para imprimir</a>");
				$this->botao_enviar = false;
				$this->url_cancelar = "otopic_atendido_lst.php";
			}
			elseif ($_GET["tipo"] == "det")
			{
				$obj_relatorios = new relatorios("Todas as ocorrências");
				
				foreach ($_SESSION["arr_pessoas"] as $indice=>$valor)
				{
					//pessoa
					if($valor[2+$i] == 1)
					{
						$obj_pessoa = new clsPessoa_($valor[1+$i]);
						$det_pessoa = $obj_pessoa->detalhe();
						if($det_pessoa)
						{
							$obj_relatorios->novalinha(array("Nome", $det_pessoa["nome"]));
							
							if($det_pessoa["tipo"] == "F")
							{
								$objPessoa = new clsPessoaFisica();
								$detalhe = $objPessoa->queryRapida($valor[1+$i], "idpes", "complemento","nome", "cpf", "logradouro", "idtlog", "numero", "apartamento","cidade","sigla_uf", "cep", "ddd_1", "fone_1", "ddd_2", "fone_2", "ddd_mov", "fone_mov", "ddd_fax", "fone_fax", "email", "url", "tipo", "sexo", "data_nasc");		
								if($detalhe)
								{
									if($detalhe["fone_1"])
									{
										$ddd = $detalhe["ddd_1"] ? "( {$detalhe["ddd_1"]} )" : "";
										$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$detalhe["fone_1"]}") );
									}
									
									if($detalhe["fone_2"])
									{
										$ddd = $detalhe["ddd_2"] ? "( {$detalhe["ddd_2"]} )" : "";
										$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$detalhe["fone_2"]}") );
									}
									
									if($detalhe["fone_mov"])
									{
										$ddd = $detalhe["ddd_mov"] ? "( {$detalhe["ddd_mov"]} )" : "";
										$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$detalhe["fone_mov"]}") );
									}
									
									if($detalhe["fone_fax"])
									{
										$ddd = $detalhe["ddd_fax"] ? "( {$detalhe["ddd_fax"]} )" : "";
										$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$detalhe["fone_fax"]}") );
									}
									

									$obj_pessoa_observacao = new clsPessoaObservacao();
									$lista = $obj_pessoa_observacao->lista(null, $valor[1+$i]);

									if(strlen($lista[0]["obs"]) < 65)
									{
										$obj_relatorios->novalinha(array("Assunto:", $lista[0]["obs"]) );
									}
									else 
									{	
										$assunto = quebra_linhas_pdf($lista[0]["obs"],65);
										$obj_relatorios->novalinha(array("Assunto:", $assunto),0,15*(count(explode("\n",$assunto))+1) );
									} 
								}
							}
							elseif($det_pessoa["tipo"] == "J")
							{
								$objPessoaJuridica = new clsPessoaJuridica();
								list ($cod_pessoa_fj, $nm_pessoa, $id_federal, $endereco, $cep, $nm_bairro, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $http, $tipo_pessoa, $razao_social, $ins_est, $ins_mun, $cidade, $idtlog) = $objPessoaJuridica->queryRapida($idpes, "idpes","fantasia","cnpj","logradouro","cep","bairro","ddd_1","fone_1","ddd_2","fone_2","ddd_mov","fone_mov","ddd_fax","fone_fax","email","url","tipo","nome","insc_estadual","insc_municipal","cidade", "idtlog");

								if($telefone_1)
								{
									$ddd = $ddd_telefone_1 ? "( {$ddd_telefone_1} )" : "";
									$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$telefone_1}") );
								}
								
								if($telefone_2)
								{
									$ddd = $ddd_telefone_2 ? "( {$ddd_telefone_2} )" : "";
									$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$telefone_2}") );
								}
								
								if($telefone_mov)
								{
									$ddd = $ddd_telefone_mov ? "( {$ddd_telefone_mov} )" : "";
									$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$telefone_mov}") );
								}
								
								if($telefone_fax)
								{
									$ddd = $ddd_telefone_fax ? "( {$ddd_telefone_fax} )" : "";
									$obj_relatorios->novalinha(array("Telefone:", "{$ddd}{$telefone_fax}") );
								}
								
								$obj_pessoa_observacao = new clsPessoaObservacao();
								$lista = $obj_pessoa_observacao->lista(null, $valor[1+$i]);
								
								if(strlen($lista[0]["obs"]) < 65)
									{
										$obj_relatorios->novalinha(array("Assunto:", $lista[0]["obs"]) );
									}
									else 
									{
										
										$assunto = quebra_linhas_pdf($lista[0]["obs"],65);
										$obj_relatorios->novalinha(array("Assunto:", $assunto),0,15*(count(explode("\n",$assunto))+1) );
									}				
							}	
							$obj_relatorios->novalinha(array("", ""));
						}
					}
					else
					{
						//pessoa AUXILIAR 
						$obj_pessoa_auxiliar = new clsPessoaAuxiliar($valor[1+$i]);
						$det_pessoa_auxiliar = $obj_pessoa_auxiliar->detalhe();
						if($det_pessoa_auxiliar)
						{
							$obj_relatorios->novalinha(array("Nome:", $valor[0+$i]));

							$obj_auxilar_telefone = new clsPessoaAuxiliarTelefone();
							$lista_tel = $obj_auxilar_telefone->lista($valor[1+$i]);
							if($lista_tel)
							{
								foreach ($lista_tel as $indice2=>$valor2)
								{
									if($valor2["fone"])
									{
										$d = $valor2["ddd"] ? "( {$valor2["ddd"]} )" : "";
										$obj_relatorios->novalinha(array("Telefone:", "{$d}{$valor2["fone"]}"));	
									}
								}
							}
							
							$obj_pessoa_observacao = new clsPessoaObservacao();
							$lista = $obj_pessoa_observacao->lista($valor[1+$i]);
							
							if(strlen($lista[0]["obs"]) < 65)
							{
								$obj_relatorios->novalinha(array("Assunto:", $lista[0]["obs"]) );
							}
							else 
							{
								
								$assunto = quebra_linhas_pdf($lista[0]["obs"],65);
								$obj_relatorios->novalinha(array("Assunto:", $assunto),0,15*(count(explode("\n",$assunto))+1) );
								
						
							}
							
							$obj_relatorios->novalinha(array("", ""));
						}
					} 
				}
				$this->campoRotulo("imprimir", "Imprimir", "<a href=".$obj_relatorios->fechaPdf().">Clique aqui para imprimir</a>");
				$this->botao_enviar = false;
				$this->url_cancelar = "otopic_atendido_lst.php";
			}
		}
		else 
		{
			$this->campoRotulo("erro","Atenção", "Sem dados para impressão");
		}
	}

	function Novo() 
	{
		return false;
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