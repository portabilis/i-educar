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
	header( 'Content-type: text/xml' );

	require_once( "include/clsBanco.inc.php" );
	require_once( "include/funcoes.inc.php" );
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

	@session_start();
	$pessoa_logada = $_SESSION['id_pessoa'];
	@session_write_close();

	if( is_numeric( $_GET["alu"] ) && is_numeric( $_GET["ins"] ) && is_numeric( $_GET["cur"] ) && is_numeric( $_GET["esc"] ) )
	{
		$db = new clsBanco();
		$db->Consulta( "
		SELECT
			m.cod_matricula
			, m.ref_ref_cod_escola
			, m.ref_cod_curso
			, m.ref_ref_cod_serie
			, m.ano
			, eal.ano AS ano_letivo
			, c.padrao_ano_escolar
			, m.aprovado
			, COALESCE(
				(
				SELECT
					1
				FROM
					pmieducar.transferencia_solicitacao ts
				WHERE
					m.cod_matricula = ts.ref_cod_matricula_saida
					AND ts.ativo = 1
					AND ts.data_transferencia IS NULL
				), 0) AS transferencia_int
			, COALESCE(
				(
				SELECT
					1
				FROM
					pmieducar.transferencia_solicitacao ts
				WHERE
					m.cod_matricula = ts.ref_cod_matricula_saida
					AND ts.ativo = 1
					AND ts.data_transferencia IS NOT NULL
					AND ts.ref_cod_matricula_entrada IS NULL
				), 0) AS transferencia_ext
		FROM
			pmieducar.matricula m
			, pmieducar.escola_ano_letivo eal
			, pmieducar.curso c
		WHERE
			m.ref_cod_aluno = '{$_GET["alu"]}'
			AND m.ultima_matricula = 1
			AND m.ativo = 1
			AND m.ref_ref_cod_escola = eal.ref_cod_escola
			AND eal.andamento = 1
			AND eal.ativo = 1
			AND m.ref_cod_curso = c.cod_curso
			AND m.aprovado != 6
			AND c.ref_cod_instituicao = '{$_GET["ins"]}'
		ORDER BY
			m.cod_matricula ASC
		");
		
		// caso o aluno nao tenha nenhuma matricula em determinada instituicao
		if (!$db->numLinhas())
		{
			$db->Consulta( "
			SELECT
				s.cod_serie
				, s.nm_serie
			FROM
				pmieducar.serie s
				, pmieducar.escola_serie es
				, pmieducar.curso c
			WHERE
				es.ref_cod_escola = '{$_GET["esc"]}'
				AND es.ref_cod_serie = s.cod_serie
				AND s.ativo = 1
				AND c.cod_curso = '{$_GET["cur"]}'
				AND s.ref_cod_curso = c.cod_curso
				AND c.ref_cod_instituicao = '{$_GET["ins"]}'
			ORDER BY
				s.nm_serie ASC
			");

			if ($db->numLinhas())
			{
				while ( $db->ProximoRegistro() )
				{
					list( $cod, $nome ) = $db->Tupla();
					echo "<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
				}
			}
		} // caso o aluno tenha matricula(s) em determinada instituicao
		else
		{
			$resultado = array();

			$db2 = new clsBanco();
			while ( $db->ProximoRegistro() )
			{
				unset($lst_serie);
				$lst_serie = array();

				list( $matricula, $escola, $curso, $serie, $ano, $ano_letivo, $padrao_ano_escolar, $aprovado, $transferencia_int, $transferencia_ext ) = $db->Tupla();
//				echo "<teste>matricula={$matricula}, escola={$escola}, curso={$curso}, serie={$serie}, ano={$ano}, ano_letivo={$ano_letivo}, padrao_escolar={$padrao_ano_escolar}, aprovado={$aprovado}, transf_int={$transferencia_int}, transf_ext={$transferencia_ext}</teste><br>\n";

				// CASO O ALUNO TENHA ALGUMA SOLICITACAO DE TRANSFERENCIA EXTERNA EM ABERTO,
				// LIBERA TODAS AS SERIES
				
				if( $transferencia_ext )
				{
					$db2->Consulta( "
					SELECT
						s.cod_serie
						, s.nm_serie
					FROM
						pmieducar.serie s
						, pmieducar.escola_serie es
						, pmieducar.curso c
					WHERE
						es.ref_cod_escola = '{$_GET["esc"]}'
						AND es.ref_cod_serie = s.cod_serie
						AND s.ativo = 1
						AND c.cod_curso = '{$_GET["cur"]}'
						AND s.ref_cod_curso = c.cod_curso
						AND c.ref_cod_instituicao = '{$_GET["ins"]}'
					ORDER BY
						s.nm_serie ASC
					");

					if ($db2->numLinhas())
					{
						while ( $db2->ProximoRegistro() )
						{
							list( $cod, $nome ) = $db2->Tupla();
							$resultado[$cod] = $nome;
						}
					}

					break;
				}
				
				if ( ($escola == $_GET["esc"]) /*&& ( (($padrao_ano_escolar == 1) && ($ano_letivo > $ano)) || ($padrao_ano_escolar != 1) ) */)
				{
					// curso matriculado igual ao curso escolhido
					if ($curso == $_GET["cur"])
					{
						// situacao reprovado
						if ( ($aprovado == 2) && (($ano_letivo > $ano) || !$padrao_ano_escolar) )
						{
							// lista msm serie
							$db2->Consulta( "
							SELECT
								cod_serie
								, nm_serie
							FROM
								pmieducar.serie
							WHERE
								cod_serie = '{$serie}'
								AND ativo = 1
							ORDER BY
								nm_serie ASC
							");
							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									list( $cod, $nome ) = $db2->Tupla();
//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
									$lst_serie[$cod] = $nome;
								}
							}

						} // situacao aprovado
						else if ( ($aprovado == 1) && (($ano_letivo > $ano) || !$padrao_ano_escolar) )
						{
							// lista serie sequencia
							$db2->Consulta( "
							SELECT
								s.cod_serie
								, s.nm_serie
							FROM
								pmieducar.serie s
								, pmieducar.sequencia_serie ss
							WHERE
								ss.ref_serie_origem = '{$serie}'
								AND ss.ref_serie_destino = s.cod_serie
								AND ss.ativo = 1
							ORDER BY
								s.nm_serie ASC
							");

							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									list( $cod, $nome ) = $db2->Tupla();
//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
									$lst_serie[$cod] = $nome;
								}
							}
						}
					} // curso matriculado diferente do curso escolhido
					else
					{
						// curso eh diferente, + faz parte da sequencia
						// situacao aprovado
						if ( ($aprovado == 1) && (($ano_letivo > $ano) || !$padrao_ano_escolar) )
						{
							// lista serie sequencia
							$db2->Consulta( "
							SELECT
								s.cod_serie
								, s.nm_serie
							FROM
								pmieducar.serie s
								, pmieducar.sequencia_serie ss
							WHERE
								ss.ref_serie_origem = '{$serie}'
								AND ss.ref_serie_destino = s.cod_serie
								AND s.ref_cod_curso = '{$_GET["cur"]}'
								AND ss.ativo = 1
							ORDER BY
								s.nm_serie ASC
							");

							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									list( $cod, $nome ) = $db2->Tupla();
//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
									$lst_serie[$cod] = $nome;
								}
							}

						}
						// curso eh diferente, e nao faz parte da sequencia
						// situacao aprovado, reprovado ou em andamento
						if ( ($aprovado == 1) || ($aprovado == 2) || ($aprovado == 3) )
						{
							// lista somente a 1a serie do curso (sequencia)
							$db2->Consulta( "
							SELECT
								so.ref_cod_curso as curso_origem
								, ss.ref_serie_origem as serie_origem
								, sd.ref_cod_curso as curso_destino
								, ss.ref_serie_destino as serie_destino
							FROM
								pmieducar.sequencia_serie ss
								, pmieducar.serie so
								, pmieducar.serie sd
							WHERE
								ss.ativo = 1
								AND ref_serie_origem = so.cod_serie
								AND ref_serie_destino = sd.cod_serie
							ORDER BY
								ss.ref_serie_origem ASC
							");

							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									$sequencias[] = $db2->Tupla();
								}
							}

							$db2->Consulta( "
							SELECT
								distinct( o.ref_serie_origem )
							FROM
								pmieducar.sequencia_serie o
								, pmieducar.escola_serie es
							WHERE NOT EXISTS
							(
								SELECT
									1
								FROM
									pmieducar.sequencia_serie d
								WHERE
									o.ref_serie_origem = d.ref_serie_destino
							)
							");

							if ($db2->numLinhas())
							{
								$pertence_sequencia = false;
								$achou_serie = false;
								$reset = false;

								while ( $db2->ProximoRegistro() )
								{
									list( $ini_sequencia ) = $db2->Tupla();

									$ini_serie = $ini_sequencia;
									reset($sequencias);

									do
									{
										if( $reset )
										{
											reset($sequencias);
											$reset = false;
										}

										$sequencia = current($sequencias);
										$aux_serie = $sequencia['serie_origem'];

										if ($ini_serie == $aux_serie)
										{
											if ($serie == $aux_serie)
											{
												// achou serie da matricula
												$achou_serie = true;
											}
											/*if ($sequencia['curso_origem'] == $curso)
											{
												// curso pertence a sequencia
												$pertence_sequencia = true;
												$serie_sequencia[] = $sequencia['serie_origem'];
											}
											else */if ($sequencia['curso_destino'] == $curso)
											{
												// curso pertence a sequencia
												$pertence_sequencia = true;
												$serie_sequencia[] = $sequencia['serie_destino'];
												$ini_serie = $sequencia['serie_destino'];
//												reset($sequencias);
												$reset = true;
											}
											else
											{
												$ini_serie = $sequencia['serie_destino'];
//												reset($sequencias);
												$reset = true;
											}
										}
									} while ( each($sequencias) );

									if ($achou_serie && $pertence_sequencia)
									{
										// curso escolhido pertence a sequencia da serie da matricula
										break;
									}
								}
								if (/*$achou_serie && */!$pertence_sequencia)
								{
									$sql = "
									SELECT
										s.cod_serie
										, s.nm_serie
									FROM
										pmieducar.serie s
										, pmieducar.escola_serie es
									WHERE
										es.ref_cod_escola = '{$_GET["esc"]}'
										AND s.cod_serie = es.ref_cod_serie
										AND s.ref_cod_curso = '{$_GET["cur"]}'
										AND s.ativo = 1";
									if (is_array($serie_sequencia))
									{
										foreach ($serie_sequencia as $series)
											$sql .=	" AND s.cod_serie != '{$series}' ";
									}

									$sql .= "
									ORDER BY
										s.nm_serie ASC
									";
									$db2->Consulta( $sql );
									if ($db2->numLinhas())
									{
										while ( $db2->ProximoRegistro() )
										{
											list( $cod, $nome ) = $db2->Tupla();
//											echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
											$lst_serie[$cod] = $nome;
										}
									}
								}
							}
						}
					}
				}
				else if( ($escola != $_GET["esc"]) && ($transferencia_int == 1) )
				{
					// curso matriculado igual ao curso escolhido
					if ($curso == $_GET["cur"])
					{
						// situacao reprovado ou em andamento
						if ( ($aprovado == 2) || ($aprovado == 3) )
						{
							// lista msm serie
							$db2->Consulta( "
							SELECT
								cod_serie
								, nm_serie
							FROM
								pmieducar.serie
							WHERE
								cod_serie = '{$serie}'
								AND ativo = 1
							ORDER BY
								nm_serie ASC
							");

							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									list( $cod, $nome ) = $db2->Tupla();
//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
									$lst_serie[$cod] = $nome;
								}
							}

						} // situacao aprovado
						elseif ($aprovado == 1)
						{
							// lista serie sequencia
							$db2->Consulta( "
							SELECT
								s.cod_serie
								, s.nm_serie
							FROM
								pmieducar.serie s
								, pmieducar.sequencia_serie ss
							WHERE
								ss.ref_serie_origem = '{$serie}'
								AND ss.ref_serie_destino = s.cod_serie
								AND ss.ativo = 1
							ORDER BY
								s.nm_serie ASC
							");

							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									list( $cod, $nome ) = $db2->Tupla();
//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
									$lst_serie[$cod] = $nome;
								}
							}

						}
					} // curso matriculado diferente do curso escolhido
					else
					{
						// curso eh diferente, + faz parte da sequencia
						if ($aprovado == 1)
						{
							// lista serie sequencia
							$db2->Consulta( "
							SELECT
								s.cod_serie
								, s.nm_serie
							FROM
								pmieducar.serie s
								, pmieducar.sequencia_serie ss
							WHERE
								ss.ref_serie_origem = '{$serie}'
								AND ss.ref_serie_destino = s.cod_serie
								AND s.ref_cod_curso = '{$_GET["cur"]}'
								AND ss.ativo = 1
							ORDER BY
								s.nm_serie ASC
							");

							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									list( $cod, $nome ) = $db2->Tupla();
//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
									$lst_serie[$cod] = $nome;
								}
							}
						}
						// curso eh diferente, e nao faz parte da sequencia
						// situacao aprovado, reprovado ou em andamento
						if ( ($aprovado == 1) || ($aprovado == 2) || ($aprovado == 3) )
						{
							// lista somente a 1a serie do curso (sequencia)
							$db2->Consulta( "
							SELECT
								so.ref_cod_curso as curso_origem
								, ss.ref_serie_origem as serie_origem
								, sd.ref_cod_curso as curso_destino
								, ss.ref_serie_destino as serie_destino
							FROM
								pmieducar.sequencia_serie ss
								, pmieducar.serie so
								, pmieducar.serie sd
							WHERE
								ss.ativo = 1
								AND ref_serie_origem = so.cod_serie
								AND ref_serie_destino = sd.cod_serie
							ORDER BY
								ss.ref_serie_origem ASC
							");
							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									$sequencias[] = $db2->Tupla();
								}
							}

							$db2->Consulta( "
							SELECT
								distinct( o.ref_serie_origem )
							FROM
								pmieducar.sequencia_serie o
								, pmieducar.escola_serie es
							WHERE NOT EXISTS
							(
								SELECT
									1
								FROM
									pmieducar.sequencia_serie d
								WHERE
									o.ref_serie_origem = d.ref_serie_destino
							)
							");

							if ($db2->numLinhas())
							{
								$pertence_sequencia = false;
								$achou_serie = false;
								$reset = false;

								while ( $db2->ProximoRegistro() )
								{
									list( $ini_sequencia ) = $db2->Tupla();

									$ini_serie = $ini_sequencia;
									reset($sequencias);

									do
									{
										if( $reset )
										{
											reset($sequencias);
											$reset = false;
										}

										$sequencia = current($sequencias);
										$aux_serie = $sequencia['serie_origem'];

										if ($ini_serie == $aux_serie)
										{
											if ($serie == $aux_serie)
											{
												// achou serie da matricula
												$achou_serie = true;
											}
											/*if ($sequencia['curso_origem'] == $curso)
											{
												// curso pertence a sequencia
												$pertence_sequencia = true;
												$serie_sequencia[] = $sequencia['serie_origem'];
											}
											else */if ($sequencia['curso_destino'] == $curso)
											{
												// curso pertence a sequencia
												$pertence_sequencia = true;
												$serie_sequencia[] = $sequencia['serie_destino'];
												$ini_serie = $sequencia['serie_destino'];
//												reset($sequencias);
												$reset = true;
											}
											else
											{
												$ini_serie = $sequencia['serie_destino'];
//												reset($sequencias);
												$reset = true;
											}
										}
									} while ( each($sequencias) );

									if ($achou_serie && $pertence_sequencia)
									{
										// curso escolhido pertence a sequencia da serie da matricula
										break;
									}
								}
								if (/*$achou_serie && */!$pertence_sequencia)
								{
									$sql = "
									SELECT
										s.cod_serie
										, s.nm_serie
									FROM
										pmieducar.serie s
										, pmieducar.escola_serie es
									WHERE
										es.ref_cod_escola = '{$_GET["esc"]}'
										AND s.cod_serie = es.ref_cod_serie
										AND s.ref_cod_curso = '{$_GET["cur"]}'
										AND s.ativo = 1";
									if (is_array($serie_sequencia))
									{
										foreach ($serie_sequencia as $series)
											$sql .=	" AND s.cod_serie != '{$series}' ";
									}

									$sql .= "
									ORDER BY
										s.nm_serie ASC
									";
									$db2->Consulta( $sql );
									if ($db2->numLinhas())
									{
										while ( $db2->ProximoRegistro() )
										{
											list( $cod, $nome ) = $db2->Tupla();
//											echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
											$lst_serie[$cod] = $nome;
										}
									}
								}
							}
						}
					}
				}
				else if( ($escola != $_GET["esc"]) && (!$transferencia_int) )
				{
					// curso matriculado diferente do curso escolhido
					if ($curso != $_GET["cur"])
					{
						// situacao aprovado, reprovado ou em andamento
							
						if ( ($aprovado == 1) || ($aprovado == 2) )
						{
							// lista somente a 1a serie do curso (sequencia)
							$db2->Consulta( "
							SELECT
								so.ref_cod_curso as curso_origem
								, ss.ref_serie_origem as serie_origem
								, sd.ref_cod_curso as curso_destino
								, ss.ref_serie_destino as serie_destino
							FROM
								pmieducar.sequencia_serie ss
								, pmieducar.serie so
								, pmieducar.serie sd
							WHERE
								ss.ativo = 1
								AND ref_serie_origem = so.cod_serie
								AND ref_serie_destino = sd.cod_serie
							ORDER BY
								ss.ref_serie_origem ASC
							");
							
							if ($db2->numLinhas())
							{
								while ( $db2->ProximoRegistro() )
								{
									$sequencias[] = $db2->Tupla();
								}
							}

							$db2->Consulta( "
							SELECT
								distinct( o.ref_serie_origem )
							FROM
								pmieducar.sequencia_serie o
								, pmieducar.escola_serie es
							WHERE NOT EXISTS
							(
								SELECT
									1
								FROM
									pmieducar.sequencia_serie d
								WHERE
									o.ref_serie_origem = d.ref_serie_destino
							)
							");
							
							if ($db2->numLinhas())
							{
								$pertence_sequencia = false;
								$achou_serie = false;
								$reset = false;

								while ( $db2->ProximoRegistro() )
								{
									list( $ini_sequencia ) = $db2->Tupla();

									$ini_serie = $ini_sequencia;
									reset($sequencias);

									do
									{
										if( $reset )
										{
											reset($sequencias);
											$reset = false;
										}

										$sequencia = current($sequencias);
										$aux_serie = $sequencia['serie_origem'];
										
										if ($ini_serie == $aux_serie)
										{
											if ($serie == $aux_serie)
											{
												// achou serie da matricula
												$achou_serie = true;
											}
											/*if ($sequencia['curso_origem'] == $curso)
											{
												// curso pertence a sequencia
												$pertence_sequencia = true;
												$serie_sequencia[] = $sequencia['serie_origem'];
											}
											else */if ($sequencia['curso_destino'] == $curso)
											{
												// curso pertence a sequencia
												$pertence_sequencia = true;
												$serie_sequencia[] = $sequencia['serie_destino'];
												$ini_serie = $sequencia['serie_destino'];
//												reset($sequencias);
												$reset = true;
											}
											else
											{
												$ini_serie = $sequencia['serie_destino'];
//												reset($sequencias);
												$reset = true;
											}
										}
									} while ( each($sequencias) );

									if ($achou_serie && $pertence_sequencia)
									{
										// curso escolhido pertence a sequencia da serie da matricula
										break;
									}
								}
								if (/*$achou_serie && */$pertence_sequencia)
								{
									$sql = "
									SELECT
										s.cod_serie
										, s.nm_serie
									FROM
										pmieducar.serie s
										, pmieducar.escola_serie es
									WHERE
										es.ref_cod_escola = '{$_GET["esc"]}'
										AND s.cod_serie = es.ref_cod_serie
										AND s.ref_cod_curso = '{$_GET["cur"]}'
										AND s.ativo = 1";

									if (is_array($serie_sequencia))
									{
										foreach ($serie_sequencia as $series)
											$sql .=	" AND s.cod_serie != '{$series}' ";
									}

									$sql .= "
									ORDER BY
										s.nm_serie ASC
									";
									$db2->Consulta( $sql );
									if ($db2->numLinhas())
									{
										while ( $db2->ProximoRegistro() )
										{
											list( $cod, $nome ) = $db2->Tupla();
//											echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
											$lst_serie[$cod] = $nome;
										}
									}
								}
							}
						}
					}
					else 
					{					
						
						// curso matriculado igual ao curso escolhido
						if ($curso == $_GET["cur"])
						{
							
							// situacao reprovado ou em andamento ou transferido
							if ( ($aprovado == 2) || ($transferencia_int == 1) )
							{
								// lista msm serie
								$db2->Consulta("SELECT
													cod_serie
													, nm_serie
												FROM
													pmieducar.serie
												WHERE
													cod_serie = '{$serie}'
													AND ativo = 1
												ORDER BY
													nm_serie ASC
												");
								
								if ($db2->numLinhas())
								{
									while ( $db2->ProximoRegistro() )
									{
										list( $cod, $nome ) = $db2->Tupla();
										//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
										$lst_serie[$cod] = $nome;
									}
								}

							} // situacao aprovado
							elseif ($aprovado == 1)
							{
								// lista serie sequencia
								$db2->Consulta( "
							SELECT
								s.cod_serie
								, s.nm_serie
							FROM
								pmieducar.serie s
								, pmieducar.sequencia_serie ss
							WHERE
								ss.ref_serie_origem = '{$serie}'
								AND ss.ref_serie_destino = s.cod_serie
								AND ss.ativo = 1
							ORDER BY
								s.nm_serie ASC
							");

								if ($db2->numLinhas())
								{
									while ( $db2->ProximoRegistro() )
									{
										list( $cod, $nome ) = $db2->Tupla();
										//									echo "	<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
										$lst_serie[$cod] = $nome;
									}
								}

							}
						}
					}
				}

				if ( empty($resultado) )
				{
					$resultado = $lst_serie;
				}
				else
				{
					$resultado = array_intersect_assoc($lst_serie,$resultado);
				}
			}
			if (!empty($resultado))
			{
				foreach ($resultado as $cod => $nome)
				{
					echo "<serie cod_serie=\"{$cod}\">{$nome}</serie>\n";
				}
			}
		}
	}
	echo "</query>";

?>