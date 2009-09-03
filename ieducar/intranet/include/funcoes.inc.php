<?php
/**
 *
 * @version SVN: $Id$
 * @author  Prefeitura Municipal de Itajaí
 * @updated 29/03/2007
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí
 *					ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

	/**
	 * Adiciona zeros a esquerda de um numero
	 *
	 * @param int $num
	 * @param int $digitos
	 * @return string
	 */
	function addLeadingZero( $num, $digitos = 2 )
	{
		if( is_numeric($num) )
		{
			if ( $digitos > 1 )
			{
				for ($i=1;$i<$digitos;$i++)
				{
					if($num<pow(10,$i))
					{
						$num = str_repeat("0", $digitos - $i ) . $num;
						break;
					}
				}
			}
			return $num;
		}
		return str_repeat("0", $digitos );
	}

	function add2LeadingZero( $num )
	{
		return addLeadingZero($num,3);
	}

	function calculoIdade($diaNasc,$mesNasc,$anoNasc)
	{
		list ($dia,$mes,$ano) = explode("/",date("d/m/Y"));
		$idade = $ano-$anoNasc;
		$idade = (($mes<$mesNasc) OR (($mes==$mesNasc) AND ($dia<$diaNasc))) ? --$idade : $idade;
		return $idade;
	}

	function idFederal2int( $str )
	{
		$id_federal = str_replace( ".", "", str_replace( "-", "", str_replace( "/", "", $str ) ) );
		return ereg_replace("^0+","",$id_federal);
	}

	function int2CPF( $int )
	{
		$str = str_repeat( "0", 11 - strlen( $int ) ) . $int;
		return substr( $str, 0, 3 ) . "." . substr( $str, 3, 3 ). "." . substr( $str, 6, 3 ) . "-" . substr( $str, 9, 2 );
	}

	function int2Fone( $int )
	{
		return substr( $int, 0, -4 ) . "-" . substr( $int, -4 );
	}

	function int2CNPJ( $int )
	{
		if( strlen( $int ) < 14 )
		{
			$str = str_repeat( "0", 14 - strlen( $int ) ) . $int;
		}
		else
		{
			$str = $int;
		}
		return substr( $str, 0, 2 ) . "." . substr( $str, 2, 3 ). "." . substr( $str, 5, 3 ) . "/" . substr( $str, 8, 4 ) . "-" . substr( $str, 12, 2 );
	}

  /**
   * Formata um valor numérico em uma representação string de CEP.
   *
   * @param  string|int  $int
   * @return string
   */
  function int2CEP($int)
  {
    if ($int) {
      $int = (string) str_pad($int, 8, '0', STR_PAD_LEFT);
      return substr($int, 0, 5) . '-' . substr($int, 5, 3);
    }
    else {
      return '';
    }
  }

	function limpa_acentos( $str_nome )
	{
		$procura1 = array( "á","é","í","ó","ú","à","è","ì","ò","ù","ä","ë","ï","ö","ü","ç","ã","õ", "ô", "ê" );
		$substitui1 = array( "a","e","i","o","u","a","e","i","o","u","a","e","i","o","u","c","a","o", "o", "e" );

		$procura2 = array( "Á","É","Í","Ó","Ú","À","È","Ì","Ò","Ù","Ä","Ë","Ï","Ö","Ü","Ç","Ã", "Õ", "Ê", "Ô" );
		$substitui2 = array( "A","E","I","O","U","A","E","I","O","U","A","E","I","O","U","C","A", "O", "E", "O" );
		$str_nome = str_replace( $procura1, $substitui1, $str_nome );
		$str_nome = str_replace( $procura2, $substitui2, $str_nome );
		return $str_nome;
	}

	function transforma_minusculo( $str_nome )
	{
		$nome = strtolower($str_nome);
		$arrayNome = explode(" ", $nome);
		$nome ="";
		foreach ($arrayNome as $parte) {
			if( $parte != "de" && $parte != "da" && $parte != "dos" && $parte != "do" && $parte != "das" && $parte != "e")
			{
				$nome .= strtoupper(substr($parte,0,1)).substr($parte,1)." ";
			}
			else
			{
				$nome .= $parte." ";
			}
		}
		$procura1 = array( "Á","É","Í","Ó","Ú","À","È","Ì","Ò","Ù","Ä","Ë","Ï","Ö","Ü","Ç","Ã", "Õ", "Â", "Ô" );
		$substitui1 = array( "á","é","í","ó","ú","à","è","ì","ò","ù","ä","ë","ï","ö","ü","ç","ã", "õ", "â", "ô" );

		$nome = str_replace($procura1, $substitui1, $nome);

		return $nome;
	}

	function quebra_linhas_pdf( $str_texto, $qtd_letras_linha = 60)
	{
		$comp_comp = str_replace("\n"," ", $str_texto );
		$tamanho_linha = $qtd_letras_linha;
		$gruda ="";
		$compromisso2 = "";
		while (strlen($comp_comp) > $tamanho_linha)
		{
			$i = $tamanho_linha;
			while (substr($comp_comp,$i,1) != " " && $i > 0) {
				$i--;
			}
			if($i == 0) $i=$tamanho_linha;
			$compromisso2 .= $gruda.substr($comp_comp,0,$i);
			$comp_comp = substr($comp_comp,$i);
			$gruda = "\n";
		}
		$compromisso2 .= "$gruda $comp_comp";

		$comp_comp = ($compromisso2) ? $compromisso2: $comp_comp;
		$comp_comp = str_replace("\n  ", "\n",$comp_comp);
		$comp_comp = str_replace("\n ", "\n",$comp_comp);
		return  $comp_comp;
	}

	/*
		Funcoes foneticas (segundo as mesmas regras das funcoes do banco PG)
	*/
	function fonetiza_palavra( $palavra )
	{
		$i = -1;
		$fonetizado = "";

		// limpa todas as letras acentuadas e passa para minusculas
		$acentuadasMin = array( "á", "é", "í", "ó", "ú", "â", "ê", "î", "ô", "û", "ä", "ë", "ï", "ö", "ü", "à", "è", "ì", "ò", "ù", "ã", "?", "õ", "?", "ı", "ÿ", "ñ", "ç" );
		$acentuadasMai = array( "Á", "É", "Í", "Ó", "Ú", "Â", "Ê", "Î", "Ô", "Û", "Ä", "Ë", "Ï", "Ö", "Ü", "À", "È", "Ì", "Ò", "Ù", "Ã", "?", "Õ", "?", "İ", "?", "Ñ", "Ç" );
		$letras_ok = array( "a", "e", "i", "o", "u", "a", "e", "i", "o", "u", "a", "e", "i", "o", "u", "a", "e", "i", "o", "u", "a", "i", "o", "u", "y", "y", "n", "c" );
		$palavra = str_replace( $acentuadasMin, $letras_ok, $palavra );
		$palavra = str_replace( $acentuadasMai, $letras_ok, $palavra );
		$palavra = strtolower( $palavra );

		// loop nas letras
		while ( $i++ < strlen( $palavra ) )
		{
			// define as letras
			$letra_atual = substr( $palavra, $i, 1 );
			$letra_prox = substr( $palavra, $i + 1, 1 );
			$letra_prox2 = substr( $palavra, $i + 2, 1 );

			if( $i )
			{
				$letra_ante = substr( $palavra, $i, -1 );
			}
			else
			{
				$letra_ante = "";
			}

			// numeros - ok
			if( is_numeric( $letra_atual ) )
			{
				$fonetizado .= $letra_atual;
				continue;
			}
			// letras iguais - pula
			if ( $letra_atual == $letra_prox )
			{
				continue;
			}
			// A I ou O - ok
			if ( $letra_atual == "a" || $letra_atual == "i" || $letra_atual == "o" )
			{
				$fonetizado .= $letra_atual;
				continue;
			}
			// E
			if ( $letra_atual == "e" )
			{
				$fonetizado .= "i";
				continue;
			}
			// R
			if ( $letra_atual == "r" )
			{
				$fonetizado .= "h";
				continue;
			}
			// S
			if ( $letra_atual == "s" )
			{
				if( $letra_prox != "a" && $letra_prox != "e" && $letra_prox != "i" && $letra_prox != "o" && $letra_prox != "u" && $letra_prox != "y" && strlen( $fonetizado ) == 0 )
				{
					$fonetizado .= "is";
					continue;
				}
				if( $letra_prox == "c" && $letra_prox2 == "h" )
				{
					continue;
				}
				if( $letra_prox == "h" )
				{
					$fonetizado .= "ks";
					$i++;
					continue;
				}
				$fonetizado .= $letra_atual;
				continue;
			}
			// N
			if ( $letra_atual == "n" )
			{
				if( $letra_prox == "h" )
				{
					$fonetizado .= "ni";
					continue;
				}
				if( $letra_prox != "a" && $letra_prox != "e" && $letra_prox != "i" && $letra_prox != "o" && $letra_prox != "u" && $letra_prox != "y" )
				{
					$fonetizado .= "m";
					continue;
				}
				$fonetizado .= $letra_atual;
				continue;
			}
			// L
			if ( $letra_atual == "l" )
			{
				if( $letra_prox == "h" )
				{
					$fonetizado .= "li";
					continue;
				}
				if( $letra_prox != "a" && $letra_prox != "e" && $letra_prox != "i" && $letra_prox != "o" && $letra_prox != "u" && $letra_prox != "y" )
				{
					$fonetizado .= "o";
					continue;
				}
				$fonetizado .= $letra_atual;
			}
			// D
			if ( $letra_atual == "d" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" || $letra_prox == "h" )
				{
					$fonetizado .= "d";
					continue;
				}
				$fonetizado .= "di";
				continue;
			}
			// C
			if ( $letra_atual == "c" )
			{
				if( $letra_prox == "h" && ( $letra_prox2 == "a" || $letra_prox2 == "e" || $letra_prox2 == "i" || $letra_prox2 == "o" || $letra_prox2 == "u" || $letra_prox2 == "y" ) )
				{
					$fonetizado .= "ks";
					continue;
				}
				if( $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "y" )
				{
					$fonetizado .= "s";
					continue;
				}
				if( $letra_prox == "a" || $letra_prox == "o" || $letra_prox == "u" )
				{
					$fonetizado .= "k";
					continue;
				}
			}
			// M
			if ( $letra_atual == "m" )
			{
				if( $letra_prox != "n" )
				{
					$fonetizado .= $letra_atual;
					continue;
				}
			}
			// T
			if ( $letra_atual == "t" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" || $letra_prox == "h" )
				{
					$fonetizado .= $letra_atual;
					continue;
				}
				$fonetizado .= "ti";
				continue;
			}
			// U
			if ( $letra_atual == "u" )
			{
				$fonetizado .= "o";
				continue;
			}
			// V
			if ( $letra_atual == "v" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" || $letra_prox == "h" )
				{
					$fonetizado .= $letra_atual;
					continue;
				}
				$fonetizado .= "vi";
				continue;
			}
			// G
			if ( $letra_atual == "g" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" )
				{
					if( $letra_prox == "u" )
					{
						if( $letra_prox2 == "e" || $letra_prox2 == "i" || $letra_prox2 == "y" )
						{
							$fonetizado .= "j";
							$i++;
							continue;
						}
					}
					$fonetizado .= "j";
					continue;
				}
				$fonetizado .= "ji";
				continue;
			}
			// B
			if ( $letra_atual == "b" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" || $letra_prox == "h" )
				{
					$fonetizado .= $letra_atual;
					continue;
				}
				$fonetizado .= "bi";
				continue;
			}
			// P
			if ( $letra_atual == "p" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" )
				{
					$fonetizado .= $letra_atual;
					continue;
				}
				if( $letra_prox == "h" )
				{
					$fonetizado .= "f";
					continue;
				}
				$fonetizado .= "f";
				continue;
			}
			// Z
			if ( $letra_atual == "z" )
			{
				$fonetizado .= "s";
				continue;
			}
			// F
			if ( $letra_atual == "f" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" || $letra_prox == "h" )
				{
					$fonetizado .= $letra_atual;
					continue;
				}
				$fonetizado .= "fi";
				continue;
			}
			// J
			if ( $letra_atual == "j" )
			{
				$fonetizado .= $letra_atual;
				continue;
			}
			// K
			if ( $letra_atual == "k" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" || $letra_prox == "h" )
				{
					$fonetizado .= $letra_atual;
					continue;
				}
				$fonetizado .= "ki";
				continue;
			}
			// Y
			if ( $letra_atual == "y" )
			{
				$fonetizado .= "i";
				continue;
			}
			// W
			if ( $letra_atual == "w" )
			{
				if( $i == 0 )
				{
					if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" )
					{
						$fonetizado .= "v";
						continue;
					}
					$fonetizado .= "vi";
					continue;
				}
				if( $letra_ante == "e" || $letra_ante == "i" )
				{
					if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" )
					{
						$fonetizado .= "v";
						continue;
					}
					$fonetizado .= "o";
					continue;
				}
				$fonetizado .= "v";
				continue;
			}
			// Q
			if ( $letra_atual == "q" )
			{
				if( $letra_prox == "a" || $letra_prox == "e" || $letra_prox == "i" || $letra_prox == "o" || $letra_prox == "u" || $letra_prox == "y" )
				{
					$fonetizado .= "k";
					if( $letra_prox == "u" )
					{
						$i++;
					}
					continue;
				}
				$fonetizado .= "qi";
				continue;
			}
			// X
			if ( $letra_atual  == "x" )
			{
				$fonetizado .= "ks";
				continue;
			}
		}
		return $fonetizado;
	}

	function fonetiza_texto( $texto )
	{
		$fonetico = "";

		$texto = str_replace( "-", " ", $texto );
		$array = explode( " ", $texto );
		foreach ( $array AS $palavra )
		{
			if( strlen( $fonetico ) )
			{
				$fonetico .= " ";
			}
			$fonetico .= fonetiza_palavra( $palavra );
		}
	}

	/*
		retorna 1 se data1 for maior que a data2,
		retorna 0 se a data1 for menor que a data2,
		retorna 2 se forem iguais.
	*/
	function data_maior($data1, $data2)
	{
		$data1 = explode("/",$data1);
		$data2 = explode("/",$data2);
		if($data1[2] > $data2[2])
		{
			return 1;
		}elseif($data1[2] < $data2[2])
		{

			return 0;
		}else
		{
			if($data1[1] > $data2[1])
			{
				return 1;
			}elseif($data1[1] < $data2[1])
			{
				return 0;
			}else
			{
				if($data1[0] > $data2[0])
				{
					return 1;
				}elseif($data1[0] < $data2[0])
				{
					return 0;
				}else
				{
					return 2;
				}
			}
		}

	}

	function configura_nomes($nome)
	{
		$arrayNome = explode(" ", $nome);
		if(count($arrayNome) > 3)
		{
			$nome = "{$arrayNome[0]} {$arrayNome[1]} ";
			$parte = $arrayNome[2];
			if( $parte != "de" && $parte != "da" && $parte != "dos" && $parte != "do" && $parte != "das" && $parte != "e")
			{
				$nome .= $parte;
			}
		}
		return $nome;

	}

	function minimiza_capitaliza($str)
	{
		$nome = strtolower($str);
		$arrayNome = explode(" ", $nome);
		$nome ="";
		$gruda = "";
		foreach ($arrayNome as $parte)
		{
			if( $parte != "de" && $parte != "da" && $parte != "dos" && $parte != "do" && $parte != "das" && $parte != "e")
			{
				$nome .= $gruda . strtoupper(substr($parte,0,1)).substr($parte,1);
			}
			else
			{
				$nome .= $gruda . $parte;
			}
			$gruda = " ";
		}
		$nome = str_replace( array( "Ú","Ô","Ç","Á", "É", "Í", "Ó", "Ã", "Ê", "Ï", "Ö", "Ü", "À", "È", "Ì", "Ò", "Ù", "Õ"),
							 array( "ú","ô","ç","á", "é", "í", "ó", "ã", "ê", "ï", "ö", "ü", "à", "è", "ì", "ò", "ù", "õ"), $nome );
		return $nome;
	}

	/*
		FUNCAO RBC DO OPENCALL
	*/
	function RBC_indica_fila( $cod_call_chamado, $int_cod_instituicao =false )
	{
		$db = new clsBanco();
		$db2 = new clsBanco();
		$totalSum = 0;
		$min_chars_por_palavra = 4;
		$min_ocorrencias_por_palavra = 10;

		$total_por_grupo = array();
		$qtd_por_grupo = array();
		$palavras_por_grupo = array();
		$db->Consulta( "SELECT descricao_abertura, ref_cod_call_categoria_grupo FROM call_chamado WHERE cod_call_chamado = $cod_call_chamado" );
		if( $db->ProximoRegistro() )
		{
			list( $descricao, $original_categoria_grupo ) = $db->Tupla();

			// traducoes
			$descricao = str_replace( "<br>", " ", $descricao );
			$descricao = str_replace( "@", " arroba", $descricao );
			$descricao = str_replace( "\n", " ", $descricao );
			$descricao = str_replace( "\r", " ", $descricao );

			// troca tudo que nao for letras por espacos
			$descricao = eregi_replace( "[[:punct:]0-9]", " ", $descricao );
			// remove espacos duplicados
			$descricao = eregi_replace( " {2,}", " ", $descricao );

			// quebra a descricao em espacos (separando as palavras)
			$desc_array = explode( " ", $descricao );

			// passando todas as palavras
			foreach ( $desc_array AS $palavra )
			{
				// apenas palavras com mais de $min_chars_por_palavra
				if( strlen( $palavra ) >= $min_chars_por_palavra )
				{
					// fonetiza a palavra
					$fonetico = fonetiza_palavra( $palavra );

					// apenas foneticos que mantenham mais de $min_chars_por_palavra
					if( strlen( $fonetico ) >= $min_chars_por_palavra )
					{
						$db->Consulta( "SELECT cod_palavra FROM pmidesenvolvimento.palavra WHERE fonetico = '{$fonetico}'" );
						if( $db->ProximoRegistro() )
						{
							list( $cod_palavra ) = $db->Tupla();

							$db2->Consulta( "SELECT ref_cod_call_categoria_grupo, quantidade, peso FROM pmidesenvolvimento.palavra_chave WHERE ref_cod_palavra = $cod_palavra AND quantidade > $min_ocorrencias_por_palavra" );
							while ( $db2->ProximoRegistro() )
							{
								list( $ref_categoria_grupo, $quantidade, $peso ) = $db2->Tupla();

								// valor real considerando a quantidade de ocorrencias e o peso atribuido a esta palavra
								$peso_quantidade = $quantidade * ( $peso / 100 );

								$total_por_grupo[$ref_categoria_grupo] += $peso_quantidade;
								$qtd_por_grupo[$ref_categoria_grupo] += $quantidade;
								$palavras_por_grupo[$ref_categoria_grupo] += 1;
								$totalSum += $peso_quantidade;
							}
						}
					}
				}
			}

			arsort( $total_por_grupo );
			reset( $total_por_grupo );

			$totalFilaSum = 0;
			$filas = array();
			$porcentagens = array();
			foreach ( $total_por_grupo AS $grupo => $quantidade )
			{
				//$multiplicador = 1 + ( ( $palavras_por_grupo[$grupo] -1 ) * 0.5 );

				$percentual = ( $quantidade  ) / $totalSum;
				$porcentagens[$grupo] = $percentual;

				$cod_chamados = array();
				// todos os chamados que sao dessa categoria
				$db->Consulta( "SELECT cod_call_chamado FROM call_chamado WHERE ref_cod_call_categoria_grupo = $grupo" );
				while ( $db->ProximoRegistro() )
				{
					list( $cod_chamado ) = $db->Tupla();
					$cod_chamados[$cod_chamado] = $cod_chamado;
				}

				if( count( $cod_chamados ) )
				{
					$isin = implode( ", ", $cod_chamados );
					$db->Consulta( "SELECT COUNT( 0 ) AS total, ref_cod_call_fila_atendimento FROM call_fwr_chamado WHERE ref_cod_call_chamado IN ( {$isin} ) GROUP BY ref_cod_call_fila_atendimento" );
					while ( $db->ProximoRegistro() )
					{
						list( $total, $fila_atendimento ) = $db->Tupla();
						$filas[$fila_atendimento] += $total * $percentual;
						$totalFilaSum += $total * $percentual;
					}
				}

			}

			arsort( $filas );
			reset( $filas );

			foreach ( $filas AS $cod_fila => $quantidade )
			{
				$porcentagem = 100 * ( $quantidade / $totalFilaSum );
				$nm_fila = $db->CampoUnico( "SELECT nm_fila FROM call_fila_atendimento WHERE cod_call_fila_atendimento = '{$cod_fila}'" );
				return array( $cod_fila, $nm_fila, $porcentagem );
			}
		}
	}

	/**
	* Formata a data para o formato brasileiro
	*
	* @param string $data_original data que será transformada
	* @param bool $h_m determina se o a data retornada incluirá hora e minuto
	* @param bool $h_m_s determina se o a data retornada incluirá hora, minuto e segundo
	*
	* @return string
	*/
	function dataToBrasil($data_original, $h_m = false, $h_m_s = false)
	{
		if($data_original)
		{
			$arr_data = explode(" ", $data_original);

			$data = date( "d/m/Y", strtotime( $arr_data[0] ) );

			if($h_m)
			{
				return "{$data} ".substr($arr_data[1], 0, 5);
			}
			elseif($h_m_s)
			{
				return "{$data} ".substr($arr_data[1], 0, 8);
			}

			return $data;
		}
		return false;
	}

	/**
	* Formata a data para o formato do banco
	*
	* @param string $data_original data que será transformada
	*
	* @return string
	*/
	function  dataToBanco($data_original, $inicial = null)
	{

		if($data_original)
		{
			$data = explode("/", $data_original);
			if(count($data)/* == 3 && sizeof($data[2] == 4)  &&  sizeof($data[1] == 2)  && sizeof($data[0] == 2)*/  )
			{
				if(is_null($inicial))
				{
	                 return "{$data[2]}-{$data[1]}-{$data[0]}";
				}
				if($inicial == true)
				{
					if ($data_original = null)
			         {
			                      return false;
			          }  else {

				             return "{$data[2]}-{$data[1]}-{$data[0]} 00:00:00";
					}
				}

				else if($inicial == false)
				{
					  if ($data_original = null)
				         {
				                    return false;
				          }   else  {

					                 return "{$data[2]}-{$data[1]}-{$data[0]} 23:59:59";
					}
				}


			}
			else
			  return false;
		}
		return false;
	}

	/**
	* Formata uma data vinda do postgre
	*
	* @param string $data_original data que será transformada
	*
	* @return string
	*/

	function dataFromPgToTime( $data_original )
	{
		if( strlen( $data_original ) > 16 )
		{
			$data_original = substr( $data_original, 0, 16 );
		}
		return strtotime( $data_original );
	}

/**
 * Formata uma data ISO-8601 no formato do locale pt_BR.
 *
 * O formato ISO-8601 geralmente é utilizado pelos DBMS atuais nos tipos de campos datetime/timestamp.
 * O PostgreSQL utiliza este padrão.
 *
 * @param string $data_original Data que será formatada
 * @param string $formatacao    String de formatação no padrão aceito pela função date() do PHP
 * @link  http://www.php.net/date Documentação da função PHP date()
 *
 * @return string
 */
function dataFromPgToBr($data_original, $formatacao = "d/m/Y")
{
	return date($formatacao, dataFromPgToTime($data_original));
}


	/**
	 * Funcao que troca caracteres acentuados por caracteres extendidos de HTML (para compatibilidade de encode).
	 * Ex: á = &aacute;
	 * pode substituir na ordem reversa
	 *
	 * @param string $text
	 * @param bool $reverse
	 * @return string
	 */
	function extendChars( $text, $reverse = false )
	{
		$chars = array( 	"Ã", "Â", "Á", "À", "Ä", 	"É", "Ê", "È", "Ë", 	"Í", "Ì", "Ï", "Î", 	"Ô", "Õ", "Ó", "Ò", "Ö", 	"Ú", "Ù", "Û", "Ü", 	"İ",  	"Ñ", 	"Ç",
					"ã", "â", "á", "à", "ä", 	"é", "ê", "è", "ë", 	"í", "ì", "ï", "î", 	"ô", "õ", "ó", "ò", "ö", 	"ú", "ù", "û", "ü",  	"ı",  	"ñ", 	"ç" );
		$extends = array( 	"&Atilde;", "&Acirc;", "&Aacute;", "&Agrave;", "&Auml;", 	"&Eacute;", "&Ecirc;", "&Egrave;", "&Euml;", 	"&Iacute;", "&Igrave;", "&Iuml;", "&Icirc;", 	"&Ocirc;", "&Otilde;", "&Oacute;", "&Ograve;", "&Ouml;", 	"&Uacute;", "&Ugrave;", "&Ucirc;", "&Uuml;", 	"&Yacute;", 	"&Ntilde;", 	"&Ccedil;",
					"&atilde;", "&acirc;", "&aacute;", "&agrave;", "&auml;", 	"&eacute;", "&ecirc;", "&egrave;", "&euml;", 	"&iacute;", "&igrave;", "&iuml;", "&icirc;", 	"&ocirc;", "&otilde;", "&oacute;", "&ograve;", "&ouml;", 	"&uacute;", "&ugrave;", "&ucirc;", "&uuml;", 	"&yacute;", 	"&ntilde;", 	"&ccedil;" );
		if( $reverse )
		{
			return str_replace( $extends, $chars, $text );
		}
		else
		{
			return str_replace( $chars, $extends, $text );
		}
	}

	function get_microtime()
	{
		list( $usec, $sec ) = explode( " ", microtime() );
		return $usec + $sec;
	}

//-- Início função para retorna linhas
/* -- Esta função recebe como parâmetros a string que deseja-se quebrar em linhas e o tamanho
      de caracteres que a linha vai ter, e ela retorna um array com as linhas. -- */
	function quebra_linhas( $string, $tamanho)
	{
		$string_atual = $string;
		$pos = 0;
		$linhas = array();
		while (strlen( $string_atual ) > 0 )
		{
			if( $tam < strlen( $string_atual ))
			{
				$linhas[$pos] = retorna_linha( $string_atual, $tamanho );
				$string_atual = trim( substr( $string_atual, strlen( $linhas[$pos] ) ) );
			}
			else
			{
				$linhas[$pos] = retorna_linha( $string_atual, strlen( $string_atual) );
				$string_atual = trim( substr( $string_atual, strlen( $linhas[$pos] ) ) );
			}
			$pos++;
		}
		return $linhas;
	}

	function retorna_linha( $string, $tam )
	{
		truncate($string,$tam);
	}

//-----------------------------------------------------------------------------
// Generate a Code 3 of 9 barcode
//-----------------------------------------------------------------------------
function Barcode39 ($barcode, $width, $height, $quality, $format, $text)
{
        switch ($format)
        {
                default:
                        $format = "JPEG";
                case "JPEG":
                        header ("Content-type: image/jpeg");
                        break;
                case "PNG":
                        header ("Content-type: image/png");
                        break;
                case "GIF":
                        header ("Content-type: image/gif");
                        break;
        }


        $im = ImageCreate ($width, $height)
    or die ("Cannot Initialize new GD image stream");
        $White = ImageColorAllocate ($im, 255, 255, 255);
        $Black = ImageColorAllocate ($im, 0, 0, 0);
        //ImageColorTransparent ($im, $White);
        ImageInterLace ($im, 1);



        $NarrowRatio = 20;
        $WideRatio = 55;
        $QuietRatio = 35;


        $nChars = (strlen($barcode)+2) * ((6 * $NarrowRatio) + (3 * $WideRatio) + ($QuietRatio));
        $Pixels = $width / $nChars;
        $NarrowBar = (int)(20 * $Pixels);
        $WideBar = (int)(55 * $Pixels);
        $QuietBar = (int)(35 * $Pixels);


        $ActualWidth = (($NarrowBar * 6) + ($WideBar*3) + $QuietBar) * (strlen ($barcode)+2);

        if (($NarrowBar == 0) || ($NarrowBar == $WideBar) || ($NarrowBar == $QuietBar) || ($WideBar == 0) || ($WideBar == $QuietBar) || ($QuietBar == 0))
        {
                ImageString ($im, 1, 0, 0, "Image is too small!", $Black);
                OutputImage ($im, $format, $quality);
                exit;
        }

        $CurrentBarX = (int)(($width - $ActualWidth) / 2);
        $Color = $White;
        $BarcodeFull = "*".strtoupper ($barcode)."*";
        settype ($BarcodeFull, "string");

        $FontNum = 3;
        $FontHeight = ImageFontHeight ($FontNum);
        $FontWidth = ImageFontWidth ($FontNum);
        if ($text != 0)
        {
                $CenterLoc = (int)(($width-1) / 2) - (int)(($FontWidth * strlen($BarcodeFull)) / 2);
                ImageString ($im, $FontNum, $CenterLoc, $height-$FontHeight, "$BarcodeFull", $Black);
        }
		else
		{
			$FontHeight=-2;
		}


        for ($i=0; $i<strlen($BarcodeFull); $i++)
        {
                $StripeCode = Code39 ($BarcodeFull[$i]);


                for ($n=0; $n < 9; $n++)
                {
                        if ($Color == $White) $Color = $Black;
                        else $Color = $White;


                        switch ($StripeCode[$n])
                        {
                                case '0':
                                        ImageFilledRectangle ($im, $CurrentBarX, 0, $CurrentBarX+$NarrowBar, $height-1-$FontHeight-2, $Color);
                                        $CurrentBarX += $NarrowBar;
                                        break;


                                case '1':
                                        ImageFilledRectangle ($im, $CurrentBarX, 0, $CurrentBarX+$WideBar, $height-1-$FontHeight-2, $Color);
                                        $CurrentBarX += $WideBar;
                                        break;
                        }
                }


                $Color = $White;
                ImageFilledRectangle ($im, $CurrentBarX, 0, $CurrentBarX+$QuietBar, $height-1-$FontHeight-2, $Color);
                $CurrentBarX += $QuietBar;
        }


        OutputImage ($im, $format, $quality);
}


//-----------------------------------------------------------------------------
// Output an image to the browser
//-----------------------------------------------------------------------------
function OutputImage ($im, $format, $quality)
{
        switch ($format)
        {
                case "JPEG":
                        ImageJPEG ($im, "", $quality);
                        break;
                case "PNG":
                        ImagePNG ($im);
                        break;
                case "GIF":
                        ImageGIF ($im);
                        break;
        }
}


//-----------------------------------------------------------------------------
// Returns the Code 3 of 9 value for a given ASCII character
//-----------------------------------------------------------------------------
function Code39 ($Asc)
{
        switch ($Asc)
        {
                case ' ':
                        return "011000100";
                case '$':
                        return "010101000";
                case '%':
                        return "000101010";
                case '*':
                        return "010010100"; // * Start/Stop
                case '+':
                        return "010001010";
                case '|':
                        return "010000101";
                case '.':
                        return "110000100";
                case '/':
                        return "010100010";
				case '-':
						return "010000101";
                case '0':
                        return "000110100";
                case '1':
                        return "100100001";
                case '2':
                        return "001100001";
                case '3':
                        return "101100000";
                case '4':
                        return "000110001";
                case '5':
                        return "100110000";
                case '6':
                        return "001110000";
                case '7':
                        return "000100101";
                case '8':
                        return "100100100";
                case '9':
                        return "001100100";
                case 'A':
                        return "100001001";
                case 'B':
                        return "001001001";
                case 'C':
                        return "101001000";
                case 'D':
                        return "000011001";
                case 'E':
                        return "100011000";
                case 'F':
                        return "001011000";
                case 'G':
                        return "000001101";
                case 'H':
                        return "100001100";
                case 'I':
                        return "001001100";
                case 'J':
                        return "000011100";
                case 'K':
                        return "100000011";
                case 'L':
                        return "001000011";
                case 'M':
                        return "101000010";
                case 'N':
                        return "000010011";
                case 'O':
                        return "100010010";
                case 'P':
                        return "001010010";
                case 'Q':
                        return "000000111";
                case 'R':
                        return "100000110";
                case 'S':
                        return "001000110";
                case 'T':
                        return "000010110";
                case 'U':
                        return "110000001";
                case 'V':
                        return "011000001";
                case 'W':
                        return "111000000";
                case 'X':
                        return "010010001";
                case 'Y':
                        return "110010000";
                case 'Z':
                        return "011010000";
                default:
                        return "011000100";
        }
}

	//-- Fim função para retorna linhas

	// int2IdFederal()

	function int2IdFederal( $int ) {
		$str = "".$int."";
		if( strlen( $str ) > 11 )
		{
			if( strlen( $int ) < 14 )
			{
				$str = str_repeat( "0", 14 - strlen( $int ) ) . $int;
			}
			$str = str_replace( '.', '', $str );
			$str = str_replace( '.', '', $str );
			$str = str_replace( '-', '', $str );
			$str = str_replace( '/', '', $str );
			$temp = substr( $str, 0, 2 );
			if ( strlen( $temp ) == 2 )
				$temp .= '.';
			$temp .= substr( $str, 2 ,3 );
			if ( strlen( $temp ) == 6 )
				$temp .= '.';
			$temp .= substr( $str, 5, 3 );
			if ( strlen( $temp ) == 10 )
				$temp .= '/';
			$temp .= substr( $str, 8, 4 );
			if ( strlen( $temp ) == 15 )
				$temp .= '-';
			$temp .= substr( $str, 12, 2 );
			return $temp;
		}
		else
		{
			if( strlen( $int ) < 11 )
			{
				$str = str_repeat( "0", 11 - strlen( $int ) ) . $int;
			}
			$str = str_replace( '.', '', $str );
			$str = str_replace( '.', '', $str );
			$str = str_replace( '/', '', $str );
			$str = str_replace( '-', '', $str );
			$temp = substr( $str, 0, 3 );
			if ( strlen( $temp ) == 3 )
				$temp .= '.';
			$temp .= substr( $str, 3, 3 );
			if ( strlen( $temp ) == 7 )
				$temp .= '.';
			$temp .= substr( $str, 6, 3 );
			if ( strlen( $temp ) == 11 )
				$temp .= '-';
			$temp .= substr( $str, 9, 2 );
			return $temp;
		}
	}

	function gera_silaba()
	{
		$vogais = array( "a", "e", "i", "o" );
		$consoantes = array( "b", "c", "d","f","g","j","l","m","n","p" );

		$silaba = "";

		$tipo = rand( 0, 100 );
		if ( $tipo > 99 )
		{
			$silaba .= ( $tipo % 2 ) ? "qu": "gu";
			$silaba .= $vogais[rand(0,count($vogais) - 1)];
		}
		if ( $tipo > 98 )
		{
			$silaba .= ( $tipo % 2 ) ? "nh": "ch";
			$silaba .= $vogais[rand(0,count($vogais) - 1)];
		}
		else if ( $tipo > 83 )
		{
			$silaba .= ( $tipo % 2 ) ? "ss": "rr";
			$silaba .= $vogais[rand(0,count($vogais) - 1)];
		}
		else if ( $tipo > 80 )
		{
			$silaba .= $vogais[rand(0,count($vogais) - 1)];
		}
		else
		{
			$silaba .= $consoantes[rand(0,count($consoantes) - 1)] . $vogais[rand(0,count($vogais) - 1)];
		}

		return $silaba;
	}

	function gera_palavra()
	{
		$tamanho = 3;
		$palavra = "";

		for ( $i = 0; $i < $tamanho; $i++ )
		{
			$palavra .= gera_silaba();
		}

		$palavra .= !( $tamanho % 4 ) ? "s": "";

		if( substr( $palavra, 0, 2 ) == "ss" || substr( $palavra, 0, 2 ) == "rr" || substr( $palavra, 0, 1 ) == "ç" )
		{
			$palavra = substr( $palavra, 1);
		}

		return $palavra;
	}

	/**
	 * Verifica se o valor é booleano
	 * aceita como true:
	 * 'true', 't', true, 1, '1', 'yes', 'y', 'sim', 's'
	 *
	 * @param mixed $val
	 * @return bool
	 */
	function dbBool( $val )
	{
		return ( $val === "true" || $val === "t" || $val === true || $val == 1 || $val === "yes" || $val === "y" || $val === "sim" || $val === "s" );
	}

	/**
	 * Gera um permalink da string
	 *
	 * @param string $string
	 * @return string
	 */
	function permaLink( $string )
	{
		$string = limpa_acentos($string);
		$string = str_replace(" ","_",$string);
		$string = eregi_replace("[^[:alpha:]_]","",$string);
		$string = eregi_replace("_+","_",$string);
		$string = eregi_replace("_\$","",$string);
		return strtolower( $string );
	}

	/**
	 * Corta uma string caso ela seja maior do que $size caracteres
	 * Caso $breakWords seja setado como false, quebrará a string no último espaco " "
	 * encontrado antes do caracter $size (desde que o retorno até esse ponto não ande mais caracteres do que 25% de $size)
	 *
	 * @param string $text
	 * @param int $size
	 * @param bool $breakWords
	 * @return string
	 */
	function truncate( $text, $size = 100, $breakWords = false )
	{
		if( strlen($text) > $size )
		{
			$text = substr( trim($text), 0, $size );
			$espaco = strrpos( $text, " " );
			if( $espaco !== false && ! $breakWords && $espaco / $size > 0.75 )
			{
				$text = substr( $text, 0, $espaco );
			}
			$text .= "...";
		}
		return $text;
	}


	/**
	 * capitaliza todos os caracteres de uma string incluíndo os acentuados
	 * ex: série => SÉRIE
	 * @param string $text
	 * @return string
	 */
	function str2upper($text) {
		$ASCII_SPC_MIN = "àáâãäåæçèéêëìíîïğñòóôõöùúûüıÿ??";
		$ASCII_SPC_MAX = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖÙÚÛÜİ???";
	   return strtr(strtoupper($text),$ASCII_SPC_MIN,$ASCII_SPC_MAX);
	}

	function girarTextoImagem($texto,$tamanho = 8,$altura = 130)
	{

		$largura = $tamanho + 5;

		$vertical = $altura;

		$palavras = explode(" ", $texto);

		for ($i = 0; $i < sizeof($palavras); $i++)
		{
			//verifica se a proxima palavra cabe na linha

			if($vertical-(strlen($palavras[$i])*$tamanho) < 0)
			{
					$vertical = $altura;
					$largura += $tamanho;
			}

			$vertical -= strlen($palavras[$i])*$tamanho;

		}

		$vertical = $altura;
		$horizontal = $tamanho;

		$imagem = imagecreatetruecolor($largura, $altura); //cria imagem
		$cor = imagecolorallocate($imagem, 0, 0, 0); //cor do texto

		//fundo branco
		imagefilledrectangle ( $imagem, 0, 0, ($largura), ($altura), imagecolorallocate($imagem, 255, 255, 255) );

		$y_espaco = imagettftext($imagem, $tamanho, 90, $horizontal,$vertical, $cor, 'arquivos/fontes/Vera.ttf',  " ");
		$y_espaco = $y_espaco[2];

		for ($i = 0; $i < sizeof($palavras); $i++)
		{

			$y = imagettfbbox ( $tamanho, 0, 'arquivos/fontes/Vera.ttf', $palavras[$i]);
			$y = $y[2];

			if($vertical-$y/*(strlen($palavras[$i])*$tamanho)*/ < 0)
			{
				$vertical = $altura;
				$horizontal += $tamanho + 4;
			}
			elseif ($i != 0)
			{

			}
			imagettftext($imagem, $tamanho, 90, $horizontal,$vertical, $cor, 'arquivos/fontes/Vera.ttf',  $palavras[$i]);


			$vertical -= ($y + $y_espaco);// strlen($palavras[$i])*$tamanho;

		}

		$texto = str_replace(" ","_",limpa_acentos($texto));
		 imagepng($imagem,"tmp/{$texto}.png");
//header('Content-type: image/png');
//imagepng($imagem);
//die;
		return "tmp/{$texto}.png";
	}
	function privBuildMimeArray() {
	      return array(
	         "mp3" => "audio/mpeg",
	         "wav" => "audio/x-wav",
	         "bmp" => "image/bmp",
	         "gif" => "image/gif",
	         "jpeg" => "image/jpeg",
	         "jpg" => "image/jpeg",
	         "jpe" => "image/jpeg",
	         "png" => "image/png",
	         "tiff" => "image/tiff",
	         "tif" => "image/tif",
	         "xml" => "text/xml",
	         "xsl" => "text/xml",
	         "mpeg" => "video/mpeg",
	         "mpg" => "video/mpeg",
	         "mpe" => "video/mpeg",
	         "avi" => "video/x-msvideo",
	         "pdf" => "pdf",
	         "doc" => "doc",
	         "pps" => "pps",
	         "cdr" => "cdr",

	      );
	   }
   function privFindType($ext) {
      // create mimetypes array
      $mimetypes = $this->privBuildMimeArray();

      // return mime type for extension
      if (isset($mimetypes[$ext])) {
         //return $mimetypes[$ext];
         return true;
      // if the extension wasn't found return octet-stream
      } else {
         return false;
      }

   }

   function permiteUpload($file) {


		$file_info = pathinfo($file);

		$base_name = $file_info['basename'];
		$ext = $file_info['extension'];

		return $this->privFindType($ext);

   }

?>
