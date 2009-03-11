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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Registro de Matr&iacute;culas" );
		$this->processoAp = "830";
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


	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_serie;
	var $ref_cod_curso;
	var $ref_ref_cod_serie;
	var $ref_cod_turma;
	var $ref_cod_matricula;
	
	var $ano;

	var $nm_escola;
	var $nm_instituicao;
	var $nm_curso;

	var $pdf;

	var $page_y = 139;

	var $get_link;

	var $campo_assinatura;
	
	var $total = 0;

	var $meses_do_ano = array(
							 "1" => "JANEIRO"
							,"2" => "FEVEREIRO"
							,"3" => "MARÇO"
							,"4" => "ABRIL"
							,"5" => "MAIO"
							,"6" => "JUNHO"
							,"7" => "JULHO"
							,"8" => "AGOSTO"
							,"9" => "SETEMBRO"
							,"10" => "OUTUBRO"
							,"11" => "NOVEMBRO"
							,"12" => "DEZEMBRO"
						);
						
	var $is_padrao;
	var $semestre;

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;
			}
		}
		if ($this->is_padrao || $this->ano == 2007)
			$this->semestre = null;
		
		$fonte = 'arial';
		$corTexto = '#000000';
		$where = "";
		if (is_numeric($this->ref_cod_curso))
			$where .= " AND m.ref_cod_curso = {$this->ref_cod_curso} ";
		if (is_numeric($this->ref_ref_cod_serie))
			$where .= " AND m.ref_ref_cod_serie = {$this->ref_ref_cod_serie} ";
		if (is_numeric($this->ref_cod_turma))
			$where .= " AND mt.ref_cod_turma = {$this->ref_cod_turma} ";
		if (is_numeric($this->ref_cod_escola))
			$where .= " AND m.ref_ref_cod_escola = {$this->ref_cod_escola} ";
		if (is_numeric($this->ref_cod_matricula))
			$where .= " AND m.cod_matricula = {$this->ref_cod_matricula} ";
		if (is_numeric($this->semestre))
			$where .= " AND m.semestre = {$this->semestre} ";
			
		$sql = "SELECT
			   	 p.nome
			   	 ,s.nm_serie
			   	 ,t.nm_turma
			   	 ,ab.nm_beneficio
			   	 ,m.ref_ref_cod_escola
			FROM
				  pmieducar.matricula_turma mt
				 , pmieducar.matricula 	  m
				 , pmieducar.aluno 	  a
				 , pmieducar.aluno_beneficio ab
				 , cadastro.pessoa	  p
				 , pmieducar.serie    s
				 , pmieducar.instituicao i
				 , pmieducar.escola e
				 , pmieducar.turma  t
			WHERE
				i.cod_instituicao = {$this->ref_cod_instituicao}
				AND i.cod_instituicao = e.ref_cod_instituicao
				AND e.cod_escola = m.ref_ref_cod_escola
				AND m.cod_matricula = mt.ref_cod_matricula
				AND m.ref_cod_aluno = a.cod_aluno
				AND a.ref_idpes	     = p.idpes
				AND mt.ativo 	= 1
				AND m.ativo     = 1
				AND s.cod_serie = m.ref_ref_cod_serie
				AND ab.cod_aluno_beneficio = a.ref_cod_aluno_beneficio
				AND mt.ref_cod_turma = t.cod_turma
				AND a.ref_cod_aluno_beneficio IS NOT NULL
				AND m.ano = {$this->ano}
				{$where}
			ORDER BY
				m.ref_ref_cod_escola ASC,
				s.nm_serie,
				t.nm_turma,
				to_ascii(t.nm_turma) ASC";
		$db = new clsBanco();
		$db->Consulta($sql);

		$this->total = $db->Num_Linhas();
		if ($this->total > 0) 
		{
			$obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao["nm_instituicao"];
			$this->pdf = new clsPDF("Relação dos Alunos Beneficiados - {$this->ano}", "Relação dos Alunos Beneficiados", "A4", "", false, false);
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
			 	list($nome, $nm_serie, $nm_turma, $beneficio, $ref_cod_escola) = $db->Tupla();
			 	if (is_numeric($ref_cod_escola) && $ref_cod_escola_aux != $ref_cod_escola)
			 	{
			 		$obj_escola = new clsPmieducarEscola($ref_cod_escola);
					$obj_escola = $obj_escola->detalhe();
					$nome_escola = $obj_escola['nome'];
					$ref_cod_escola_aux = $ref_cod_escola;
			 	}
			 	$esquerda_aux = $esquerda = 30;
			 	$this->pdf->linha_relativa($esquerda, $altura+=18, 0, 18);
			 	$this->pdf->linha_relativa($esquerda, $altura, $direita, 0);
			 	$this->pdf->escreve_relativo($nome, $esquerda + 3, $altura + $altura_escrita, 300, 30, $fonte, $tam_texto, $corTexto);
			 	$this->pdf->linha_relativa($esquerda += 200, $altura, 0, 18);

			 	$this->pdf->escreve_relativo($nm_serie, $esquerda + 1, $altura + $altura_escrita, 70, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 70, $altura, 0, 18);
			 	$this->pdf->escreve_relativo($nm_turma, $esquerda + 1, $altura + $altura_escrita, 80, 30, $fonte, $tam_texto, $corTexto, 'center');
			 	$this->pdf->linha_relativa($esquerda += 80, $altura, 0, 18);
			 	$this->pdf->escreve_relativo($nome_escola, $esquerda + 3, $altura + $altura_escrita, 250, 30, $fonte, $tam_texto, $corTexto);
			 	$this->pdf->linha_relativa($esquerda += 250, $altura, 0, 18);
			 	$this->pdf->escreve_relativo($beneficio, $esquerda + 3, $altura + $altura_escrita, 300, 30, $fonte, $tam_texto);
			 	$this->pdf->linha_relativa($esquerda += 182, $altura, 0, 18);
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
	     					alert("No momento nenhum aluno possui benefício");
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
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 782, 80, $fonte, 18, $corTexto, 'center' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 745, 30, 100, 80, $fonte, 12, $corTexto, 'left' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição: $this->nm_instituicao", 120, 52, 300, 80, $fonte, 9, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Relação dos Alunos Beneficiados - {$this->ano}", 30, 78, 782, 80, $fonte, 12, $corTexto, 'center' );

		$this->pdf->escreve_relativo( "Total de Benefícios: {$this->total}", 30, 95, 782, 80, $fonte, 10, $corTexto, 'center' );
		
		$this->pdf->linha_relativa(30, $altura += 100, 782, 0);
		
		$esquerda = 30;
		$altura = 30;
		$direita = 782;
		$tam_texto = 10;
		$altura = 130;

		$this->pdf->linha_relativa($esquerda, $altura, 0, 18);
		$this->pdf->escreve_relativo("Nome do Aluno", $esquerda + 3, $altura+3, 150, 30, $fonte, $tam_texto);
		$this->pdf->linha_relativa($esquerda += 200, $altura, 0, 18);

		$this->pdf->escreve_relativo("Série", $esquerda + 1, $altura+3, 70, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 70, $altura, 0, 18);

		$this->pdf->escreve_relativo("Turma", $esquerda + 1, $altura+3, 80, 30, $fonte, $tam_texto, $corTexto, 'center');
		$this->pdf->linha_relativa($esquerda += 80, $altura, 0, 18);

		$this->pdf->escreve_relativo("Escola", $esquerda + 3, $altura+3, 70, 30, $fonte, $tam_texto, $corTexto);
		$this->pdf->linha_relativa($esquerda += 250, $altura, 0, 18);

		$this->pdf->escreve_relativo("Benefício", $esquerda + 3, $altura + 3, 150, 30, $fonte, $tam_texto);
		$this->pdf->linha_relativa($esquerda += 182, $altura, 0, 18);

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
