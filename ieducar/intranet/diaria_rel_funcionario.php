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
require_once ("include/relatorio.inc.php");
require_once ("include/Geral.inc.php");


class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Relatório de Diárias" );
		$this->processoAp = "332";
	}
}

class indice extends clsCadastro
{
	var $cod_funcionario;
	var $nome_funcionario;
	var $data_partida;
	var $data_chegada;
	var $data_inicial;
	var $data_final;
	var $valor_total;

	function Inicializar()
	{
		@session_start();
		$this->cod_pessoa_fj = $_SESSION['id_pessoa'];
		session_write_close();
		$retorno = "Novo";
		return $retorno;
	}

	function Gerar()
	{
		$db = new clsBanco();
		$db->Consulta( "SELECT a.ref_funcionario FROM pmidrh.diaria a WHERE ativo = 't'" );
		$ids = array();
		while ( $db->ProximoRegistro() ) {
			list( $cod ) = $db->Tupla();
			$ids[$cod] = $cod;
		}

		if ($ids && count($ids))
		{
			$objPessoa = new clsPessoa_();
			$pessoas = $objPessoa->lista( false, false, false, false, $ids );

			$lista = array();
			$lista["0"]="Escolha um Funcionário...";
			foreach ( $pessoas AS $pessoa )
			{
				$lista[$pessoa["idpes"]] = $pessoa["nome"];
			}

			$this->campoLista( "funcionario", "Funcionário", $lista, $this->funcionario);
			$this->campoData("data_inicial", "Data Inicial", $this->data_inicial);
			$this->campoData("data_final", "Data Final", $this->data_final);
		}
		else
		{
			$this->campoRotulo("aviso","Aviso","Nenhuma Diária cadastrada");
		}
	}

	function Novo()
	{
		if ( $this->funcionario != "0" )
		{
			if ($this->data_inicial != "" ||
				$this->data_final != "")
			{
				$AND = '';
				if ($this->data_inicial)
				{
					$data = explode("/", $this->data_inicial);
					$dia_i = $data[0];
					$mes_i = $data[1];
					$ano_i = $data[2];

					$data_inicial = $ano_i."/".$mes_i."/".$dia_i." 00:00:00";

					$AND = " AND data_pedido >= '{$data_inicial}'";
				}

				if ($this->data_final)
				{

					$data_ = explode("/", $this->data_final);
					$dia_f = $data_[0];
					$mes_f = $data_[1];
					$ano_f = $data_[2];

					$data_final = $ano_f."/".$mes_f."/".$dia_f." 23:59:59";

					$AND .= " AND data_pedido <= '{$data_final}'";
				}
			}

			$sql = "SELECT ref_funcionario, data_partida, data_chegada, COALESCE(vl100,1) + COALESCE(vl75,1) + COALESCE(vl50,1) + COALESCE(vl25,1) as valor FROM pmidrh.diaria WHERE ref_funcionario = {$this->funcionario} $AND AND ativo = 't'";

			$db2 = new clsBanco();
			$nome = $db2->campoUnico("SELECT nome FROM cadastro.pessoa WHERE idpes = {$this->funcionario}");
			$nome_funcionario = $nome;

			$relatorio = new relatorios("Relatório de Diárias\nFuncionário: {$nome}", 200, false, "SEGPOG - Departamento de Logística", "A4", "Prefeitura de Itajaí\nSEGPOG - Departamento de Logística\nRua Alberto Werner, 100 - Vila Operária\nCEP. 88304-053 - Itajaí - SC");

			//tamanho do retangulo, tamanho das linhas.
			$relatorio->novaPagina(30,28);

			$relatorio->novalinha( array( "Data Partida", "Data Chegada", "Valor Total" ), 0, 13, true);

			$db3 = new clsBanco();
			$db3->Consulta( $sql );
			if( $db3->Num_Linhas() )
			{
				while ( $db3->ProximoRegistro() )
				{
					list( $cod_funcionario, $data_partida, $data_chegada, $valor_total ) = $db3->Tupla();

					$data_partida = date( "d/m/Y H:i", strtotime( substr($data_partida,0,19) ) );
					$data_chegada = date( "d/m/Y H:i", strtotime( substr($data_chegada,0,19) ) );

					$relatorio->novalinha( array( $data_partida, $data_chegada, number_format($valor_total, 2, ',', '.') ),1,13);
				}
				// pega o link e exibe ele ao usuario
				$link = $relatorio->fechaPdf();
				$this->campoRotulo("arquivo","Arquivo", "<a href='" . $link . "'>Visualizar Relatório</a>");
			}
			else
			{
				$this->campoRotulo("aviso","Aviso", "Nenhum Funcionário neste relatorio.");
			}
		}
		else
		{
			$this->campoRotulo("aviso","Aviso", "Escolha um Funcionário.");
		}

		$this->largura = "100%";
		return true;
	}

	function Editar()
	{
	}

	function Excluir()
	{
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>