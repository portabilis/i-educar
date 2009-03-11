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
		require_once ("include/relatorio.inc.php");
		require_once ("include/pmieducar/geral.inc.php");

		$ref_cod_instituicao = $_GET['ref_cod_instituicao'];
		$ref_cod_escola = $_GET['ref_cod_escola'];
		$professor = $_GET['professor'] ? true: null;

		if ($ref_cod_escola)
		{

			$obj_servidor = new clsPmieducarServidorAlocacao ();
			$obj_servidor->setCamposLista ("ref_ref_cod_instituicao,ref_cod_escola,sa.ref_cod_servidor,sum(carga_horaria) as carga_horaria");
			$obj_servidor->setOrderby ("sa.ref_ref_cod_instituicao,sa.ref_cod_escola,p.nome,sa.ref_cod_servidor");
			$obj_servidor->setGroupBy ("ref_ref_cod_instituicao,ref_cod_escola,sa.ref_cod_servidor,p.nome");
			$lst_servidor = $obj_servidor->lista (null, $ref_cod_instituicao, null, null, $ref_cod_escola, null, null, null, null, null, 1, null, null, true,$professor);

			if ( is_array($lst_servidor) )
			{

				$total_servidor = count($lst_servidor);

				$relatorio = new relatorios ( "RELAÇÃO DO QUADRO DE PROFESSORES   -                Total de Funcionário/Professores = {$total_servidor}", 120, false, "i-Educar", "A4", "Prefeitura COBRA Tecnologia\n\nSecretaria da Educação", "#515151");
				$relatorio->exibe_produzido_por = false;

				$get_nome_escola = new clsPmieducarEscola($ref_cod_escola);
				$det_nome_escola = $get_nome_escola->detalhe();

				if (is_array($det_nome_escola))
				{
					$relatorio->novalinha ( array( $det_nome_escola['nome'] ), 0, 16, true, "arial", array(), "#515151", "#d3d3d3", "#FFFFFF", false, true);
				}

				$relatorio->novalinha ( array( "Nome", "Matrícula", "Turno", "Carga Horária Disponível"), 0, 16, true, "arial", array( 210, 90, 100), "#515151", "#d3d3d3", "#FFFFFF", false, true);
//				$relatorio->novalinha ( array( "Nome", "Matrícula", "Turno", "Carga Horária Disponível"), 0, 16, true, "arial", array( 210, 50, 50), "#515151", "#d3d3d3", "#FFFFFF", false, true);

				$array_turnos = array( "1" => "M", "2" => "V", "3" => "N" );

				$cor = "#FFFFFF";
				
				foreach ($lst_servidor as $servidor)
				{
					$get_turnos = new clsPmieducarServidorAlocacao();
					$get_turnos->setCamposLista("periodo");
					$get_turnos->setGroupBy("periodo, p.nome");

					$turnos = $get_turnos->lista(null, $ref_cod_instituicao, null, null, $ref_cod_escola, $servidor['ref_cod_servidor'], null, null, null, null, 1, null, null, true);

					$turnos_txt = "";

					if (is_array($turnos))
					{
						$completar = "";
						foreach ($turnos as $turno)
						{
							$turnos_txt .= "{$completar}{$array_turnos[$turno['periodo']]}";
							$completar = "/";
						}
					}
					$sql = "SELECT nm_funcao FROM pmieducar.servidor_funcao, pmieducar.funcao WHERE ref_cod_funcao = cod_funcao AND ref_cod_servidor = {$servidor["ref_cod_servidor"]}";
					$db = new clsBanco();
//					die($sql);
					$nm_funcao = $db->CampoUnico($sql);
					$cor = $cor == "#FFFFFF" ? "#D3D3D3" : "#FFFFFF";
					$relatorio->novalinha( array( minimiza_capitaliza($servidor['nome']), $servidor['ref_cod_servidor'], $turnos_txt, $servidor['carga_horaria']) , 5, 17, false, "arial", array( 215, 90, 100 ));
					if (!empty($nm_funcao))
						$relatorio->novalinha( array("Função: {$nm_funcao}") , 20, 17, false, "arial", array( 300 ));//, "#000000", $cor);
//					$relatorio->novalinha( array( minimiza_capitaliza($servidor['nome']), $servidor['ref_cod_servidor'], $turnos_txt, $servidor['carga_horaria']) , 5, 17, false, "arial", array( 215, 50, 100 ));
				}

				// pega o link e exibe ele ao usuario
				$link = $relatorio->fechaPdf();

				echo "<center><a target='blank' href='" . $link . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
					<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

					Clique na Imagem para Baixar o instalador<br><br>
					<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
					</span>
					</center><script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=" . $link . "'}</script>";

			}
			else
			{
					echo "<center>Não existem servidores a serem listados!</center>";
			}
		}
		else
		{

			$entrou = false;

			$get_escolas = new clsPmieducarServidorAlocacao ();
			$lst_escolas = $get_escolas->listaEscolas($ref_cod_instituicao);

			if (is_array($lst_escolas))
			{

				$relatorio = new relatorios ( "RELAÇÃO DO QUADRO DE PROFESSORES", 120, false, "i-Educar", "A4", "Prefeitura COBRA Tecnologia\n\nSecretaria da Educação", "#515151");
				$relatorio->exibe_produzido_por = false;

				foreach ($lst_escolas as $escolas)
				{

					$obj_servidor = new clsPmieducarServidorAlocacao ();
					$obj_servidor->setCamposLista ("ref_ref_cod_instituicao,ref_cod_escola,sa.ref_cod_servidor,sum(carga_horaria) as carga_horaria");
					$obj_servidor->setOrderby ("sa.ref_ref_cod_instituicao,sa.ref_cod_escola,p.nome,sa.ref_cod_servidor");
					$obj_servidor->setGroupBy ("ref_ref_cod_instituicao,ref_cod_escola,sa.ref_cod_servidor,p.nome");
					$lst_servidor = $obj_servidor->lista (null, $ref_cod_instituicao, null, null, $escolas['ref_cod_escola'], null, null, null, null, null, 1, null, null, true);

					if ( is_array($lst_servidor) )
					{

						$get_nome_escola = new clsPmieducarEscola($escolas['ref_cod_escola']);
						$det_nome_escola = $get_nome_escola->detalhe();

						if (is_array($det_nome_escola))
						{
							$total_servidor = count($lst_servidor);
							$relatorio->novalinha ( array( "{$det_nome_escola['nome']} - Total de Professores: {$total_servidor}" ), 0, 16, true, "arial", array(), "#515151", "#d3d3d3", "#FFFFFF", false, true);
						}

						$relatorio->novalinha ( array( "Nome", "Matrícula", "Turno", "Carga Horária Disponível"), 0, 16, true, "arial", array( 210, 90, 100), "#515151", "#d3d3d3", "#FFFFFF", false, true);

						$array_turnos = array( "1" => "M", "2" => "V", "3" => "N" );

						foreach ($lst_servidor as $servidor)
						{
							$get_turnos = new clsPmieducarServidorAlocacao ();
							$get_turnos->setCamposLista ("periodo");
							$get_turnos->setGroupBy ("periodo, p.nome");
							$turnos = $get_turnos->lista (null, $ref_cod_instituicao, null, null, $escolas['ref_cod_escola'], $servidor['ref_cod_servidor'], null, null, null, null, 1, null, null, true);

							$turnos_txt = "";

							if (is_array($turnos))
							{
								$completar = "";
								foreach ($turnos as $turno)
								{
									$turnos_txt .= "{$completar}{$array_turnos[$turno['periodo']]}";
									$completar = "/";
								}
							}

							$relatorio->novalinha( array( minimiza_capitaliza($servidor['nome']), $servidor['ref_cod_servidor'], $turnos_txt, $servidor['carga_horaria']) , 5, 17, false, "arial", array( 215, 90, 100 ));
						}

						$entrou = true;


					}
				}
			}

			// pega o link e exibe ele ao usuario
			$link = $relatorio->fechaPdf();

			if ($entrou)
			{
				echo "<center><a target='blank' href='" . $link . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
					<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

					Clique na Imagem para Baixar o instalador<br><br>
					<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
					</span>
					</center><script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=" . $link . "'}</script>";
			}
			else
			{
				echo "<center>Não existem alunos enturmados!</center>";
			}


		}

?>
</body>
</html>
