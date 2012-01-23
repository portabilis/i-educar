<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itaja�								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");

class clsFuncionario extends clsPessoaFisica 
{
	var $idpes;
	var $matricula;
	var $senha;
	var $ativo;
	var $ref_sec;
	var $ramal;
	var $sequencial;
	var $opcao_menu;
	var $ref_cod_setor;
	var $ref_cod_funcionario_vinculo;
	var $tempo_expira_senha;
	var $tempo_expira_conta;
	var $data_troca_senha;
	var $data_reativa_conta;
	var $ref_ref_cod_pessoa_fj;
	var $proibido;
	var $cpf;
	var $matricula_permanente;

	var $ref_cod_setor_new;

	var $schema_cadastro    = "cadastro";
	var $schema_portal      = "portal";
	var $tabela_pessoa      = "pessoa";
	var $tabela_funcionario = "funcionario";
	var $tabela_fisica_cpf  = "fisica";

	// propriedades padrao

	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;


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

	function  clsFuncionario($int_idpes = false, $str_matricula = false, $int_cpf = false, $int_ref_cod_setor = false, $str_senha = false, $data_troca_senha = false, $tempo_expira_senha = false, $data_reativa_conta = false, $tempo_expira_conta = false, $ref_cod_funcionario_vinculo = false, $ramal = false, $matricula_permanente = false, $banido = false, $email = null)
	{
		$this->idpes = $int_idpes;
		$this->matricula = $str_matricula;
		$this->cpf = $int_cpf;
		$this->ref_cod_setor = $int_ref_cod_setor;
		$this->senha = $str_senha;
		$this->data_troca_senha = $data_troca_senha;
		$this->data_reativa_conta = $data_reativa_conta;
		$this->tempo_expira_senha = $tempo_expira_senha;
		$this->tempo_expira_conta = $tempo_expira_conta;
		$this->ref_cod_funcionario_vinculo = $ref_cod_funcionario_vinculo;
		$this->ramal = $ramal;
		$this->matricula_permanente = $matricula_permanente;
		$this->proibido = $banido;
		$this->email = $email;
		$this->_campos_lista = " f.ref_cod_pessoa_fj,
					     		 f.matricula,
								 f.senha,
								 f.ativo,
								 f.ramal,
								 f.sequencial,
								 f.opcao_menu,
								 f.ref_cod_setor,
								 f.ref_cod_funcionario_vinculo,
								 f.tempo_expira_senha,
								 f.tempo_expira_conta,
								 f.data_troca_senha,
								 f.data_reativa_conta,
								 f.ref_ref_cod_pessoa_fj,
								 f.proibido,
								 f.nome,
								 f.ref_cod_setor_new,
                 f.email
								 ";
	}


	function edita()
	{
		$set = "";
		$setVirgula = "SET";
		if($this->idpes)
		{
			$db = new clsBanco();
			if($this->ref_cod_setor)
			{
				$set = "{$setVirgula} ref_cod_setor_new = '$this->ref_cod_setor' ";
				$setVirgula = ", ";
			}

			if($set)
			{
				$db->Consulta("UPDATE {$this->schema_portal}.funcionario $set WHERE ref_cod_pessoa_fj = '{$this->idpes}'");
			}
		}
	}

	function lista($str_matricula=false, $str_nome=false, $int_ativo=false, $int_secretaria=false, $int_departamento=false, $int_setor=false, $int_vinculo=false, $int_inicio_limit=false, $int_qtd_registros=false, $str_ramal = false, $matricula_is_not_null = false, $int_idpes = false, $email = null )
	{
		$sql = " SELECT {$this->_campos_lista} FROM {$this->schema_portal}.v_funcionario f";
		$filtros = "";
		$filtro_pessoa = false;


		$whereAnd = " WHERE ";

		if( is_string( $str_matricula ) && $str_matricula != '')
		{
			$filtros .= "{$whereAnd} to_ascii(f.matricula) LIKE to_ascii('%{$str_matricula}%')";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$filtros .= "{$whereAnd} to_ascii(f.nome) LIKE  to_ascii('%{$str_nome}%%')";
			$whereAnd = " AND ";
			$filtro_pessoa =true;
		}
		if( is_numeric( $int_ativo ) )
		{
			$filtros .= "{$whereAnd} f.ativo = '{$int_ativo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_vinculo ) )
		{
			$filtros .= "{$whereAnd} f.ref_cod_funcionario_vinculo = '{$int_vinculo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ramal ) )
		{
			$filtros .= "{$whereAnd} f.str_ramal ILIKE '%{$str_ramal}%'f";
			$whereAnd = " AND ";
		}
		if($matricula_is_not_null)
		{
			$filtros .= "{$whereAnd} f.matricula  IS NOT NULL";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_idpes))
		{
			$filtros .= "{$whereAnd} f.ref_cod_pessoa_fj = '{$int_idpes}'";
			$whereAnd = " AND ";
			$filtro_pessoa =true;
		}

		if(is_string($str_email))
		{
			$filtros .= "{$whereAnd} f.email ILIKE '%{$str_email}%'f";
			$whereAnd = " AND ";
		}

		$limite = "";
		if($int_inicio_limit !== false  && $int_qtd_registros !== false)
		{
			$limite = "LIMIT $int_qtd_registros OFFSET $int_inicio_limit ";
		}
		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		if($int_inicio_limit !== false  && $int_qtd_registros !== false)
		{
			$sql .= "{$filtros}"." ORDER BY to_ascii(f.nome) ASC ".$limite;
		}
		else
		{
			$sql .= "{$filtros}".$this->getOrderby().$this->getLimite();
		}

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->schema_portal}.v_funcionario f {$filtros}" );
		
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
				$resultado = $db->Tupla();//$tupla;
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}


	function detalhe()
	{
		$idpesOk = false;
		if( is_numeric($this->idpes) )
		{
			$idpesOk = true;
		}
		else if($this->matricula)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT ref_cod_pessoa_fj FROM funcionario WHERE matricula = '{$this->matricula}'");
			if( $db->ProximoRegistro() )
			{
				list( $this->idpes ) = $db->Tupla();
				$idpesOk = true;
			}
		}
		else if($this->cpf)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idpes FROM {$this->schema_cadastro}.fisica WHERE cpf = '{$this->cpf}'");
			if( $db->ProximoRegistro() )
			{
				list( $this->idpes ) = $db->Tupla();
				$idpesOk = true;
			}
		}

		if( $idpesOk  )
		{

			$tupla = parent::detalhe();
			$db = new clsBanco();
			$db->Consulta("SELECT ref_cod_pessoa_fj, matricula, senha, ativo, ref_sec, ramal, sequencial, opcao_menu, ref_cod_setor, ref_cod_funcionario_vinculo, tempo_expira_senha, tempo_expira_conta, data_troca_senha, data_reativa_conta, ref_ref_cod_pessoa_fj, proibido, ref_cod_setor_new, matricula_permanente, email FROM funcionario WHERE ref_cod_pessoa_fj = '{$this->idpes}'");
			if($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				list( $this->idpes, $this->matricula, $this->senha, $this->ativo, $this->ref_sec, $this->ramal, $this->sequencial, $this->opcao_menu, $this->ref_cod_setor, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, $this->tempo_expira_conta, $this->data_troca_senha, $this->data_reativa_conta, $this->ref_ref_cod_pessoa_fj, $this->proibido ,$this->ref_cod_setor_new, $this->matricula_permanente) = $tupla;

				$tupla["idpes"] = new clsPessoaFisica( $tupla["ref_cod_pessoa_fj"] );
				$tupla[] = $tupla["idpes"];
				return $tupla;
			}
		}
		return false;
	}

	function queryRapida($int_idpes)
	{
		$this->idpes = $int_idpes;
		$this->detalhe();
		$resultado = array();
		$pos = 0;
		for ($i = 1; $i< func_num_args(); $i++ ) {
			$campo = func_get_arg($i);
			$resultado[$pos] = ($this->$campo) ? $this->$campo : "";
			$resultado[$campo] = &$resultado[$pos];
			$pos++;

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function queryRapidaCPF( $int_cpf )
	{
		$this->cpf = $int_cpf + 0;
		$this->detalhe();
		$resultado = array();
		$pos = 0;
		for ($i = 1; $i< func_num_args(); $i++ ) {
			$campo = func_get_arg($i);
			$resultado[$pos] = ($this->$campo) ? $this->$campo : "";
			$resultado[$campo] = &$resultado[$pos];
			$pos++;

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function queryRapidaMatricula( $str_matricula )
	{
		$this->matricula = $str_matricula;
		$this->detalhe();
		$resultado = array();
		$pos = 0;
		for ($i = 1; $i< func_num_args(); $i++ ) {
			$campo = func_get_arg($i);
			$resultado[$pos] = ($this->$campo) ? $this->$campo : "";
			$resultado[$campo] = &$resultado[$pos];
			$pos++;

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
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
}
?>
