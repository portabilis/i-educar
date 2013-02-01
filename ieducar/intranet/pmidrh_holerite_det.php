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

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/clsBancoMS.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Holerite" );
		$this->processoAp = "480";
	}
}

class indice extends clsDetalhe
{

	function coloca0($data)
		{
			$data = str_replace(" ", "", $data);
			return $data < 10 ? "0".$data:$data;
		}

	function Gerar()
	{

		session_start();

		/*
		 * VERIFICA PERMISSÃO DO USUÁRIO
		 */
		if ($_SESSION['autorizado_holerite'] !== true)
		{
			header("Location: pmidrh_holerite_habilita.php");
			exit();
		}



		$this->titulo = "Holerite descritivo";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_holerite = @$_GET['cod_holerite'];

		if (empty($cod_holerite))
		{
			header("Location: pmidrh_holerite_lst.php");
			exit();
		}

		$dbms = new clsBancoMS();

		/*
		 * DESC FOLHA
		 */
		$dbms = new clsBancoMS();
		$dbms->Consulta( "
			SELECT
				e.codCal, STR(DAY(e.iniCmp)), STR(MONTH(e.iniCmp)), STR(YEAR(e.iniCmp)),
				STR(DAY(e.fimCmp)), STR(MONTH(e.fimCmp)), STR(YEAR(e.fimCmp))
			FROM
				r044cal e
			WHERE
			    e.numemp = '{$_SESSION['numemp_user']}' and
			    e.codcal = '{$cod_holerite}'
			ORDER BY
					e.codCal
							" );
		if ( $dbms->ProximoRegistro() )
		{
			list ($cod, $data_inicial_d, $data_inicial_m, $data_inicial_a, $data_final_d, $data_final_m, $data_final_a) = $dbms->Tupla();

			$data_inicial = $this->coloca0($data_inicial_d)."/".$this->coloca0($data_inicial_m)."/".$this->coloca0($data_inicial_a);
			$data_final = $this->coloca0($data_final_d)."/".$this->coloca0($data_final_m)."/".$this->coloca0($data_final_a);
			$data_final_compara = $this->coloca0($data_final_a)."-".$this->coloca0($data_final_m)."-".$this->coloca0($data_final_d);

			$data_inicial_a = str_replace(" ", "", $data_inicial_a);
			if ($data_inicial_a < 2005)
			{
				$data_inicial = "13º Salário";
				$data_final = "";
				$this->addDetalhe( array("Extra", $data_inicial) );
			}
			else
			{

				$this->addDetalhe( array("Inicio período da folha", $data_inicial) );
				$this->addDetalhe( array("Fim período da folha", $data_final) );
				$data_final_compara_2 = date("Y-m-d");
				if ($data_final_compara>$data_final_compara_2)
				{
					$this->addDetalhe( array("<span style='color: red;'>Aviso</span>", "<h3 style='color: red;'>Os valores poderão ser alterados até o último dia do período da folha.</h3>") );
				}

			}
		}
		$dbms->Libera();

		$dbms->Consulta( "SELECT
				s.codeve
				, e.deseve
				, s.valeve
				, e.tipeve
			FROM
				r046ver s
				, r008evc e
			WHERE
				s.codeve=e.codeve
				and s.numemp = '{$_SESSION['numemp_user']}'
				and s.numcad='{$_SESSION['matricula_user']}'
				and s.codcal='{$cod_holerite}'
			ORDER BY
				s.codeve" );

		$descricao_holerite = "<table>";
		$descricao_holerite .= "<tr bgcolor='#96A9B7'><td>Descrição</td><td>Categoria</td><td>Valor</td></tr>";
		$total_proventos = 0;
		$total_descontos = 0;
		while ($dbms->ProximoRegistro())
		{
			
			list ($cod_evento, $descricao_evento, $valor_evento, $tipo_evento) = $dbms->Tupla();

			$cor = ($cor == "#E4E9ED") ? "#FFFFFF" : "#E4E9ED";

			if ($tipo_evento > 3)
			{
				//
			}
			else if ($tipo_evento == 1 || $tipo_evento == 2)
			{
				$total_proventos += $valor_evento;
				$valor = number_format($valor_evento, "2", ",", ".");
				$descricao_holerite .= "<tr bgcolor='{$cor}'><td>{$descricao_evento}</td><td><center>P</center></td><td align='right'>{$valor}</td></tr>";
			}
			else
			{
				$total_descontos += $valor_evento;
				$valor = number_format($valor_evento, "2", ",", ".");
				$descricao_holerite .= "<tr bgcolor='{$cor}'><td style='color: red;'>{$descricao_evento}</td><td style='color: red;'><center>D</center></td><td align='right' style='color: red;'>{$valor}</td></tr>";
			}
		}
		$total = $total_proventos - $total_descontos;
		$total_descontos = number_format($total_descontos, "2", ",", ".");
		$total_proventos = number_format($total_proventos, "2", ",", ".");
		$total = number_format($total, "2", ",", ".");

		$descricao_holerite .= "<tr bgcolor='#96A9B7'><td colspan='2'>Total de proventos</td><td align='right'>{$total_proventos}</td></tr>";
		$descricao_holerite .= "<tr bgcolor='#96A9B7'><td colspan='2'>Total de descontos</td><td align='right'>{$total_descontos}</td></tr>";
		$descricao_holerite .= "<tr bgcolor='#96A9B7'><td colspan='2'>Líquido</td><td align='right'><b>{$total}</b></td></tr>";
		$descricao_holerite .= "</table>";

		$this->addDetalhe( array("Descrição", $descricao_holerite) );

		$dbpg = new clsBanco();
		$nova_consulta = $dbpg->UnicoCampo("SELECT max(cod_visualizacao) FROM pmidrh.log_visualizacao_olerite WHERE ref_ref_cod_pessoa_fj={$_SESSION['id_pessoa']}");
		$nova_consulta++;
		//$dbpg->Libera();
		$dbpg->Consulta("INSERT INTO pmidrh.log_visualizacao_olerite (ref_ref_cod_pessoa_fj, cod_visualizacao, data_visualizacao, cod_olerite) VALUES ('{$_SESSION['id_pessoa']}', '{$nova_consulta}', now(), '{$cod_holerite}')");


		$this->url_cancelar = "pmidrh_holerite_lst.php";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>