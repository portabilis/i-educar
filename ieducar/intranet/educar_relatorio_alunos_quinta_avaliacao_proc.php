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
		$this->processoAp = "807";
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

	var $ano;

	var $cursos = array();

	var $get_link;

	var $media; 
	var $media_exame;

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
		
		if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_turma) && is_numeric($this->ano))
		{
			$obj_ref_cod_curso = new clsPmieducarCurso( $this->ref_cod_curso );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$nm_curso = $det_ref_cod_curso["nm_curso"];
			$padrao_ano_escolar = $det_ref_cod_curso["padrao_ano_escolar"];
			if ($padrao_ano_escolar)
			{
				$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
//				$lst_ano_letivo = $obj_ano_letivo->lista( $this->ref_cod_escola,$this->ano,null,null,1,null,null,null,null,1 );
				$lst_ano_letivo = $obj_ano_letivo->lista( $this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1 );
				if ( is_array($lst_ano_letivo) )
				{
					$det_ano_letivo = array_shift($lst_ano_letivo);
					$ano_letivo = $det_ano_letivo["ano"];

					$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
					$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista( $ano_letivo,$this->ref_cod_escola );
					if ( is_array($lst_ano_letivo_modulo) )
					{
						$qtd_modulos = count($lst_ano_letivo_modulo);
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
			}
			else
			{
				$obj_turma_modulo = new clsPmieducarTurmaModulo();
				$lst_turma_modulo = $obj_turma_modulo->lista( $registro["ref_cod_turma"] );
				if ( is_array($lst_turma_modulo) )
				{
					$qtd_modulos = count($lst_turma_modulo);
				}
			}
			if ($this->ano == date("Y"))
			{
				$sql = "SELECT 
							m.cod_matricula,
							(SELECT 
								nome 
							 FROM 
							 	pmieducar.aluno al, 
							 	cadastro.pessoa 
							 WHERE
								al.cod_aluno = m.ref_cod_aluno 
								AND al.ref_idpes = pessoa.idpes) as nome
						FROM 
							pmieducar.matricula m,
							 pmieducar.matricula_turma mt
						WHERE 
							mt.ref_cod_turma = {$this->ref_cod_turma}
							AND mt.ref_cod_matricula = m.cod_matricula AND m.aprovado = 3
							AND mt.ativo = 1 AND m.ativo = 1
							AND m.modulo > {$qtd_modulos}
							AND m.ano = {$this->ano}
						ORDER BY 
							nome";
			}
			else 
			{
				$sql = "SELECT 
							m.cod_matricula,
							(SELECT 
								nome 
							 FROM 
							 	pmieducar.aluno al, 
							 	cadastro.pessoa 
							 WHERE
								al.cod_aluno = m.ref_cod_aluno 
								AND al.ref_idpes = pessoa.idpes) as nome
						FROM 
							 pmieducar.matricula m,
							 pmieducar.matricula_turma mt
						WHERE 
							mt.ref_cod_turma = {$this->ref_cod_turma}
							AND mt.ref_cod_matricula = m.cod_matricula 
							AND m.aprovado IN (1, 2, 3)
							AND mt.ativo = 1 AND m.ativo = 1
							AND m.modulo > {$qtd_modulos}
							AND m.ano = {$this->ano}
						ORDER BY 
							nome";
			}
			$db = new clsBanco();
			$db->Consulta($sql);
			if ($db->Num_Linhas())
			{
				$alunos = array();
				$obj_disciplinas = new clsPmieducarEscolaSerieDisciplina();
				$obj_disciplinas->setOrderby("nm_disciplina");
				$obj_disciplinas->setCamposLista("cod_disciplina, nm_disciplina");
				$lst_disciplinas = $obj_disciplinas->lista($this->ref_cod_serie, $this->ref_cod_escola, null, 1, true);
				
				$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
				$obj_curso->setCamposLista("media, media_exame, nm_curso");
				$det_curso = $obj_curso->detalhe();
				$this->media = $det_curso["media"];
				$this->media_exame = $det_curso["media_exame"];
				
				$relatorio = new relatorios("Relação de alunos em 5ª avaliação", 210, false, "Relação de alunos em 5ª avaliação", "A4", "{$this->nm_instituicao}\n{$this->nm_escola}\n{$this->nm_curso}\n{$this->nm_serie} -  Turma: $this->nm_turma         ".date("d/m/Y"));
				$relatorio->setMargem(20,20,20,20);

				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
				$nm_escola  = $obj_escola->detalhe();
				$nm_escola  = $nm_escola["nome"];
				$nm_curso   = $det_curso["nm_curso"];
				
				$obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
				$obj_serie->setCamposLista("nm_serie");
				$det_serie = $obj_serie->detalhe();
				$nm_serie  = $det_serie["nm_serie"];
				
				$obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
				$obj_turma->setCamposLista("nm_turma");
				$det_turma = $obj_turma->detalhe();
				$nm_turma  = $det_turma["nm_turma"];
				
				$relatorio->novalinha(array("Nome Escola: {$nm_escola}    Ano: {$this->ano}"), 0, 12, true, 'arial', false, "#000000", "#d3d3d3", "#FFFFFF", false, true);				
				$relatorio->novalinha(array("Curso: {$nm_curso}    Ano/Série: {$nm_serie}    Turma: {$nm_turma}    Date: ".date("d/m/Y")), 0, 12, true, 'arial', false, "#000000", "#d3d3d3", "#FFFFFF", false, true);				
				$relatorio->novalinha( array("Matrícula", "Nome Aluno", "Disciplinas", "Pontos", "Nota 5º Av. Passar"),0,12,true,"arial",array( 50, 200, 150, 50),"#515151","#d3d3d3","#FFFFFF",false,true);
				
				while ($db->ProximoRegistro())
				{
					list($cod_matricula, $nome_aluno) = $db->Tupla();
					foreach ($lst_disciplinas as $disciplina) 
					{
						$obj_nota_aluno = new clsPmieducarNotaAluno();
						$obj_nota_aluno->setOrderby("modulo ASC");
						$lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, $this->ref_cod_serie, $this->ref_cod_escola, $disciplina["cod_disciplina"], $cod_matricula, null, null, null, null, null, null, 1);
						$aluno_notas = array();
						$aluno_notas_normal = array();
						if (is_array($lst_nota_aluno))
						{
							$aluno_notas[$disciplina["cod_disciplina"]] = 0;
							foreach ($lst_nota_aluno as $nota_aluno)
							{
								$obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores($nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"]);
								$det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
								$aluno_notas[$disciplina["cod_disciplina"]] += $det_avaliacao_valores["valor"];  
							}
							$aluno_notas_normal[$disciplina["cod_disciplina"]] = $aluno_notas[$disciplina["cod_disciplina"]];
							$aluno_notas[$disciplina["cod_disciplina"]] /= count($lst_nota_aluno);
							$aluno_notas[$disciplina["cod_disciplina"]] = sprintf("%01.1f",$aluno_notas[$disciplina["cod_disciplina"]]);
						}
						
						if (is_array($aluno_notas))
						{
							foreach ($aluno_notas as $cod_disciplina => $media) 
							{
								if ($media < $this->media && $this->media_exame)
								{
									//FÓRMULA: 30-(SOMA DE PONTOS DOS 4 BIMESTRES)/2.Ex: 30-23/2=3,5
									$nota_necessaria_passar = (30 - $aluno_notas_normal[$cod_disciplina]) / 2;
									$relatorio->novalinha( array($cod_matricula, $nome_aluno, $disciplina["nm_disciplina"], $aluno_notas_normal[$cod_disciplina], $nota_necessaria_passar),0,12,false,"arial",array( 50, 200, 150, 50),"#515151","#d3d3d3","#FFFFFF",false,true);
								}
							}
						}
					}
				}
				$this->get_link = $relatorio->fechaPdf();

				echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";
		
				echo "<html><center>Se o download não iniciar automaticamente <br /><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
					<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
		
					Clique na Imagem para Baixar o instalador<br><br>
					<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
					</span>
					</center>";
			} 
			else 
			{
				echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');}</script>";
				echo "Nenhum aluno está em exame";
			}
		}
		
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