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
require_once( "clsConfigItajai.inc.php" );
require_once( "include/clsCronometro.inc.php" );
require_once( "include/clsEmail.inc.php" );
class clsBancoSQL_
{

	/*protected*/var $strHost			=	"";	// Nome ou ip do servidor de dados
	/*protected*/var $strBanco			=	"";	// Nome do Banco de Dados
	/*protected*/var $strUsuario		=	"";	// Usu&aacute;rio devidamente autorizado a acessar o Banco
	/*protected*/var $strSenha			=	"";	// Senha do Usu&aacute;rio do Banco
	/*private*/  var $strFraseConexao 	= "";

	/*protected*/var $bLink_ID			=	0;	// Identificador de Conex&atilde;o
	/*protected*/var $bConsulta_ID		=	0;		// Identificador de Resultado de Consulta
	/*protected*/var $arrayStrRegistro	=	array();	// Tupla resultante de uma consulta
	/*protected*/var $iLinha			=	0;	// Ponteiro interno para a Tupla atual da consulta

	/*protected*/var $bErro_no			=	0;	// Se ocorreu erro na consulta, retorna Falso
	/*protected*/var $strErro			=	"";	// Frase de descri&ccedil;&atilde;o do Erro Retornado
	/*protected*/var $bDepurar			=	false;	// Ativa ou desativa fun&ccedil;oes de depura&ccedil;&atilde;o

	/*protected*/var $bAuto_Limpa		=	false;	//" 1" para limpar o resultado assim que chegar ao &uacute;ltimo registro

	/*private*/var $strStringSQL			=	"";
	/*protected*/var $transactionBlock		=	false;
	/*protected*/var $savePoints			=	array();
	/*protected*/var $showReportarErro		=	true;

	/*public*/ 	var $executandoEcho = false;


	/*
	Monta frase de conexão
	*/
	/*private*/ function FraseConexao()
	{
		$this->strFraseConexao = "";
		if (!empty($this->strHost))
		{
			$this->strFraseConexao .= "host={$this->strHost} ";
		}
		if (!empty($this->strBanco))
		{
			$this->strFraseConexao .= "dbname={$this->strBanco} ";
		}
		if (!empty($this->strUsuario))
		{
			$this->strFraseConexao .= "user={$this->strUsuario} ";
		}
		if (!empty($this->strSenha))
		{
			$this->strFraseConexao .= "password={$this->strSenha} ";
		}
	}

	/*
	Conecta-se em Banco, com Usuario e Senha, na Porta do Host
	e guarda o indentificador da conex&atilde;o em Link_ID.
	Se a conex&atilde;o falhar ele interrompe a execu&ccedil;&atilde;o do script.
	*/
	/*private*/ function Conecta()
	{
		/*
		Testa se o link est&aacute; ativo
		Se estiver inativo,
		junta as propriedades que tiveram valores atribuidos
		para formar a Frase de conex&atilde;o
		*/
		if ( 0 == $this->bLink_ID )
		{
			$this->FraseConexao();

			/* Fun&ccedil;&atilde;o para depura&ccedil;&atilde;o */
			if ($this->bDepurar)
			{
				printf("<br>Depurar: Frase de Conex&atilde;o : %s, %s, %s<br>\n", $this->strFraseConexao);
			}

			$this->bLink_ID=pg_connect($this->strFraseConexao);

			if (!$this->bLink_ID)
			{
				$this->Interrompe("Link inv&aacute;lido, conex&atilde;o falhou!");
			}
			//$this->Consulta( "SET TIMEZONE TO gmt2" );
		}
	}

	/*
	Executa uma instru&ccedil;&atilde;o SQL e retorna um identificador para o resultado
	Se a frase SQL for inv&aacute;lida ele interrompe a execu&ccedil;&atilde;o do script.
	*/

	/*private*/ function Consulta( $consulta )
	{
		$cronometro = new clsCronometro();
		$cronometro->marca( "inicio" );
		/* Testa se a consulta é vazia */
		if (empty($consulta))
		{
			return false;
		}
		else
		{
			$this->strStringSQL = $consulta;
		}

		$this->strStringSQLOriginal = $this->strStringSQL;

		$this->Conecta();

		/* Funcao para depuracao */
		if ($this->bDepurar)
		{
			printf("<br>Depurar: Frase de Consulta = %s<br>\n", $this->strStringSQL);
		}

		/*
			Alteracoes de padrao MySQL para PostgreSQL
		*/

		// Altera o Limit
		$this->strStringSQL = eregi_replace( "LIMIT[ ]{0,3}([0-9]+)[ ]{0,3},[ ]{0,3}([0-9]+)", "LIMIT \\2 OFFSET \\1", $this->strStringSQL );
		// Altera selects com YEAR( campo ) ou MONTH ou DAY
		$this->strStringSQL = eregi_replace( "(YEAR|MONTH|DAY)[(][ ]{0,3}(([a-z]|_|[0-9])+)[ ]{0,3}[)]", "EXTRACT( \\1 FROM \\2 )", $this->strStringSQL );
		// Remove os ORDER BY das querys COUNT()
		//$this->strStringSQL = eregi_replace( "(SELECT.*COUNT[(][0|\*][)].*FROM.*)(ORDER BY.*)", "\\1", $this->strStringSQL );
		// Altera os LIKE para ILIKE (ignore case)
		$this->strStringSQL = eregi_replace( " LIKE ", " ILIKE ", $this->strStringSQL );

		$this->strStringSQL = eregi_replace( "([a-z_0-9.]+) +ILIKE +'([^']+)'", "to_ascii(\\1) ILIKE to_ascii('\\2')", $this->strStringSQL );
		$this->strStringSQL = eregi_replace( "fcn_upper_nrm", "to_ascii", $this->strStringSQL );
		/*
			Verificacoes de Injection
		*/
		if($_GET['depurar'] == 'mostraasquerypramim')
		{
			echo $this->strStringSQL."<br><br>";
		}


		$temp = explode( "'", $this->strStringSQL );
		for ( $i = 0; $i < count( $temp ); $i++ )
		{
			// ignora o que esta entre aspas
			if( ! ( $i % 2 ) )
			{
				// fora das aspas, verifica se há algo errado no SQL
				if( eregi( "(--|#|/\*)", $temp[$i] ) )
				{
					$erroMsg = "Protecao contra injection: " . date( "Y-m-d H:i:s" );
					echo "<!-- {$this->strStringSQL} -->";
					$this->Interrompe( $erroMsg );
				}
			}
		}

		/* Executa a Consulta */
		if ($this->executandoEcho)
		{
			echo $this->strStringSQL."\n<br>";
			//return true;
		}

		$this->bConsulta_ID = pg_query($this->bLink_ID, $this->strStringSQL);// or $this->Interrompe( "Ocorreu um erro ao consultar o banco de dados.", true );
		$this->strErro = pg_result_error($this->bConsulta_ID);
		$this->bErro_no = ($this->strErro == "") ? false : true;


		$this->iLinha   = 0;

		/* Testa se a consulta foi v&aacute;lida */
		if ( !$this->bConsulta_ID )
		{
			$erroMsg = "SQL invalido: {$this->strStringSQL}<br>\n";
			die( $erroMsg );
			if( $this->transactionBlock )
			{
				/*
				if( count( $this->savePoints ) )
				{
					$this->rollBack();
					$erroMsg .= "<!-- retornando ao ultimo SavePoint -->\n";
				}
				*/
			}
			$this->Interrompe( $erroMsg );
			return false;
			//$this->Interrompe("SQL invalido: ".$this->strStringSQL);
		}

		$cronometro->marca( "fim" );
		$tempoTotal = $cronometro->getTempoTotal();

		$objConfig = new clsConfig();
		if( $tempoTotal > $objConfig->arrayConfig["intSegundosQuerySQL"] )
		{
			$conteudo = "<table border=\"1\" width=\"100%\">";
			$conteudo .= "<tr><td><b>Data</b>:</td><td>" . date( "d/m/Y H:i:s", time() ) . "</td></tr>";
			$conteudo .= "<tr><td><b>Script</b>:</td><td>{$_SERVER["PHP_SELF"]}</td></tr>";
			$conteudo .= "<tr><td><b>Tempo da query</b>:</td><td>{$tempoTotal} segundos</td></tr>";
			$conteudo .= "<tr><td><b>Tempo max permitido</b>:</td><td>{$objConfig->arrayConfig["intSegundosQuerySQL"]} segundos</td></tr>";
			$conteudo .= "<tr><td><b>SQL Query Original</b>:</td><td>{$this->strStringSQLOriginal}</td></tr>";
			$conteudo .= "<tr><td><b>SQL Query Executado</b>:</td><td>{$this->strStringSQL}</td></tr>";
			$conteudo .= "<tr><td><b>URL get</b>:</td><td>{$_SERVER['QUERY_STRING']}</td></tr>";
			$conteudo .= "<tr><td><b>Metodo</b>:</td><td>{$_SERVER["REQUEST_METHOD"]}</td></tr>";
			if ( $_SERVER["REQUEST_METHOD"] == "POST" )
			{
				$conteudo .= "<tr><td><b>POST vars</b>:</td><td>";
				foreach ( $_POST AS $var => $val )
				{
					$conteudo .= "{$var} => {$val}<br>";
				}
				$conteudo .= "</td></tr>";
			} else if ( $_SERVER["REQUEST_METHOD"] == "GET" )
			{
				$conteudo .= "<tr><td><b>GET vars</b>:</td><td>";
				foreach ( $_GET AS $var => $val )
				{
					$conteudo .= "{$var} => {$val}<br>";
				}
				$conteudo .= "</td></tr>";
			}
			if( $_SERVER["HTTP_REFERER"] )
			{
				$conteudo .= "<tr><td><b>Referrer</b>:</td><td>{$_SERVER["HTTP_REFERER"]}</td></tr>";
			}
			$conteudo .= "</table>";

			$objEmail = new clsEmail( $objConfig->arrayConfig['ArrStrEmailsAdministradores'], "[INTRANET - PMI] Desempenho de query", $conteudo );
			$objEmail->envia();
		}

		return $this->bConsulta_ID;
	}

	/**
	 * Inicia umbloco de transacao (transaction block)
	 *
	 * @return bool
	 */
	function begin()
	{
		if( ! $this->transactionBlock )
		{
			$this->Consulta( "BEGIN" );
			$this->transactionBlock = true;
			// reseta os savePoints
			$this->savePoints = array();
			return true;
		}
		// tratamento de erro informando que ja esta dentro de um transaction block
		return false;
	}

	/**
	 * Processa umbloco de transacao (transaction block)
	 *
	  * @return bool
	 */
	function commit()
	{
		if( $this->transactionBlock )
		{
			$this->Consulta( "COMMIT" );
			$this->transactionBlock = false;
			// reseta os savePoints
			$this->savePoints = array();
			return true;
		}
		// tratamento de erro informando que nao esta dentro de um transaction block
		return false;
	}

	/**
	 * Cria um novo savePoint
	 *
	  * @param string  $strSavePointName [Opcional] nome do savePoint a ser criado
	 *  @return bool
	 */
	function savePoint( $strSavePointName=false )
	{
		if( $this->transactionBlock )
		{
			if( $strSavePointName )
			{
				foreach ( $this->savePoints AS $key => $nome )
				{
					// nao podemos ter dois savepoints com o mesmo nome
					if( $nome == $strSavePointName )
					{
						return false;
					}
				}
				$this->savePoints[] = $strSavePointName;
				$this->Consulta( "SAVEPOINT $strSavePointName" );
				return true;
			}
			else
			{
				$nome = "save_" . count( $this->savePoints );
				$this->savePoints[] = $nome;
				$this->Consulta( "SAVEPOINT $nome" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Cria um novo savePoint
	 *
	  * @param string  $strSavePointName nome do savePoint onde se deseja voltar, se nao for definido volta ao ultimo savepoint criado
	 *  @return bool
	 */
	function rollBack( $strSavePointName=false )
	{
		if( $this->transactionBlock )
		{
			if( count( $this->savePoints ) )
			{
				if( $strSavePointName )
				{
					foreach ( $this->savePoints AS $key => $nome )
					{
						// se achar eh porque tem o savePoint
						if( $nome == $strSavePointName )
						{
							$this->savePoints = array_slice( $this->savePoints, 0, $key );
							$this->Consulta( "ROLLBACK TO {$strSavePointName}" );
							return true;
						}
					}
				}
				else
				{
					// se nao tem um nome definido ele volta ao ultimo savePoint
					$lastPos = count( $this->savePoints ) - 1;
					$strSavePointName = $this->savePoints[$lastPos];
					$this->savePoints = array_slice( $this->savePoints, 0, ( $lastPos - 1 ) );
					$this->Consulta( "ROLLBACK TO {$strSavePointName}" );
				}
			}
		}
		return false;
	}

	/*
	* metodo que retorna o valor do ultimo ID inserido em uma query
	*/
	/*private*/ function InsertId( $str_sequencia = false )
	{
		// return pg_last_oid($this->bConsulta_ID);
		if( $str_sequencia )
		{
			$this->Consulta( "SELECT currval('{$str_sequencia}'::text)" );
			$this->ProximoRegistro();
			list( $valor ) = $this->Tupla();
			return $valor;
		}
		return false;
	}

	function UltimoID( $str_sequencia = false )
	{
		return $this->InsertId( $str_sequencia );
	}

	/*
	Avan&ccedil;a um resgistro no resultado da consulta corrente,
	retorna Falso se chegar ao fim do resultado.
	É necess&aacute;rio se fazer uma chamada a essa fun&ccedil;&atilde;o
	antes de se acessar o primeiro registro do resultado,
	se o resultado for vazio, ele reotrna Falso,
	sen&atilde;o posiciona para leitura o primeiro registro.
	*/
	/*public*/ function ProximoRegistro()
	{
		/*
		Carrega Registro com o resultado da Tupla indexada por Linha
		e incrementa Linha
		*/
		$this->arrayStrRegistro = @pg_fetch_array($this->bConsulta_ID);

		/* Testa por erros */
		$this->strErro = pg_result_error($this->bConsulta_ID);
		$this->bErro_no = ($this->strErro == "")? false : true ;

		/*
		Testa se Registro est&aacute; vazio,
		sinal que se chegou ao fim da Consulta.
		Verifica ent&atilde;o de Auto_Limpa é Verdade,
		se for, limpa o resultado da consulta.
		*/
		$stat = is_array($this->arrayStrRegistro);
		if ($this->bDepurar && $stat)
			printf("<br>Depurar: Registro : %s <br>\n", implode($this->arrayStrRegistro));
		if (!$stat && $this->bAuto_Limpa)
		{
			$this->Libera();
		}
		return $stat;
	}

	/*public*/ function Procura($Pos)
	{
		$this->iLinha = $Pos;
	}

	/*
	Retorna as linhas afetadas em Consultas com instru&ccedil;&otilde;es
	INSERT, UPDATE e DELETE
	*/
	/*public*/ function Linhas_Afetadas()
	{
		return @pg_affected_rows($this->bConsulta_ID);
	}

	/*
	Retorna as linhas afetadas em Consultas com instru&ccedil;&otilde;es
	INSERT, UPDATE e DELETE
	*/
	/*public*/ function Libera()
	{
		pg_free_result($this->bConsulta_ID);
		$this->bConsulta_ID = 0;
		$this->strStringSQL = "";
	}

	/**
	 * Alias para numLinhas mantido para compatibilidade com versões anteriores
	 */
	/*public*/ function Num_Linhas()
	{
		return $this->numLinhas();
	}

	/**
	 * Retorna o número de linhas do resultado de um Consulta
	 */
	/*public*/ function numLinhas()
	{
		return pg_num_rows($this->bConsulta_ID);
	}

	/**
	 * Alias para numCampos mantido para compatibilidade com versões anteriores
	 */
	/*public*/ function Num_Campos()
	{
		return $this->numCampos();
	}

	/**
	 * Retorna o número de campo do resultado de um Consulta
	 */
	/*public*/ function numCampos()
	{
		return pg_num_fields($this->bConsulta_ID);
	}

	/*
	Retorna o valor do campo Nome de Consulta	com instru&ccedil;&atilde;o SELECT
	*/
	/*public*/ function Campo($Nome)
	{
		return $this->arrayStrRegistro[$Nome];
	}

	/*
	Retorna a tupla atual da Consulta atual
	*/
	/*public*/ function Tupla()
	{
		return $this->arrayStrRegistro;
	}

	/*
	Retorna um único campo de uma pesquisa de uma tupla
	*/
	/*public*/ function UnicoCampo($consulta)
	{
		$this->Consulta($consulta);
		if( $this->ProximoRegistro() )
		{
			list ($campo) = $this->Tupla();
			$this->Libera();
			return $campo;
		}
		return false;
	}
	/*
	Retorna um único campo de uma pesquisa de uma tupla
	*/
	/*public*/ function CampoUnico($consulta)
	{
		return $this->UnicoCampo( $consulta );
	}

	/*
	Retorna uma única tupla de uma pesquisa
	*/
	/*public*/ function UnicaTupla($consulta)
	{
		$this->Consulta($consulta);
		$this->ProximoRegistro();
		$tupla = $this->Tupla();
		$this->Libera();
		return $tupla;
	}

	/*
	Retorna uma única tupla de uma pesquisa
	*/
	/*public*/ function TuplaUnica($consulta)
	{
		return $this->UnicaTupla;
	}

	/*
	Interrompe a execu&ccedil;&atilde;o do Programa e Imprime mensagens de Erro
	*/
	/*private*/ function Interrompe($msg, $getError = false )
	{
		if( $getError )
		{
			$this->strErro = pg_result_error($this->bConsulta_ID);
			$this->bErro_no = ($this->strErro == "") ? false : true;
		}
		/*
		printf("</td></tr></table><br><b>arquivo {$_SERVER['SCRIPT_FILENAME']}<br><br>Erro de Banco de Dados:</b> %s<br>\n", $msg);
		printf("<b>Erro do PgSQL </b>: %s (%s)<br>\n", $this->bErro_no, $this->strErro);
		die("Sess&atilde;o Interrompida.");
		*/
		$erro1 = substr(md5('1erro'), 0, 10);
		$erro2 = substr(md5('2erro'), 0, 10);

		function show($data, $func = "var_dump")
		{
			ob_start();
			$func( $data );
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		@session_start();
		$_SESSION['vars_session'] = show( $_SESSION );
		$_SESSION['vars_post'] = show( $_POST );
		$_SESSION['vars_get'] = show( $_GET );
		$_SESSION['vars_cookie'] = show( $_COOKIE );
		$_SESSION['vars_erro1'] = $msg;
		$_SESSION['vars_erro2'] = $this->strErro;
		$_SESSION['vars_server'] = show( $_SERVER );
		$id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		if( $this->showReportarErro )
		{
			$array_idpes_erro_total = array();
			$array_idpes_erro_total[4910] = true;
			$array_idpes_erro_total[2151] = true;
			$array_idpes_erro_total[8194] = true;
			$array_idpes_erro_total[7470] = true;
			$array_idpes_erro_total[4637] = true;
			$array_idpes_erro_total[4702] = true;
			$array_idpes_erro_total[1801] = true;

			if( ! $array_idpes_erro_total[$id_pessoa] )
			{
				die( "<script>document.location.href = 'erro_banco.php';</script>" );
			}
			else
			{
				printf("</td></tr></table><b>Erro de Banco de Dados:</b> %s<br><br>\n", $msg);
				printf("<b>SQL:</b> %s<br><br>\n", $this->strStringSQL );
				printf("<b>Erro do PgSQL </b>: %s (%s)<br><br>\n", $this->bErro_no, $this->strErro);
				die("Sess&atilde;o Interrompida.");
			}
		}
		else
		{
			//echo $msg . "\n";
			die( $this->strErro . "\n" );
		}
	}
}
?>
