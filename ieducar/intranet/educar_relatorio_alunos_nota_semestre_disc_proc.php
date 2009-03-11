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
require_once ("include/relatorio.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Rela&ccedil;&atilde;o de alunos/nota bimestres" );
		$this->processoAp = "811";
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
	var $ref_cod_curso;
	var $ref_cod_modulo;

	var $ano;
	
	var $is_padrao;
	var $semestre;

	var $cursos = array();

	var $get_link;


	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}
		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$this->ref_cod_modulo = explode("-",$this->ref_cod_modulo);
		$this->ref_cod_modulo = array_pop($this->ref_cod_modulo);

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

		if($this->ref_cod_escola){

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];

			$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		}

	     $obj_calendario = new clsPmieducarEscolaAnoLetivo();
	     $lista_calendario = $obj_calendario->lista($this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1,null);

	     $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
	     $det_turma = $obj_turma->detalhe();
	     $this->nm_turma = $det_turma['nm_turma'];

	     $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
	     $det_serie = $obj_serie->detalhe();
	     $this->nm_serie = $det_serie['nm_serie'];

		 $obj_pessoa = new clsPessoa_($det_turma["ref_cod_regente"]);
		 $det = $obj_pessoa->detalhe();
		 $this->nm_professor = $det["nome"];

	     if(!$lista_calendario)
	     {
	     	echo '<script>
	     			alert("Escola não possui calendário definido para este ano");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
	     }

		$obj = new clsPmieducarSerie();
		$obj->setOrderby('cod_serie,etapa_curso');
		$lista_serie_curso = $obj->lista(null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();
		$this->nm_curso = $det_curso['nm_curso'];

		$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao($det_curso['ref_cod_tipo_avaliacao']);
		$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
		$conceitual = $det_tipo_avaliacao['conceitual'];

		if ($this->is_padrao || $this->ano == 2007) {
			$this->semestre = null;
		}			
		
		$obj_matricula_turma = new clsPmieducarMatriculaTurma();
		$obj_matricula_turma->setOrderby('nome_ascii');
//		$lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula, $this->ref_cod_turma, null, null, null, null, null, null, 1, $this->ref_cod_serie, $this->ref_cod_curso, $this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array(1,2,3),null,null,$this->ano,null,null,null,null,true);
		$lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula, $this->ref_cod_turma, null, null, null, null, null, null, 1, $this->ref_cod_serie, $this->ref_cod_curso, $this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array(1,2,3),null,null,$this->ano,null,null,null,null,true, null, null, true, null, $this->semestre);
		//$obj_disciplinas = new clsPmieducarDisciplinaSerie();
		$obj_disciplinas = new clsPmieducarEscolaSerieDisciplina();
		$lst_disciplinas = $obj_disciplinas->lista($this->ref_cod_serie,$this->ref_cod_escola,null,1);

		if($lst_matricula_turma)
		{

			$relatorio = new relatorios("Espelho de Notas Bimestral {$this->ref_cod_modulo}º Bimestre Ano {$this->ano}", 210, false, "Espelho de Notas Bimestral", "A4", "{$this->nm_instituicao}\n{$this->nm_escola}\n{$this->nm_curso}\n{$this->nm_serie} -  Turma: $this->nm_turma             ".date("d/m/Y"));
			$relatorio->setMargem(20,20,50,50);
			$relatorio->exibe_produzido_por = false;


			//$relatorio->novalinha( array( "Cód. Aluno", "Nome do Aluno", "1Mº",  "M.Parcial", "Exame", "M.Final", "Faltas"),0,16,true,"arial",array( 75, 160, 120, 55, 50, 50),"#515151","#d3d3d3","#FFFFFF",false,true);
			//$relatorio->novalinha( array( "Cód. Aluno", "Nome do Aluno", "1Mº", "2Mº",  "M.Parcial", "Exame", "M.Final", "Faltas"),0,16,true,"arial",array( 75, 160, 60, 60, 55, 50, 50),"#515151","#d3d3d3","#FFFFFF",false,true);
			//$relatorio->novalinha( array( "Cód. Aluno", "Nome do Aluno", "1Mº", "2Mº", "3Mº",  "M.Parcial", "Exame", "M.Final", "Faltas"),0,16,true,"arial",array( 75, 160, 40, 40, 40, 55, 50, 50),"#515151","#d3d3d3","#FFFFFF",false,true);

			$db = new clsBanco();

			foreach ($lst_disciplinas as $disciplina)
			{
				$obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
				$det_disciplina = $obj_disciplina->detalhe();

				$array_disc[$det_disciplina['cod_disciplina']] = ($det_disciplina['abreviatura']);
				$array_cab[] = str2upper($det_disciplina['abreviatura']);
			}

			//if($conceitual)
			{
				asort($array_disc);
				sort($array_cab);
				$array_cab = array_merge(array( "Cód.", "Nome do Aluno"  ),$array_cab);
			}


			$divisoes = array( 40, 165 );
			$divisoes_texto = array( 40, 165 );

			if(!$conceitual)
				$tamanho_divisao = 32 + ( 10 - count($array_disc) ) * 5;
			else
				$tamanho_divisao = 23 + ( 15 - count($array_disc) ) * 5;
			for($ct=0;$ct<20;$ct++)
			{
				$divisoes[] = $tamanho_divisao;
				$divisoes_texto[] = $tamanho_divisao;
			}
			$relatorio->novalinha( $array_cab ,0,16,true,"arial",$divisoes,"#515151","#d3d3d3","#ffffff",false,true);

			if(!$conceitual)
			{
				$campo_nota = "COALESCE(nota,valor) ";
			}
			else
			{
				$campo_nota = "nome ";
			}

			if($conceitual)
			{
				$tam_fonte = 8;
				$tam_linha = 11;
			}
			else
			{
				$tam_fonte = null;
				$tam_linha = 16;
			}
			foreach ($lst_matricula_turma as $matricula)
			{

				$consulta = "SELECT ref_cod_disciplina
							        ,$campo_nota  as nota
							        ,modulo
							   FROM pmieducar.nota_aluno
							        LEFT OUTER JOIN
							        pmieducar.tipo_avaliacao_valores
							        ON ( ref_ref_cod_tipo_avaliacao = ref_cod_tipo_avaliacao
							 	    AND ref_sequencial              = sequencial )
							  WHERE ref_cod_matricula  = {$matricula['ref_cod_matricula']}
							    AND ref_cod_escola     = {$this->ref_cod_escola}
							    AND ref_cod_serie      = {$this->ref_cod_serie}
							    AND modulo 			   = {$this->ref_cod_modulo}
							    AND nota_aluno.ativo   = 1
						 	  GROUP BY ref_cod_disciplina
						 	        ,modulo
							        ,$campo_nota
							  ORDER BY ref_cod_disciplina ASC ";

				$db->Consulta($consulta);

				unset($notas);
				while ($db->ProximoRegistro())
				{
					$registro = $db->Tupla();

					$notas[$registro['ref_cod_disciplina']] = $registro['nota'];

				}

				if( strlen( $matricula['nome'] ) > 24 )
				{
					$matricula['nome'] = explode(" ",$matricula['nome']);
					if(is_array($matricula['nome'] ))
					{
						$nome_aluno = array_shift($matricula['nome']);
					}
					if(is_array($matricula['nome'] ))
					{
						$nome_aluno .= " ".array_shift($matricula['nome']);
					}
					if(is_array($matricula['nome'] ))
					{
						$nome_aluno .= " ".array_pop($matricula['nome']);
					}
					$matricula['nome'] = $nome_aluno;
				}

				unset($array_val);
				$array_val = array();
				$array_val[] = $matricula['ref_cod_aluno'];
				$array_val[] = $matricula['nome'];
				foreach ($array_disc as $cod_disc => $disc)
				{
					if(!$conceitual)
						$array_val[] = $notas[$cod_disc] ? number_format( $notas[$cod_disc] ,2,'.','') : $notas[$cod_disc];
					else
						$array_val[] = $notas[$cod_disc];

				}

				$relatorio->novalinha($array_val,0,$tam_linha,false,"arial",$divisoes_texto,"#515151","#d3d3d3","#FFFFFF",false,true,null,$tam_fonte);

			}

			$this->get_link = $relatorio->fechaPdf();
		}


		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";
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
