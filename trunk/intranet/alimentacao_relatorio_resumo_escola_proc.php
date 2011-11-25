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
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/clsPDF.inc.php");
require_once( "include/alimentacao/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relatório - Resumo por escola" );
		$this->processoAp = "10009";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsCadastro
{


	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;


	var $ano;
	var $mes;
	
	var $nm_instituicao;
	
	var $pdf;

	var $page_y = 139;

	var $get_link;

	var $campo_assinatura;
	
	var $total = 0;

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;
			}
		}
		
		$fonte = 'arial';
		$corTexto = '#000000';
		$filtro = "";
		if (is_numeric($this->ano))
			$filtro .= " AND date_part('year', eme.dt_cadastro) = {$this->ano} ";
		if (is_numeric($this->mes))
			$filtro .= " AND eme.mes = '{$this->mes}' ";
					
		$sql = "select 
				sum(custo) as custototal,
				(sum(custo)/sum(refeicoes)) as custoporrefeicao,
				(sum(calorias)/sum(alunosdias)) as caloriasdiarias,
				(sum(proteinas)/sum(alunosdias)) as proteinasdiarias,
				(sum(custo_familiar)) as custo_agrifamiliar,
				ref_escola,
				escola
				from
				(
				select 
				sum((emep.pesoouvolume/pf.pesoouvolume_un)*pf.preco_un) as custo,
				sum((((emep.pesoouvolume*1000)/(p.fator_correcao/100))/100)*p.calorias) as calorias,
				sum((((emep.pesoouvolume*1000)/(p.fator_correcao/100))/100)*p.proteinas) as proteinas,
				eme.ref_escola,
				eme.mes,
				(eme.alunos*eme.dias*eme.refeicoes) as refeicoes,
				(eme.alunos*eme.dias) as alunosdias,
				sum(case when agri_familiar = 1 then ((emep.pesoouvolume/pf.pesoouvolume_un)*pf.preco_un) else 0 end) as custo_familiar
				 from alimentacao.envio_mensal_escola eme join alimentacao.envio_mensal_escola_produto emep
				on eme.ideme=emep.ref_envio_mensal_escola {$filtro}
				join alimentacao.produto p on emep.ref_produto=p.idpro
				left join alimentacao.produto_fornecedor pf
				on emep.ref_produto=pf.ref_produto and eme.mes between pf.mes_inicio and pf.mes_fim
				group by eme.ref_escola, eme.ano, eme.mes, eme.alunos, eme.dias, eme.refeicoes
				) tb1
				join
				(
				select cod_escola, (case when nome is null then nm_escola else nome end) as escola from pmieducar.escola e left join pmieducar.escola_complemento ec
				on e.cod_escola=ec.ref_cod_escola
				left join cadastro.pessoa p 
				on p.idpes=e.ref_idpes
				)
				tb2
				on tb1.ref_escola=tb2.cod_escola
				group by ref_escola, escola
				order by escola
				";
		$db = new clsBanco();
		$db->Consulta($sql);

		$this->total = $db->Num_Linhas();
		if ($this->total > 0) 
		{
			
			$this->nm_instituicao = "Prefeitura X";
			$this->pdf = new clsPDF("Resumo por Escola - {$this->ano}", "Resumo por Escola", "A4", "", false, false);
			$this->pdf->largura  = 842.0;
			$this->pdf->altura = 595.0;
			$this->page_y = 125;
			$this->pdf->OpenFile();
			$this->addCabecalho();
			$esquerda = 30;
			$altura = 130 + 18 * 2;
			$direita = 782;
			$tam_texto = 9;
			$altura = 130;
			$altura_escrita = 3;
			$ref_cod_escola_aux = null;
			while ( $db->ProximoRegistro() )
		 	{
			 	list($custototal,$custoporrefeicao,$caloriasdiarias,$proteinasdiarias,$custo_agrifamiliar,$ref_escola,$escola) = $db->Tupla();
			 	
			 	$esquerda_aux = $esquerda = 30;
			 	$this->pdf->linha_relativa($esquerda, $altura+=18, 0, 18);
			 	$this->pdf->linha_relativa($esquerda, $altura, $direita, 0);
			 	
				$this->pdf->escreve_relativo($escola, $esquerda + 3, $altura + $altura_escrita, 200, 30, $fonte, $tam_texto, $corTexto);
			 	$this->pdf->linha_relativa($esquerda += 210, $altura, 0, 18);

			 	$this->pdf->escreve_relativo("R$ ".number_format($custototal,2,",","."), $esquerda + 1, $altura + $altura_escrita, 100, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 102, $altura, 0, 18);
				
				$this->pdf->escreve_relativo("R$ ".number_format($custoporrefeicao,2,",","."), $esquerda + 1, $altura + $altura_escrita, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 117, $altura, 0, 18);
				
				$this->pdf->escreve_relativo(number_format($caloriasdiarias,2,",",".")." Kcal", $esquerda + 1, $altura + $altura_escrita, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 118, $altura, 0, 18);
				
				$this->pdf->escreve_relativo(number_format($proteinasdiarias,2,",",".")." gramas", $esquerda + 1, $altura + $altura_escrita, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 117, $altura, 0, 18);
				
				$this->pdf->escreve_relativo("R$ ".number_format($custo_agrifamiliar,2,",",".")." (".number_format((($custo_agrifamiliar/$custototal)*100),1,",",".")."%)", $esquerda + 1, $altura + $altura_escrita, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 118, $altura, 0, 18);
			 	
			 	$this->pdf->linha_relativa($esquerda_aux, $altura + 18, $direita, 0);
			 	if ($altura > $this->pdf->altura - 50)
			 	{
			 		$this->pdf->ClosePage();
			 		$this->pdf->OpenPage();
			 		$this->addCabecalho();
			 		$esquerda = 30;
			 		$altura = 130 + 18*2;
			 		$direita = 782;
			 		$tam_texto = 9;
			 		$altura = 130;

			 		$altura_escrita = 5;
			 	}
		 	}
		 	$this->pdf->CloseFile();
		 	$this->get_link = $this->pdf->GetLink();

		 	echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		 	echo "<html><center>Se o download não iniciar automaticamente <br /><a target='_blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
					<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
		
					Clique na Imagem para Baixar o instalador<br><br>
					<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
					</span>
					</center>";
		}
		else 
		{
			echo '<script>
	     					alert("Nenhum registro encontrado.");
	     					window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
			     		  </script>';
			     	return true;
		}	
	}

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 782, 85 );
		$this->pdf->InsertJpng( "gif", "c:/paginas/ieducar/intranet/imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA X", 30, 30, 782, 80, $fonte, 18, $corTexto, 'center' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 745, 30, 100, 80, $fonte, 12, $corTexto, 'left' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição: $this->nm_instituicao", 120, 52, 300, 80, $fonte, 9, $corTexto, 'left' );
		
		if($this->mes=="")
		{
			$mes = "todos";
		}
		else
		{
			$obj_envio = new clsAlimentacaoEnvioMensalEscola();
			$mes = $obj_envio->getMes($this->mes);
		}
		
		$this->pdf->escreve_relativo( "Resumo por Escola - Ano: {$this->ano} / Mês: {$mes} ", 30, 78, 782, 80, $fonte, 12, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Total de Escolas: {$this->total}", 30, 95, 782, 80, $fonte, 10, $corTexto, 'center' );
		
		$this->pdf->linha_relativa(30, $altura += 100, 782, 0);
		
		$esquerda = 30;
		$altura = 30;
		$direita = 782;
		$tam_texto = 10;
		$altura = 130;

		$this->pdf->linha_relativa($esquerda, $altura, 0, 18);
		$this->pdf->escreve_relativo("Escola", $esquerda + 3, $altura+3, 200, 30, $fonte, $tam_texto);
		$this->pdf->linha_relativa($esquerda += 210, $altura, 0, 18);

		$this->pdf->escreve_relativo("Custo Total", $esquerda + 1, $altura+3, 100, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 102, $altura, 0, 18);
		
		$this->pdf->escreve_relativo("Custo por Refeição", $esquerda + 1, $altura+3, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 117, $altura, 0, 18);
		
		$this->pdf->escreve_relativo("Calorias Diárias/Aluno", $esquerda + 1, $altura+3, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 118, $altura, 0, 18);
		
		$this->pdf->escreve_relativo("Proteínas Diárias/Aluno", $esquerda + 1, $altura+3, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 117, $altura, 0, 18);
		
		$this->pdf->escreve_relativo("Agric. Familiar", $esquerda + 1, $altura+3, 115, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 118, $altura, 0, 18);

		$this->page_y +=19;

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

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();


?>
