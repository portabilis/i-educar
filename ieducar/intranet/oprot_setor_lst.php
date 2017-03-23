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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Protocolo - Setores" );
		$this->processoAp = "375";
	}
}

class miolo1 extends clsListagem
{
	var $nivel0;
	var $nivel1;
	var $nivel2;
	var $nivel3;
	var $nivel4;
	var $nm_setor;
	var $sgl_setor;
	
	function Gerar()
	{
		@session_start();
		$obj_setor = new clsSetor();
		$this->nome = "form1";
		$this->funcAcaoNome = $this->nome;
		$total = 0;
		
		$id_pesssoa = $_SESSION['id_pessoa'];
		
		$this->titulo = "Setores";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet", false );

		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

		/*
			Filtro
		*/
		
		foreach ( $_GET AS $key => $value )
		{
			$this->$key = $value;
		}
		
		
		$this->nm_setor = $_GET["nm_setor"] ? $_GET["nm_setor"] : null;
		$this->sgl_setor = $_GET["sgl_setor"] ? $_GET["sgl_setor"] : null;
		
		include( "include/form_setor.inc.php" );
		
		$this->campoTexto("nm_setor", "Nome do Setor", $this->nm_setor, 30, 255);
		$this->campoTexto("sgl_setor", "Sigla do Setor", $this->sgl_setor, 30, 255);
		/*
			Fim Filtro
		*/
		
		$this->addCabecalhos( array( "Setor" ) );
		
		if($this->nm_setor || $this->sgl_setor)
		{
			$lista = $obj_setor->lista(null, nul, null, $this->nm_setor, $this->sgl_setor, null, null, null, null, 1, null, null, null, "nm_setor");
			
			if($lista)
			{
				foreach ($lista as $key=>$valor)
				{
					$niveis = $obj_setor->getNiveis($valor["cod_setor"]);
					$str = "";
					for($i=0; $i<count($niveis); $i++)
					{
						$obj = new clsSetor($niveis[$i]);
						$det = $obj->detalhe();
						if($i == 0)
						{
							if($det["sgl_setor"] == $this->sgl_setor)
							{
								$str .= "<b>$det[sgl_setor]</b>";
								$cod_setor = $det["cod_setor"];
							}
							else 
							{
								$str .= "$det[sgl_setor]";
							}
						}
						else 
						{
							if($det["sgl_setor"] == $this->sgl_setor)
							{
								$str .= " > <b>$det[sgl_setor]</b>";
								$cod_setor = $det["cod_setor"];
							}
							else 
							{
								$str .= " > $det[sgl_setor]";
							}
						}
					}
					$this->addLinhas(array("<a href=oprot_setor_det.php?cod_setor=$cod_setor>$str</a>"));
					$total++;
				}
			}
		}
		else
		{

			if( $this->setor_0 )
			{
				$objSetores = new clsSetor( $this->setor_0 );
				$listaSetores0[] = $objSetores->detalhe();
			}
			else 
			{
				$objSetores = new clsSetor();
				$listaSetores0 = $objSetores->lista(null, null, null, null, null, null, null, null, null, 1, 0);
			}
	
			if($listaSetores0)
			{
				foreach ($listaSetores0 as $key0=>$valor0)
				{
					$this->addLinhas(array("<a href=oprot_setor_det.php?cod_setor={$valor0["cod_setor"]}>$valor0[sgl_setor]</a>"));
					$total++;
					
					if( $this->setor_1 )
					{
						$objSetores = new clsSetor( $this->setor_1 );
						$listaSetores1[] = $objSetores->detalhe();
					}
					else 
					{
						$objSetores = new clsSetor();
						$listaSetores1 = $objSetores->lista($valor0["cod_setor"], null, null, null, null, null, null, null, null, 1, 1);
					}
					
					if($listaSetores1)
					{
						foreach ($listaSetores1 as $key1=>$valor1)
						{
							$a = "<font color=#9EA3A9>$valor0[sgl_setor] ></font> $valor1[sgl_setor]";
							$this->addLinhas(array("<a href=oprot_setor_det.php?cod_setor=$valor1[cod_setor]>$a</a></font>"));
							$total++;
							
							if( $this->setor_2 )
							{
								$objSetores = new clsSetor( $this->setor_2 );
								$listaSetores2[] = $objSetores->detalhe();
							}
							else 
							{
								$objSetores = new clsSetor();
								$listaSetores2 = $objSetores->lista($valor1["cod_setor"], null, null, null, null, null, null, null, null, 1, 2);
							}
							
							if($listaSetores2)
							{
								foreach($listaSetores2 as $key2=>$valor2)
								{
									$a = "<font color=#9EA3A9>$valor0[sgl_setor] > $valor1[sgl_setor] ></font> $valor2[sgl_setor]";
									$this->addLinhas(array("<a href=oprot_setor_det.php?cod_setor=$valor2[cod_setor]>$a</a></font>"));
									$total++;
									
									if( $this->setor_3 )
									{
										$objSetores = new clsSetor( $this->setor_3 );
										$listaSetores3[] = $objSetores->detalhe();
									}
									else 
									{
										$objSetores = new clsSetor();
										$listaSetores3 = $objSetores->lista($valor2["cod_setor"], null, null, null, null, null, null, null, null, 1, 3);
									}
									
									if($listaSetores3)
									{
										foreach($listaSetores3 as $key3=>$valor3)
										{
											$a = "<font color=#9EA3A9>$valor0[sgl_setor] > $valor1[sgl_setor] > $valor2[sgl_setor] > </font>$valor3[sgl_setor] ";
											$this->addLinhas(array("<a href=oprot_setor_det.php?cod_setor=$valor3[cod_setor]>$a</a></font>"));
											$total++;
											
											if( $this->setor_4 )
											{
												$objSetores = new clsSetor( $this->setor_4 );
												$listaSetores4[] = $objSetores->detalhe();
											}
											else 
											{
												$objSetores = new clsSetor();
												$listaSetores4 = $objSetores->lista($valor3["cod_setor"], null, null, null, null, null, null, null, null, 1, 4);
											}
											
											if($listaSetores4)
											{
												foreach($listaSetores4 as $key4=>$valor4)
												{
													$a = "<font color=#9EA3A9>$valor0[sgl_setor] > $valor1[sgl_setor] > $valor2[sgl_setor] > $valor3[sgl_setor] ></font> $valor4[sgl_setor]";
													$this->addLinhas(array("<a href=oprot_setor_det.php?cod_setor=$valor4[cod_setor]>$a</a></font>"));
													$total++;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		
		$this->acao = "go(\"oprot_setor_cad.php\")";
		$this->nome_acao = "Novo";
		$this->largura = "100%";
		@session_write_close();
	}
	
}


$pagina = new clsIndex();

$miolo = new miolo1();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
<script>
function setFiltro()
{
	alert("filtro");
}
</script>

