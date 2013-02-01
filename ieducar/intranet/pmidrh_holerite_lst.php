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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/clsBancoMS.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Holerites" );
		$this->processoAp = "480";
	}
}

class indice extends clsListagem
{
	function coloca0($data)
		{
			$data = str_replace(" ", "", $data);
			return $data < 10 ? "0".$data:$data;
		}

	function Gerar()
	{

		$this->titulo = "holerites";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array("Início período folha", "Final período folha") );

		session_start();

		/*
		 * VERIFICA PERMISSÃO DO USUÁRIO
		 */

		if ($_SESSION['autorizado_holerite'] !== true)
		{
			header("Location: pmidrh_holerite_habilita.php");
		}

		/*
		 * VER NUMERO DA EMPRESA DO FUNCIONÁRIO E SALVAR NA SEÇÂO.
		 */
		if (!isset($_SESSION['numemp_user']))
		{
			$dbms = new clsBancoMS();
			$numEmp = $dbms->UnicoCampo("SELECT numEmp FROM r034fun WHERE numcad='{$_SESSION['matricula_user']}'");
			$_SESSION['numemp_user'] = $numEmp;
		}


		/*
		 *  MOSTRA LISTAGEM DOS OLERITE DISPONÍVEIS DO USUÁRIO
		 */

		$dbms = new clsBancoMS();

		$dbms->Consulta( "
			SELECT
				count(0)
			FROM
				r044cal e
			WHERE
			    e.numemp = '{$_SESSION['numemp_user']}' and
			    e.codcal in (
			    	SELECT
			    		distinct(codcal)
			    	FROM
			    		r046ver
			    	WHERE
			    		numcad='{$_SESSION['matricula_user']}'
			    		    )
			    		    and
			   STR(DAY(e.iniCmp)) = 31
		");

		if($dbms->ProximoRegistro())
		{

			$total_13 = $dbms->Tupla();

			$total_13 = $total_13[0];
		}


		$dbms->Consulta( "
			SELECT
				e.codCal, STR(DAY(e.iniCmp)), STR(MONTH(e.iniCmp)), STR(YEAR(e.iniCmp)),
				STR(DAY(e.fimCmp)), STR(MONTH(e.fimCmp)), STR(YEAR(e.fimCmp))
			FROM
				r044cal e
			WHERE
			    e.numemp = '{$_SESSION['numemp_user']}' and
			    e.codcal in (
			    	SELECT
			    		distinct(codcal)
			    	FROM
			    		r046ver
			    	WHERE
			    		numcad='{$_SESSION['matricula_user']}'
			    		    )
			ORDER BY
					e.codCal
							" );

		$num_13 = 0;
		$bool_13 = false;
		while ( $dbms->ProximoRegistro() )
		{
			list ($cod, $data_inicial_d, $data_inicial_m, $data_inicial_a, $data_final_d, $data_final_m, $data_final_a) = $dbms->Tupla();

			$data_inicial = $this->coloca0($data_inicial_d)."/".$this->coloca0($data_inicial_m)."/".$this->coloca0($data_inicial_a);
			$data_final = $this->coloca0($data_final_d)."/".$this->coloca0($data_final_m)."/".$this->coloca0($data_final_a);

			$data_inicial_a = str_replace(" ", "", $data_inicial_a);
			if ($data_inicial_a == 1900)
			{
				$num_13++;
				$bool_13 = true;
				$data_inicial = "13º Salário";
				$data_final = "";
			}

			$hoje = mktime(0,0,0,date("m"),date("d"),date("Y"));
			$holerite_data = mktime(0,0,0,$data_final_m,27,$data_final_a);
			$holerite_data_13 = mktime(0,0,0,11,27,date("Y"));
			//echo $hoje."////".$holerite_data."<br>";
			//

			if( $hoje - $holerite_data > 0 || $_SESSION['id_pessoa'] == 2151 || $_SESSION['id_pessoa'] == 725 || $_SESSION['id_pessoa'] == 4310)
			{
				if( !$bool_13 || ( $bool_13 && ($num_13 < $total_13 || $hoje - $holerite_data_13 > 0) ) || $_SESSION['id_pessoa'] == 2151 || $_SESSION['id_pessoa'] == 725 || $_SESSION['id_pessoa'] == 4310 )
				$this->addLinhas( array("<a href='pmidrh_holerite_det.php?cod_holerite={$cod}'>{$data_inicial}</a>", "<a href='pmidrh_holerite_det.php?cod_holerite={$cod}'>{$data_final}</a>") );
			}
			$bool_13 = false;
		}

		//$this->acao = "go(\"secretarias_cad.php\")";
		//$this->nome_acao = "Novo";

		$this->largura = "100%";

	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>