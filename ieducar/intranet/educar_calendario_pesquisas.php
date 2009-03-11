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

	 	$obj_permissoes = new clsPermissoes();
	 	$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
	 	if($nivel_usuario <=4 && !empty($nivel_usuario)){
		$retorno .= '<tr>
						<td height="24" colspan="2" class="formdktd">
						<span class="form">
						<b>Filtros de busca</b>
						</span>
						</td>
						</tr>';

	 		$retorno .= '<form action="" method="post" id="formcadastro" name="formcadastro">';
			if ($obrigatorio)
			{
				$instituicao_obrigatorio = $escola_obrigatorio = true;
			}
			else
			{
				$instituicao_obrigatorio = isset($instituicao_obrigatorio) ? $instituicao_obrigatorio : $obrigatorio;
				$escola_obrigatorio = isset($escola_obrigatorio) ? $escola_obrigatorio : $obrigatorio;
			}

			if ($desabilitado)
			{
				$instituicao_desabilitado = $escola_desabilitado = true;
			}
			else
			{
				$instituicao_desabilitado = isset($instituicao_desabilitado) ? $instituicao_desabilitado : $desabilitado;
				$escola_desabilitado = isset($escola_desabilitado) ? $escola_desabilitado : $desabilitado;
			}

		 	$obj_permissoes = new clsPermissoes();
		 	$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

			if( $nivel_usuario == 1 )
			{
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$opcoes = array( "" => "Selecione" );
					$obj_instituicao = new clsPmieducarInstituicao();
					$obj_instituicao->setCamposLista("cod_instituicao, nm_instituicao");
					$obj_instituicao->setOrderby("nm_instituicao ASC");
					$lista = $obj_instituicao->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,1);
					if ( is_array( $lista ) && count( $lista ) )
					{
						foreach ( $lista as $registro )
						{
							$opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
						}
					}
				}
				else
				{
					echo "<!--\nErro\nClasse clsPmieducarInstituicao n&atilde;o encontrada\n-->";
					$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
				}
				if ($get_escola)
				{
					$retorno .= '<tr id="tr_status">
								<td valign="top" class="formlttd">
								<span class="form">Institui&ccedil;&atilde;o</span>
								<span class="campo_obrigatorio">*</span>
								<br/>
								<sub style="vertical-align: top;"/>
								</td>';
					$retorno .= '<td valign="top" class="formlttd"><span class="form">';
					$retorno .=  "<select onchange=\"habilitaCampos('ref_cod_instituicao');\" class='geral' name='ref_cod_instituicao' id='ref_cod_instituicao'>";

					reset( $opcoes );
					while (list( $chave, $texto ) = each($opcoes ))
					{
						$retorno .=  "<option id=\"ref_cod_instituicao_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

						if( $chave==$this->ref_cod_instituicao)
						{
							$retorno .= " selected";
						}
						$retorno .=  ">$texto</option>";
					}
					$retorno .=  "</select>";
					$retorno .= '</span>
									</td>
									</tr>';
					//$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"getDuplo();",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
				}
			}
			if ( $nivel_usuario == 2 ) {
				if ( $get_instituicao )
				{
					$obj_per 			  	   = new clsPermissoes();
					$this->ref_cod_instituicao = $obj_per->getInstituicao( $this->pessoa_logada );
					$retorno .= "<input type='hidden' id='red_cod_instituicao' value='{$this->ref_cod_instituicao}'>";
				//	$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
				}
			}
			elseif ( $nivel_usuario != 1 )
			{
			 	$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
				$det_usuario = $obj_usuario->detalhe();
				$this->ref_cod_instituicao = $det_usuario["ref_cod_instituicao"];
				if ($nivel_usuario == 4 || $nivel_usuario == 8)
				{
					$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
					$det_usuario = $obj_usuario->detalhe();
					$this->ref_cod_escola = $det_usuario["ref_cod_escola"];

				}
			}

			if ($nivel_usuario == 1 || $nivel_usuario == 2)
			{
				if ($get_escola)
				{
					if( class_exists( "clsPmieducarEscola" ) )
					{
						$opcoes_escola = array( "" => "Selecione" );
						$todas_escolas = "escola = new Array();\n";
						$obj_escola = new clsPmieducarEscola();
						$lista = $obj_escola->lista(null,null,null,null,null,null,null,null,null,null,1);
						if ( is_array( $lista ) && count( $lista ) )
						{
							foreach ( $lista as $registro )
							{
								$todas_escolas .= "escola[escola.length] = new Array( {$registro["cod_escola"]}, '{$registro['nome']}', {$registro["ref_cod_instituicao"]} );\n";
							}
						}
						echo "<script>{$todas_escolas}</script>";
					}
					else
					{
						echo "<!--\nErro\nClasse clsPmieducarEscola n&atilde;o encontrada\n-->";
						$opcoes_escola = array( "" => "Erro na gera&ccedil;&atilde;o" );
					}
					if ($this->ref_cod_instituicao)
					{
						if( class_exists( "clsPmieducarEscola" ) )
						{
							$opcoes_escola = array( "" => "Selecione" );
							$obj_escola = new clsPmieducarEscola();
							$lista = $obj_escola->lista(null,null,null,$this->ref_cod_instituicao,null,null,null,null,null,null,1);
							if ( is_array( $lista ) && count( $lista ) )
							{
								foreach ( $lista as $registro )
								{
									$opcoes_escola["{$registro["cod_escola"]}"] = "{$registro['nome']}";
								}
							}
						}
						else
						{
							echo "<!--\nErro\nClasse clsPmieducarEscola n&atilde;o encontrada\n-->";
							$opcoes_escola = array( "" => "Erro na gera&ccedil;&atilde;o" );
						}
					}
					if ($get_escola)
					{
						$retorno .= '<tr id="tr_escola">
									<td valign="top" class="formmdtd">
									<span class="form">Escola</span>
									<span class="campo_obrigatorio">*</span>
									<br/>
									<sub style="vertical-align: top;"/>
									</td>';
						$retorno .= '<td valign="top" class="formmdtd"><span class="form">';

						$disabled = !$this->ref_cod_escola && $nivel_usuario == 1?  "disabled='true' " : "" ;
						$retorno .=  " <select class='geral' name='ref_cod_escola' {$disabled} id='ref_cod_escola'>";

						reset( $opcoes_escola );
						while (list( $chave, $texto ) = each($opcoes_escola ))
						{
							$retorno .=  "<option id=\"ref_cod_escola_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

							if( $chave==$this->ref_cod_escola)
							{
								$retorno .= " selected";
							}
							$retorno .=  ">$texto</option>";
						}
						$retorno .=  "</select>";
						$retorno .= '</span>
										</td>
										</tr>';

						//$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao,"getDuplo();",null,null,null,$instituicao_desabilitado,$instituicao_obrigatorio);
					}
					//$this->campoLista( "ref_cod_escola", "Escola", $opcoes_escola, $this->ref_cod_escola,null,null,null,null,$escola_desabilitado,$escola_obrigatorio );
				}
			}

			if (isset($get_cabecalho))
			{
				if ($nivel_usuario == 1 || $nivel_usuario == 2)
					${$get_cabecalho}[] = "Escola";
				if ($nivel_usuario == 1)
					${$get_cabecalho}[] = "Institui&ccedil;&atilde;o";
			}

			$validacao = "";
			if($nivel_usuario== 1){
				$validacao = 'if(!document.getElementById("ref_cod_instituicao").value){
						alert("Por favor, selecione uma instituicao");
						return false;
						}
						if(!document.getElementById("ref_cod_escola").value){
						alert("Por favor, selecione uma escola");
						return false;
						} ';
			}elseif($nivel_usuario== 2 ){
				$validacao = '
						if(!document.getElementById("ref_cod_escola").value){
						alert("Por favor, selecione uma escola");
						return false;
						} ';
			}
						$retorno .= '<tr id="tr_escola">
									<td valign="top" class="formldtd">
									<span class="form">Ano</span>
									<span class="campo_obrigatorio">*</span>
									<br/>
									<sub style="vertical-align: top;"/>
									</td>';
						$retorno .= '<td valign="top" class="formldtd"><span class="form">';
						$retorno .=  " <select class='geral' name='ano' id='ano'>";
			$lim = 5;
		for($a = date('Y') ; $a < date('Y') + $lim ; $a++ ){

			$retorno .=  "<option value=\"".$a."\"";

			if($a == $_POST['ano'])
			{
				$retorno .= " selected";
			}
			$retorno .=  ">$a</option>";
		}
		$retorno .=  "</select>";
		$retorno .= '</span>
						</td>
						</tr>';
			$retorno .= '</form>';
			$retorno .= "<tr>
	<td colspan='2' class='formdktd'/>
	</tr>
	<tr>
	<td align='center' colspan='2'>
	<script language='javascript'>
	function acao() {
	{$validacao}

	document.formcadastro.submit();
	 }
	</script>
	<input type='button' id='botao_busca' value='busca' onclick='javascript:acao();' class='botaolistagem'/>
	</td>
	</tr><tr><td>&nbsp;</td></tr>";
	?>

	<?
	if ( $nivel_usuario == 1 || $nivel_usuario == 2 )
	{
	?>
	<script>
	//caso seja preciso executar uma funcao no onchange da instituicao adicionar uma funcao as seguintes variaveis no arquivo
	//que precisar , assim, toda vez que for chamada a funcao serao executadas estas funcoes
	var before_getEscola = function(){}
	var after_getEscola  = function(){}

	function getEscola()
	{
		before_getEscola();

		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		if ( document.getElementById('ref_cod_escola') )
			var campoEscola = document.getElementById('ref_cod_escola');
		if ( document.getElementById('ref_ref_cod_escola') )
			var campoEscola = document.getElementById('ref_ref_cod_escola');

		campoEscola.length = 1;
		campoEscola.options[0] = new Option( 'Selecione uma escola', '', false, false );
		for (var j = 0; j < escola.length; j++)
		{
			if (escola[j][2] == campoInstituicao)
			{
				campoEscola.options[campoEscola.options.length] = new Option( escola[j][1], escola[j][0],false,false);
			}
		}
		if ( campoEscola.length == 1 && campoInstituicao != '' ) {
			campoEscola.options[0] = new Option( 'A instituiï¿½ï¿½o nï¿½o possui nenhuma escola', '', false, false );
		}

		after_getEscola();
	}

	function habilitaCampos(campo)
	{
		var campo_instituicao = document.getElementById("ref_cod_instituicao");
		var campo_escola = document.getElementById("ref_cod_escola");

		if(  campo == "")
		{
			campo_instituicao.disabled = true;
			campo_escola.disabled = true;
		}
		else if( campo == "ref_cod_instituicao" )
		{
			campo_escola.disabled = false;
			getEscola();
		}

	}
	</script>
	<? }

}//nivel usuario <=3
if($nivel_usuario == 4){ ?>

<?}?>