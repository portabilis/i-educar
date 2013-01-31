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
* Criado em 22/12/2006 16:57 pelo gerador automatico de classes
*/

require_once( "include/portal/geral.inc.php" );

class clsPortalFuncionario
{
	var $ref_cod_pessoa_fj;
	var $matricula;
	var $senha;
	var $ativo;
	var $ref_sec;
	var $ramal;
	var $sequencial;
	var $opcao_menu;
	var $ref_cod_administracao_secretaria;
	var $ref_ref_cod_administracao_secretaria;
	var $ref_cod_departamento;
	var $ref_ref_ref_cod_administracao_secretaria;
	var $ref_ref_cod_departamento;
	var $ref_cod_setor;
	var $ref_cod_funcionario_vinculo;
	var $tempo_expira_senha;
	var $tempo_expira_conta;
	var $data_troca_senha;
	var $data_reativa_conta;
	var $ref_ref_cod_pessoa_fj;
	var $proibido;
	var $ref_cod_setor_new;
	var $matricula_new;
	var $matricula_permanente;
	var $tipo_menu;

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
	 * @param integer ref_cod_pessoa_fj
	 * @param string matricula
	 * @param string senha
	 * @param integer ativo
	 * @param integer ref_sec
	 * @param string ramal
	 * @param string sequencial
	 * @param string opcao_menu
	 * @param integer ref_cod_administracao_secretaria
	 * @param integer ref_ref_cod_administracao_secretaria
	 * @param integer ref_cod_departamento
	 * @param integer ref_ref_ref_cod_administracao_secretaria
	 * @param integer ref_ref_cod_departamento
	 * @param integer ref_cod_setor
	 * @param integer ref_cod_funcionario_vinculo
	 * @param integer tempo_expira_senha
	 * @param integer tempo_expira_conta
	 * @param string data_troca_senha
	 * @param string data_reativa_conta
	 * @param integer ref_ref_cod_pessoa_fj
	 * @param integer proibido
	 * @param integer ref_cod_setor_new
	 * @param integer matricula_new
	 * @param integer matricula_permanente
	 * @param integer tipo_menu
	 *
	 * @return object
	 */
	function clsPortalFuncionario( $ref_cod_pessoa_fj = null, $matricula = null, $senha = null, $ativo = null, $ref_sec = null, $ramal = null, $sequencial = null, $opcao_menu = null, $ref_cod_administracao_secretaria = null, $ref_ref_cod_administracao_secretaria = null, $ref_cod_departamento = null, $ref_ref_ref_cod_administracao_secretaria = null, $ref_ref_cod_departamento = null, $ref_cod_setor = null, $ref_cod_funcionario_vinculo = null, $tempo_expira_senha = null, $tempo_expira_conta = null, $data_troca_senha = null, $data_reativa_conta = null, $ref_ref_cod_pessoa_fj = null, $proibido = null, $ref_cod_setor_new = null, $matricula_new = null, $matricula_permanente = null, $tipo_menu = null, $email = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}funcionario";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_pessoa_fj, matricula, senha, ativo, ref_sec, ramal, sequencial, opcao_menu, ref_cod_setor, ref_cod_funcionario_vinculo, tempo_expira_senha, tempo_expira_conta, data_troca_senha, data_reativa_conta, ref_ref_cod_pessoa_fj, proibido, ref_cod_setor_new, matricula_new, matricula_permanente, tipo_menu, email";

		if( is_numeric( $ref_ref_cod_pessoa_fj ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_ref_cod_pessoa_fj );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_pessoa_fj = $ref_ref_cod_pessoa_fj;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_pessoa_fj = $ref_ref_cod_pessoa_fj;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_ref_cod_pessoa_fj}'" ) )
				{
					$this->ref_ref_cod_pessoa_fj = $ref_ref_cod_pessoa_fj;
				}
			}
		}
		if( is_numeric( $ref_ref_cod_departamento ) && is_numeric( $ref_ref_ref_cod_administracao_secretaria ) && is_numeric( $ref_cod_setor ) )
		{
			if( class_exists( "clsAdministracaoSetor" ) )
			{
				$tmp_obj = new clsAdministracaoSetor( $ref_ref_cod_departamento, $ref_ref_ref_cod_administracao_secretaria, $ref_cod_setor );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_departamento = $ref_ref_cod_departamento;
						$this->ref_ref_ref_cod_administracao_secretaria = $ref_ref_ref_cod_administracao_secretaria;
						$this->ref_cod_setor = $ref_cod_setor;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_departamento = $ref_ref_cod_departamento;
						$this->ref_ref_ref_cod_administracao_secretaria = $ref_ref_ref_cod_administracao_secretaria;
						$this->ref_cod_setor = $ref_cod_setor;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM administracao_setor WHERE ref_cod_departamento = '{$ref_ref_cod_departamento}' AND ref_ref_cod_administracao_secretaria = '{$ref_ref_ref_cod_administracao_secretaria}' AND cod_setor = '{$ref_cod_setor}'" ) )
				{
					$this->ref_ref_cod_departamento = $ref_ref_cod_departamento;
					$this->ref_ref_ref_cod_administracao_secretaria = $ref_ref_ref_cod_administracao_secretaria;
					$this->ref_cod_setor = $ref_cod_setor;
				}
			}
		}
		if( is_numeric( $ref_ref_cod_administracao_secretaria ) && is_numeric( $ref_cod_departamento ) )
		{
			if( class_exists( "clsAdministracaoDepartamento" ) )
			{
				$tmp_obj = new clsAdministracaoDepartamento( $ref_ref_cod_administracao_secretaria, $ref_cod_departamento );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_administracao_secretaria = $ref_ref_cod_administracao_secretaria;
						$this->ref_cod_departamento = $ref_cod_departamento;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_administracao_secretaria = $ref_ref_cod_administracao_secretaria;
						$this->ref_cod_departamento = $ref_cod_departamento;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM administracao_departamento WHERE ref_cod_administracao_secretaria = '{$ref_ref_cod_administracao_secretaria}' AND cod_departamento = '{$ref_cod_departamento}'" ) )
				{
					$this->ref_ref_cod_administracao_secretaria = $ref_ref_cod_administracao_secretaria;
					$this->ref_cod_departamento = $ref_cod_departamento;
				}
			}
		}
		if( is_numeric( $ref_cod_administracao_secretaria ) )
		{
			if( class_exists( "clsAdministracaoSecretaria" ) )
			{
				$tmp_obj = new clsAdministracaoSecretaria( $ref_cod_administracao_secretaria );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_administracao_secretaria = $ref_cod_administracao_secretaria;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_administracao_secretaria = $ref_cod_administracao_secretaria;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM administracao_secretaria WHERE cod_administracao_secretaria = '{$ref_cod_administracao_secretaria}'" ) )
				{
					$this->ref_cod_administracao_secretaria = $ref_cod_administracao_secretaria;
				}
			}
		}
		if( is_numeric( $ref_cod_pessoa_fj ) )
		{
			if( class_exists( "clsCadastroFisica" ) )
			{
				$tmp_obj = new clsCadastroFisica( $ref_cod_pessoa_fj );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_pessoa_fj = $ref_cod_pessoa_fj;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_pessoa_fj = $ref_cod_pessoa_fj;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.fisica WHERE idpes = '{$ref_cod_pessoa_fj}'" ) )
				{
					$this->ref_cod_pessoa_fj = $ref_cod_pessoa_fj;
				}
			}
		}
		if( is_numeric( $ref_cod_setor_new ) )
		{
			if( class_exists( "clsPmidrhSetor" ) )
			{
				$tmp_obj = new clsPmidrhSetor( $ref_cod_setor_new );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_setor_new = $ref_cod_setor_new;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_setor_new = $ref_cod_setor_new;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.setor WHERE cod_setor = '{$ref_cod_setor_new}'" ) )
				{
					$this->ref_cod_setor_new = $ref_cod_setor_new;
				}
			}
		}


		if( is_string( $matricula ) )
		{
			$this->matricula = $matricula;
		}
		if( is_string( $senha ) )
		{
			$this->senha = $senha;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if( is_numeric( $ref_sec ) )
		{
			$this->ref_sec = $ref_sec;
		}
		if( is_string( $ramal ) )
		{
			$this->ramal = $ramal;
		}
		if( is_string( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_string( $opcao_menu ) )
		{
			$this->opcao_menu = $opcao_menu;
		}
		if( is_numeric( $ref_cod_funcionario_vinculo ) )
		{
			$this->ref_cod_funcionario_vinculo = $ref_cod_funcionario_vinculo;
		}
		if( is_numeric( $tempo_expira_senha ) )
		{
			$this->tempo_expira_senha = $tempo_expira_senha;
		}
		if( is_numeric( $tempo_expira_conta ) )
		{
			$this->tempo_expira_conta = $tempo_expira_conta;
		}
		if( is_string( $data_troca_senha ) )
		{
			$this->data_troca_senha = $data_troca_senha;
		}
		if( is_string( $data_reativa_conta ) )
		{
			$this->data_reativa_conta = $data_reativa_conta;
		}
		if( is_numeric( $proibido ) )
		{
			$this->proibido = $proibido;
		}
		if( is_numeric( $matricula_new ) )
		{
			$this->matricula_new = $matricula_new;
		}
		if( is_numeric( $matricula_permanente ) )
		{
			$this->matricula_permanente = $matricula_permanente;
		}
		if( is_numeric( $tipo_menu ) )
		{
			$this->tipo_menu = $tipo_menu;
		}

		if(is_string($email))
			$this->email = $email;

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_pessoa_fj ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_pessoa_fj ) )
			{
				$campos .= "{$gruda}ref_cod_pessoa_fj";
				$valores .= "{$gruda}'{$this->ref_cod_pessoa_fj}'";
				$gruda = ", ";
			}
			if( is_string( $this->matricula ) )
			{
				$campos .= "{$gruda}matricula";
				$valores .= "{$gruda}'{$this->matricula}'";
				$gruda = ", ";
			}
			if( is_string( $this->senha ) )
			{
				$campos .= "{$gruda}senha";
				$valores .= "{$gruda}'{$this->senha}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_sec ) )
			{
				$campos .= "{$gruda}ref_sec";
				$valores .= "{$gruda}'{$this->ref_sec}'";
				$gruda = ", ";
			}
			if( is_string( $this->ramal ) )
			{
				$campos .= "{$gruda}ramal";
				$valores .= "{$gruda}'{$this->ramal}'";
				$gruda = ", ";
			}
			if( is_string( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_string( $this->opcao_menu ) )
			{
				$campos .= "{$gruda}opcao_menu";
				$valores .= "{$gruda}'{$this->opcao_menu}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_administracao_secretaria ) )
			{
				$campos .= "{$gruda}ref_cod_administracao_secretaria";
				$valores .= "{$gruda}'{$this->ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_administracao_secretaria ) )
			{
				$campos .= "{$gruda}ref_ref_cod_administracao_secretaria";
				$valores .= "{$gruda}'{$this->ref_ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_departamento ) )
			{
				$campos .= "{$gruda}ref_cod_departamento";
				$valores .= "{$gruda}'{$this->ref_cod_departamento}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_ref_cod_administracao_secretaria ) )
			{
				$campos .= "{$gruda}ref_ref_ref_cod_administracao_secretaria";
				$valores .= "{$gruda}'{$this->ref_ref_ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_departamento ) )
			{
				$campos .= "{$gruda}ref_ref_cod_departamento";
				$valores .= "{$gruda}'{$this->ref_ref_cod_departamento}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_setor ) )
			{
				$campos .= "{$gruda}ref_cod_setor";
				$valores .= "{$gruda}'{$this->ref_cod_setor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcionario_vinculo ) )
			{
				$campos .= "{$gruda}ref_cod_funcionario_vinculo";
				$valores .= "{$gruda}'{$this->ref_cod_funcionario_vinculo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->tempo_expira_senha ) )
			{
				$campos .= "{$gruda}tempo_expira_senha";
				$valores .= "{$gruda}'{$this->tempo_expira_senha}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->tempo_expira_conta ) )
			{
				$campos .= "{$gruda}tempo_expira_conta";
				$valores .= "{$gruda}'{$this->tempo_expira_conta}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_troca_senha ) )
			{
				$campos .= "{$gruda}data_troca_senha";
				$valores .= "{$gruda}{$this->data_troca_senha}";
				$gruda = ", ";
			}
			if( is_string( $this->data_reativa_conta ) )
			{
				$campos .= "{$gruda}data_reativa_conta";
				$valores .= "{$gruda}{$this->data_reativa_conta}";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_pessoa_fj ) )
			{
				$campos .= "{$gruda}ref_ref_cod_pessoa_fj";
				$valores .= "{$gruda}'{$this->ref_ref_cod_pessoa_fj}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->proibido ) )
			{
				$campos .= "{$gruda}proibido";
				$valores .= "{$gruda}'{$this->proibido}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_setor_new ) )
			{
				$campos .= "{$gruda}ref_cod_setor_new";
				$valores .= "{$gruda}'{$this->ref_cod_setor_new}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->matricula_new ) )
			{
				$campos .= "{$gruda}matricula_new";
				$valores .= "{$gruda}'{$this->matricula_new}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->matricula_permanente ) )
			{
				$campos .= "{$gruda}matricula_permanente";
				$valores .= "{$gruda}'{$this->matricula_permanente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->tipo_menu ) )
			{
				$campos .= "{$gruda}tipo_menu";
				$valores .= "{$gruda}'{$this->tipo_menu}'";
				$gruda = ", ";
			}

			if(is_string($this->email))
			{
				$campos .= "{$gruda}email";
				$valores .= "{$gruda}'{$this->email}'";
				$gruda = ", ";
			}

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return true;//$db->InsertId( "{$this->_tabela}_ref_cod_pessoa_fj_seq");
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
		if( is_numeric( $this->ref_cod_pessoa_fj ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->matricula ) )
			{
				$set .= "{$gruda}matricula = '{$this->matricula}'";
				$gruda = ", ";
			}
			if( is_string( $this->senha ) )
			{
				$set .= "{$gruda}senha = '{$this->senha}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_sec ) )
			{
				$set .= "{$gruda}ref_sec = '{$this->ref_sec}'";
				$gruda = ", ";
			}
			if( is_string( $this->ramal ) )
			{
				$set .= "{$gruda}ramal = '{$this->ramal}'";
				$gruda = ", ";
			}
			if( is_string( $this->sequencial ) )
			{
				$set .= "{$gruda}sequencial = '{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_string( $this->opcao_menu ) )
			{
				$set .= "{$gruda}opcao_menu = '{$this->opcao_menu}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_administracao_secretaria ) )
			{
				$set .= "{$gruda}ref_cod_administracao_secretaria = '{$this->ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_administracao_secretaria ) )
			{
				$set .= "{$gruda}ref_ref_cod_administracao_secretaria = '{$this->ref_ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_departamento ) )
			{
				$set .= "{$gruda}ref_cod_departamento = '{$this->ref_cod_departamento}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_ref_cod_administracao_secretaria ) )
			{
				$set .= "{$gruda}ref_ref_ref_cod_administracao_secretaria = '{$this->ref_ref_ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_departamento ) )
			{
				$set .= "{$gruda}ref_ref_cod_departamento = '{$this->ref_ref_cod_departamento}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_setor ) )
			{
				$set .= "{$gruda}ref_cod_setor = '{$this->ref_cod_setor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcionario_vinculo ) )
			{
				$set .= "{$gruda}ref_cod_funcionario_vinculo = '{$this->ref_cod_funcionario_vinculo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->tempo_expira_senha ) )
			{
				$set .= "{$gruda}tempo_expira_senha = '{$this->tempo_expira_senha}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->tempo_expira_conta ) )
			{
				$set .= "{$gruda}tempo_expira_conta = '{$this->tempo_expira_conta}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_troca_senha ) )
			{
				$set .= "{$gruda}data_troca_senha = '{$this->data_troca_senha}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_reativa_conta ) )
			{
				$set .= "{$gruda}data_reativa_conta = '{$this->data_reativa_conta}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_pessoa_fj ) )
			{
				$set .= "{$gruda}ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->proibido ) )
			{
				$set .= "{$gruda}proibido = '{$this->proibido}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_setor_new ) )
			{
				$set .= "{$gruda}ref_cod_setor_new = '{$this->ref_cod_setor_new}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->matricula_new ) )
			{
				$set .= "{$gruda}matricula_new = '{$this->matricula_new}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->matricula_permanente ) )
			{
				$set .= "{$gruda}matricula_permanente = '{$this->matricula_permanente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->tipo_menu ) )
			{
				$set .= "{$gruda}tipo_menu = '{$this->tipo_menu}'";
				$gruda = ", ";
			}

			if(is_string($this->email))
			{
				$set .= "{$gruda}email = '{$this->email}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param string str_matricula
	 * @param string str_senha
	 * @param integer int_ativo
	 * @param integer int_ref_sec
	 * @param string str_ramal
	 * @param string str_sequencial
	 * @param string str_opcao_menu
	 * @param integer int_ref_cod_administracao_secretaria
	 * @param integer int_ref_ref_cod_administracao_secretaria
	 * @param integer int_ref_cod_departamento
	 * @param integer int_ref_ref_ref_cod_administracao_secretaria
	 * @param integer int_ref_ref_cod_departamento
	 * @param integer int_ref_cod_setor
	 * @param integer int_ref_cod_funcionario_vinculo
	 * @param integer int_tempo_expira_senha
	 * @param integer int_tempo_expira_conta
	 * @param string date_data_troca_senha_ini
	 * @param string date_data_troca_senha_fim
	 * @param string date_data_reativa_conta_ini
	 * @param string date_data_reativa_conta_fim
	 * @param integer int_ref_ref_cod_pessoa_fj
	 * @param integer int_proibido
	 * @param integer int_ref_cod_setor_new
	 * @param integer int_matricula_new
	 * @param integer int_matricula_permanente
	 * @param integer int_tipo_menu
	 *
	 * @return array
	 */
	function lista( $str_matricula = null, $str_senha = null, $int_ativo = null, $int_ref_sec = null, $str_ramal = null, $str_sequencial = null, $str_opcao_menu = null, $int_ref_cod_administracao_secretaria = null, $int_ref_ref_cod_administracao_secretaria = null, $int_ref_cod_departamento = null, $int_ref_ref_ref_cod_administracao_secretaria = null, $int_ref_ref_cod_departamento = null, $int_ref_cod_setor = null, $int_ref_cod_funcionario_vinculo = null, $int_tempo_expira_senha = null, $int_tempo_expira_conta = null, $date_data_troca_senha_ini = null, $date_data_troca_senha_fim = null, $date_data_reativa_conta_ini = null, $date_data_reativa_conta_fim = null, $int_ref_ref_cod_pessoa_fj = null, $int_proibido = null, $int_ref_cod_setor_new = null, $int_matricula_new = null, $int_matricula_permanente = null, $int_tipo_menu = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_pessoa_fj ) )
		{
			$filtros .= "{$whereAnd} ref_cod_pessoa_fj = '{$int_ref_cod_pessoa_fj}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_matricula ) )
		{
			$filtros .= "{$whereAnd} matricula LIKE '%{$str_matricula}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_senha ) )
		{
			$filtros .= "{$whereAnd} senha LIKE '%{$str_senha}%'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_sec ) )
		{
			$filtros .= "{$whereAnd} ref_sec = '{$int_ref_sec}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ramal ) )
		{
			$filtros .= "{$whereAnd} ramal LIKE '%{$str_ramal}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial LIKE '%{$str_sequencial}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_opcao_menu ) )
		{
			$filtros .= "{$whereAnd} opcao_menu LIKE '%{$str_opcao_menu}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_administracao_secretaria ) )
		{
			$filtros .= "{$whereAnd} ref_cod_administracao_secretaria = '{$int_ref_cod_administracao_secretaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_administracao_secretaria ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_administracao_secretaria = '{$int_ref_ref_cod_administracao_secretaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_departamento ) )
		{
			$filtros .= "{$whereAnd} ref_cod_departamento = '{$int_ref_cod_departamento}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_ref_cod_administracao_secretaria ) )
		{
			$filtros .= "{$whereAnd} ref_ref_ref_cod_administracao_secretaria = '{$int_ref_ref_ref_cod_administracao_secretaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_departamento ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_departamento = '{$int_ref_ref_cod_departamento}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_setor ) )
		{
			$filtros .= "{$whereAnd} ref_cod_setor = '{$int_ref_cod_setor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_funcionario_vinculo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_funcionario_vinculo = '{$int_ref_cod_funcionario_vinculo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_tempo_expira_senha ) )
		{
			$filtros .= "{$whereAnd} tempo_expira_senha = '{$int_tempo_expira_senha}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_tempo_expira_conta ) )
		{
			$filtros .= "{$whereAnd} tempo_expira_conta = '{$int_tempo_expira_conta}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_troca_senha_ini ) )
		{
			$filtros .= "{$whereAnd} data_troca_senha >= '{$date_data_troca_senha_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_troca_senha_fim ) )
		{
			$filtros .= "{$whereAnd} data_troca_senha <= '{$date_data_troca_senha_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_reativa_conta_ini ) )
		{
			$filtros .= "{$whereAnd} data_reativa_conta >= '{$date_data_reativa_conta_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_reativa_conta_fim ) )
		{
			$filtros .= "{$whereAnd} data_reativa_conta <= '{$date_data_reativa_conta_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_pessoa_fj ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_pessoa_fj = '{$int_ref_ref_cod_pessoa_fj}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_proibido ) )
		{
			$filtros .= "{$whereAnd} proibido = '{$int_proibido}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_setor_new ) )
		{
			$filtros .= "{$whereAnd} ref_cod_setor_new = '{$int_ref_cod_setor_new}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_matricula_new ) )
		{
			$filtros .= "{$whereAnd} matricula_new = '{$int_matricula_new}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_matricula_permanente ) )
		{
			$filtros .= "{$whereAnd} matricula_permanente = '{$int_matricula_permanente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_tipo_menu ) )
		{
			$filtros .= "{$whereAnd} tipo_menu = '{$int_tipo_menu}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

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
		if( is_numeric( $this->ref_cod_pessoa_fj ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'" );
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
		if( is_numeric( $this->ref_cod_pessoa_fj ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'" );
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
		if( is_numeric( $this->ref_cod_pessoa_fj ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
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
