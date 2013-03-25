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
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 12/02/2007 15:36 pelo gerador automatico de classes
*/

require_once( "include/public/geral.inc.php" );

class clsPublicLogradouro
{
	var $idlog;
	var $idtlog;
	var $nome;
	var $idmun;
	var $geom;
	var $ident_oficial;
	var $idpes_rev;
	var $data_rev;
	var $origem_gravacao;
	var $idpes_cad;
	var $data_cad;
	var $operacao;
	var $idsis_rev;
	var $idsis_cad;

	// propriedades padrao

	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;

	/**
	 * Nome do schema
	 *
	 * @var string
	 */
	var $_schema;

	/**
	 * Nome da tabela
	 *
	 * @var string
	 */
	var $_tabela;

	/**
	 * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
	 *
	 * @var string
	 */
	var $_campos_lista;

	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;

	/**
	 * Valor que define a quantidade de registros a ser retornada pelo metodo lista
	 *
	 * @var int
	 */
	var $_limite_quantidade;

	/**
	 * Define o valor de offset no retorno dos registros no metodo lista
	 *
	 * @var int
	 */
	var $_limite_offset;

	/**
	 * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
	 *
	 * @var string
	 */
	var $_campo_order_by;


	/**
	 * Construtor (PHP 4)
	 *
	 * @param integer idlog
	 * @param string idtlog
	 * @param string nome
	 * @param integer idmun
	 * @param string geom
	 * @param string ident_oficial
	 * @param integer idpes_rev
	 * @param string data_rev
	 * @param string origem_gravacao
	 * @param integer idpes_cad
	 * @param string data_cad
	 * @param string operacao
	 * @param integer idsis_rev
	 * @param integer idsis_cad
	 *
	 * @return object
	 */
	function clsPublicLogradouro( $idlog = null, $idtlog = null, $nome = null, $idmun = null, $geom = null, $ident_oficial = null, $idpes_rev = null, $data_rev = null, $origem_gravacao = null, $idpes_cad = null, $data_cad = null, $operacao = null, $idsis_rev = null, $idsis_cad = null )
	{
		$db = new clsBanco();
		$this->_schema = "public.";
		$this->_tabela = "{$this->_schema}logradouro";

		$this->_campos_lista = $this->_todos_campos = "l.idlog, l.idtlog, l.nome, l.idmun, l.geom, l.ident_oficial, l.idpes_rev, l.data_rev, l.origem_gravacao, l.idpes_cad, l.data_cad, l.operacao, l.idsis_rev, l.idsis_cad";

		if( is_string( $idtlog ) )
		{
			if( class_exists( "clsUrbanoTipoLogradouro" ) )
			{
				$tmp_obj = new clsUrbanoTipoLogradouro( $idtlog );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idtlog = $idtlog;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idtlog = $idtlog;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM urbano.tipo_logradouro WHERE idtlog = '{$idtlog}'" ) )
				{
					$this->idtlog = $idtlog;
				}
			}
		}
		if( is_numeric( $idsis_rev ) )
		{
			if( class_exists( "clsAcessoSistema" ) )
			{
				$tmp_obj = new clsAcessoSistema( $idsis_rev );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idsis_rev = $idsis_rev;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idsis_rev = $idsis_rev;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM acesso.sistema WHERE idsis = '{$idsis_rev}'" ) )
				{
					$this->idsis_rev = $idsis_rev;
				}
			}
		}
		if( is_numeric( $idsis_cad ) )
		{
			if( class_exists( "clsAcessoSistema" ) )
			{
				$tmp_obj = new clsAcessoSistema( $idsis_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idsis_cad = $idsis_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idsis_cad = $idsis_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM acesso.sistema WHERE idsis = '{$idsis_cad}'" ) )
				{
					$this->idsis_cad = $idsis_cad;
				}
			}
		}
		if( is_numeric( $idpes_rev ) )
		{
			if( class_exists( "clsCadastroPessoa" ) )
			{
				$tmp_obj = new clsCadastroPessoa( $idpes_rev );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idpes_rev = $idpes_rev;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idpes_rev = $idpes_rev;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.pessoa WHERE idpes = '{$idpes_rev}'" ) )
				{
					$this->idpes_rev = $idpes_rev;
				}
			}
		}
		if( is_numeric( $idpes_cad ) )
		{
			if( class_exists( "clsCadastroPessoa" ) )
			{
				$tmp_obj = new clsCadastroPessoa( $idpes_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idpes_cad = $idpes_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idpes_cad = $idpes_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.pessoa WHERE idpes = '{$idpes_cad}'" ) )
				{
					$this->idpes_cad = $idpes_cad;
				}
			}
		}


		if( is_numeric( $idlog ) )
		{
			$this->idlog = $idlog;
		}
		if( is_string( $nome ) )
		{
			$this->nome = $nome;
		}
		if( is_numeric( $idmun ) )
		{
			$this->idmun = $idmun;
		}
		if( is_string( $geom ) )
		{
			$this->geom = $geom;
		}
		if( is_string( $ident_oficial ) )
		{
			$this->ident_oficial = $ident_oficial;
		}
		if( is_string( $data_rev ) )
		{
			$this->data_rev = $data_rev;
		}
		if( is_string( $origem_gravacao ) )
		{
			$this->origem_gravacao = $origem_gravacao;
		}
		if( is_string( $data_cad ) )
		{
			$this->data_cad = $data_cad;
		}
		if( is_string( $operacao ) )
		{
			$this->operacao = $operacao;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->idtlog ) && is_string( $this->nome ) && is_numeric( $this->idmun ) && is_string( $this->origem_gravacao ) && is_string( $this->operacao ) && is_numeric( $this->idsis_cad ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_string( $this->idtlog ) )
			{
				$campos .= "{$gruda}idtlog";
				$valores .= "{$gruda}'{$this->idtlog}'";
				$gruda = ", ";
			}
			if( is_string( $this->nome ) )
			{
				$campos .= "{$gruda}nome";
				$valores .= "{$gruda}'" . addslashes($this->nome) . "'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idmun ) )
			{
				$campos .= "{$gruda}idmun";
				$valores .= "{$gruda}'{$this->idmun}'";
				$gruda = ", ";
			}
			if( is_string( $this->geom ) )
			{
				$campos .= "{$gruda}geom";
				$valores .= "{$gruda}'{$this->geom}'";
				$gruda = ", ";
			}
			if( is_string( $this->ident_oficial ) )
			{
				$campos .= "{$gruda}ident_oficial";
				$valores .= "{$gruda}'{$this->ident_oficial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpes_rev ) )
			{
				$campos .= "{$gruda}idpes_rev";
				$valores .= "{$gruda}'{$this->idpes_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_rev ) )
			{
				$campos .= "{$gruda}data_rev";
				$valores .= "{$gruda}'{$this->data_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->origem_gravacao ) )
			{
				$campos .= "{$gruda}origem_gravacao";
				$valores .= "{$gruda}'{$this->origem_gravacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpes_cad ) )
			{
				$campos .= "{$gruda}idpes_cad";
				$valores .= "{$gruda}'{$this->idpes_cad}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cad";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if( is_string( $this->operacao ) )
			{
				$campos .= "{$gruda}operacao";
				$valores .= "{$gruda}'{$this->operacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_rev ) )
			{
				$campos .= "{$gruda}idsis_rev";
				$valores .= "{$gruda}'{$this->idsis_rev}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_cad ) )
			{
				$campos .= "{$gruda}idsis_cad";
				$valores .= "{$gruda}'{$this->idsis_cad}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "seq_logradouro" );
		}
		return false;
	}

	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{
		if( is_numeric( $this->idlog ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->idtlog ) )
			{
				$set .= "{$gruda}idtlog = '{$this->idtlog}'";
				$gruda = ", ";
			}
			if( is_string( $this->nome ) )
			{
				$set .= "{$gruda}nome = '" . addslashes($this->nome) . "'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idmun ) )
			{
				$set .= "{$gruda}idmun = '{$this->idmun}'";
				$gruda = ", ";
			}
			if( is_string( $this->geom ) )
			{
				$set .= "{$gruda}geom = '{$this->geom}'";
				$gruda = ", ";
			}
			if( is_string( $this->ident_oficial ) )
			{
				$set .= "{$gruda}ident_oficial = '{$this->ident_oficial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpes_rev ) )
			{
				$set .= "{$gruda}idpes_rev = '{$this->idpes_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_rev ) )
			{
				$set .= "{$gruda}data_rev = '{$this->data_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->origem_gravacao ) )
			{
				$set .= "{$gruda}origem_gravacao = '{$this->origem_gravacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpes_cad ) )
			{
				$set .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cad ) )
			{
				$set .= "{$gruda}data_cad = '{$this->data_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->operacao ) )
			{
				$set .= "{$gruda}operacao = '{$this->operacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_rev ) )
			{
				$set .= "{$gruda}idsis_rev = '{$this->idsis_rev}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_cad ) )
			{
				$set .= "{$gruda}idsis_cad = '{$this->idsis_cad}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE idlog = '{$this->idlog}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param string str_idtlog
	 * @param string str_nome
	 * @param integer int_idmun
	 * @param string str_geom
	 * @param string str_ident_oficial
	 * @param integer int_idpes_rev
	 * @param string date_data_rev_ini
	 * @param string date_data_rev_fim
	 * @param string str_origem_gravacao
	 * @param integer int_idpes_cad
	 * @param string date_data_cad_ini
	 * @param string date_data_cad_fim
	 * @param string str_operacao
	 * @param integer int_idsis_rev
	 * @param integer int_idsis_cad
	 *
	 * @return array
	 */
	function lista( $str_idtlog = null, $str_nome = null, $int_idmun = null, $str_geom = null, $str_ident_oficial = null, $int_idpes_rev = null, $date_data_rev_ini = null, $date_data_rev_fim = null, $str_origem_gravacao = null, $int_idpes_cad = null, $date_data_cad_ini = null, $date_data_cad_fim = null, $str_operacao = null, $int_idsis_rev = null, $int_idsis_cad = null, $int_idpais = null, $str_sigla_uf = null, $int_idlog = null )
	{
		$select  = ", m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais ";
		$from = "l, public.municipio m, public.uf u, public.pais p ";

		$sql = "SELECT {$this->_campos_lista}{$select} FROM {$this->_tabela} {$from}";
		$whereAnd = " AND ";

		$filtros = " WHERE l.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais ";

		if( is_numeric( $int_idlog ) )
		{
			$filtros .= "{$whereAnd} l.idlog = '{$int_idlog}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_idtlog ) )
		{
			$filtros .= "{$whereAnd} l.idtlog LIKE '%{$str_idtlog}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$filtros .= "{$whereAnd} l.nome LIKE E'%" . addslashes($str_nome) . "%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idmun ) )
		{
			$filtros .= "{$whereAnd} l.idmun = '{$int_idmun}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$filtros .= "{$whereAnd} l.geom LIKE '%{$str_geom}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ident_oficial ) )
		{
			$filtros .= "{$whereAnd} l.ident_oficial LIKE '%{$str_ident_oficial}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpes_rev ) )
		{
			$filtros .= "{$whereAnd} l.idpes_rev = '{$int_idpes_rev}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_rev_ini ) )
		{
			$filtros .= "{$whereAnd} l.data_rev >= '{$date_data_rev_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_rev_fim ) )
		{
			$filtros .= "{$whereAnd} l.data_rev <= '{$date_data_rev_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_origem_gravacao ) )
		{
			$filtros .= "{$whereAnd} l.origem_gravacao LIKE '%{$str_origem_gravacao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpes_cad ) )
		{
			$filtros .= "{$whereAnd} l.idpes_cad = '{$int_idpes_cad}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cad_ini ) )
		{
			$filtros .= "{$whereAnd} l.data_cad >= '{$date_data_cad_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cad_fim ) )
		{
			$filtros .= "{$whereAnd} l.data_cad <= '{$date_data_cad_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_operacao ) )
		{
			$filtros .= "{$whereAnd} l.operacao LIKE '%{$str_operacao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idsis_rev ) )
		{
			$filtros .= "{$whereAnd} l.idsis_rev = '{$int_idsis_rev}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idsis_cad ) )
		{
			$filtros .= "{$whereAnd} l.idsis_cad = '{$int_idsis_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpais ) )
		{
			$filtros .= "{$whereAnd} p.idpais = '{$int_idpais}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sigla_uf ) )
		{
			$filtros .= "{$whereAnd} u.sigla_uf = '{$str_sigla_uf}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$from}{$filtros}" );

		$db->Consulta( $sql );

		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->idlog ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} l WHERE l.idlog = '{$this->idlog}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contrário retorna false.
	 *
	 * @return bool
	 */
	function existe()
	{
		if( is_numeric( $this->idlog ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE idlog = '{$this->idlog}'" );
			if( $db->ProximoRegistro() )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluir()
	{
		if( is_numeric( $this->idlog ) )
		{
//			delete
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE idlog = '{$this->idlog}'" );
			return true;
		}
		return false;
	}

	/**
	 * Define quais campos da tabela serao selecionados na invocacao do metodo lista
	 *
	 * @return null
	 */
	function setCamposLista( $str_campos )
	{
		$this->_campos_lista = $str_campos;
	}

	/**
	 * Define que o metodo Lista devera retornoar todos os campos da tabela
	 *
	 * @return null
	 */
	function resetCamposLista()
	{
		$this->_campos_lista = $this->_todos_campos;
	}

	/**
	 * Define limites de retorno para o metodo lista
	 *
	 * @return null
	 */
	function setLimite( $intLimiteQtd, $intLimiteOffset = null )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		$this->_limite_offset = $intLimiteOffset;
	}

	/**
	 * Retorna a string com o trecho da query resposavel pelo Limite de registros
	 *
	 * @return string
	 */
	function getLimite()
	{
		if( is_numeric( $this->_limite_quantidade ) )
		{
			$retorno = " LIMIT {$this->_limite_quantidade}";
			if( is_numeric( $this->_limite_offset ) )
			{
				$retorno .= " OFFSET {$this->_limite_offset} ";
			}
			return $retorno;
		}
		return "";
	}

	/**
	 * Define campo para ser utilizado como ordenacao no metolo lista
	 *
	 * @return null
	 */
	function setOrderby( $strNomeCampo )
	{
		// limpa a string de possiveis erros (delete, insert, etc)
		//$strNomeCampo = eregi_replace();

		if( is_string( $strNomeCampo ) && $strNomeCampo )
		{
			$this->_campo_order_by = $strNomeCampo;
		}
	}

	/**
	 * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
	 *
	 * @return string
	 */
	function getOrderby()
	{
		if( is_string( $this->_campo_order_by ) )
		{
			return " ORDER BY {$this->_campo_order_by} ";
		}
		return "";
	}

}
?>