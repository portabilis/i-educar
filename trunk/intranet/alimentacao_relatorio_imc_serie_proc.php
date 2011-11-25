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

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relatório - IMC por Série" );
		$this->processoAp = "10008";
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
	var $sexo;
	var $ref_escola;
	
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
		$filtro_anoescola = "";
		$filtro_sexo = "";
		if (is_numeric($this->ref_escola))
			$filtro_anoescola .= " AND a.ref_escola = {$this->ref_escola} ";
		if (is_numeric($this->ano))
			$filtro_anoescola .= " AND date_part('year', a.dt_cadastro) = {$this->ano} ";
		if ($this->sexo!="ambos")
			$filtro_sexo .= " AND c.sexo = '{$this->sexo}' ";
					
		$sql = "select ref_serie, nm_serie, (sum(imc)/count(1)) as imc from alimentacao.imc a
				join
				pmieducar.serie s
				on s.cod_serie=a.ref_serie {$filtro->anoescola}
				join
				(
				select cod_aluno, sexo from pmieducar.aluno a join cadastro.fisica f
				on a.ref_idpes=f.idpes
				) c
				on c.cod_aluno=a.ref_aluno {$filtro->sexo}
				group by ref_serie, nm_serie
				order by nm_serie
				";
		$db = new clsBanco();
		$db->Consulta($sql);

		$this->total = $db->Num_Linhas();
		if ($this->total > 0) 
		{
			
			$this->nm_instituicao = "Prefeitura X";
			$this->pdf = new clsPDF("IMC por Série - {$this->ano}", "IMC por Série", "A4", "", false, false);
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
			 	list($ref_serie,$serie, $imc) = $db->Tupla();
			 	
			 	$esquerda_aux = $esquerda = 30;
			 	$this->pdf->linha_relativa($esquerda, $altura+=18, 0, 18);
			 	$this->pdf->linha_relativa($esquerda, $altura, $direita, 0);
			 	$this->pdf->escreve_relativo($serie, $esquerda + 3, $altura + $altura_escrita, 600, 30, $fonte, $tam_texto, $corTexto);
			 	$this->pdf->linha_relativa($esquerda += 650, $altura, 0, 18);

			 	$this->pdf->escreve_relativo(number_format($imc,2,",",""), $esquerda + 1, $altura + $altura_escrita, 132, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 132, $altura, 0, 18);
			 	
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

		if($this->ref_escola=="")
		{
			$escola = "todas";
		}
		else
		{
			$obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_escola );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$escola = $det_ref_cod_escola["nome"];
		}
		
		$this->pdf->escreve_relativo( "IMC por Série - Ano: {$this->ano} / Escola: {$escola} / Sexo: {$this->sexo}", 30, 78, 782, 80, $fonte, 12, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Total de Séries: {$this->total}", 30, 95, 782, 80, $fonte, 10, $corTexto, 'center' );
		
		$this->pdf->linha_relativa(30, $altura += 100, 782, 0);
		
		$esquerda = 30;
		$altura = 30;
		$direita = 782;
		$tam_texto = 10;
		$altura = 130;

		$this->pdf->linha_relativa($esquerda, $altura, 0, 18);
		$this->pdf->escreve_relativo("Série", $esquerda + 3, $altura+3, 600, 30, $fonte, $tam_texto);
		$this->pdf->linha_relativa($esquerda += 650, $altura, 0, 18);

		$this->pdf->escreve_relativo("IMC", $esquerda + 1, $altura+3, 130, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 132, $altura, 0, 18);

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
