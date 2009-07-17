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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Demonstrativo de alunos defasados idade/s&eacute;rie" );
		$this->processoAp = "944";
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
	var $ano;
	var $mes;

	var $nm_escola;
	var $nm_instituicao;
	var $qt_anos = 11;
	var $idade_inicial = 6 ;

	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $get_link = false;
	var $cursos = array();

	var $array_ano_idade = array();

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

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}

		if(empty($this->cursos))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhum curso selecionado!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}

		//Dias previstos do mes
	     // Qual o primeiro dia do mes
	     $primeiroDiaDoMes = mktime(0,0,0,$this->mes,1,$this->ano);
	     // Quantos dias tem o mes
	     $NumeroDiasMes = date('t',$primeiroDiaDoMes);

	     //informacoes primeiro dia do mes
		 $dateComponents = getdate($primeiroDiaDoMes);

	     // What is the name of the month in question?
	     $NomeMes = $mesesDoAno[$dateComponents['mon']];

	     // What is the index value (0-6) of the first day of the
	     // month in question.
	     $DiaSemana = $dateComponents['wday'];

		$cursos_in = '';
		$conc = '';
		foreach ($this->cursos as $curso)
		{
			$cursos_in .= "{$conc}{$curso}";
			$conc = ",";
		}

		$db= new clsbanco();
		$consulta = "SELECT (SELECT coalesce(min(s.idade_inicial),0) as min
								  FROM pmieducar.serie  s
								       ,pmieducar.turma t
								 WHERE s.cod_serie     = t.ref_ref_cod_serie
								   AND s.ref_cod_curso in ($cursos_in )) as min
								,
								(SELECT coalesce(max(s.idade_final),0)  as max
								  FROM pmieducar.serie  s
								 WHERE s.ref_cod_curso in ( $cursos_in)) as max";

		$db->Consulta($consulta);
		$db->ProximoRegistro();
		$max_min = $db->Tupla();


		$consulta = "SELECT distinct
					       coalesce(s.idade_inicial,0) as min
					       ,coalesce(s.idade_final,0)  as max
					  FROM pmieducar.serie  s
					 WHERE  s.ref_cod_curso in ( $cursos_in )";


		$faixa_min_max = array();

		$db->Consulta($consulta);
		while($db->ProximoRegistro())
			$numeros[] = $db->Tupla();

		$faixa_min_max = array($numeros[0][0],$numeros[count($numeros)-1][1]);

		$consulta2 = "SELECT distinct
					         s.idade_inicial
					    FROM pmieducar.serie  s
					   WHERE  s.ref_cod_curso in ( $cursos_in )

			   		   UNION

					  SELECT distinct
					         s.idade_final
					    FROM pmieducar.serie  s
					   WHERE s.ref_cod_curso in ( $cursos_in ) ";


		$idades = array();

		$db->Consulta($consulta2);
		while($db->ProximoRegistro())
			$idades[] = array_shift($db->Tupla());

		$consulta3 = "SELECT distinct
					         s.idade_inicial
					    FROM pmieducar.serie  s
					   WHERE s.ref_cod_curso in ( $cursos_in )";


		$db->Consulta($consulta3);
		while($db->ProximoRegistro())
			$faixa[] = $db->Tupla();


		$ultima_idade = null;
		while(sizeof($idades))
		{

			$idade = array_shift($idades);
			if($idade == $faixa_min_max[0])
			{
				$ultima_idade = array_shift($idades);
				$this->array_ano_idade[] = array('ano' => ($this->ano - $idade) . " - " .($this->ano -$ultima_idade),'idade' => $idade . " - " . $ultima_idade ,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
				if(!empty($idades))
				{
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 1,'idade' => $ultima_idade + 1,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 2,'idade' => $ultima_idade + 2,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 3,'idade' => $ultima_idade + 3,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$ultima_idade = $ultima_idade + 3;
					while($ultima_idade > $idades[0] + 3)
						$ultima_idade = array_shift($idades);
				}elseif(sizeof($this->array_ano_idade) == 1)
				{
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 1,'idade' => $ultima_idade + 1,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 2,'idade' => $ultima_idade + 2,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$this->array_ano_idade[] = array('ano' => $this->ano - $ultima_idade - 3,'idade' => $ultima_idade + 3,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$ultima_idade = $ultima_idade + 3;
				}

			}

			foreach ($faixa as $key => $value)
			{

				for($ct = $ultima_idade + 1;$ct<= $idade + 3;$ct++)
				{
					$this->array_ano_idade[] = array('ano' => $this->ano - $ct,'idade' => ((sizeof($idades) === 0 && $ct == $idade + 3) ? "" : "" ).$ct,'total_serie' => 0,'total_geral' => 0,'total_geral_ambos' => 0);
					$ultima_idade = $ct;
				}

				break;

			}
			$ultima_idade = idade > $ultima_idade ? $idade : $ultima_idade;
		}

		$altura2 = 300;
		$altura = 50;

		$expande = 24;

		$flag_defasado = 1;

		$fonte = 'arial';
		$corTexto = '#000000';

		$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);

		$det_escola = $obj_escola->detalhe();
		$this->nm_escola = $det_escola["nome"];
		if ($det_escola) {
			$this->pdf = new clsPDF("Demonstrativo de Alunos Defasados Nominal Idade/Série - {$this->ano}", "Demonstrativo de Alunos Defasados Nominal Idade/Série - {$this->ano}", "A4", "", false, false);
			$obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

			$this->pdf->OpenPage();
			$this->addCabecalho();
			$this->novaPagina();

			$altura2 = 300;
			$altura = 30;
			$direita = $this->pdf->largura - 30 * 2;
			$expande = 24;
			$esquerda = 30;
			$inicio_escrita = 125 + $altura;
			$fonte = 'arial';
			$corTexto = '#000000';

			$flag_defasado = 1;

			$total_geral_masc_serie = 0;
			$total_geral_fem_serie = 0;
			$total_geral_anee = 0;
			$total_geral_nao_anee = 0;
			$total_geral_alf = 0;
			$total_geral_nao_alf = 0;
			foreach ($this->cursos as $curso) {

				$obj = new clsPmieducarSerie();
				$obj->setOrderby("idade_inicial,idade_final");
				$lista_serie_curso = $obj->lista(null,null,null,$curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

				$total_curso_masc_serie = 0;
				$total_curso_fem_serie = 0;
				$total_curso_anee = 0;
				$total_curso_nao_anee = 0;
				$total_curso_alf = 0;
				$total_curso_nao_alf = 0;

				if($lista_serie_curso) {

					foreach ($lista_serie_curso as $serie)
					{
						$defasados = array();
						$flagTurmas = 0;
						$flagAlunos = 0;

						$obj_matricula = new clsPmieducarMatricula();
						$lista_matricula_serie = $obj_matricula->lista(null,null,$this->ref_cod_escola,$serie['cod_serie'],null,null,null,array(1,2,3),null,null,null,null,1,$this->ano,$curso,$this->ref_cod_instituicao,null,null,null,$ct_analfabeto,null,null,null,null,null,null,null,$this->mes,true);
						if($lista_matricula_serie)
						{
							foreach ($lista_matricula_serie as $matricula)
							{
								if($matricula['data_nasc'])
								{
									$ano_nasc = explode("-",$matricula['data_nasc']);
									$idade_aluno = $this->ano - $ano_nasc[0];
								}
								foreach ($this->array_ano_idade as $key => $value)
								{
									if(strpos($value['idade'],"-") && is_numeric($idade_aluno))
									{
										$idade = explode("-",$value['idade'] );

										if( ( $idade_aluno > $serie['idade_final'] + $flag_defasado
											&& $idade_aluno >= $idade[0]
											&& $idade_aluno <= $idade[1])
											||
											($idade_aluno > $serie['idade_final'] + $flag_defasado
											&& $key == count($this->array_ano_idade) - 1)
											)
										{
											$desafados[] = $matricula;
											$obj_pessoa = new clsFisica($matricula["ref_idpes"]);
											$det_pessoa = $obj_pessoa->detalhe();
											$obj_aluno = new clsPmieducarAluno($matricula["ref_cod_aluno"]);
											$det_aluno = $obj_aluno->detalhe();
											$anee = false;
											if (is_numeric($matricula["ref_idpes"]))
											{
												$sql = "SELECT 1 FROM cadastro.fisica_deficiencia, cadastro.deficiencia
															WHERE cod_deficiencia = ref_cod_deficiencia AND
															ref_idpes = {$matricula["ref_idpes"]}";
												$db = new clsBanco();
												$anee = $db->CampoUnico($sql);
											}
											$defasados[count($desafados)] = array("mat" => $matricula, "sexo" => $det_pessoa["sexo"], "analfabeto" => $det_aluno["analfabeto"], "anee" => $anee);
										}
									}
									elseif( is_numeric($idade_aluno))
									{
										$idade = $value['idade'] ;
										if($idade_aluno > $serie['idade_final'] + $flag_defasado
												&& $idade_aluno == $idade
												||
												$idade_aluno >= $idade &&
												$key == count($this->array_ano_idade) - 1)
										{
											$desafados[] = $matricula;
											$obj_pessoa = new clsFisica($matricula["ref_idpes"]);
											$det_pessoa = $obj_pessoa->detalhe();
											$obj_aluno = new clsPmieducarAluno($matricula["ref_cod_aluno"]);
											$det_aluno = $obj_aluno->detalhe();
											$anee = false;
											if (is_numeric($matricula["ref_idpes"]))
											{
												$sql = "SELECT 1 FROM cadastro.fisica_deficiencia, cadastro.deficiencia
															WHERE cod_deficiencia = ref_cod_deficiencia AND
															ref_idpes = {$matricula["ref_idpes"]}";
												$db = new clsBanco();
												$anee = $db->CampoUnico($sql);
											}
											$defasados[count($desafados)] = array("mat" => $matricula, "sexo" => $det_pessoa["sexo"], "analfabeto" => $det_aluno["analfabeto"], "anee" => $anee);
										}
									}
								}
							}
						}
						$altura_aux = 13;
						$total_masc_serie = 0;
						$total_fem_serie = 0;
						$total_anee = 0;
						$total_nao_anee = 0;
						$total_alf = 0;
						$total_nao_alf = 0;
						$esquerda = 30;
						foreach ($defasados as $alunos) {
							if ($inicio_escrita > $this->pdf->altura - 50) {
						  		$inicio_escrita = 125 + $altura;
						  		$this->pdf->ClosePage();
						  		$this->pdf->OpenPage();
						  		$this->addCabecalho();
						  		$this->novaPagina();
						  	}

							$this->pdf->quadrado_relativo( $esquerda, $inicio_escrita, $direita, $altura_aux);
						    $this->pdf->escreve_relativo( $serie["nm_serie"], $esquerda += 5 , $inicio_escrita, 40, 50, $fonte, 9, $corTexto, 'center' );
						    $this->pdf->linha_relativa($esquerda += 40, $inicio_escrita, 0, $altura_aux);
						    $this->pdf->escreve_relativo( $alunos["mat"]["nome"], $esquerda += 5 , $inicio_escrita, 260, 50, $fonte, 9, $corTexto, 'left' );
						    $this->pdf->linha_relativa($esquerda += 260, $inicio_escrita, 0, $altura_aux);
						    $this->pdf->escreve_relativo( dataFromPgToBr($alunos["mat"]["data_nasc"]), $esquerda += 5 , $inicio_escrita, 70, 50, $fonte, 9, $corTexto, 'center' );
						  	$espaco = 50;
						  	$altura_aux = 12;
						    $this->pdf->linha_relativa($esquerda += $espaco + 20, $inicio_escrita, 0, $altura_aux);
						    if ($alunos["sexo"] == "M") {
						    	$this->pdf->escreve_relativo( "X", $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
						    	$total_masc_serie++;
						    } else {
						    	$this->pdf->escreve_relativo( "X", $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
						    	$total_fem_serie++;
						    }
						    $this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
						    $this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);

						    $this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
							if ($alunos["anee"]) {
						    	$this->pdf->escreve_relativo( "X", $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
						    	$total_anee++;
							} else {
								$this->pdf->escreve_relativo( "X", $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
								$total_nao_anee++;
							}
						    $this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
						    $this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);

						    $this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
							if (!$alunos["analfabeto"]) {
						    	$this->pdf->escreve_relativo( "X", $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
						    	$total_alf++;
							} else {
								$this->pdf->escreve_relativo( "X", $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
								$total_nao_alf++;
							}
							$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
						    $this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
							$inicio_escrita += $altura_aux;
							$esquerda = 30;
						}
						if ($inicio_escrita > $this->pdf->altura - 50) {
							$inicio_escrita = 125 + $altura;
							$this->pdf->ClosePage();
							$this->pdf->OpenPage();
							$this->addCabecalho();
							$this->novaPagina();
						}
						if ($total_masc_serie || $total_fem_serie || $total_anee || $total_curso_anee || $total_nao_alf || $total_nao_alf) {
							$this->pdf->quadrado_relativo( $esquerda, $inicio_escrita, $direita, $altura_aux);
							$esquerda = 30;
							$esquerda = 345;
							$espaco = 50;
							$altura_aux = 12;
							$this->pdf->escreve_relativo( "Totais Série {$serie["nm_serie"]}", $esquerda - 150, $inicio_escrita, 150, 50, $fonte, 9, $corTexto, 'left' );
							$this->pdf->linha_relativa($esquerda += $espaco + 20, $inicio_escrita, 0, $altura_aux);
							$this->pdf->escreve_relativo( $total_masc_serie, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
							$this->pdf->escreve_relativo( $total_fem_serie, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
							$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
							$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
							$this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
							$this->pdf->escreve_relativo( $total_anee, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
							$this->pdf->escreve_relativo( $total_nao_anee, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
							$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
							$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
							$this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
							$this->pdf->escreve_relativo( $total_alf, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
							$this->pdf->escreve_relativo( $total_nao_alf, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
							$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
							$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
							$inicio_escrita += $altura_aux;
						}
						$total_curso_masc_serie += $total_masc_serie;
						$total_curso_fem_serie += $total_fem_serie;
						$total_curso_anee += $total_anee;
						$total_nao_anee += $total_curso_anee;
						$total_curso_alf += $total_alf;
						$total_curso_nao_alf += $total_nao_alf;
						$esquerda = 30;
					}
				}
				if ($inicio_escrita > $this->pdf->altura - 50) {
					$inicio_escrita = 125 + $altura;
					$this->pdf->ClosePage();
					$this->pdf->OpenPage();
					$this->addCabecalho();
					$this->novaPagina();
				}
				$this->pdf->quadrado_relativo( $esquerda, $inicio_escrita, $direita, $altura_aux);
				$esquerda = 345;
				$espaco = 50;
				$altura_aux = 12;
				$obj_curso = new clsPmieducarCurso($curso);
				$det_curso = $obj_curso->detalhe();
				$this->pdf->escreve_relativo( "Totais Curso {$det_curso["nm_curso"]}", $esquerda - 150, $inicio_escrita, 300, 50, $fonte, 9, $corTexto, 'left' );

				$this->pdf->linha_relativa($esquerda += $espaco + 20, $inicio_escrita, 0, $altura_aux);
				$this->pdf->escreve_relativo( $total_curso_masc_serie, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
				$this->pdf->escreve_relativo( $total_curso_fem_serie, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
				$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
				$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
				$this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
				$this->pdf->escreve_relativo( $total_curso_anee, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
				$this->pdf->escreve_relativo( $total_curso_nao_anee, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
				$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
				$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
				$this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
				$this->pdf->escreve_relativo( $total_curso_alf, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
				$this->pdf->escreve_relativo( $total_curso_nao_alf, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
				$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
				$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
				$total_geral_masc_serie += $total_curso_masc_serie;
				$total_geral_fem_serie += $total_curso_fem_serie;
				$total_geral_anee += $total_curso_anee;
				$total_geral_nao_anee += $total_curso_nao_anee;
				$total_geral_alf += $total_curso_alf;
				$total_geral_nao_alf += $total_curso_nao_alf;
				$inicio_escrita += $altura_aux;
			}
			$esquerda = 30;
			$this->pdf->quadrado_relativo( $esquerda, $inicio_escrita, $direita, $altura_aux);
			$esquerda = 345;
			$espaco = 50;
			$altura_aux = 12;
			$obj_curso = new clsPmieducarCurso($curso);
			$det_curso = $obj_curso->detalhe();
			$this->pdf->escreve_relativo( "Totais Gerais", $esquerda - 100, $inicio_escrita, 300, 50, $fonte, 9, $corTexto, 'left' );
			$this->pdf->linha_relativa($esquerda += $espaco + 20, $inicio_escrita, 0, $altura_aux);
			$this->pdf->escreve_relativo( $total_geral_masc_serie, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
			$this->pdf->escreve_relativo( $total_geral_fem_serie, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
			$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
			$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
			$this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
			$this->pdf->escreve_relativo( $total_geral_anee, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
			$this->pdf->escreve_relativo( $total_geral_nao_anee, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
			$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
			$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
			$this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura_aux);
			$this->pdf->escreve_relativo( $total_geral_alf, $esquerda, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
			$this->pdf->escreve_relativo( $total_geral_nao_alf, $esquerda + $espaco / 2, $inicio_escrita, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
			$this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita, 0, $altura_aux);
			$this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura_aux);
			$altura_linha = 23;
			$inicio_escrita_y = 175;
		}

		$this->get_link = $this->pdf->GetLink();
		//header( "location: " . $this->pdf->GetLink() );
		$this->pdf->CloseFile();

		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";

	}

  public function addCabecalho()
  {
    /**
     * Variável global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configuração do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Variável que controla a altura atual das caixas
    $altura   = 30;
    $fonte    = 'arial';
    $corTexto = '#000000';
    $direita  = $this->pdf->largura - 30 * 2;

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $altura, $direita, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Paginador
    $this->pdf->escreve_relativo(date("d/m/Y"), 25, 30, $direita, 80, $fonte,
      10, $corTexto, 'right');

    // Título principal
    $titulo = $config->get($config->titulo, "i-Educar");
    $this->pdf->escreve_relativo($titulo, 30, 30, $direita, 80,
      $fonte, 18, $corTexto, 'center');

    // Dados escola
    $this->pdf->escreve_relativo("Instituição: $this->nm_instituicao", 120, 58,
      300, 80, $fonte, 10, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola: {$this->nm_escola}",136, 70, 300, 80,
      $fonte, 10, $corTexto, 'left');

    // Título
    $this->pdf->escreve_relativo("Demonstrativo de Alunos Defasados Nominal Idade/Série - {$this->ano}",
      30, 85, $direita, 80, $fonte, 12, $corTexto, 'center');

    // Data
    $this->pdf->escreve_relativo("{$this->meses_do_ano[$this->mes]}/{$this->ano}",
      45, 100, $direita, 80, $fonte, 10, $corTexto, 'left');
  }


	function novaPagina()
	{
		$altura2 = 300;
		$altura = 30;
		$direita = $this->pdf->largura - 30 * 2;
		$expande = 24;
		$esquerda = 30;
		$inicio_escrita = 125;
		$fonte = 'arial';
		$corTexto = '#000000';

	    $this->pdf->quadrado_relativo( $esquerda, $inicio_escrita, $direita, $altura);
	    $this->pdf->escreve_relativo( "Série", $esquerda += 5 , $inicio_escrita + $altura / 2, 35, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda += 40, $inicio_escrita, 0, $altura);
	    $this->pdf->escreve_relativo( "Nome Aluno", $esquerda += 5 , $inicio_escrita + $altura / 2, 100, 50, $fonte, 9, $corTexto, 'left' );
	    $this->pdf->linha_relativa($esquerda += 260, $inicio_escrita, 0, $altura);
	    $this->pdf->escreve_relativo( "Data Nascimento", $esquerda += 5 , $inicio_escrita + $espaco / 2, 70, 50, $fonte, 9, $corTexto, 'center' );
	  	$espaco = 50;
	  	$altura_aux = 14;
	    $this->pdf->linha_relativa($esquerda += $espaco + 20, $inicio_escrita, 0, $altura);
	    $this->pdf->escreve_relativo( "Sexo", $esquerda, $inicio_escrita, $espaco, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda, $inicio_escrita + $altura_aux, $espaco, 0);
	    $this->pdf->escreve_relativo( "M", $esquerda, $inicio_escrita + $altura_aux + 2, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita + $altura_aux, 0, $altura - $altura_aux);
	    $this->pdf->escreve_relativo( "F", $esquerda + $espaco / 2, $inicio_escrita + $altura_aux + 2, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura);

	    $this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura);
	    $this->pdf->escreve_relativo( "ANEE", $esquerda, $inicio_escrita + 2, $espaco, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda, $inicio_escrita + $altura_aux, $espaco, 0);
	    $this->pdf->escreve_relativo( "S", $esquerda, $inicio_escrita + $altura_aux + 2, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita + $altura_aux, 0, $altura - $altura_aux);
	    $this->pdf->escreve_relativo( "N", $esquerda + $espaco / 2, $inicio_escrita + $altura_aux + 2, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura);

	    $this->pdf->linha_relativa($esquerda, $inicio_escrita, 0, $altura);
	    $this->pdf->escreve_relativo( "Alfab.", $esquerda, $inicio_escrita, $espaco, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda, $inicio_escrita + $altura_aux, $espaco, 0);
	    $this->pdf->escreve_relativo( "S", $esquerda, $inicio_escrita + $altura_aux + 2, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda + $espaco / 2, $inicio_escrita + $altura_aux, 0, $altura - $altura_aux);
	    $this->pdf->escreve_relativo( "N", $esquerda + $espaco / 2, $inicio_escrita + $altura_aux + 2, $espaco / 2, 50, $fonte, 9, $corTexto, 'center' );
	    $this->pdf->linha_relativa($esquerda += $espaco, $inicio_escrita, 0, $altura);

	}

	function rodape()
	{
		$corTexto = '#000000';
		$fonte = 'arial';
		$dataAtual = date("d/m/Y");
		$this->pdf->escreve_relativo( "Data: $dataAtual", 36,756, 100, 50, $fonte, 7, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Assinatura do Diretor(a)", 68,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Assinatura do secretário(a)", 677,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa(52,517,130,0);
		$this->pdf->linha_relativa(660,517,130,0);
	}

	function corta_string( $string, $tamanho = 300, $add_fim = "" )
	{
		if( strlen($string) > $tamanho )
		{
			$string = substr( $string, 0, $tamanho ) . $add_fim;
		}
		return $string;
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