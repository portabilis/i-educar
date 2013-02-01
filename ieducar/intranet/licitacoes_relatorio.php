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
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Relatorio Licitações por período" );
		$this->processoAp = "154";
	}
}

class indice extends clsCadastro
{
	var $data_inicial,
		$link,
		$data_final,
		$minRepasses,
		$maxRepasses;

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
		$this->campoData("data_inicial","Data Inicial","");
		$this->campoData("data_final","Data Final","");
	}
	
	// retorna uma substring até o segundo espaco em branco
	// para pegar nome +1 sobrenome
	function segundoEspaco( $str )
	{
		$pos = strpos( $str, " " );
		$pos = strpos( $str, " ", $pos + 1 );
		if( $pos )
		{
			return substr( $str, 0, $pos );
		}
		return $str;
	}
	
	function Novo()
	{
		if(!$this->data_inicial)
		{
			$this->data_inicial =false;
		}
		else 
		{
			$titulo = "($this->data_inicial - ";
			$data = explode("/", $this->data_inicial);
			$this->data_inicial = "{$data[2]}/{$data[1]}/{$data[0]}";
		}
		if(!$this->data_final)
		{
			$titulo .= date('d/m/Y',time()).")";
			$this->data_final = false;
		}
		else 
		{
			$titulo .= "$this->data_final)";
			$data = explode("/", $this->data_final);
			$this->data_final = "{$data[2]}/{$data[1]}/{$data[0]}";
		}
		// gera a lista de licitacoes deste periodo
		$where = "";
		if( $this->data_inicial )
		{
			$where .= " AND data_hora >= {$this->data_inicial}";
		}
		if( $this->data_final )
		{
			$where .= " AND data_hora <= {$this->data_final}";
		}
		$db = new clsBanco();
		$db2 = new clsBanco();
		$db->Consulta( "
			SELECT 
				cod_compras_licitacoes, 
				ref_ref_cod_pessoa_fj, 
				ref_cod_compras_modalidade, 
				numero, 
				objeto, 
				data_hora, 
				ref_pregoeiro, 
				ref_equipe1, 
				ref_equipe2, 
				ref_equipe3, 
				ano_processo, 
				mes_processo, 
				seq_processo, 
				seq_portaria, 
				ano_portaria, 
				valor_referencia, 
				valor_real, 
				ref_cod_compras_final_pregao 
			FROM 
				compras_licitacoes, 
				compras_pregao_execucao
			WHERE 
				ref_cod_compras_licitacoes = cod_compras_licitacoes 
				$where
		" );
		if( $db->Num_Linhas() )
		{
			$relatorio = new relatorios("Relatório de Licitações",100,false,"Intranet - CTIMA", "A4h" );
			$relatorio->setMargem( 25, 25 );
			while( $db->ProximoRegistro() )
			{
				$objPessoa = new clsPessoaFisica();
				$nm_final = "";
				list( $cod_compras_licitacoes, $ref_ref_cod_pessoa_fj, $ref_cod_compras_modalidade, $numero, $objeto, $data_hora, $ref_pregoeiro, $ref_equipe1, $ref_equipe2, $ref_equipe3, $ano_processo, $mes_processo, $seq_processo, $seq_portaria, $ano_portaria, $valor_referencia, $valor_real, $ref_cod_compras_final_pregao ) = $db->Tupla();
				if( $ref_cod_compras_final_pregao )
				{
					$nm_final = $db2->UnicoCampo( "SELECT nm_final FROM compras_final_pregao WHERE cod_compras_final_pregao = '{$ref_cod_compras_final_pregao}'" );
				}
			
				list( $nm_pregoeiro ) = $objPessoa->queryRapida( $ref_pregoeiro, "nome" );
				
			
				list( $nm_equipe1 ) = $objPessoa->queryRapida( $ref_equipe1, "nome" );
				
				$nm_equipe1 = substr( $nm_equipe1, 0, 18 ) . "...";
				
			
				list( $nm_equipe2 ) = $objPessoa->queryRapida( $ref_equipe2, "nome" );
				
				$nm_equipe2 = substr( $nm_equipe2, 0, 18 ) . "...";
				
			
				list( $nm_equipe3 ) = $objPessoa->queryRapida( $ref_equipe3, "nome" );
				
				$nm_equipe3 = substr( $nm_equipe3, 0, 18 ) . "...";
				
				// escreve os dados da licitacao
			
				$relatorio->novalinha( array( "Data", "Número" ), 0, 13, true, "arial", 110 );
				$relatorio->novalinha( array( date( "d/m/Y", strtotime( substr($data_hora,0,19) ) ), $numero ), 0, 13, false, "arial", 110 );
				$relatorio->novalinha( array( "Processo", "Portaria" ), 0, 13, true, "arial", 110 );
				$relatorio->novalinha( array( "{$ano_processo} {$mes_processo} {$seq_processo}", "{$ano_portaria} {$seq_portaria}" ), 0, 13, false, "arial", 110 );
				$relatorio->novalinha( array( "Objeto:", $objeto ), 0, 26, false, "arial", 110 );
				$relatorio->novalinha( array( "Pregoeiro:", $nm_pregoeiro ), 0, 13, false, "arial", 110 );
				$relatorio->novalinha( array( "Equipe:", "{$nm_equipe1},", "{$nm_equipe2},", "{$nm_equipe3}." ), 0, 13, false, "arial", 110 );
				$relatorio->novalinha( array( "Valor Referencia", "Valor Final", "Diferença", "%", "Status" ), 0, 13, false, "arial", array( 110, 100, 100, 70, 100 ) );
				$porcentagem = 100;
				if( $valor_referencia )
				{
					$porcentagem = ( 100 - ( $valor_real / $valor_referencia ) * 100 );
				}
				$relatorio->novalinha( array( number_format( $valor_referencia, "2", ",", "." ), number_format( $valor_real, "2", ",", "." ), number_format( ( $valor_referencia - $valor_real ), "2", ",", "." ), number_format( $porcentagem, "2", ",", "." ) . "%", $nm_final ), 0, 13, false, "arial", array( 110, 100, 100, 70, 100 ) );
			}
			// pega o link e exibe ele ao usuario
			$link = $relatorio->fechaPdf();
			$this->campoRotulo("arquivo","Arquivo", "<a href='" . $link . "'>Clique aqui para Baixar</a>");
		}
		$this->largura = "100%";
		return true;
	}

	function Editar()
	{
	}

	function Excluir()
	{

		return true;
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>