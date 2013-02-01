<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * Ficha com os dados do aluno.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Aluno
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

?>
<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="pt">
	<head>

	   	<title> <!-- #&TITULO&# --> </title>
		<link rel=stylesheet type='text/css' href='styles/styles.css' />
		<link rel=stylesheet type='text/css' href='styles/novo.css' />
		<link rel=stylesheet type='text/css' href='styles/menu.css' />
		<!-- #&ESTILO&# -->

		<script type='text/javascript' src='scripts/padrao.js'></script>
		<script type='text/javascript' src='scripts/novo.js'></script>
		<script type='text/javascript' src='scripts/dom.js'></script>
		<script type='text/javascript' src='scripts/menu.js'></script>

		<!-- #&SCRIPT&# -->

		<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<!-- #&REFRESH&# -->

		<meta name='Author' content='Prefeitura de Itajaí' />
		<meta name='Description' content='Portal da Prefeitura de Itajaí' />
		<meta name='Keywords' content='portal, prefeitura, itajaí, serviço, cidadão' />

		<link rel="icon" href="imagens/logo_itajai.ico" type="image/x-icon">
	</head>
	<body onload="parent.EscondeDiv('LoadImprimir');">
<?php
		require_once ("include/clsBase.inc.php");
		require_once ("include/clsCadastro.inc.php");
		require_once ("include/clsBanco.inc.php");
		require_once( "include/pmieducar/geral.inc.php" );
		require_once ("include/clsPDF.inc.php");


		@session_start();
		$pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		if($obj_permissoes->nivel_acesso($pessoa_logada) > 7)
			header("location: index.php");

		$cod_aluno = $_GET['ref_cod_aluno'];

		$obj = new clsPmieducarAluno( $cod_aluno );
		$registro  = $obj->detalhe();
		if ($registro)
		{
			foreach( $registro AS $campo => $val )
			{	// passa todos os valores obtidos no registro para atributos do objeto
				$$campo = $val;
			}

			$obj_pessoa = new clsPessoaFj($ref_idpes);
			$det_pessoa = $obj_pessoa->detalhe();
			$nome  = $det_pessoa["nome"];
			$email =  $det_pessoa["email"];

			if ($det_pessoa["ddd_1"] && $det_pessoa["fone_1"])
			{
				$telefone = "{$det_pessoa["ddd_1"]} {$det_pessoa["fone_1"]}";
			}
			else
			{
				if ($det_pessoa["ddd_2"] && $det_pessoa["fone_2"])
				{
					$telefone = "{$det_pessoa["ddd_2"]} {$det_pessoa["fone_2"]}";
				}
			}

			if ($det_pessoa["ddd_mov"] && $det_pessoa["fone_mov"])
			{
				$celular = "{$det_pessoa["ddd_mov"]} {$det_pessoa["fone_mov"]}";
			}

			$obj_fisica = new clsFisica($ref_idpes);
			$det_fisica = $obj_fisica->detalhe();

			$ideciv = $det_fisica["ideciv"]->ideciv;
			$obj_est_civil = new clsEstadoCivil($ideciv);
			$det_est_civil = $obj_est_civil->detalhe();
			$est_civil = $det_est_civil['descricao'];

			$dt_nasc = dataToBrasil($det_fisica["data_nasc"]);
			$sexo = $det_fisica["sexo"];

			$nacionalidade = $det_fisica["nacionalidade"];
			if ($nacionalidade == 1)
			{
				$nacionalidade = "Brasileiro";
			}
			else if ($nacionalidade == 2)
			{
				$nacionalidade = "Naturalizado Brasileiro";
			}
			else if ($nacionalidade == 3)
			{
				$nacionalidade = "Estrangeiro";
			}

			$det_pais = $det_fisica['idpais_estrangeiro']->detalhe();

			$pais = $det_pais['nome'];

			$idmun_nascimento = $det_fisica["idmun_nascimento"]->idmun;
			$obj_mun_nasc = new clsMunicipio($idmun_nascimento);
			$det_mun_nasc = $obj_mun_nasc->detalhe();
			$naturalidade = $det_mun_nasc['nome'];
			$naturalidade_uf = $det_mun_nasc['sigla_uf']->sigla_uf;

		 	$obj_religiao = new clsPmieducarReligiao( $ref_cod_religiao );
		 	$det_religiao = $obj_religiao->detalhe();
		 	$religiao = $det_religiao['nm_religiao'];

		 	$obj_beneficio = new clsPmieducarAlunoBeneficio( $ref_cod_aluno_beneficio );
		 	$det_beneficio = $obj_beneficio->detalhe();
		 	$beneficio = $det_beneficio['nm_beneficio'];

			$idpes_pai = $det_fisica["idpes_pai"];
			if($idpes_pai)
			{
				$obj_pessoa_pai = new clsPessoaFj($idpes_pai);
				$det_pessoa_pai = $obj_pessoa_pai->detalhe();
				if($det_pessoa_pai)
				{
					$nm_pai = $det_pessoa_pai["nome"];
					//cpf
					$obj_cpf = new clsFisica($idpes_pai);
					$det_cpf = $obj_cpf->detalhe();
					if( $det_cpf )
					{
						$cpf_pai = int2CPF( $det_cpf["cpf"] );
					}
				}
			}
			$idpes_mae = $det_fisica["idpes_mae"];
			if($idpes_mae)
			{
				$obj_pessoa_mae = new clsPessoaFj($idpes_mae);
				$det_pessoa_mae = $obj_pessoa_mae->detalhe();
				if($det_pessoa_mae)
				{
					$nm_mae = $det_pessoa_mae["nome"];
					//cpf
					$obj_cpf = new clsFisica($idpes_mae);
					$det_cpf = $obj_cpf->detalhe();
					if( $det_cpf )
					{
						$cpf_mae = int2CPF( $det_cpf["cpf"] );
					}
				}
			}

			$obj_fisica_cpf = new clsFisica($ref_idpes);
			$det_fisica_cpf = $obj_fisica_cpf->detalhe();
			$cpf = int2CPF($det_fisica_cpf["cpf"]);

			$obj_endereco = new clsPessoaEndereco($ref_idpes);
			if($obj_endereco_det = $obj_endereco->detalhe())
			{
				$id_cep = $obj_endereco_det['cep']->cep;
				$id_bairro = $obj_endereco_det['idbai']->detalhe();
				$id_logradouro = $obj_endereco_det['idlog']->detalhe();
				$id_mun = $id_bairro['idmun']->detalhe();
				$id_logradouro = $id_logradouro['idlog']->detalhe();
				$numero = $obj_endereco_det['numero'];
				$letra = $obj_endereco_det['letra'];
				$complemento = $obj_endereco_det['complemento'];
				$andar = $obj_endereco_det['andar'];
				$apto = $obj_endereco_det['apartamento'];
				$bloco = $obj_endereco_det['bloco'];

				$idtlog = $id_logradouro[1];


				$cidade =   $id_mun['nome'];
				$bairro =   $id_bairro['nome'];
				$logradouro =  $id_logradouro['nome'];
				$endereco_uf =  $id_bairro['idmun']->sigla_uf;

				$cep = int2CEP($id_cep);
			}
			else
			{
				$obj_endereco = new clsEnderecoExterno($ref_idpes);
				if($obj_endereco_det = $obj_endereco->detalhe())
				{
					$id_cep         = $obj_endereco_det['cep'];
					$cidade =  $obj_endereco_det['cidade'];
					$bairro =  $obj_endereco_det['bairro'];
					$logradouro =  $obj_endereco_det['logradouro'];

					$numero    	= $obj_endereco_det['numero'];
					$letra    	= $obj_endereco_det['letra'];
					$complemento  = $obj_endereco_det['complemento'];
					$andar    	= $obj_endereco_det['andar'];
					$apto  = $obj_endereco_det['apartamento'];
					$bloco	    = $obj_endereco_det['bloco'];

					$idtlog = $obj_endereco_det['idtlog']->idtlog;
			 		$endereco_uf = $obj_endereco_det['sigla_uf']->sigla_uf;
					$cep = int2CEP($id_cep);
				}
			}

			if($ref_idpes)
			{
				$obj_fisica_raca = new clsCadastroFisicaRaca($ref_idpes);
		 		$det_fisica_raca = $obj_fisica_raca->detalhe();
		 		$raca_aluno = $det_fisica_raca['ref_cod_raca'];

				$obj_deficiencia_pessoa = new clsCadastroFisicaDeficiencia();
				$lst_deficiencia_pessoa = $obj_deficiencia_pessoa->lista($ref_idpes);
				if (is_array($lst_deficiencia_pessoa))
				{
					foreach ($lst_deficiencia_pessoa as $campo)
					{
						$obj_deficiencia = new clsCadastroDeficiencia( $campo['ref_cod_deficiencia'] );
						$det_deficiencia = $obj_deficiencia->detalhe();
						$deficiencia_aluno[$campo['ref_cod_deficiencia']] = $det_deficiencia['nm_deficiencia'];
					}
				}

				$ObjDocumento = new clsDocumento($ref_idpes);
				$detalheDocumento = $ObjDocumento->detalhe();

				$rg = $detalheDocumento['rg'];

				if($detalheDocumento['data_exp_rg'])
				{
					$rg_data = date( "d/m/Y", strtotime( substr($detalheDocumento['data_exp_rg'],0,19) ) );
				}

				$obj_orgao_emissor_rg = new clsOrgaoEmissorRg($detalheDocumento['idorg_exp_rg']);
				$det_orgao_emissor_rg = $obj_orgao_emissor_rg->detalhe();
				$org_expedidor = $det_orgao_emissor_rg['sigla'];

				$org_expedidor_uf = $detalheDocumento['sigla_uf_exp_rg'];

				if ($detalheDocumento['tipo_cert_civil'] == 91)
				{
					$cert_civil = "Nascimento";
				}
				else if ($detalheDocumento['tipo_cert_civil'] == 92)
				{
					$cert_civil = "Casamento";
				}
				$termo = $detalheDocumento['num_termo'];
				$livro = $detalheDocumento['num_livro'];
				$folha = $detalheDocumento['num_folha'];

				if($detalheDocumento['data_emissao_cert_civil'])
				{
					$cert_civil_data = date( "d/m/Y", strtotime( substr($detalheDocumento['data_emissao_cert_civil'],0,19) ) );
				}

				$cert_civil_uf = $detalheDocumento['sigla_uf_cert_civil'];

				$cartorio = $detalheDocumento['cartorio_cert_civil'];

				$tit_eleitor = $detalheDocumento['num_tit_eleitor'];
				$zona = $detalheDocumento['zona_tit_eleitor'];
				$secao = $detalheDocumento['secao_tit_eleitor'];
			}

			if($tipo_responsavel == 'p')
				$ref_idpes_responsavel = $det_fisica['idpes_pai'];
			elseif($tipo_responsavel == 'm')
				$ref_idpes_responsavel = $det_fisica['idpes_mae'];
			else
				$ref_idpes_responsavel = $det_fisica["idpes_responsavel"];

			$obj_pessoa = new clsPessoaFj($ref_idpes_responsavel);
			$det_pessoa = $obj_pessoa->detalhe();
			$nome_resp  = $det_pessoa["nome"];
			$email_resp =  $det_pessoa["email"];

			if ($det_pessoa["ddd_1"] && $det_pessoa["fone_1"])
			{
				$telefone_resp = "{$det_pessoa["ddd_1"]} {$det_pessoa["fone_1"]}";
			}
			else
			{
				if ($det_pessoa["ddd_2"] && $det_pessoa["fone_2"])
				{
					$telefone_resp = "{$det_pessoa["ddd_2"]} {$det_pessoa["fone_2"]}";
				}
			}

			if ($det_pessoa["ddd_mov"] && $det_pessoa["fone_mov"])
			{
				$celular_resp = "{$det_pessoa["ddd_mov"]} {$det_pessoa["fone_mov"]}";
			}

			$obj_fisica = new clsFisica($ref_idpes_responsavel);
			$det_fisica = $obj_fisica->detalhe();
			$sexo_resp = $det_fisica["sexo"];
			$cpf_resp = int2CPF($det_fisica["cpf"]);

			$obj_endereco = new clsPessoaEndereco($ref_idpes_responsavel);
			if($obj_endereco_det = $obj_endereco->detalhe())
			{
				$id_cep = $obj_endereco_det['cep']->cep;
				$id_bairro = $obj_endereco_det['idbai']->detalhe();
				$id_logradouro = $obj_endereco_det['idlog']->detalhe();
				$id_mun = $id_bairro['idmun']->detalhe();
				$id_logradouro = $id_logradouro['idlog']->detalhe();
				$numero_resp = $obj_endereco_det['numero'];
				$letra_resp = $obj_endereco_det['letra'];
				$complemento_resp = $obj_endereco_det['complemento'];
				$andar_resp = $obj_endereco_det['andar'];
				$apto_resp= $obj_endereco_det['apartamento'];
				$bloco_resp = $obj_endereco_det['bloco'];

				$idtlog = $id_logradouro[1];

				$cidade_resp =  $id_mun['nome'];
				$bairro_resp =  $id_bairro['nome'];
				$logradouro_resp =  $id_logradouro['nome'];
				$endereco_uf_resp =  $obj_endereco_det['sigla_uf'];
				$cep_resp = int2CEP($id_cep);

			}
			else
			{
				$obj_endereco = new clsEnderecoExterno($ref_idpes_responsavel);
				if($obj_endereco_det = $obj_endereco->detalhe())
				{
					$id_cep = $obj_endereco_det['cep'];
					$cidade_resp =  $obj_endereco_det['cidade'];
					$bairro_resp =  $obj_endereco_det['bairro'];
					$logradouro_resp =  $obj_endereco_det['logradouro'];
					$numero_resp = $obj_endereco_det['numero'];
					$letra_resp = $obj_endereco_det['letra'];
					$complemento_resp = $obj_endereco_det['complemento'];
					$andar_resp = $obj_endereco_det['andar'];
					$apto_resp = $obj_endereco_det['apartamento'];
					$bloco_resp = $obj_endereco_det['bloco'];

					$idtlog = $obj_endereco_det['idtlog']->idtlog;
			 		$endereco_uf_resp = $obj_endereco_det['sigla_uf']->sigla_uf;
					$cep_resp = int2CEP($id_cep);
				}
			}
		}


		$pdf = new clsPDF("Ficha do Aluno", "Ficha do Aluno", "A4", "", false, false);

		$fonte = 'arial';
		$corTexto = '#000000';

		$altura_linha = 23;
		$inicio_escrita_y = 175;

		$pdf->OpenPage();

		$tam_titulo = 11;
		$tam_letra = 9;
		$espessura_linha = 0.5;

	//******************** DADOS PESSOAIS ********************//
		$X_quadrado = 30;
		$Y_quadrado = 120;
		$largura_quadrado = 536;
		$altura_quadrado = 230;

		$largura_titulo = 500;
		$altura_titulo = 15;

    /**
     * Variável global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configuração do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $pdf->quadrado_relativo(30, 30, 536, 85);
    $pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, "i-Educar");
    $pdf->escreve_relativo($titulo, 30, 50, 536, 80, $fonte,
      18, $corTexto, 'center');
    $pdf->escreve_relativo("Secretaria da Educação", 30, 75, 536, 80, $fonte,
      14, $corTexto, 'center');

    $pdf->quadrado_relativo($X_quadrado, $Y_quadrado, $largura_quadrado,
      $altura_quadrado, $espessura_linha );
    $pdf->escreve_relativo("DADOS PESSOAIS DO ALUNO", $X_quadrado + 4,
      $Y_quadrado + 3, $largura_titulo, $altura_titulo, $fonte, $tam_titulo );
    $pdf->linha_relativa($X_quadrado, $Y_quadrado + 20, $largura_quadrado, 0,
      $espessura_linha );

		$X_coluna = 34;
		$largura = 285;
		$altura = 30;

		if (!$nome)
		{
			$nome = "_______________________________________________";
		}
		if (!$dt_nasc)
		{
			$dt_nasc = "_____ / _____ / _________";
		}

		$Y_linha = $Y_quadrado + 40;
		$pdf->escreve_relativo( "NOME:  {$nome}", $X_coluna, $Y_linha, 285, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "DATA DE NASCIMENTO:  {$dt_nasc}", 335, $Y_linha, 235, $altura, $fonte, $tam_letra );


		if ($sexo == "F")
		{
			$fem = " X ";
			$mas = "   ";
		}
		elseif ($sexo == "M")
		{
			$fem = "   ";
			$mas = " X ";
		}
		else
		{
			$fem = "   ";
			$mas = "   ";
		}

		if (!$est_civil)
		{
			$est_civil = "______________________________";
		}
		$Y_linha += $altura;
		$pdf->escreve_relativo( "SEXO:   ({$fem}) Feminino      ({$mas}) Masculino", $X_coluna, $Y_linha, 285, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "ESTADO CIVIL:  {$est_civil}", 335, $Y_linha, 235, $altura, $fonte, $tam_letra );

		if (!$naturalidade)
		{
			$naturalidade = "______________________________________";
		}
		if (!$naturalidade_uf)
		{
			$naturalidade_uf = "___________________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "NATURALIDADE:  {$naturalidade}", $X_coluna, $Y_linha, 285, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "ESTADO:  {$naturalidade_uf}", 335, $Y_linha, 235, $altura, $fonte, $tam_letra );

		if (!$nacionalidade)
		{
			$nacionalidade = "_____________________________________";
		}
		if (!$pais)
		{
			$pais = "____________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "NACIONALIDADE:  {$nacionalidade}", $X_coluna, $Y_linha, 285, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "PAÍS DE ORIGEM:  {$pais}", 335, $Y_linha, 235, $altura, $fonte, $tam_letra );

		if (!$religiao)
		{
			$religiao = "___________________________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "RELIGIÃO:  {$religiao}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );

		$obj_raca = new clsCadastroRaca();
		$obj_raca->setOrderby( "nm_raca ASC" );
		$lst_raca = $obj_raca->lista( null,null,null,null,null,null,null,true );
		if (is_array($lst_raca))
		{
			$count = 0;
			$passou = false;
			$texto = "RAÇA: ";
			foreach ($lst_raca as $raca)
			{
				$count++;
				if (strlen($texto) >= 102)
				{
					$Y_linha += $altura;
					$pdf->escreve_relativo( "{$texto}", $X_coluna, $Y_linha, 526, $altura, $fonte, $tam_letra );
					$texto = "";
				}
				if ($raca['cod_raca'] == $raca_aluno)
				{
					$texto .= "      ( X ) {$raca['nm_raca']}";
				}
				else
				{
					$texto .= "      (   ) {$raca['nm_raca']}";
				}
			}
			if (!$passou)
			{
				$Y_linha += $altura;
				$pdf->escreve_relativo( "{$texto}", $X_coluna, $Y_linha, 526, $altura, $fonte, $tam_letra );
				$texto = "";
			}
			if (strlen($texto) > 0)
			{
				$Y_linha += $altura;
				$pdf->escreve_relativo( "{$texto}", $X_coluna, $Y_linha, 526, $altura, $fonte, $tam_letra );
			}
		}

	//********************************************************//

	//*********************** FILIACAO ***********************//
		$Y_quadrado += $altura_quadrado;
		$altura_quadrado = 100;
		$altura_titulo = $Y_quadrado + 15;
		$pdf->quadrado_relativo( $X_quadrado, $Y_quadrado, $largura_quadrado, $altura_quadrado, $espessura_linha );
		$pdf->escreve_relativo( "FILIAÇÃO", $X_quadrado+4, $Y_quadrado+3, $largura_titulo, $altura_titulo, $fonte, $tam_titulo );
		$pdf->linha_relativa( $X_quadrado, $Y_quadrado + 20, $largura_quadrado, 0, $espessura_linha );

		if (!$nm_pai)
		{
			$nm_pai = "________________________________________";
		}
		if (!$cpf_pai)
		{
			$cpf_pai = "_________________________ - ______";
		}

		$Y_linha = $Y_quadrado + 40;
		$pdf->escreve_relativo( "NOME DO PAI:  {$nm_pai}", $X_coluna, $Y_linha, 285, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "CPF DO PAI:  {$cpf_pai}", 335, $Y_linha, 235, $altura, $fonte, $tam_letra );

		if (!$nm_mae)
		{
			$nm_mae = "_______________________________________";
		}
		if (!$cpf_mae)
		{
			$cpf_mae = "________________________ - ______";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "NOME DA MÃE:  {$nm_mae}", $X_coluna, $Y_linha, 285, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "CPF DA MÃE:  {$cpf_mae}", 335, $Y_linha, 235, $altura, $fonte, $tam_letra );
	//********************************************************//

	//*********************** ENDERECO ***********************//
		$Y_quadrado += $altura_quadrado;
		$altura_quadrado = 130;
		$altura_titulo = $Y_quadrado + 15;
		$pdf->quadrado_relativo( $X_quadrado, $Y_quadrado, $largura_quadrado, $altura_quadrado, $espessura_linha );
		$pdf->escreve_relativo( "ENDEREÇO", $X_quadrado+4, $Y_quadrado+3, $largura_titulo, $altura_titulo, $fonte, $tam_titulo );
		$pdf->linha_relativa( $X_quadrado, $Y_quadrado + 20, $largura_quadrado, 0, $espessura_linha );

		if (!$logradouro)
		{
			$logradouro = "_______________________________________";
		}
		if (!$numero)
		{
			$numero = "_______";
		}
		if (!$letra)
		{
			$letra = "___";
		}
		if (!$bloco)
		{
			$bloco = "___";
		}
		if (!$apto)
		{
			$apto = "________";
		}
		if (!$andar)
		{
			$andar = "________";
		}

		$Y_linha = $Y_quadrado + 40;
		$pdf->escreve_relativo( "ENDEREÇO:  {$tipo_logradouro} {$logradouro}   Nº:  {$numero}   LETRA:  {$letra}   BLOCO:  {$bloco}   ANDAR:  {$andar}   APTO:  {$apto}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );

		if (!$bairro)
		{
			$bairro = "_____________________";
		}
		if (!$cidade)
		{
			$cidade = "________________________";
		}
		if (!$endereco_uf)
		{
			$endereco_uf = "_____";
		}
		if (!$cep)
		{
			$cep = "____________ - _____";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "BAIRRO:  {$bairro}   CIDADE:  {$cidade}   ESTADO:  {$endereco_uf}   CEP:  {$cep}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );

		if (!$complemento)
		{
			$complemento = "_________________________________________________________________________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "COMPLEMENTO:  {$complemento}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );
	//********************************************************//

	//*********************** OUTRAS INFORMACOES ***********************//
		$Y_quadrado += $altura_quadrado;
		$altura_quadrado = 130;
		$altura_titulo = $Y_quadrado + 15;
		$pdf->quadrado_relativo( $X_quadrado, $Y_quadrado, $largura_quadrado, $altura_quadrado, $espessura_linha );
		$pdf->escreve_relativo( "OUTRAS INFORMAÇÕES", $X_quadrado+4, $Y_quadrado+3, $largura_titulo, $altura_titulo, $fonte, $tam_titulo );
		$pdf->linha_relativa( $X_quadrado, $Y_quadrado + 20, $largura_quadrado, 0, $espessura_linha );

		if (!$beneficio)
		{
			$beneficio = "______________________________";
		}
		if ($analfabeto)
		{
			$analfabeto = "  ( X ) Não      (   ) Sim";
		}
		else if ($analfabeto == "")
		{
			$analfabeto = "  (   ) Não      (   ) Sim";
		}
		else
		{
			$analfabeto = "  (   ) Não      ( X ) Sim";
		}

		$Y_linha = $Y_quadrado + 40;
		$pdf->escreve_relativo( "BENEFÍCIO:  {$beneficio}", $X_coluna, $Y_linha, 215, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "ALFABETIZADO:  {$analfabeto}", 265, $Y_linha, 235, $altura, $fonte, $tam_letra );
		$pdf->quadrado_relativo( $largura+190, $Y_linha-10, 80, 90, $espessura_linha );
		$pdf->escreve_relativo( "FOTO", $largura+217, $Y_linha+20, 510-$largura, $altura, $fonte, $tam_letra );
/*
		if ($caminho_foto)
		{
			$foto = pathinfo("arquivos/educar/aluno/small/{$caminho_foto}");
			//echo  $foto['extension'];die;
			$pdf->InsertJpng("gif" , "arquivos/educar/aluno/small/{$caminho_foto}", $largura+190, $Y_linha+35, 0.90 );
		}
*/

		if (!$telefone)
		{
			$telefone = "( ___ )  __________ - ____________";
		}
		if (!$celular)
		{
			$celular = "( ___ )  __________ - ____________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "TELEFONE:  {$telefone}", $X_coluna, $Y_linha, 215, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "CELULAR:  {$celular}", 265, $Y_linha, 235, $altura, $fonte, $tam_letra );

		if (!$email)
		{
			$email = "_______________________________________________________________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "EMAIL:  {$email}", $X_coluna, $Y_linha, 500, $altura, $fonte, $tam_letra );
	//********************************************************//

	//*********************** DEFICIENCIAS ***********************//
		$Y_quadrado += $altura_quadrado;
		$altura_quadrado = 110;
		$altura_titulo = $Y_quadrado + 15;
		$pdf->quadrado_relativo( $X_quadrado, $Y_quadrado, $largura_quadrado, $altura_quadrado, $espessura_linha );
		$pdf->escreve_relativo( "DEFICIÊNCIAS", $X_quadrado+4, $Y_quadrado+3, $largura_titulo, $altura_titulo, $fonte, $tam_titulo );
		$pdf->linha_relativa( $X_quadrado, $Y_quadrado + 20, $largura_quadrado, 0, $espessura_linha );

		$Y_linha = $Y_quadrado;

		$obj_deficiencias = new clsCadastroDeficiencia();
		$obj_deficiencias->setOrderby( "nm_deficiencia ASC" );
		$lst_deficiencia = $obj_deficiencias->lista();
		if (is_array($lst_deficiencia))
		{
			$count = 0;
			$passou = false;
			$texto = "";
			foreach ($lst_deficiencia as $deficiencia)
			{
				$count++;
				if (strlen($texto) >= 102)
				{
					$Y_linha += $altura;
					$pdf->escreve_relativo( "{$texto}", $X_coluna, $Y_linha, 526, $altura, $fonte, $tam_letra );
					$texto = "";
				}

				if ($deficiencia_aluno[$deficiencia['cod_deficiencia']])
				{
					$texto .= "      ( X ) {$deficiencia['nm_deficiencia']}";
				}
				else
				{
					$texto .= "      (   ) {$deficiencia['nm_deficiencia']}";
				}
			}
			if (!$passou)
			{
				$Y_linha += $altura;
				$pdf->escreve_relativo( "{$texto}", $X_coluna, $Y_linha, 526, $altura, $fonte, $tam_letra );
				$texto = "";
			}
			if (strlen($texto) > 0)
			{
				$Y_linha += $altura;
				$pdf->escreve_relativo( "{$texto}", $X_coluna, $Y_linha, 526, $altura, $fonte, $tam_letra );
			}
		}

	//********************************************************//

		$pdf->ClosePage();

		$pdf->OpenPage();

	//*********************** DOCUMENTOS ***********************//
		$X_quadrado = 30;
		$largura_quadrado = 535;
		$largura_titulo = 500;

		$Y_quadrado = 30;
		$altura_quadrado = 220;
		$altura_titulo = 15;

		$pdf->quadrado_relativo( $X_quadrado, $Y_quadrado, $largura_quadrado, $altura_quadrado, $espessura_linha );
		$pdf->escreve_relativo( "DOCUMENTOS", $X_quadrado+4, $Y_quadrado+3, $largura_titulo, $altura_titulo, $fonte, $tam_titulo );
		$pdf->linha_relativa( $X_quadrado, $Y_quadrado + 20, $largura_quadrado, 0, $espessura_linha );

		if ($cpf == "000.000.000-00" || $cpf == "")
		{
			$cpf = "_____________________________ - ________";
		}

		$Y_linha = $Y_quadrado + 40;
		$pdf->escreve_relativo( "CPF:  {$cpf}", $X_coluna, $Y_linha, $largura, $altura, $fonte, $tam_letra );

		if (!$rg)
		{
			$rg = "_______________________";
		}
		if (!$org_expedidor)
		{
			$org_expedidor = "____________";
		}
		if (!$org_expedidor_uf)
		{
			$org_expedidor_uf = "____";
		}
		if (!$rg_data)
		{
			$rg_data = "____ / ____ / _________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "IDENTIDADE:  {$rg}", $X_coluna, $Y_linha, 180, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "ORGÃO EXP. / UF:  {$org_expedidor} / {$org_expedidor_uf}", 230, $Y_linha, 175, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "DATA:  {$rg_data}", 420, $Y_linha, 150, $altura, $fonte, $tam_letra );

		if (!$tit_eleitor)
		{
			$tit_eleitor = "______________________";
		}
		if (!$zona)
		{
			$zona = "______________________";
		}
		if (!$secao)
		{
			$secao = "___________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "TÍTULO DE ELEITOR:  {$tit_eleitor}", $X_coluna, $Y_linha, 210, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "ZONA:  {$zona}", 260, $Y_linha, 175, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "SEÇÃO:  {$secao}", 420, $Y_linha, 150, $altura, $fonte, $tam_letra );

		if (!$cert_civil)
		{
			$cert_civil = "______________________";
		}
		if (!$cert_civil_uf)
		{
			$cert_civil_uf = "___________________";
		}
		if (!$cert_civil_data)
		{
			$cert_civil_data = "____ / ____ / _________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "CERTIFICADO CIVIL:  {$cert_civil}", $X_coluna, $Y_linha, 210, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "ESTADO:  {$cert_civil_uf}", 260, $Y_linha, 175, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "DATA:  {$cert_civil_data}", 420, $Y_linha, 150, $altura, $fonte, $tam_letra );

		if (!$termo)
		{
			$termo = "___________________________";
		}
		if (!$livro)
		{
			$livro = "___________________________";
		}
		if (!$folha)
		{
			$folha = "___________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "TERMO:  {$termo}", $X_coluna, $Y_linha, 180, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "LIVRO:  {$livro}", 230, $Y_linha, 175, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "FOLHA:  {$folha}", 420, $Y_linha, 150, $altura, $fonte, $tam_letra );

		if (!$cartorio)
		{
			$cartorio = "_____________________________________________________________________________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "CARTÓRIO:  {$cartorio}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );

	//********************************************************//

//*********************************** DADOS RESPONSAVEL ***********************************//

	//******************** RESPONSAVEL ********************//
		$Y_quadrado += $altura_quadrado;
		$altura_quadrado = 255;
		$altura_titulo = $Y_quadrado + 15;

		$pdf->quadrado_relativo( $X_quadrado, $Y_quadrado, $largura_quadrado, $altura_quadrado, $espessura_linha );
		$pdf->escreve_relativo( "DADOS DO RESPONSÁVEL", $X_quadrado+4, $Y_quadrado+3, $largura_titulo, $altura_titulo, $fonte, $tam_titulo );
		$pdf->linha_relativa( $X_quadrado, $Y_quadrado + 20, $largura_quadrado, 0, $espessura_linha );

		$X_coluna = 34;
		$largura = 285;

		if (!$nome_resp)
		{
			$nome_resp = "_________________________________________________________________________________________________";
		}

		$Y_linha = $Y_quadrado + 40;
		$pdf->escreve_relativo( "NOME:  {$nome_resp}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );

		$Y_linha += $altura;
		if ($sexo_resp == "F")
		{
			$fem = " X ";
			$mas = "   ";
		}
		elseif ($sexo_resp == "M")
		{
			$fem = "   ";
			$mas = " X ";
		}
		else
		{
			$fem = "   ";
			$mas = "   ";
		}
		if ($cpf_resp == "000.000.000-00" || $cpf_resp == "")
		{
			$cpf_resp = "_____________________________ - ________";
		}
		$pdf->escreve_relativo( "CPF:  {$cpf_resp}", $X_coluna, $Y_linha, $largura, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "SEXO:   ({$fem}) Feminino      ({$mas}) Masculino", $largura+50, $Y_linha, 510-$largura, $altura, $fonte, $tam_letra );

		$logradouro_resp = $logradouro_resp ? $logradouro_resp : $logradouro;
		if (!$logradouro_resp)
		{
			$logradouro_resp = "_______________________________________";
		}
		$numero_resp = $numero_resp ? $numero_resp : $numero;
		if (!$numero_resp)
		{
			$numero_resp = "_______";
		}
		$letra_resp = $letra_resp ? $letra_resp : $letra;
		if (!$letra_resp)
		{
			$letra_resp = "___";
		}
		$bloco_resp = $bloco_resp ? $bloco_resp : $bloco;
		if (!$bloco_resp)
		{
			$bloco_resp = "___";
		}
		$apto_resp = $apto_resp ? $apto_resp : $apto;
		if (!$apto_resp)
		{
			$apto_resp = "________";
		}

		$Y_linha += $altura;
		$tipo_logradouro_resp = $tipo_logradouro_resp ? $tipo_logradouro_resp : $tipo_logradouro;
		$andar_resp = $andar_resp ? $andar_resp : $andar;

		$pdf->escreve_relativo( "ENDEREÇO:  {$tipo_logradouro_resp} {$logradouro_resp}   Nº:  {$numero_resp}   LETRA:  {$letra_resp}   BLOCO:  {$bloco_resp}   ANDAR:  {$andar_resp}   APTO:  {$apto_resp}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );

		$bairro_resp = $bairro_resp ? $bairro_resp : $bairro;
		if (!$bairro_resp)
		{
			$bairro_resp = "_____________________";
		}
		$cidade_resp = $cidade_resp ? $cidade_resp : $cidade;
		if (!$cidade_resp)
		{
			$cidade_resp = "________________________";
		}
		$endereco_uf_resp = $endereco_uf_resp ? $endereco_uf_resp : $endereco_uf;
		if (!$endereco_uf_resp)
		{
			$endereco_uf_resp = "_____";
		}
		$cep_resp = $cep_resp ? $cep_resp : $cep;
		if (!$cep_resp)
		{
			$cep_resp = "____________ - _____";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "BAIRRO:  {$bairro_resp}   CIDADE:  {$cidade_resp}   ESTADO:  {$endereco_uf_resp}   CEP:  {$cep_resp}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );

		$complemento_resp = $complemento_resp ? $complemento_resp : $complemento;
		if (!$complemento_resp)
		{
			$complemento_resp = "_________________________________________________________________________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "COMPLEMENTO:  {$complemento_resp}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );
		$telefone_resp = $telefone_resp ? $telefone_resp : $telefone;
		if (!$telefone_resp)
		{
			$telefone_resp = "( _____ )  ______________ - _______________";
		}
		$celular_resp = $celular_resp ? $celular_resp : $celular;
		if (!$celular_resp)
		{
			$celular_resp = "( _____ )  ______________ - _______________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "TELEFONE:  {$telefone_resp}", $X_coluna, $Y_linha, 255, $altura, $fonte, $tam_letra );
		$pdf->escreve_relativo( "CELULAR:  {$celular_resp}", 310, $Y_linha, 250, $altura, $fonte, $tam_letra );
		$email_resp = $email_resp ? $email_resp : $email;
		if (!$email_resp)
		{
			$email_resp = "_________________________________________________________________________________________________";
		}

		$Y_linha += $altura;
		$pdf->escreve_relativo( "EMAIL:  {$email_resp}", $X_coluna, $Y_linha, 536, $altura, $fonte, $tam_letra );
	//********************************************************//

		$pdf->escreve_relativo( "Assinatura da Secretária(o)", 68,590, 100, 50, $fonte, 7, $corTexto, 'left' );
		$pdf->escreve_relativo( "Assinatura do Responsável", 400,590, 100, 50, $fonte, 7, $corTexto, 'left' );
		$pdf->linha_relativa(52,587,130,0);
		$pdf->linha_relativa(384,587,130,0);
	//****************************************************************************************//

	if (is_numeric($cod_aluno))
	{
		$sql = "SELECT MAX(cod_matricula) FROM pmieducar.matricula WHERE ref_cod_aluno = {$cod_aluno} AND ativo = 1";
		$db = new clsBanco();
		$ref_cod_matricula = $db->CampoUnico($sql);
		if (is_numeric($ref_cod_matricula))
		{
			$obj_matricula = new clsPmieducarMatricula();
			$obj_matricula->setOrderby("ano ASC");
			$lst_matricula = $obj_matricula->lista( $ref_cod_matricula );
			if($lst_matricula)
				$registro = array_shift($lst_matricula);

			$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$nm_curso = $det_ref_cod_curso["nm_curso"];

			$obj_serie = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
			$det_serie = $obj_serie->detalhe();
			$nm_serie = $det_serie["nm_serie"];

			$ano = $registro["ano"];
		}
	}

		$pdf->ClosePage();
		$pdf->OpenPage();
//		$fonte = 'arial';
			$corTexto = '#000000';
			$esquerda = 30;
			$direita  = 535;
			$altura   = 30;
//			$tam_titulo = 12;
//			$tam_letra = 10;
			$espessura_linha = 0.5;
			$pdf->quadrado_relativo( 30, 30, 536, 350, $espessura_linha );
//			$pdf->quadrado_relativo( $esquerda, $altura, 535, $pdf->altura - 150, $espessura_linha );

			$pdf->escreve_relativo("DADOS DE MATRÍCULA", $esquerda+5, $altura+5, 350, 100, $fonte, $tam_titulo);
			$pdf->linha_relativa($esquerda, $altura += 20, 535, 0, $espessura_linha);

			$pdf->escreve_relativo("ANO: {$ano}", $esquerda + 5, $altura+=5, 500, 100, $fonte, $tam_letra);
			$pdf->escreve_relativo("SEMESTRE: ", $esquerda + 120, $altura, 350, 100, $fonte, $tam_letra);
			$pdf->escreve_relativo("CURSO: {$nm_curso}", $esquerda + 290, $altura, 350, 100, $fonte, $tam_letra);

			$pdf->escreve_relativo("ANO/SÉRIE: {$nm_serie}", $esquerda + 5, $altura += 25, 350, 100, $fonte, $tam_letra);
			$pdf->escreve_relativo("TURNO: ", $esquerda + 290, $altura, 200, 200, $fonte, $tam_letra);
//			$pdf->escreve_relativo("Cor/Raça: {$nm_raca}", $esquerda + 235, $altura, 200, 200, $fonte, $tam_letra);
//			$pdf->escreve_relativo("Religião: {$religiao}", $esquerda + 350, $altura, 200, 200, $fonte, $tam_letra);

			$pdf->linha_relativa($esquerda, $altura += 20, 535, 0, $espessura_linha);
			$pdf->escreve_relativo("PROCEDÊNCIA DO ALUNO", $esquerda + 5, $altura +=2, 500, 100, $fonte, $tam_titulo);
			$pdf->linha_relativa($esquerda, $altura += 20, 535, 0, $espessura_linha);
//
			$texto = "ESTABELECIMENTO DE ENSINO DE ORIGEM: ____________________________________________________";
			$pdf->escreve_relativo($texto, $esquerda + 5, $altura+=5, 700, 100, $fonte, $tam_letra);

			$texto = "MUNICÍPIO: ___________________________________________________    ESTADO: _____________________";
			$pdf->escreve_relativo($texto, $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);

			$texto = "ANO/SÉRIE CURSADO NO ANO ANTERIOR: ________________________________________________";
			$pdf->escreve_relativo($texto, $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);

			$pdf->escreve_relativo("SITUAÇÃO DO ALUNO NO ANO ANTERIOR", $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);

			$pdf->escreve_relativo("(  ) FOI APROVADO", $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);

			$pdf->escreve_relativo("(  ) FOI REPROVADO", $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);

			$pdf->escreve_relativo("(  ) ABANDONOU", $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);

			$texto = "(  ) NÃO FREQUENTOU: ___________________________________________________________________";
			$pdf->escreve_relativo($texto, $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);

			$pdf->escreve_relativo("DATA: ".date("d/m/Y"), $esquerda + 5, $altura += 25, 700, 100, $fonte, $tam_letra);





		$pdf->CloseFile();
		$link = $pdf->GetLink();

		echo "<center><a target='blank' href='" . $link . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";
?>
</body>
</html>
