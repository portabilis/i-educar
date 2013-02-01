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
		$this->SetTitulo( "{$this->_instituicao} Relatorio de Diária" );
		$this->processoAp = "337";
	}
}

class indice extends clsCadastro
{
	var $secretaria;
	var $data_inicial;
	var $data_final;

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
		$db->Consulta( "SELECT cod_setor, nm_setor FROM pmidrh.setor WHERE nivel = 0 ORDER BY nm_setor ASC" );
		$opcoes = array();
		$opcoes["0"] = "Escolha uma Secretaria...";
		while ( $db->ProximoRegistro() ) {
			list( $cod, $nome ) = $db->Tupla();
			$opcoes[$cod] = $nome;
		}

		$this->campoLista( "secretaria", "Secretaria", $opcoes, $this->secretaria );
		$this->campoData("data_inicial", "Data Inicial", $this->data_inicial);
		$this->campoData("data_final", "Data Final", $this->data_final);
	}

	function Novo()
	{
		if ( $this->secretaria != 0 )
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

			if ( $this->secretaria )
			{
				$sql = "SELECT d.ref_funcionario, d.vl100 + d.vl75 + d.vl50 + d.vl25 FROM pmidrh.diaria d, portal.funcionario f, pmidrh.setor s where d.ref_funcionario = f.ref_cod_pessoa_fj AND d.ref_cod_setor = {$this->secretaria} AND d.ativo = 't' {$AND} GROUP BY d.ref_funcionario, d.vl100, d.vl75, d.vl50, d.vl25";
				//$sql = "SELECT d.ref_funcionario, sum( COALESCE(d.vl100,1) + COALESCE(d.vl75,1) + COALESCE(d.vl50,1) + COALESCE(d.vl25,1) ) FROM pmidrh.diaria d, portal.funcionario f, pmidrh.setor s where d.ref_funcionario = f.ref_cod_pessoa_fj AND d.ref_cod_setor = {$this->secretaria} AND d.ativo = 't' {$AND} GROUP BY d.ref_funcionario";
				$db = new clsBanco();
				$nome = $db->campoUnico(" SELECT nm_setor FROM pmidrh.setor WHERE cod_setor = {$this->secretaria}");

				$relatorio = new relatorios("Relatório de Diárias por Secretaria\nSecretaria: {$nome}", 200, false, "SEGPOG - Departamento de Logística", "A4", "Prefeitura de Itajaí\nSEGPOG - Departamento de Logística\nRua Alberto Werner, 100 - Vila Operária\nCEP. 88304-053 - Itajaí - SC");

				//tamanho do retangulo, tamanho das linhas.
				$relatorio->novaPagina(30,28);

				$relatorio->novalinha( array( "Funcionário", "Valor Total" ), 0, 13, true);

				$db = new clsBanco();
				$db->Consulta( $sql );
				if( $db->Num_Linhas() )
				{
					while ( $db->ProximoRegistro() )
					{
						list( $cod_funcionario, $v100, $v75, $v50, $v25 ) = $db->Tupla();
						
						$db2 = new clsBanco();
						$nome_funcionario = $db2->campoUnico("SELECT nome FROM cadastro.pessoa WHERE idpes = {$cod_funcionario}");
						$relatorio->novalinha( array( $nome_funcionario, number_format($v100+$v75+$v50+$v25, 2, ',', '.') ),1,13);
					}
					// pega o link e exibe ele ao usuario
					$link = $relatorio->fechaPdf();
					$this->campoRotulo("arquivo","Arquivo", "<a href='" . $link . "'>Visualizar Relatório</a>");
				}
				else
				{
					$this->campoRotulo("aviso","Aviso", "Nenhum Funcionário neste relatório.");
				}
			}
			else
			{
				$this->campoRotulo("aviso","Aviso", "Escolha uma Secretaria.");
			}

			$this->largura = "100%";
			return true;
		}
		else
		{
			$this->campoRotulo("aviso","Aviso", "Nenhum Funcionário neste relatório.");
		}
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