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

  require_once '../includes/bootstrap.php';
  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryForDisabledApi();

	require_once( "include/clsBanco.inc.php" );
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";
	if( isset( $_GET["dp"] ) && isset( $_GET["hp"] ) && isset( $_GET["dc"] ) && isset( $_GET["hc"] ) && isset( $_GET["grupo"] ) && isset( $_GET["est"] ) )
	{
		$result_100 = 0;
		$result_75 = 0;
		$result_50 = 0;
		$result_25 = 0;

		// trata os valores de data e hora
		$data = explode( "_", $_GET["dp"] );
		$data_partida = "{$data[2]}-{$data[1]}-{$data[0]}";

		$data = explode( "_", $_GET["dc"] );
		$data_chegada = "{$data[2]}-{$data[1]}-{$data[0]}";

		$hora_p = explode( "_", $_GET["hp"] );
		$hora_partida = "{$hora_p[0]}:{$hora_p[1]}";

		$hora_c = explode( "_", $_GET["hc"] );
		$hora_chegada = "{$hora_c[0]}:{$hora_c[1]}";

		// calcula quantas diarias 100% 75% etc

		$date_partida = "{$data_partida} {$hora_partida}";
		$date_chegada = "{$data_chegada} {$hora_chegada}";

		$time_partida .= strtotime( $date_partida );
		$time_chegada .= strtotime( $date_chegada );

		$diferenca = $time_chegada - $time_partida;
		$dif_original = $diferenca;

		if( $diferenca > 0 )
		{
			// se fechar 24 horas eh 100% ( 60 * 60 * 24 )
			$result_100 = floor( $diferenca / 86400 );
			$diferenca -= $result_100 * 86400;

			$dif_horas = floor( $diferenca / ( 60 * 60 ) );
			//verifica se deu algum de 12 horas ( 60 * 60 * 12 )
			if( $diferenca >= 43200 )
			{
				// tem 12 horas, verifica se foi na virada da noite (com pernoite eh 75% sem pernoite eh 50%)
				if( $hora_p[0] + $dif_horas >= 22 )
				{
					$result_75++;
					// reduzimos 12 horas
					$diferenca -= 43200;
				}
				else
				{
					$result_50++;
					// reduzimos 12 horas
					$diferenca -= 43200;
				}
			}

			//verifica se deu algum de 11 horas ( 60 * 60 * 11 )
			if( $diferenca >= 39600 )
			{
				$result_50++;
				$diferenca -= 39600;
			}

			//verifica se deu algum de 11 horas ( 60 * 60 * 10 )
			if( $diferenca >= 36000 )
			{
				$result_50++;
				$diferenca -= 36000;
			}

			//verifica se deu algum de 11 horas ( 60 * 60 * 9 )
			if( $diferenca >= 32400 )
			{
				$result_50++;
				$diferenca -= 32400;
			}

			//verifica se deu algum de 11 horas ( 60 * 60 * 8 )
			if( $diferenca >= 28800 )
			{
				$result_50++;
				$diferenca -= 28800;
			}

			//verifica se deu algum de 11 horas ( 60 * 60 * 7 )
			if( $diferenca >= 25200 )
			{
				$result_50++;
				$diferenca -= 25200;
			}

			//verifica se deu algum de 11 horas ( 60 * 60 * 6 )
			if( $diferenca >= 21600 )
			{
				$result_50++;
				$diferenca -= 21600;
			}

			//verifica se deu algum de 11 horas ( 60 * 60 * 5 )
			if( $diferenca >= 18000 )
			{
				$result_25++;
				$diferenca -= 18000;
			}

			//verifica se deu algum de 4 horas ( 60 * 60 * 4 )
			if( $diferenca >= 14400 )
			{
				$result_25++;
				$diferenca -= 14400;
			}

			$db = new clsBanco();
			$db->Consulta( "SELECT p100, p75, p50, p25 FROM pmidrh.diaria_valores WHERE ref_cod_diaria_grupo = '{$_GET["grupo"]}' AND estadual='{$_GET["est"]}' AND data_vigencia < '{$date_partida}' ORDER BY data_vigencia DESC LIMIT 1 OFFSET 0" );
			if ( $db->ProximoRegistro() )
			{
				list( $p100, $p75, $p50, $p25 ) = $db->Tupla();
			}
			else
			{
				$db->Consulta( "SELECT p100, p75, p50, p25 FROM pmidrh.diaria_valores WHERE ref_cod_diaria_grupo = '{$_GET["grupo"]}' AND data_vigencia < '{$date_partida}' ORDER BY data_vigencia DESC LIMIT 1 OFFSET 0" );
				$db->ProximoRegistro();
				list( $p100, $p75, $p50, $p25 ) = $db->Tupla();
			}

			$vl100 = $result_100 * $p100;
			$vl75 = $result_75 * $p75;
			$vl50 = $result_50 * $p50;
			$vl25 = $result_25 * $p25;

			echo "	<item>" . number_format( $vl100, 2, ",", "." ) . "</item>\n";
			echo "	<item>" . number_format( $vl75, 2, ",", "." ) . "</item>\n";
			echo "	<item>" . number_format( $vl50, 2, ",", "." ) . "</item>\n";
			echo "	<item>" . number_format( $vl25, 2, ",", "." ) . "</item>\n";
		}
		else
		{
			echo "	<item>erro!</item>\n	<item>erro!</item>\n	<item>erro!</item>\n	<item>erro!</item>\n";
		}
	}
	else
	{
		echo "	<item>erro!</item>\n	<item>erro!</item>\n	<item>erro!</item>\n	<item>erro!</item>\n";
	}
	echo "</query>";
?>