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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relat&oacute;rio de alunos por idade" );
		$this->processoAp = "836";
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

		/*if(empty($this->ref_cod_turma))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}*/

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

	     if(!$lista_calendario)
	     {
	     	echo '<script>
	     			alert("Escola não possui calendário definido para este ano");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
	     }



		if($this->ref_cod_turma)
			$where = "	AND ref_cod_turma = {$this->ref_cod_turma}";

		if($this->ref_cod_serie)
			$where .= "	AND ref_ref_cod_serie   = {$this->ref_cod_serie}";
	
			//DISTINCT extract( year from ( age( now(), data_nasc ) ) ) as idade
		$sql = "SELECT EXTRACT(YEAR FROM age( to_date ( EXTRACT( year from now() )   || '-12-31' ,'yyyy-mm-dd') , data_nasc) ) as idade
				  FROM pmieducar.matricula_turma mt
				       ,pmieducar.matricula      m
				       ,pmieducar.aluno          a
				       ,cadastro.fisica          f
				 WHERE cod_matricula = ref_cod_matricula
				   AND mt.ativo = 1
				   AND m.ativo  = 1
				   AND m.ref_cod_aluno = a.cod_aluno
				   AND f.idpes = a.ref_idpes
				   $where
				   AND ref_ref_cod_escola  = {$this->ref_cod_escola}
				   ANd ref_cod_curso       = {$this->ref_cod_curso}
				   AND data_nasc IS NOT NULL
				   AND m.ano = {$this->ano}
				 ORDER BY idade";
		$db = new clsBanco();

		$db->Consulta($sql);

		$array_idades = array();
		if($db->Num_Linhas())
		{
			while($db->ProximoRegistro())
			{
				$registro = $db->Tupla();
				$array_idades_[$registro['idade']] = "-";
				$array_idades[$registro['idade']] = $registro['idade'];
			}
		}
		else
		{
			echo '<script>
	     			alert("Nenhum aluno está matriculado");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;;
		}

		if($array_idades)
		{

			$relatorio = new relatorios("Relatório de alunos por idades       Ano - {$this->ano}", 210, false, "Relatório de alunos por idade", "A4", "{$this->nm_instituicao}\n{$this->nm_escola}\n\n".date("d/m/Y"));
			$relatorio->setMargem(20,20,50,50);
			$relatorio->exibe_produzido_por = false;


			$array_cab = array_merge(array( "Série", "Turma"  ),$array_idades);
			$array_cab[] = "Total";

			$divisoes = array( 100, 80 );
			$divisoes_texto = array( 100, 80 );


			$tamanho_divisao = 23 + ( 15 - count($array_idades) -1 ) * 2;


			for($ct=0;$ct<20;$ct++)
			{
				$divisoes[] = $tamanho_divisao;
				$divisoes_texto[] = $tamanho_divisao;
			}

			$relatorio->novalinha( $array_cab ,0,16,true,"arial",$divisoes,"#515151","#d3d3d3","#ffffff",false,true);


			$db = new clsBanco();

			$obj_curso = new clsPmieducarCurso();
			$lst_curso = $obj_curso->lista($this->ref_cod_curso,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao);

			if($lst_curso)
			{

				foreach ($lst_curso as $curso)
				{
					$relatorio->novalinha( array($curso['nm_curso']) ,0,16,true,"arial",false,"#515151","#d3d3d3","#ffffff",false,true);

					$obj = new clsPmieducarSerie();
					$obj->setOrderby('cod_serie,etapa_curso');
					$lista_serie_curso = $obj->lista($this->ref_cod_serie,null,null,$curso['cod_curso'],null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

					if($lista_serie_curso)
					{
						foreach ($lista_serie_curso as $serie)
						{
							$obj_turma = new clsPmieducarTurma();
							$lst_turma = $obj_turma->lista($this->ref_cod_turma,null,null,$serie['cod_serie'],$this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1,null,null,null,null,null,null,null,null,null,$curso['cod_curso'],$this->ref_cod_instituicao);

							if($lst_turma)
							{
								foreach ($lst_turma as $turma)
								{
									$array_idades = $array_idades_;

									$total = 0;

									$sql = "SELECT count(1) as total
		       									   ,EXTRACT(YEAR FROM age( to_date ( EXTRACT( year from now() )   || '-12-31' ,'yyyy-mm-dd') , data_nasc) ) as idade
											  FROM pmieducar.matricula_turma mt
											       ,pmieducar.matricula      m
											       ,pmieducar.aluno          a
											       ,cadastro.fisica          f
											 WHERE cod_matricula = ref_cod_matricula
											   AND mt.ativo = 1
											   AND m.ativo  = 1
											   AND m.ref_cod_aluno = a.cod_aluno
											   AND f.idpes = a.ref_idpes
											   AND ref_cod_turma = {$turma['cod_turma']}
											   AND ref_ref_cod_serie   = {$serie['cod_serie']}
											   AND ref_ref_cod_escola  = {$this->ref_cod_escola}
											   ANd ref_cod_curso       = {$curso['cod_curso']}
											   AND m.ano = {$this->ano}
											   AND aprovado IN (1,2,3)
											   
											   AND data_nasc IS NOT NULL
											 GROUP BY EXTRACT(YEAR FROM age( to_date ( EXTRACT( year from now() )   || '-12-31' ,'yyyy-mm-dd') , data_nasc) )
											 ORDER BY idade";

									$db->Consulta($sql);

									if($db->Num_Linhas())
									{
										while ($db->ProximoRegistro())
										{
											$registro = $db->Tupla();

											$array_idades[$registro['idade']] = $registro['total'];

											$total += $registro['total'];

										}

										$valores = array_merge(array($serie['nm_serie'],$turma['nm_turma']),$array_idades,array($total));

										$relatorio->novalinha( $valores ,0,16,false,"arial",$divisoes,"#515151","#d3d3d3","#ffffff",false,true);

									}

								}
							}
						}
					}
				}
			//$relatorio->quebraPagina();

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
