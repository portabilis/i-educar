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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Ficha de Leitura, Escrita" );
		$this->processoAp = "825"; //alterar
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
	var $ref_cod_turma;

	var $ano;
	var $mes;
	var $mes_inicial;
	var $mes_final;

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;
	var $sequencial;
	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;
	var $nm_professor;
	var $nm_turma;
	var $nm_serie;
	var $nm_disciplina;

	var $page_y = 125;

	var $get_file;

	var $cursos = array();

	var $get_link;

	var $total;

	var $ref_cod_modulo;
	var $data_ini,$data_fim;

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
		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$fonte = 'arial';
		$corTexto = '#000000';

		if(empty($this->ref_cod_turma))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}
		//$this->mes = "5";//teste
		//$qtd_dias = 28;

		$this->pdf = new clsPDF("Ficha de Leitura, Escrita e Língua Oral", "Ficha de Leitura, Escrita e Língua Oral", "A4", "", false, false);
		$this->pdf->largura  = 842.0;
  		$this->pdf->altura = 595.0;

		$this->pdf->OpenPage();

		$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
		$det_escola = $obj_escola->detalhe();
		$this->nm_escola = $det_escola['nome'];

		$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
		$det_instituicao = $obj_instituicao->detalhe();
		$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		$obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
		$det_turma = $obj_turma->detalhe();
		$this->nm_turma = $det_turma['nm_turma'];

		$obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
		$det_serie = $obj_serie->detalhe();
		$this->nm_serie = $det_serie['nm_serie'];

		$this->addCabecalho();

		$this->pdf->escreve_relativo("Nº", 35, 245, 40, 20);
		$this->pdf->escreve_relativo("Nome do aluno", 65, 245, 80, 20);

		$this->pdf->escreve_relativo("Leitura", 355, 142, 80, 20);
		$this->pdf->escreve_relativo("Escrita", 490, 142, 80, 20);
		$this->pdf->escreve_relativo("Língua Oral", 665, 142, 80, 20);
		$this->pdf->escreve_relativo("Relaciona letra ao som", 490, 157, 70, 60, null, 10);


		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Não encadeia idéias em história representada por desenho", 8,90);
		$this->pdf->InsertJpng('png',$imagem,290,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Encadeia idéias em história representada por desenhos", 8,90);
		$this->pdf->InsertJpng('png',$imagem,344,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Lê palavras do suporte utilizado", 8,90);
		$this->pdf->InsertJpng('png',$imagem,395,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Lê frases do suporte utilizado", 8,90);
		$this->pdf->InsertJpng('png',$imagem,425,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Não relaciona letra com som", 8,90);
		$this->pdf->InsertJpng('png',$imagem,457,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("No seu próprio nome", 8,70);
		$this->pdf->InsertJpng('png',$imagem,485,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Palavras", 8,60);
		$this->pdf->InsertJpng('png',$imagem,522,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Frases", 8,60);
		$this->pdf->InsertJpng('png',$imagem,552,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Não escuta com atenção leitura feita pelo professor", 8,90);
		$this->pdf->InsertJpng('png',$imagem,578,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Escuta com atenção leitura feita pelo professor", 8,90);
		$this->pdf->InsertJpng('png',$imagem,635,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Comenta a história ouvida ou lida", 8,80);
		$this->pdf->InsertJpng('png',$imagem,700,250,1);
		//-----------------------------------------------------------------------

		//-----------------------------------------------------------------------
		$imagem = girarTextoImagem("Reconta a história ouvida ou lida", 8,80);
		$this->pdf->InsertJpng('png',$imagem,760,250,1);
		//-----------------------------------------------------------------------

		$this->pdf->linha_relativa(30, 140, 780, 0);
		$this->pdf->linha_relativa(30, 140, 0, 120);
		$this->pdf->linha_relativa(810, 140, 0, 120);
		$this->pdf->linha_relativa(30, 260, 780, 0);

		$this->pdf->linha_relativa(60, 140, 0, 120);
		$this->pdf->linha_relativa(285, 140, 0, 120);
		$this->pdf->linha_relativa(450, 140, 0, 120);
		$this->pdf->linha_relativa(570, 140, 0, 120);

		$this->pdf->linha_relativa(285, 155, 525, 0);

		$this->pdf->linha_relativa(340, 155, 0, 105);
		$this->pdf->linha_relativa(390, 155, 0, 105);
		$this->pdf->linha_relativa(420, 155, 0, 105);

		$this->pdf->linha_relativa(480, 180, 90, 0);
		$this->pdf->linha_relativa(480, 155, 0, 105);
		$this->pdf->linha_relativa(510, 180, 0, 80);
		$this->pdf->linha_relativa(540, 180, 0, 80);

		$this->pdf->linha_relativa(630, 155, 0, 105);
		$this->pdf->linha_relativa(690, 155, 0, 105);
		$this->pdf->linha_relativa(750, 155, 0, 105);

		$qtd_quebra = 21;
		$base = 245;
		$linha = 1;

		if ($this->is_padrao || $this->ano == 2007) {
			$this->semestre = null;
		}
		
		$obj_matricula = new clsPmieducarMatriculaTurma();
		$obj_matricula->setOrderby('nome_ascii');
		$lst_matricula = $obj_matricula->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array(1,2,3),null,null,$this->ano,null,true,null,null,1, null, null, null, null, $this->semestre);

		if(is_array($lst_matricula))
		{
			//for ($ordem = 1; $ordem <= $total_alunos; $ordem++)//foreach de alunos
			foreach ($lst_matricula as $ordem => $matricula)
			{
				$ordem++;
				$ordem = sprintf("%02d",$ordem);

				if($linha % $qtd_quebra == 0)
				{
					//nova pagina
					$this->pdf->ClosePage();
					$this->pdf->OpenPage();
					$base = 30;
					$linha = 0;
					$this->pdf->linha_relativa(30, 30, 780, 0);
					$qtd_quebra = 35;
				}
				$this->pdf->linha_relativa(30, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(60, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(30, ($base+15)+($linha*15), 780, 0);

				$this->pdf->escreve_relativo($ordem, 40, ($base+3)+($linha*15), 15, 15, null, 8);
				$this->pdf->escreve_relativo($matricula['nome'], 65, ($base+3)+($linha*15), 215, 15, null, 8);

				$this->pdf->linha_relativa(285, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(450, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(570, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(340, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(390, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(420, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(480, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(510, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(540, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(630, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(690, $base+($linha*15), 0, 15);
				$this->pdf->linha_relativa(750, $base+($linha*15), 0, 15);

				$this->pdf->linha_relativa(810, $base+($linha*15), 0, 15);//fim
				$linha++;
			}
		}
		//escrever total
		$this->pdf->linha_relativa(30, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(60, $base+($linha*15), 0, 15);

		$this->pdf->escreve_relativo("Total", 35, ($base+3)+($linha*15), 20, 15, null, 8);

		$this->pdf->linha_relativa(285, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(450, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(570, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(340, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(390, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(420, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(480, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(510, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(540, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(630, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(690, $base+($linha*15), 0, 15);
		$this->pdf->linha_relativa(750, $base+($linha*15), 0, 15);

		$this->pdf->linha_relativa(810, $base+($linha*15), 0, 15);//fim
		$this->pdf->linha_relativa(30, $base+(($linha+1)*15), 780, 0);

		$this->pdf->ClosePage();
		$this->pdf->CloseFile();
		$this->get_link = $this->pdf->GetLink();


		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";
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
		$this->pdf->escreve_relativo( date("d/m/Y"), 25, 30, 782, 80, $fonte, 10, $corTexto, 'right' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição:$this->nm_instituicao", 119, 52, 300, 80, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola:{$this->nm_escola}",132, 64, 300, 80, $fonte, 7, $corTexto, 'left' );


		$this->pdf->escreve_relativo( "Série:{$this->nm_serie}",136, 76, 300, 80, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Turma:{$this->nm_turma}",132, 88, 300, 80, $fonte, 7, $corTexto, 'left' );

		//titulo
		$this->pdf->escreve_relativo( "FICHA LEITURA E ESCRITA I", 30, 75, 782, 80, $fonte, 12, $corTexto, 'center' );

		//Data
		$this->pdf->escreve_relativo( "{$this->meses_do_ano[$this->mes]} DE $this->ano", 45, 100, 782, 80, $fonte, 10, $corTexto, 'center' );

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
