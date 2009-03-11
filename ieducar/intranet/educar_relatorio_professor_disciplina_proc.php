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
		$this->SetTitulo( " i-Educar - Rela&ccedil;&atilde;o Professores Disciplinas" );
		$this->processoAp = "827";
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
	var $ref_cod_curso;
	var $ref_cod_disciplina;

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


		$fonte = 'arial';
		$corTexto = '#000000';

		/*if(empty($this->ref_cod_turma))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}*/

		$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
		$det_instituicao = $obj_instituicao->detalhe();
		$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		if($this->ref_cod_escola)
		{

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];
		}

		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();
		$this->nm_curso = $det_curso['nm_curso'];

		if($this->ref_cod_disciplina)
			$where = " AND cod_disciplina = {$this->ref_cod_disciplina}";

		if($this->ref_cod_escola)
		{
			$sql = "SELECT cod_servidor
					       ,nome
					       ,sa.carga_horaria
					       ,CASE periodo
					         WHEN 1 THEN 'Matutino'
					         WHEN 2 THEN 'Vespertino'
						 ELSE 'Noturno'
					        END as turno
					       ,nm_disciplina
					  FROM pmieducar.servidor s
					       ,pmieducar.servidor_disciplina sd
					       ,pmieducar.servidor_alocacao   sa
					       ,pmieducar.disciplina
					       ,cadastro.pessoa
					 WHERE cod_servidor = sd.ref_cod_servidor
					   AND cod_servidor = sa.ref_cod_servidor
					   AND ref_cod_instituicao = sd.ref_ref_cod_instituicao
					   AND ref_cod_instituicao = sa.ref_ref_cod_instituicao
					   AND cod_disciplina = ref_cod_disciplina
					   AND cod_servidor   = idpes
					   AND ref_cod_instituicao = {$this->ref_cod_instituicao}
					   AND ref_cod_escola      = {$this->ref_cod_escola}
					   $where
					   AND sa.ativo = 1
					   AND s.ativo  = 1
					   ORDER BY nome
					          ,nm_disciplina

					";
		}
		else
		{

			/*$sql = "SELECT cod_servidor
					       ,nome
					       ,CAST(s.carga_horaria || ' hour' as interval) as carga_horaria
					       ,nm_disciplina
					  FROM pmieducar.servidor s
					       ,pmieducar.servidor_disciplina sd
					       ,pmieducar.disciplina
					       ,cadastro.pessoa
					 WHERE cod_servidor = sd.ref_cod_servidor
					   AND cod_servidor = idpes
					   AND ref_cod_instituicao = sd.ref_ref_cod_instituicao
					   AND cod_disciplina = ref_cod_disciplina
					   AND ref_cod_instituicao = {$this->ref_cod_instituicao}
					   $where
					   AND s.ativo  = 1
					 ORDER BY nome
					          ,nm_disciplina
					";*/
				$sql = "SELECT cod_servidor
					       ,nome
					       ,CAST(s.carga_horaria || ' hour' as interval) as carga_horaria
					       ,nm_disciplina
					       ,cod_disciplina
					       ,CASE periodo
										         WHEN 1 THEN 'Matutino'
										         WHEN 2 THEN 'Vespertino'
											     WHEN 3 THEN 'Noturno'
										        END as turno
					  FROM pmieducar.servidor s
					       ,pmieducar.servidor_disciplina sd
					       ,pmieducar.disciplina
					       ,cadastro.pessoa
					       ,pmieducar.servidor_alocacao sa
					 WHERE cod_servidor = sd.ref_cod_servidor
					   AND cod_servidor = idpes
					   AND ref_cod_instituicao = sd.ref_ref_cod_instituicao
					   AND cod_disciplina = ref_cod_disciplina
					   AND ref_cod_instituicao = {$this->ref_cod_instituicao}
					   $where
					   AND s.ativo  = 1
					   AND cod_servidor = sa.ref_cod_servidor
					 ORDER BY nome
					          ,nm_disciplina
					";
		}


		$db = new clsBanco();

		$db->Consulta($sql);

		$nm_disciplina = null;
		if($db->Num_Linhas())
		{

			$relatorio = new relatorios("Professores por Disciplina", 210, false, "Professores por Disciplina", "A4", "{$this->nm_instituicao}\n{$this->nm_escola}\n{$this->nm_curso}");
			$relatorio->setMargem(20,20,50,50);
			$relatorio->exibe_produzido_por = false;

			while ($db->ProximoRegistro())
			{
				$registro = $db->Tupla();

				if($registro['nm_disciplina'] != $nm_disciplina)
				{
					$relatorio->novalinha( array(  "{$registro['nm_disciplina']}"),0,16,true,"arial",array( 75, 330, 100),"#515151","#d3d3d3","#FFFFFF",false,false);

					if($this->ref_cod_escola)
					{
						$relatorio->novalinha( array(  "Matrícula","Nome", "Carga Horária", "Turno"),0,16,true,"arial",array( 75, 320, 100),"#515151","#d3d3d3","#FFFFFF",false,true);
					}
					else
					{
//						$relatorio->novalinha( array(  "Matrícula","Nome", "Carga Horária"),0,16,true,"arial",array( 75, 330, 100),"#515151","#d3d3d3","#FFFFFF",false,true);
						$relatorio->novalinha( array(  "Matrícula","Nome", "Carga Horária", "Turno"),0,16,true,"arial",array( 75, 320, 100),"#515151","#d3d3d3","#FFFFFF",false,true);
					}
					$nm_disciplina = $registro['nm_disciplina'];
				}
				$relatorio->novalinha( array(  "{$registro['cod_servidor']}","{$registro['nome']}", "{$registro['carga_horaria']}", "{$registro['turno']}"),0,16,false,"arial",array( 75, 330, 80),"#515151","#d3d3d3","#FFFFFF",false,false);
			}

			/*while ($db->ProximoRegistro())
			{
				$registro = $db->Tupla();

				if($registro['nm_disciplina'] != $nm_disciplina)
				{
					$relatorio->novalinha( array(  "{$registro['nm_disciplina']}"),0,16,true,"arial",array( 75, 330, 100),"#515151","#d3d3d3","#FFFFFF",false,false);

					if($this->ref_cod_escola)
					{
						$relatorio->novalinha( array(  "Matrícula","Nome", "Carga Horária", "Turno"),0,16,true,"arial",array( 75, 320, 100),"#515151","#d3d3d3","#FFFFFF",false,true);
					}
					else
					{
						
						$relatorio->novalinha( array(  "Matrícula","Nome", "Carga Horária"),0,16,true,"arial",array( 75, 330, 100),"#515151","#d3d3d3","#FFFFFF",false,true);
//						$relatorio->novalinha( array(  "Matrícula","Nome", "Carga Horária", "Turno"),0,16,true,"arial",array( 75, 320, 100),"#515151","#d3d3d3","#FFFFFF",false,true);
					}
					$nm_disciplina = $registro['nm_disciplina'];
				}
				/*if (!$this->ref_cod_escola)
				{
					$sql_turno = "SELECT CASE periodo
										         WHEN 1 THEN 'Matutino'
										         WHEN 2 THEN 'Vespertino'
											 ELSE 'Noturno'
										        END as turno
									  FROM
									  		pmieducar.servidor_alocacao
									  WHERE
									  		ref_cod_servidor = {$registro["cod_servidor"]}
									  		AND ativo = 1";
						$db2 = new clsBanco();
						$registro["turno"] = $db2->CampoUnico($sql_turno);
				}*/
//				$relatorio->novalinha( array(  "{$registro['cod_servidor']}","{$registro['nome']}", "{$registro['carga_horaria']}", "{$registro['turno']}"),0,16,false,"arial",array( 75, 330, 80),"#515151","#d3d3d3","#FFFFFF",false,false);
//			}

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
