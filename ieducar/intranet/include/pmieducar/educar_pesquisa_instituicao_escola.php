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
?>
<?  	/*
		 	caso o campo seja de busca criar uma variavel
		 	  ***  $obrigatorio = false;  *** antes de dar o include do arquivo

		 	//- Adicionado por Adriano Erik Weiguert Nagasava
		 	$escola_obrigatorio = true //- Indica se o campo escola vai ser obrigatório ou não

		 	$obrigatorio = true;
			include("include/pmieducar/educar_pesquisa_instituicao_escola.php");


		*/

		//** 4 - Escola 2 - institucional 1 - poli-institucional
		$obj_permissao = new clsPermissoes();
	 	$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

	 	$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
		$obj_usuario->setCamposLista("ref_cod_instituicao,ref_cod_escola");
		$det_obj_usuario = $obj_usuario->detalhe();

		$instituicao_usuario = $det_obj_usuario["ref_cod_instituicao"];

		if($nivel_usuario == 1){

				$opcoes_instituicao = array( "" => "Selecione" );

				$objTemp = new clsPmieducarInstituicao();
				$objTemp->setCamposLista("cod_instituicao,nm_instituicao");


				$lista_instituicao23 = $objTemp->lista();
				if ( is_array( $lista_instituicao23 ) && count( $lista_instituicao23 ) )
				{
					foreach ( $lista_instituicao23 as $registro )
					{
						$opcoes_instituicao["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
					}
				}

		/*		if(isset($_GET["ref_cod_instituicao"]) &&  !empty($_GET["ref_cod_instituicao"]) && is_array($opcoes_instituicao) && array_key_exists($_GET["ref_cod_instituicao"],$opcoes_instituicao) )
				{
					$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];
				}
				else
				{
					$this->ref_cod_instituicao = null;
				}*/

				//**** javascript  Array dinamico das instituicoes - escolas
				$obj_instituicao = new clsPmieducarInstituicao();
				$lista_instituicao23 = $obj_instituicao->lista();

				$instituicoes = "";
				if($lista_instituicao23){
					foreach ($lista_instituicao23 as $instituicao)
					{
						$obj_escola = new clsPmieducarEscola();
						//$obj_escola->setCamposLista("cod_escola,ref_idpes");
						$lista_escola23 = $obj_escola->lista(null,null,null,$instituicao["cod_instituicao"],null,null,null,null,null,null,1);

						$escolas = " instituicao['_{$instituicao["cod_instituicao"]}'] = new Array();\n";

						if($lista_escola23)
						{
							//$escolas = "instituicao['_{$instituicao["cod_instituicao"]}'] = new Array({$obj_escola->_total});\n";

							foreach ($lista_escola23 as $escola) {

						/*		if($escola['ref_idpes'])
								{
									$obj_juridica = new clsJuridica($escola['ref_idpes']);
									$det_juridica = $obj_juridica->detalhe();
									$escola['nm_escola'] = $det_juridica['fantasia'];

								}else
								{
									$obj_escola_complemento = new clsPmieducarEscolaComplemento($escolas['cod_escola']);
									$obj_escola_complemento->setCamposLista("nm_escola");
									$det_escola_complemento = $obj_escola_complemento->detalhe();
									$escola['nm_escola'] = $det_escola_complemento['nm_escola'];
								}
							*/
								$escolas .= " instituicao['_{$instituicao["cod_instituicao"]}'][instituicao['_{$instituicao["cod_instituicao"]}'].length] = new Array({$escola["cod_escola"]},'{$escola["nome"]}');\n";
							}

						}
						$instituicoes .="{$escolas}";
					}

					echo $script = "<script> var numero_intituicoes = {$obj_instituicao->_total} \n var instituicao = new Array(); \n {$instituicoes}</script>\n";
				}
				//**
				echo "<!-- {$this->ref_cod_instituicao} -->";
				$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes_instituicao, $this->ref_cod_instituicao,"EscolaInstituicao();",null,null,null,null,$obrigatorio);
		}

		if($nivel_usuario == 1 || $nivel_usuario == 2) {
			//$nivel_usuario = $nivel_usuario == 1 ? null : $nivel_usuario;

			$selecione = $nivel_usuario == 2 ? "Selecione uma escola" : "Selecione uma escola";
			$opcoes = array( "" => $selecione );
			if( class_exists( "clsPmieducarEscola" ) )
			{
				$objTemp = new clsPmieducarEscola();
				if((!empty($this->ref_cod_instituicao) && $nivel_usuario == 1) || $nivel_usuario == 2)
				{
					if($nivel_usuario == 2)
						$this->ref_cod_instituicao = $instituicao_usuario;

					$lista_escola23 = $objTemp->lista(null,null,null,$this->ref_cod_instituicao,null,null,nul,null,null,null,1);

					if ( is_array( $lista_escola23 ) && count( $lista_escola23 ) )
					{
						foreach ( $lista_escola23 as $registro )
						{
					/*		if($escola['ref_idpes'])
							{
								$obj_juridica = new clsJuridica($escola['ref_idpes']);
								$det_juridica = $obj_juridica->detalhe();
								$escola['nm_escola'] = $det_juridica['fantasia'];

							}else
							{
								$obj_escola_complemento = new clsPmieducarEscolaComplemento($escolas['cod_escola']);
								$obj_escola_complemento->setCamposLista("nm_escola");
								$det_escola_complemento = $obj_escola_complemento->detalhe();
								$escola['nm_escola'] = $det_escola_complemento['nm_escola'];
							}
							*/
							$opcoes["{$registro['cod_escola']}"] = "{$registro['nome']}";
						}
					}
				}
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarEscola nao encontrada\n-->";
				$opcoes = array( "" => "Erro na geracao" );
			}

	/*		if(isset($_GET["ref_cod_escola"]) &&  !empty($_GET["ref_cod_escola"]) && is_array($opcoes) && array_key_exists($_GET["ref_cod_escola"],$opcoes) )
			{
				$this->ref_cod_escola = $_GET["ref_cod_escola"];
				$escola_in = null;
			}
			else
			{
				$this->ref_cod_escola = null;
				if(is_array($key_escola))
					$escola_in = implode("," , $key_escola);
			}		*/

//--- Modificado por Adriano Erik Weiguert Nagasava ---
			$aux = $obrigatorio;
			if ( isset( $escola_obrigatorio ) ) {
				if ( $escola_obrigatorio )
					$aux = true;
				else
					$aux = false;
			}
			$this->campoLista( "ref_cod_escola", "Escola", $opcoes, $this->ref_cod_escola,null,null,null,null,null,$aux );
		}

if($nivel_usuario == 1){
?>
<script>
//caso seja preciso executar uma funcao no onchange da instituicao adicionar uma funcao as seguintes variaveis no arquivo
//que precisar , assim, toda vez que for chamada a funcao serao executadas estas funcoes
var before = function(){}
var after  = function(){}

function EscolaInstituicao()
{
	before();

	var codInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoEscola = document.getElementById('ref_cod_escola');

	campoEscola.length = 1;
	if(!codInstituicao){
		campoEscola.options[0].text = "Selecione uma escola";
		return;
	}

	campoEscola.length = 1;

	try{
		var tamanho = eval("instituicao['_" + codInstituicao + "'].length");

		for(var ct = 0 ; ct < tamanho ; ct++){
			campoEscola.options[ct + 1] = new Option( eval("instituicao['_" + codInstituicao + "'][" + ct + "][1]" ),eval("instituicao['_" + codInstituicao + "'][" + ct + "][0]"),false,false);

		}





		if(tamanho == 0){
			campoEscola.options[0].text = "Instituição sem escola";
		}
		else{
			campoEscola.options[0].text = "Selecione uma escola";
		}



	}catch(e){
		alert(e.message);
	}

	after();
}

</script>
<? }
echo ""; ?>