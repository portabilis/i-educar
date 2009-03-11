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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Rela&ccedil;&atilde;o de Alunos ANEEs" );
		$this->processoAp = "900";

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
	var $totalDiasUteis;
	var $necessidades;

	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $cursos = array();
	var $get_link = false;

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
		
		@session_start();
		$pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if(!$_POST)
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNão existem dados!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     		</script>';
	     	return true;
		}

		if(empty($this->cursos))
		{
	     	echo '<script>

	     			alert("Erro ao gerar relatório!\nNenhum curso selecionado!");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
	     			//window.location = window.location;
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
	     /**
	      * @todo negar lista_calendario e descomentar array_shift
	      */
	     if(!$lista_calendario)
	     {
	     	echo '<script>
	     			alert("Escola não possui calendário definido para este ano");
	     			window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));

	     		</script>';
	     	return true;
	     }

	    $obj_calendario = new clsPmieducarCalendarioAnoLetivo();
	    $lst_calendario = $obj_calendario->lista(null,$this->ref_cod_escola,null,null,$this->ano,null,null,null,null,1,null,$this->ref_cod_instituicao);

	    if($lst_calendario)
	    	$calendario = array_shift($lst_calendario);


		$obj_calendario_dia = new clsPmieducarCalendarioDia();
		$lista_dias = $obj_calendario_dia->lista($calendario['cod_calendario_ano_letivo'],$this->mes,null,null,null,null,null,null,null,1);
		$dias_mes = array();

		if($lista_dias)
		{
			foreach ($lista_dias as $dia) {
				$obj_motivo = new clsPmieducarCalendarioDiaMotivo($dia['ref_cod_calendario_dia_motivo']);
				$det_motivo = $obj_motivo->detalhe();
				$dias_mes[$dia['dia']] = strtolower($det_motivo['tipo']);
			}
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

	     //total de dias uteis + dias extra-letivos - dia nao letivo - fim de semana
	     $this->totalDiasUteis = 0;
	     //echo '<pre>';print_r($dias_mes);die;
	     for($dia = 1; $dia <= $NumeroDiasMes; $dia++)
	     {
	     	if($DiaSemana >= 7)
	     		$DiaSemana = 0;

	     	if($DiaSemana != 0 && $DiaSemana != 6){
	     		if(!(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('n')))
	     			$this->totalDiasUteis++;
	     	}elseif(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('e'))
				$this->totalDiasUteis++;

	     	$DiaSemana++;

	     }


		$registros_por_pagina = 27;

		$this->pdf = new clsPDF("Relação de Alunos ANEEs", "Relação de Alunos ANEEs", "A4", "", false, false);

		$cursos_in = '';
		/*$conc = '';
		foreach ($this->cursos as $curso)
		{
			$cursos_in .= "{$conc}{$curso}";
			$conc = ",";
		}*/
		$cursos_in = implode(',',$this->cursos);

		$db = new clsBanco();

		$consulta = " SELECT coalesce(count(1),0)
						 FROM pmieducar.aluno      a
						      ,pmieducar.matricula m
						      ,pmieducar.escola    e
						      ,pmieducar.serie     s
						      ,cadastro.pessoa     p
						WHERE a.ref_idpes  = p.idpes
						  AND a.cod_aluno  = m.ref_cod_aluno
						  AND e.cod_escola = m.ref_ref_cod_escola
						  AND m.ultima_matricula = 1
						  AND s.cod_serie  = m.ref_ref_cod_serie
						  AND s.ref_cod_curso in($cursos_in)
						  AND a.ativo = 1
						  AND m.aprovado in (1,2,3)
						  AND m.ativo = 1
						  AND e.ativo = 1
						  AND s.ativo = 1
						  AND m.ano = {$this->ano}
						";
//die($consulta);
		$total = $db->CampoUnico($consulta);
		$this->total_paginas = ( int )( $total / 27 ) + 1 ;
		$this->pdf->OpenPage();

		//linha externa
		$altura2 = 620;
		$altura = 80;

		$expande = 24;

		$centralizado = abs(($altura - 10) / 2) + 120;


		//$necessidades = array('cegueira','baixa visao','surdez leve','surdocegueira','sindrome down','Def. Fisica','Autismo','Def. multiplas','cond. tipicas','raver boy','surdocegueira');
		/*$obj_deficiencia = new clsCadastroDeficiencia();
		$obj_deficiencia->_campos_lista = "nm_deficiencia,cod_deficiencia,0 as subtotal,0 as total_curso,0 as total";
		$obj_deficiencia->setOrderby( "nm_deficiencia ASC" );
		$this->necessidades = $obj_deficiencia->lista();*/

		$select = "SELECT distinct nm_deficiencia,cod_deficiencia,0 as subtotal,0 as total_curso,0 as total
					 FROM pmieducar.aluno	    a
					      ,pmieducar.matricula m
					      ,cadastro.fisica_deficiencia fd
					      ,cadastro.deficiencia        d
					WHERE a.cod_aluno = m.ref_cod_aluno
					  AND a.ref_idpes = fd.ref_idpes
					  AND cod_deficiencia  = ref_cod_deficiencia
					  AND ano = $this->ano
					  AND ref_ref_cod_escola = $this->ref_cod_escola
					  AND m.ativo = 1
					  AND m.aprovado in (1,2,3)
					  AND a.ativo = 1
					  AND ref_cod_curso in ($cursos_in)";

		$this->necessidades = array();
		$db->Consulta($select);
		if($db->Num_Linhas())
		{

			while ($db->ProximoRegistro())
			{
				$this->necessidades[] = $db->Tupla();
			}
		}

		$this->addCabecalho();
		$this->novaPagina();

		//$inicio_escrita_x = 36;
		$inicio_escrita_y = 215;
		$inicio_linha_y = 232;

		$numero_x = 36;
		//posicao nome
		$nome_x = 65 + $expande;

		//posicao data nasc
		$nasc_x = 180 + $expande;

		//posicao serie
		$serie_x = 226  + $expande;

		//posicao turno
		$turno_x = 255 + $expande;

		//posicao alfabetizado
		$alfabetizado_x = 278 + $expande;
		//posicao necessidade
		$necessidade_x = 381 + $expande;

		$necec_x = $alfabetizado_x + 23;
		$linha_neces_x = 385;
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		/**
		 * busca todas as series de cada curso selecionado
		 */

		$contador_registros = 0;

		for($ct =0; $ct < $registros_por_pagina;$ct++){
				$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
				$inicio_linha_y += 20;
		}

		$total_alfabetos_geral = 0;
		$total_analfabetos_geral = 0;
		$total_geral_alunos = 0;
		foreach ($this->cursos as $curso) {


			$total_curso = 0;
			$total_analfabetos_serie = 0;
			$total_analfabetos_curso = 0;
			$total_alfabetos_serie = 0;
			$total_alfabetos_curso = 0;


			$obj = new clsPmieducarSerie();
			
			$obj->setOrderby("s.nm_serie");
			
			$lista_serie_curso = $obj->lista(null,null,null,$curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

			//$obj_curso = new clsPmieducarCurso($serie['ref_cod_curso']);
			$obj_curso = new clsPmieducarCurso($curso);
			$det_curso = $obj_curso->detalhe();

			$alunos=array();
			
			
			if($lista_serie_curso){
				foreach ($lista_serie_curso as $serie) {
					/**
					 * busca todas as matriculas de cada curso
					 */
					$subtotal_serie = 0;
				
					$obj_matricula = new clsPmieducarMatricula();
					$lista_matricula_serie = $obj_matricula->lista(null,null,$this->ref_cod_escola,$serie['cod_serie'],null,null,null,array(1,2,3),null,null,null,null,1,$this->ano,$curso,$this->ref_cod_instituicao,null,null,null,null,null,null,null,true);

//					$total_geral_alunos += count($lista_matricula_serie);

//					$subtotal_serie = count($lista_matricula_serie);
//					$total_curso = $total_curso + $subtotal_serie;
					/*				
					$total_geral_alunos += $obj_matricula->_total;

					$subtotal_serie = $obj_matricula->_total;
					$total_curso = $total_curso + $subtotal_serie;
					*/


					if($lista_matricula_serie)
					{

						/**
						 * busca dados da matricula de um aluno de uma turma de uma serie =p
						 */
						
						foreach ($lista_matricula_serie as $matricula) {
							$total_curso++;
							$contador_registros++;
							$total_geral_alunos++;
							$subtotal_serie++;
							if($contador_registros > $registros_por_pagina)
							{
								$contador_registros = 1;
								$inicio_escrita_y = 215;
								$inicio_linha_y = 232;
								$this->pdf->ClosePage();
								$this->pdf->OpenPage();
								$this->addCabecalho();
								$this->novaPagina();
								for($ct =0; $ct < $registros_por_pagina;$ct++){
										$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
										$inicio_linha_y += 20;
								}
								$inicio_linha_y = 232;
							}

							$obj_aluno = new clsPmieducarAluno();

							$det_aluno = array_shift($obj_aluno->lista($matricula['ref_cod_aluno']));

							$obj_fisica = new clsFisica($det_aluno['ref_idpes']);
							$det_fisica = $obj_fisica->detalhe();

							$obj_matricula_turma = new clsPmieducarMatriculaTurma();
							$det_matricula_turma = $obj_matricula_turma->lista($matricula['cod_matricula'],null,null,null,null,null,null,null,1,$serie['cod_serie'],$curso,$this->ref_cod_escola,$this->ref_cod_instituicao);
							if (is_array($det_matricula_turma))
								$det_matricula_turma = array_shift($det_matricula_turma);

							if($det_matricula_turma['hora_inicial'] <= '12:00')
								$turno = 'M';
							elseif($det_matricula_turma['hora_inicial'] > '12:00' && $det_matricula_turma['hora_inicial'] <= '18:00')
								$turno = 'V';
							else
								$turno = 'N';
							/**
							 * INFORMACOES DE CADAS ALUNO
							 */
//							$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
							//Nº
							$this->pdf->escreve_relativo( " ", $numero_x,$inicio_escrita_y+3, 50, 20, $fonte, 7, $corTexto, 'left' );
							//Nome
							$this->pdf->escreve_relativo( $det_aluno['nome_aluno'], $nome_x - $expande - 12,$inicio_escrita_y, 210, 20, $fonte, 7, $corTexto, 'left' );
							//data nascimento
							//$this->pdf->escreve_relativo( "05/09/1984", $nasc_x + $expande + 6,$inicio_escrita_y+1, 60, 20, $fonte, 7, $corTexto, 'left' );
							$this->pdf->escreve_relativo( dataToBrasil($det_fisica['data_nasc']), $serie_x + $expande - 50,$inicio_escrita_y+3, 60, 20, $fonte, 7, $corTexto, 'left' );
							//serie
							$this->pdf->escreve_relativo( ' ', $turno_x + $expande -25,$inicio_escrita_y+3, 60, 20, $fonte, 7, $corTexto, 'left' );
							//turno
							$this->pdf->escreve_relativo( $turno, $alfabetizado_x + $expande - 24,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
							//nao alfa
							$this->pdf->escreve_relativo( $det_aluno['analfabeto'] == 1 ? 'X' : '' , $turno_x + $expande + 25,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
							//alfabetizado
							$this->pdf->escreve_relativo( $det_aluno['analfabeto'] == 0 ? 'X' : '', $alfabetizado_x + $expande + 24,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );

							$total_analfabetos_serie = (int)$total_analfabetos_serie + (((int)$det_aluno['analfabeto'] == 1) ? 1 : 0);
							$total_analfabetos_curso = (int)$total_analfabetos_curso + (((int)$det_aluno['analfabeto'] == 1) ? 1 : 0);
							$total_analfabetos_geral = (int)$total_analfabetos_geral + (((int)$det_aluno['analfabeto'] == 1) ? 1 : 0);
							$total_alfabetos_serie   = (int)$total_alfabetos_serie + (((int)$det_aluno['analfabeto'] == 0) ? 1 : 0);
							$total_alfabetos_curso   = (int)$total_alfabetos_curso + (((int)$det_aluno['analfabeto'] == 0) ? 1 : 0);
							$total_alfabetos_geral   = (int)$total_alfabetos_geral + (((int)$det_aluno['analfabeto'] == 0) ? 1 : 0);

							$obj_aluno_deficiencia = new clsCadastroFisicaDeficiencia();
							$lista_aluno_deficiencia = false;
							$lista_aluno_deficiencia = $obj_aluno_deficiencia->lista($det_aluno['ref_idpes']);

							if($lista_aluno_deficiencia)
							{
								foreach ($lista_aluno_deficiencia as $deficiencia)
								{
									$necec_x = $alfabetizado_x + 23;
									foreach ($this->necessidades  as $key=>$n)
									{
										if($deficiencia['ref_cod_deficiencia'] == $n['cod_deficiencia'])
										{
											$this->pdf->escreve_relativo( "X", $necec_x + 1 ,$inicio_escrita_y + 2, 100, $altura, $fonte, 11, $corTexto, 'center' );
											$this->necessidades[$key]['subtotal'] = (int)$this->necessidades[$key]['subtotal'] + 1;
											$this->necessidades[$key]['total_curso'] = $this->necessidades[$key]['total_curso'] + 1 ;
											$this->necessidades[$key]['total'] = $this->necessidades[$key]['total'] + 1 ;
										}
										else
										{
											$this->pdf->escreve_relativo( "-", $necec_x +1 ,$inicio_escrita_y + 2, 100, $altura, $fonte, 10, $corTexto, 'center' );
										}
										$necec_x += 18;
										//$linha_neces_x += 18;
									}
								}
							}

							//$inicio_linha_y += 20;
							$inicio_escrita_y += 20;


						}
							$contador_registros++;
							if($contador_registros > $registros_por_pagina)
							{
								$contador_registros = 1;
								$inicio_escrita_y = 215;
								$inicio_linha_y = 232;
								$this->pdf->ClosePage();
								$this->pdf->OpenPage();
								$this->addCabecalho();
								$this->novaPagina();
								for($ct =0; $ct < $registros_por_pagina;$ct++){
										$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
										$inicio_linha_y += 20;
								}
								$inicio_linha_y = 232;
							}
					//	if($subtotal_serie){
							/**
							 * subtotal serie
							 */
//							$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
							$obj_turmas = new clsPmieducarTurma();
							$total_turmas = count($obj_turmas->lista(null,null,null,$serie['cod_serie'],$this->ref_cod_escola));
							$this->pdf->escreve_relativo( "{$total_turmas}", $numero_x+2,$inicio_escrita_y+3, 50, 20, $fonte, 7, $corTexto, 'left' );
							$this->pdf->escreve_relativo( "SUBTOTAL SÉRIE {$serie['nm_serie']}", $nome_x - $expande + 5,$inicio_escrita_y + 3, 177, 20, $fonte, 6, $corTexto, 'left' );

							$this->pdf->quadrado_relativo($nasc_x + $expande - 9,$inicio_escrita_y-3, 49, 20,0.1,"#D2D2D2","#6F5E5E");
							//echo $total_analfabetos_serie;
							//echo $total_alfabetos_serie ;
							//serie
							$this->pdf->escreve_relativo( $subtotal_serie, $turno_x + $expande -25,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
							//$this->pdf->quadrado_relativo($nasc_x + $expande + 40,$inicio_escrita_y-3, 25, 20,0.1,"#D2D2D2","#6F5E5E");

							//nao alfa
							$this->pdf->escreve_relativo( ((int)$total_analfabetos_serie == 0) ? '-' : $total_analfabetos_serie , $turno_x + $expande + 27,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
							//alfabetizado
							$this->pdf->escreve_relativo( ((int)$total_alfabetos_serie == 0) ? '-' : $total_alfabetos_serie, $alfabetizado_x + $expande + 24,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
							$total_analfabetos_serie = $total_alfabetos_serie = 0;
							// = 0;

							$this->pdf->quadrado_relativo($nasc_x + $expande + 65,$inicio_escrita_y-3, 26, 20,0.1,"#D2D2D2","#6F5E5E");

							$necec_x = $alfabetizado_x + 23;

							foreach ($this->necessidades  as $key => $n)
							{

								$this->pdf->escreve_relativo( $n['subtotal'] == 0 ? '-' : $n['subtotal'], $necec_x + 1 ,$inicio_escrita_y + 2, 100, $altura, $fonte, 10, $corTexto, 'center' );
								$this->necessidades[$key]['subtotal'] = (int)0;
								$necec_x += 18;
								//$linha_neces_x += 18;
							}

							//$inicio_linha_y += 20;
							$inicio_escrita_y += 20;
							/**
							 *
							 */

						//}
					}

				}
			}

			$contador_registros++;
			if($contador_registros > $registros_por_pagina)
			{
				$contador_registros = 1;
				$inicio_escrita_y = 215;
				$inicio_linha_y = 232;
				$this->pdf->ClosePage();
				$this->pdf->OpenPage();
				$this->addCabecalho();
				$this->novaPagina();
				for($ct =0; $ct < $registros_por_pagina;$ct++){
						$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
						$inicio_linha_y += 20;
				}
				$inicio_linha_y = 232;
			}
			$this->pdf->escreve_relativo( " ", $numero_x,$inicio_escrita_y+3, 50, 20, $fonte, 7, $corTexto, 'left' );
			$this->pdf->escreve_relativo( "TOTAL CURSO {$det_curso['nm_curso']}", $nome_x - $expande - 20,$inicio_escrita_y + 3, 177, 20, $fonte, 7, $corTexto, 'center' );

			$this->pdf->quadrado_relativo($nasc_x + $expande - 9,$inicio_escrita_y-3, 49, 20,0.1,"#D2D2D2","#6F5E5E");

			//serie
			$this->pdf->escreve_relativo( $total_curso == 0 ? '-' : $total_curso, $turno_x + $expande - 25,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
			//$this->pdf->quadrado_relativo($nasc_x + $expande + 40,$inicio_escrita_y-3, 25, 20,0.1,"#D2D2D2","#6F5E5E");

			//nao alfa
			$this->pdf->escreve_relativo( $total_analfabetos_curso == 0 ? '-' : $total_analfabetos_curso , $turno_x + $expande + 26,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
			//alfabetizado
			$this->pdf->escreve_relativo( $total_alfabetos_curso == 0 ? '-' : $total_alfabetos_curso, $alfabetizado_x + $expande + 23,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
			//$total_analfabetos_curso = $total_alfabetos_curso = 0;

			$this->pdf->quadrado_relativo($nasc_x + $expande + 65,$inicio_escrita_y-3, 26, 20,0.1,"#D2D2D2","#6F5E5E");

			$necec_x = $alfabetizado_x + 23;

			foreach ($this->necessidades  as $key => $n)
			{

				$this->pdf->escreve_relativo( $n['total_curso'] == 0 ? '-' : $n['total_curso'], $necec_x + 1 ,$inicio_escrita_y + 2, 100, $altura, $fonte, 10, $corTexto, 'center' );
				$this->necessidades[$key]['total_curso'] = (int)0;
				$necec_x += 18;

			}

			$inicio_escrita_y += 20;
			/*$contador_registros++;
			if($contador_registros > $registros_por_pagina)
			{
				$contador_registros = 0;
				$inicio_escrita_y = 215;
				$inicio_linha_y = 232;
				$this->pdf->ClosePage();
				$this->pdf->OpenPage();
				$this->addCabecalho();
				$this->novaPagina();
				for($ct =0; $ct < $registros_por_pagina;$ct++){
						$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
						$inicio_linha_y += 20;
				}
				$inicio_linha_y = 232;
			}*/
		}
			$contador_registros++;
			if($contador_registros > $registros_por_pagina)
			{
				$contador_registros = 1;
				$inicio_escrita_y = 215;
				$inicio_linha_y = 232;
				$this->pdf->ClosePage();
				$this->pdf->OpenPage();
				$this->addCabecalho();
				$this->novaPagina();
				for($ct =0; $ct < $registros_por_pagina;$ct++){
						$this->pdf->linha_relativa(30,$inicio_linha_y,535,0);
						$inicio_linha_y += 20;
				}
				$inicio_linha_y = 232;
			}
			$this->pdf->escreve_relativo( " ", $numero_x,$inicio_escrita_y+3, 50, 20, $fonte, 7, $corTexto, 'left' );
			$this->pdf->escreve_relativo( "TOTAL GERAL ", $nome_x - $expande - 20,$inicio_escrita_y + 3, 177, 20, $fonte, 7, $corTexto, 'center' );

			$this->pdf->quadrado_relativo($nasc_x + $expande - 9,$inicio_escrita_y-3, 49, 20,0.1,"#D2D2D2","#6F5E5E");

			//serie
			$this->pdf->escreve_relativo( $total_geral_alunos == 0 ? '-' : $total_geral_alunos, $turno_x + $expande -25,$inicio_escrita_y, 60, 20, $fonte, 10, $corTexto, 'left' );
			//$this->pdf->quadrado_relativo($nasc_x + $expande + 40,$inicio_escrita_y-3, 25, 20,0.1,"#D2D2D2","#6F5E5E");

			//nao alfa
			$this->pdf->escreve_relativo( $total_analfabetos_geral == 0 ? '-' : $total_analfabetos_geral , $turno_x + $expande + 26,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
			//alfabetizado
			$this->pdf->escreve_relativo( $total_alfabetos_geral == 0 ? '-' : $total_alfabetos_geral, $alfabetizado_x + $expande + 23,$inicio_escrita_y+2, 60, 20, $fonte, 10, $corTexto, 'left' );
			//$total_analfabetos_geral = $total_alfabetos_geral = 0;

			$this->pdf->quadrado_relativo($nasc_x + $expande + 65,$inicio_escrita_y-3, 26, 20,0.1,"#D2D2D2","#6F5E5E");

			$necec_x = $alfabetizado_x + 23;

			foreach ($this->necessidades  as $key => $n)
			{

				$this->pdf->escreve_relativo( $n['total'] == 0 ? '-' : $n['total'], $necec_x + 1 ,$inicio_escrita_y + 2, 100, $altura, $fonte, 10, $corTexto, 'center' );
				$this->necessidades[$key]['total'] = (int)0;
				$necec_x += 18;
				//$linha_neces_x += 18;
			}


		/**
		 *
		 */

		$this->rodape();

		$this->pdf->ClosePage();
		$this->get_link = $this->pdf->GetLink();
		$this->pdf->CloseFile();

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
		$this->pdf->quadrado_relativo( 30, $altura, 535, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//paginador
		//$this->pdf->escreve_relativo( "Página $this->pagina_atual de $this->total_paginas", 25, 30, 535, 80, $fonte, 10, $corTexto, 'right' );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 535, 80, $fonte, 18, $corTexto, 'center' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 500, 30, 100, 80, $fonte, 12, $corTexto, 'left' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição:$this->nm_instituicao", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola:{$this->nm_escola}",136, 70, 300, 80, $fonte, 10, $corTexto, 'left' );

		//titulo
		$this->pdf->escreve_relativo( "Relação de Alunos ANEEs", 30, 85, 535, 80, $fonte, 14, $corTexto, 'center' );

		//Data
		$this->pdf->escreve_relativo( "{$this->meses_do_ano[$this->mes]}/{$this->ano}", 45, 100, 535, 80, $fonte, 10, $corTexto, 'left' );
	    $this->pdf->escreve_relativo( "Dias previstos: {$this->totalDiasUteis}", 220, 100, 535, 80, $fonte, 10, $corTexto, 'center' );
	}

	function novaPagina()
	{
		$altura2 = 627;
		$altura = 86;

		$expande = 24;
	    $this->pdf->quadrado_relativo( 30, 125, 535, $altura2);


		$fonte = 'arial';
		$corTexto = '#000000';
		//linha nao alfabetizado
		//$pdf->quadrado_relativo( 30, 125, 350, $altura);

		$this->pdf->quadrado_relativo( 30, 125, 535, $altura );

		//linha NEEs
		$this->pdf->quadrado_relativo( 30, 125, 535, 15);

		//linha alfabetizado
		$this->pdf->quadrado_relativo( 30, 125, 310 + $expande, $altura);

		//linha nao alfabetizado
		$this->pdf->quadrado_relativo( 30, 125, 285 + $expande + 3, $altura);

		//linha turno
		$this->pdf->quadrado_relativo( 30, 125, 284 + $expande - 19, $altura);

		//linha serie
		$this->pdf->quadrado_relativo( 30, 125, 254 + $expande - 15, $altura);

		//linha data nasc
		$this->pdf->quadrado_relativo( 30, 125, 230 + $expande - 16, $altura);

	    //linha nome aluno
		$this->pdf->quadrado_relativo( 30, 125, 170 + $expande - 5, $altura);
		//$this->pdf->quadrado_relativo( 30, 125, 170 + $expande + 30, $altura);

		//linha Nº
		$this->pdf->quadrado_relativo( 30, 125, 20, $altura);


		$centralizado = abs(($altura - 10) / 2) + 120;

		//posicao numero
		$numero_x = 36;
		$this->pdf->escreve_relativo( "Nº", $numero_x,$centralizado, 50, 50, $fonte, 7, $corTexto, 'left' );
		//posicao nome
		$nome_x = 65 + $expande;
		$this->pdf->escreve_relativo( "Nome do Aluno(a)", $nome_x -5,$centralizado, 100, 50, $fonte, 7, $corTexto, 'center' );
		//posicao data nasc
		$nasc_x = 180 + $expande;
		$this->pdf->escreve_relativo( "Data \n Nascimento", $nasc_x - 10,$centralizado - 6, 100, 40, $fonte, 7, $corTexto, 'center' );
		//posicao serie
		$serie_x = 226  + $expande;
		//$this->pdf->escreve_relativo( "Série", $serie_x,$centralizado, 92, 40, $fonte, 7, $corTexto, 'center' );
		$this->pdf->escreve_relativo( "Série", $serie_x - 13,$centralizado, 88, 40, $fonte, 7, $corTexto, 'center' );
		//posicao turno
		$turno_x = 235 + $expande;
		$this->pdf->escreve_relativo( "Turno", $turno_x +1,$centralizado, 92, 40, $fonte, 7, $corTexto, 'center' );
		//posicao alfabetizado
		$nao_alfabetizado_x = 257 + $expande;
		$this->pdf->escreve_relativo( strtoupper( "n\nÃ\no\n\nA\nl\nf\na\nb\ne\nt\ni\nz\na\nd\no\n" ), $nao_alfabetizado_x ,2 + 125, 100, $altura, $fonte, 5, $corTexto, 'center' );
		//posicao alfabetizado
		$alfabetizado_x = 280 + $expande;
		$this->pdf->escreve_relativo( strtoupper( "a\nl\nf\na\nb\ne\nt\ni\nz\na\nd\no\n" ), $alfabetizado_x,2 + 125, 100, $altura, $fonte, 5, $corTexto, 'center' );
		//posicao necessidade
		$necessidade_x = 381 + $expande;
		$this->pdf->escreve_relativo( "Tipo de Necessidade Educacional Especial", $necessidade_x, 4 + 125, 130, $altura, $fonte, 6, $corTexto, 'center' );

		$necec_x = $alfabetizado_x + 23;
		$linha_neces_x = 385;
		foreach ($this->necessidades  as $n)
		{
			$n['nm_deficiencia'] =  eregi_replace("([^\n\r\t])","\n\\1",$n['nm_deficiencia']);

			$this->pdf->escreve_relativo( strtoupper( $n['nm_deficiencia']), $necec_x -1 ,12 + 125 , 100, $altura, $fonte, 4.5, $corTexto, 'center' );

			$this->pdf->linha_relativa($linha_neces_x,140,0,612);
			$necec_x += 18;
			$linha_neces_x += 18;
		}

		//nome
		$this->pdf->linha_relativa(50,211,0,541);
		//nome
		$this->pdf->linha_relativa(224 -5,211,0,541);
		//data nasc
		$this->pdf->linha_relativa(308 - 40,211,0,541);
		//serie
		$this->pdf->linha_relativa(308 - 15,211,0,541);
		//turno
		$this->pdf->linha_relativa(330-11,211 ,0,541);
		//nao alfabeizado
		$this->pdf->linha_relativa(364,211,0,541);
		//alfabetizado
		$this->pdf->linha_relativa(342,211,0,541);

	}

	function rodape()
	{
		$corTexto = '#000000';
		$dataAtual = date("d/m/Y");
		$this->pdf->escreve_relativo( "Data: $dataAtual", 36,756, 100, 50, $fonte, 7, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Assinatura do Diretor(a)", 68,795, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Assinatura do secretário(a)", 398,795, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa(52,792,130,0);
		$this->pdf->linha_relativa(385,792,130,0);
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
