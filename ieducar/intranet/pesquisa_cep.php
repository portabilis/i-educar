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

require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");
require_once ("include/funcoes.inc.php");

$tipo = 1;


if (!empty($_POST['cep']) || !empty($_POST['cidade']) || !empty($_POST['logradouro']))
{
	$tipo = 3;


	if($_POST['cep'])
	{
		$_POST["cep"] = str_replace( "-", "", $_POST["cep"] );
		$objCepLogBairro = new clsCepLogradouroBairro();
		$listaCepLogBairro = $objCepLogBairro->lista(false,$_POST['cep']);
		if($listaCepLogBairro)
		{
			foreach ($listaCepLogBairro as $juncao) {
				$detalheBairro = $juncao['idbai']->detalhe();
				$nome_bairro = $detalheBairro['nome'];
				$idbai = $detalheBairro['idbai'];
				$detalheMunicipio = $detalheBairro['idmun']->detalhe();
				$nome_cidade = $detalheMunicipio['nome'];
				$detalhe_estado = $detalheMunicipio['sigla_uf']->detalhe();
				$estado = $detalhe_estado['sigla_uf'];
				$detalheCepLogradouro = $juncao['idlog']->detalhe();
				$cep = $detalheCepLogradouro['cep'];
				$detalheLogradouro = $detalheCepLogradouro['idlog']->detalhe();
				$nome_logradouro = $detalheLogradouro['nome'];
				$detalheTipoLog = $detalheLogradouro['idtlog']->detalhe();
				$idtlog = $detalheTipoLog['idtlog'];
				$idlog = $detalheLogradouro['idlog'];
				$resultado[] = array($nome_cidade,$nome_bairro,$idbai,$nome_logradouro,$idlog, $cep, $estado, $idtlog);
			}
		}

	}
	elseif($_POST['cidade'])
	{
		$_POST["cidade"] = strtoupper( limpa_acentos( $_POST['cidade'] ) );
		$resultado = "";
		$objMunicipio = new clsMunicipio();
		$lista = $objMunicipio->lista( $_POST['cidade'] );
		if($lista)
		{
			foreach ($lista as $cidade)
			{
				$nome_cidade = $cidade['nome'];
				$detalhe_estado = $cidade['sigla_uf']->detalhe();
				$estado = $detalhe_estado['sigla_uf'];
				$objBairro = new clsBairro();
				$listaBairro = $objBairro->lista($cidade['idmun'],false);

				if($listaBairro)
				{
					foreach ($listaBairro as $bairro)
					{

						$nome_bairro = $bairro['nome'];
						$idbai = $bairro['idbai'];

						$objCepLogBairro = new clsCepLogradouroBairro();
						$listaCepLogBairro = $objCepLogBairro->lista(false,false,$bairro['idbai'],false,false);
						if($listaCepLogBairro)
						{
							foreach ($listaCepLogBairro as $id=>$juncao)
							{
								$detalheCepLogradouro = $juncao['idlog']->detalhe();
								$detalheLogradouro = $detalheCepLogradouro['idlog']->detalhe();
								$detalheTipoLog = $detalheLogradouro['idtlog']->detalhe();
								$idtlog = $detalheTipoLog['idtlog'];


								$nome_logradouro = $detalheLogradouro['nome'];
								$idlog = $detalheLogradouro['idlog'];
								$cep = $detalheCepLogradouro['cep'];

								if($_POST['logradouro'])
								{
									if(substr_count(strtolower($nome_logradouro),strtolower($_POST['logradouro'])) > 0)
									{
										$resultado[] = array($nome_cidade,$nome_bairro,$idbai,$nome_logradouro,$idlog, $cep,$estado,$idtlog);
									}
								}
								else
								{
									$resultado[] = array($nome_cidade,$nome_bairro,$idbai,$nome_logradouro,$idlog, $cep, $estado,$idtlog);
								}

							} //foreach ($listaCepLogBairro as $id=>$juncao)
						} //if(count($listaCepLogBairro))
					} //foreach ($listaBairro as $bairro)
				} //if($listaBairro)
			} //foreach ($lista as $cidade)
		} //if($lista)
	}elseif($_POST['logradouro'])
	{
		$obj_logradouro = new clsLogradouro();
		$lista_logradouros =$obj_logradouro->lista(false,$_POST['logradouro']);
		if($lista_logradouros)
		{

			foreach ($lista_logradouros as $logradouro)
			{
				$objCepLogBairro = new clsCepLogradouroBairro();
				$listaCepLogBairro = $objCepLogBairro->lista($logradouro['idlog'],false,"",false,false);
				if($listaCepLogBairro)
				{
					foreach ($listaCepLogBairro as $id=>$juncao)
					{

						$detalheCepLogradouro = $juncao['idlog']->detalhe();
						$detalheLogradouro = $detalheCepLogradouro['idlog']->detalhe();
						$detalheTipoLog = $detalheLogradouro['idtlog']->detalhe();
						$idtlog = $detalheTipoLog['idtlog'];

						$nome_logradouro = $detalheLogradouro['nome'];
						$idlog = $detalheLogradouro['idlog'];
						$cep = $detalheCepLogradouro['cep'];

						$detalhe_bairro = $juncao['idbai']->detalhe();
						$nome_bairro = $detalhe_bairro['nome'];
						$idbai = $detalhe_bairro["idbai"];
						$detalhe_cidade = $detalhe_bairro['idmun']->detalhe();
						$nome_cidade = $detalhe_cidade['nome'];

						$resultado[] = array($nome_cidade,$nome_bairro,$idbai,$nome_logradouro,$idlog, $cep, $estado,$idtlog);
					}
				}
			} //foreach ($listaCepLogBairro as $id=>$juncao)
		}
	}

	if ($resultado)
	{
		$tipo = 3;
		$total = count($resultado);
		$classe = $md ? 'formmdtd' : 'formlttd';




		//$db->Consulta( $fields.$sql );

		$bor  = '<table style="font-size: 10px; font-family: verdana, arial;" width="100%" cellspacing="3" cellpadding="1">';
		$bor .= "<tr bgcolor='#BABABA'><td><b>[selecionar]</b></a><td><b>Cidade</b><td><b>Bairro</b><td><b>Logradouro</b><td><b>CEP</b><td></tr>";
		$bor .= "<tr bgcolor='#BABABA'><td colspan=5><i>Total de Registros: {$total}</tr>";

		if($resultado)
		{
			foreach ($resultado AS $logradouro)
			{
				$cor_fundo = $cor_fundo == "#DADADA" ? "#FFFFFF" : "#DADADA";

				$cidade = $logradouro[0];
				$bairro = $logradouro[1];
				$idbai = $logradouro[2];
				$log    = $logradouro[3];
				$idlog = $logradouro[4];
				$cep    = $logradouro[5];
				$estado = $logradouro[6];
				$idtlog = $logradouro[7];

				$obj_logradouro = new clsLogradouro( $idlog );
				$det = $obj_logradouro->detalhe();
				$objMun = new clsMunicipio( $det["idmun"] );
				$detMun = $objMun->detalhe();
				$uf = $detMun["sigla_uf"];
				$detUF = $uf->detalhe();
				$estado = $detUF["sigla_uf"];

				$bor .= "<tr bgcolor='{$cor_fundo}'><td><a href='#' onclick='javascript:enviar(\"{$_POST['campo']}\", \"{$cep}\", \"{$idbai}\", \"{$idlog}\", \"{$cidade}\", \"{$bairro}\", \"{$log}\", \"{$estado}\", \"{$idtlog}\")'>[selecionar]</a><td>{$cidade}<td>{$bairro}<td>{$log}<td>{$cep}<td></tr>";
			}
		}

		$bor .= "<tr bgcolor='{$cor_fundo}'><td><a href='#' onclick='javascript:enviar(\"{$_POST['campo']}\", \"\", \"\", \"\", \"\", \"\", \"\", \"\")'>[selecionar]</a><td colspan=5>Adicionar Novo Endereço</td></tr>";

		$bor .= '</table>';

		$bor = $funcao . $bor;
	}
	else
	{
		$tipo = 2;
	}

}

if ($tipo != 3)
{
	$bor = '<table style="font-size: 11px; font-family: verdana, arial;" width="100%" cellspacing="8" cellpadding="5"><form action="" method="POST" name="form">';

	if ($tipo == 2)
	{
		$bor .= '<tr bgcolor="#BABABA"><td colspan="2"><b style="color: red">SEM RESULTADOS</b>';
		$bor .= "<tr bgcolor='{$cor_fundo}'><td><a href='#' onclick='javascript:enviar(\"{$_POST['campo']}\", \"\", \"\", \"\", \"\", \"\", \"\", \"\")'>[selecionar]</a><td colspan=5>Adicionar Novo Endereço</td></tr>";

	}

	$bor .='
		<tr bgcolor="#BABABA"><td colspan="3"><b>FILTROS DE BUSCA</b>
		<tr><td>CEP:<td><input type="text" name="cep" style="font-size: 11px; font-family: verdana, arial;" size="50">
		<tr><td>Logradouro:<td><input type="text" name="logradouro" style="font-size: 11px; font-family: verdana, arial;">
		<tr><td>Cidade:<td><input type="text" name="cidade" style="font-size: 11px; font-family: verdana, arial;">
		<tr bgcolor="#BABABA"><td colspan=2 align="center"><input type="submit" value="Pesquisar" style="font-size: 11px; font-family: verdana, arial;"> <input type="reset" value="Limpar" style="font-size: 11px; font-family: verdana, arial;">
		</form></table>';
}

?>
<script type='text/javascript' src='scripts/padrao.js'></script>
<script type='text/javascript' src='scripts/novo.js'></script>
<script>
/*function enviar( campo, cep, idbai, idlog, cidade, bairro, logradouro, estado, idtlog )
{
	if( cep )
	{
		desabilita = true;

	}else
	{
		desabilita = false;
	}
	setValores(window.opener.document, desabilita, cep, idlog, logradouro, cidade, estado, "", idbai, "", "", "", "");
	window.close();
}*/
</script>
<script>

function enviar( campo, cep, idbai, idlog, cidade, bairro, logradouro, estado, idtlog )
{
	if( cep )
	{
		window.opener.document.getElementById('cep_').value = cep.substr(0,5)+'-'+cep.substr(5);
		window.opener.document.getElementById('cep_').disabled = true;
		window.opener.document.getElementById('cidade').value = cidade;
		window.opener.document.getElementById('cidade').disabled = true;
		window.opener.document.getElementById('bairro').value = bairro;
		window.opener.document.getElementById('bairro').disabled = true;
		window.opener.document.getElementById('logradouro').value = logradouro;
		window.opener.document.getElementById('logradouro').disabled = true;
		window.opener.document.getElementById('numero').disabled = false;
		if(window.opener.document.getElementById('letra'))
		{
			window.opener.document.getElementById('letra').disabled = false;
		}
		if(window.opener.document.getElementById('complemento'))
		{
			window.opener.document.getElementById('complemento').disabled = false;
		}
		window.opener.document.getElementById('cep').value = cep.substr(0,5)+'-'+cep.substr(5);
		window.opener.document.getElementById('cidade').value = cidade;
		window.opener.document.getElementById('bairro').value = bairro;
		window.opener.document.getElementById('logradouro').value = logradouro;
		window.opener.document.getElementById('sigla_uf').value = estado;
		window.opener.document.getElementById('idlog').value = idlog;
		window.opener.document.getElementById('idbai').value = idbai;
		opt_estado = 'sigla_uf_'+estado;
		selecionado = window.opener.document.getElementById(opt_estado);
		window.opener.document.getElementById('sigla_uf').disabled = true;
		opt_idtlog = 'idtlog_'+idtlog;
		window.opener.document.getElementById(opt_idtlog).selected = true;
		window.opener.document.getElementById('idtlog').disabled = true;


	}else
	{
		window.opener.document.getElementById('cep').value = '';
		window.opener.document.getElementById('idlog').value = '';
		window.opener.document.getElementById('idbai').value = '';
		window.opener.document.getElementById('cep_').disabled = false;
		window.opener.document.getElementById('cep_').value = '';
		window.opener.document.getElementById('cidade').disabled = false;
		window.opener.document.getElementById('cidade').value = '';
		window.opener.document.getElementById('bairro').disabled = false;
		window.opener.document.getElementById('bairro').value = '';
		window.opener.document.getElementById('sigla_uf').disabled = false;
		window.opener.document.getElementById('sigla_uf').value = '';
		window.opener.document.getElementById('logradouro').disabled = false
		window.opener.document.getElementById('logradouro').value = ''
		window.opener.document.getElementById('numero').disabled = false;
		window.opener.document.getElementById('numero').value = '';
		if(window.opener.document.getElementById('letra'))
		{
			window.opener.document.getElementById('letra').disabled = false;
			window.opener.document.getElementById('letra').value = '';
		}
		if(window.opener.document.getElementById('complemento'))
		{
			window.opener.document.getElementById('complemento').disabled = false;
			window.opener.document.getElementById('complemento').value = '';
		}
		window.opener.document.getElementById('idtlog').disabled = false;
		window.opener.document.getElementById('idtlog').value = '';
	}

	window.close();
}

</script>

<html>
	<head>
		<title>Pesquisa de CEP</title>
	</head>
	<body>
		<?=$bor?>
	</body>
</html>