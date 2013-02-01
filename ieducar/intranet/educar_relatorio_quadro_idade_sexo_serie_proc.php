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
		require_once( "include/pmieducar/geral.inc.php" );

		$ref_cod_escola = $_GET['ref_cod_escola'];
		$ref_cod_curso = $_GET['ref_cod_curso'];
		$ref_ref_cod_serie = $_GET['ref_ref_cod_serie'];
		$ref_cod_turma = $_GET['ref_cod_turma'];
		$ano_requisitado = $_GET['ano'];
		
		$ano_requisitado = $_GET["ano"];
		$is_padrao       = $_GET["is_padrao"];

		if (is_numeric($_GET["sem1"]) && $ano_requisitado != 2007 && !$is_padrao)
			$semestre = $_GET["sem1"];
		elseif (is_numeric($_GET["sem2"]) && $ano_requisitado != 2007 && !$is_padrao)
			$semestre = $_GET["sem2"];
		else
			$semestre = null;
		
		$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo();
//		$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $ref_cod_escola,null,null,null,1,null,null,null,null,1 );
		$lst_escola_ano_letivo = $obj_escola_ano_letivo->lista( $ref_cod_escola,$ano_requisitado,null,null,null,null,null,null,null,1 );
		if (is_array( $lst_escola_ano_letivo ))
		{
			$det_escola_ano_letivo = array_shift( $lst_escola_ano_letivo );
			$ano = $det_escola_ano_letivo['ano'];

			$obj_matricula_turma = new clsPmieducarMatricula();
			$lst_total_series = $obj_matricula_turma->getTotalAlunosIdadeSexoEscola($ref_cod_escola,$ref_cod_curso,$ref_ref_cod_serie, $ano);
			if ( is_array($lst_total_series) )
			{
				$relatorio = new relatorios("QUADRO ALUNOS SINTÉTICO Idade x Sexo   -   Ano {$ano}                                    Total de Alunos = {$lst_total_series[0]['_total']}",120,false, "i-Educar", "A4", "Prefeitura COBRA Tecnologia\nSecretaria da Educação\n\n".date("d/m/Y"), "#515151");
				$relatorio->exibe_produzido_por = false;
				$obj_curso = new clsPmieducarCurso($ref_cod_curso);
				$det_curso = $obj_curso->detalhe();
				$relatorio->novalinha( array( $det_curso['nm_curso'] ),0,16,true,"arial",array( ),"#515151","#d3d3d3","#FFFFFF",false,true);
				$relatorio->novalinha( array( "Série", "Idade", "Sexo", "Total Alunos" ),0,16,true,"arial",array( 90,80,80 ),"#515151","#d3d3d3","#FFFFFF",false,true);
				foreach ($lst_total_series as $serie)
				{
					$serie['sexo']  = $serie['sexo'] == 'F' ? 'Feminino' : 'Masculino';
					$relatorio->novalinha( array( $serie['nm_serie'],  $serie['idade'],  $serie['sexo'],  $serie['total_alunos_serie'] ) , 5, 17, false, "arial", array( 90,70,100 ));
				}
				// pega o link e exibe ele ao usuario
				$link = $relatorio->fechaPdf();

				echo "<center><a target='blank' href='" . $link . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
					<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

					Clique na Imagem para Baixar o instalador<br><br>
					<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
					</span>
					</center><script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$link."'}</script>";
			}
			else
			{
					echo '<script>
	     					alert("Não existem alunos matriculados!");
	     					window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		  </script>';
			     	return true;
			}
		}
		else 
		{
			echo '<script>
	     					alert("Escola não possui calendário definido para este ano");
	     					window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		  </script>';
			     	return true;
		}

?>
</body>
</html>
