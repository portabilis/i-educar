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
* @author Prefeitura Municipal de Itajaï¿½
*
* Criado em 17/07/2006 09:18 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarExemplar
{
	var $cod_exemplar;
	var $ref_cod_fonte;
	var $ref_cod_motivo_baixa;
	var $ref_cod_acervo;
	var $ref_cod_situacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $permite_emprestimo;
	var $preco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $data_aquisicao;
	var $tombo;

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
	 * @return object
	 */
	function clsPmieducarExemplar( $cod_exemplar = null, $ref_cod_fonte = null, $ref_cod_motivo_baixa = null, $ref_cod_acervo = null, $ref_cod_situacao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $permite_emprestimo = null, $preco = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $data_aquisicao = null, $tombo = null )
	{

		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}exemplar";

		$this->_campos_lista = $this->_todos_campos = "e.cod_exemplar, e.ref_cod_fonte, e.ref_cod_motivo_baixa, e.ref_cod_acervo, e.ref_cod_situacao, e.ref_usuario_exc, e.ref_usuario_cad, e.permite_emprestimo, e.preco, e.data_cadastro, e.data_exclusao, e.ativo, e.data_aquisicao, e.tombo";

		if( is_numeric( $ref_cod_fonte ) )
		{
			if( class_exists( "clsPmieducarFonte" ) )
			{
				$tmp_obj = new clsPmieducarFonte( $ref_cod_fonte );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_fonte = $ref_cod_fonte;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_fonte = $ref_cod_fonte;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.fonte WHERE cod_fonte = '{$ref_cod_fonte}'" ) )
				{
					$this->ref_cod_fonte = $ref_cod_fonte;
				}
			}
		}
		if( is_numeric( $ref_cod_motivo_baixa ) )
		{
			if( class_exists( "clsPmieducarMotivoBaixa" ) )
			{
				$tmp_obj = new clsPmieducarMotivoBaixa( $ref_cod_motivo_baixa );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_motivo_baixa = $ref_cod_motivo_baixa;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_motivo_baixa = $ref_cod_motivo_baixa;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.motivo_baixa WHERE cod_motivo_baixa = '{$ref_cod_motivo_baixa}'" ) )
				{
					$this->ref_cod_motivo_baixa = $ref_cod_motivo_baixa;
				}
			}
		}
		if( is_numeric( $ref_cod_acervo ) )
		{
			if( class_exists( "clsPmieducarAcervo" ) )
			{
				$tmp_obj = new clsPmieducarAcervo( $ref_cod_acervo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_acervo = $ref_cod_acervo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_acervo = $ref_cod_acervo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.acervo WHERE cod_acervo = '{$ref_cod_acervo}'" ) )
				{
					$this->ref_cod_acervo = $ref_cod_acervo;
				}
			}
		}
		if( is_numeric( $ref_cod_situacao ) )
		{
			if( class_exists( "clsPmieducarSituacao" ) )
			{
				$tmp_obj = new clsPmieducarSituacao( $ref_cod_situacao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_situacao = $ref_cod_situacao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_situacao = $ref_cod_situacao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.situacao WHERE cod_situacao = '{$ref_cod_situacao}'" ) )
				{
					$this->ref_cod_situacao = $ref_cod_situacao;
				}
			}
		}
		if( is_numeric( $ref_usuario_exc ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'" ) )
				{
					$this->ref_usuario_exc = $ref_usuario_exc;
				}
			}
		}
		if( is_numeric( $ref_usuario_cad ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'" ) )
				{
					$this->ref_usuario_cad = $ref_usuario_cad;
				}
			}
		}


		if( is_numeric( $cod_exemplar ) )
		{
			$this->cod_exemplar = $cod_exemplar;
		}
		if( is_numeric( $permite_emprestimo ) )
		{
			$this->permite_emprestimo = $permite_emprestimo;
		}
		if( is_numeric( $preco ) )
		{
			$this->preco = $preco;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if( is_string( $data_aquisicao ) )
		{
			$this->data_aquisicao = $data_aquisicao;
		}
		if (is_numeric($tombo))
		{
			$this->tombo = $tombo;
		}
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_fonte ) && is_numeric( $this->ref_cod_acervo ) && is_numeric( $this->ref_cod_situacao ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->permite_emprestimo ) && is_numeric( $this->preco ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_fonte ) )
			{
				$campos .= "{$gruda}ref_cod_fonte";
				$valores .= "{$gruda}'{$this->ref_cod_fonte}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_motivo_baixa ) )
			{
				$campos .= "{$gruda}ref_cod_motivo_baixa";
				$valores .= "{$gruda}'{$this->ref_cod_motivo_baixa}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo ) )
			{
				$campos .= "{$gruda}ref_cod_acervo";
				$valores .= "{$gruda}'{$this->ref_cod_acervo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_situacao ) )
			{
				$campos .= "{$gruda}ref_cod_situacao";
				$valores .= "{$gruda}'{$this->ref_cod_situacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->permite_emprestimo ) )
			{
				$campos .= "{$gruda}permite_emprestimo";
				$valores .= "{$gruda}'{$this->permite_emprestimo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->preco ) )
			{
				$campos .= "{$gruda}preco";
				$valores .= "{$gruda}'{$this->preco}'";
				$gruda = ", ";
			}
			if (is_numeric($this->tombo))
			{
				$campos .= "{$gruda}tombo";
				$valores .= "{$gruda}'{$this->tombo}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			
                        if( is_string( $this->data_aquisicao ) )
			{
				$campos .= "{$gruda}data_aquisicao";
				$valores .= "{$gruda}'{$this->data_aquisicao}'";
				$gruda = ", ";
			}
                        
                        if($this->tombo != NULL){
                            $sql = "SELECT * FROM pmieducar.exemplar WHERE tombo = {$this->tombo}";
                            $consulta = new clsBanco();
                            $tombo = $consulta->CampoUnico($sql);
                            if($tombo != NULL){
                                return false;
                            }
                        }

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_exemplar_seq");
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
		if( is_numeric( $this->cod_exemplar ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_fonte ) )
			{
				$set .= "{$gruda}ref_cod_fonte = '{$this->ref_cod_fonte}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_motivo_baixa ) )
			{
				$set .= "{$gruda}ref_cod_motivo_baixa = '{$this->ref_cod_motivo_baixa}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo ) )
			{
				$set .= "{$gruda}ref_cod_acervo = '{$this->ref_cod_acervo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_situacao ) )
			{
				$set .= "{$gruda}ref_cod_situacao = '{$this->ref_cod_situacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->permite_emprestimo ) )
			{
				$set .= "{$gruda}permite_emprestimo = '{$this->permite_emprestimo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->preco ) )
			{
				$set .= "{$gruda}preco = '{$this->preco}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_aquisicao ) )
			{
				$set .= "{$gruda}data_aquisicao = '{$this->data_aquisicao}'";
				$gruda = ", ";
			}

			if(is_numeric($this->tombo))
			{
				$set .= "{$gruda}tombo = '{$this->tombo}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_exemplar = '{$this->cod_exemplar}'" );
				return true;
			}
		}
		return false;
	}
	/**
	 * Verifica se o tombo a ser cadastrado j� n�o foi cadastrado
	 *
	 * @return boolean
	 */
	function retorna_tombo_valido($bibliotecaId, $exceptExemplarId = null, $tombo=null) {
    	if (empty($bibliotecaId))
    		throw new Exception("Deve ser enviado um argumento '\$bibliotecaId' ao método 'retorna_tombo_maximo'");
		if (empty($tombo))
			return true;
		 // Sem essa regra ao editar e salvar com o mesmo tombo retornaria falso
   		 if (! empty($exceptExemplarId))
   			   $exceptExemplar = " and exemplar.cod_exemplar !=  $exceptExemplarId";
   		 else
     			 $exceptExemplar = '';

		$sql = "SELECT tombo FROM pmieducar.exemplar, pmieducar.acervo WHERE exemplar.ativo = 1 and exemplar.ref_cod_acervo = acervo.cod_acervo and tombo = $tombo and acervo.ref_cod_biblioteca = $bibliotecaId $exceptExemplar";

		$db = new clsBanco();
		$consulta = $db->CampoUnico($sql);
		if ($consulta==$tombo){
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_cod_exemplar = null, $int_ref_cod_fonte = null, $int_ref_cod_motivo_baixa = null, $int_ref_cod_acervo = null, $int_ref_cod_situacao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_permite_emprestimo = null, $int_preco = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $date_data_aquisicao_ini = null, $date_data_aquisicao_fim = null, $int_ref_exemplar_tipo = null, $str_titulo_livro = null,$int_ref_cod_biblioteca = null, $str_titulo = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $int_tombo = null )	{
		$sql = "SELECT {$this->_campos_lista}, a.ref_cod_biblioteca, a.titulo FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b";

		$whereAnd = " AND";
		$filtros = " WHERE e.ref_cod_acervo = a.cod_acervo AND a.ref_cod_biblioteca = b.cod_biblioteca ";

		if( is_numeric( $int_cod_exemplar ) )
		{
			$filtros .= "{$whereAnd} e.cod_exemplar = '{$int_cod_exemplar}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_fonte ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_fonte = '{$int_ref_cod_fonte}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_motivo_baixa ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_motivo_baixa = '{$int_ref_cod_motivo_baixa}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acervo ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_acervo = '{$int_ref_cod_acervo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_situacao ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_situacao = '{$int_ref_cod_situacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} e.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} e.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_permite_emprestimo ) )
		{
			$filtros .= "{$whereAnd} e.permite_emprestimo = '{$int_permite_emprestimo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_preco ) )
		{
			$filtros .= "{$whereAnd} e.preco = '{$int_preco}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} e.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} e.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} e.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} e.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} e.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} e.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_aquisicao_ini ) )
		{
			$filtros .= "{$whereAnd} e.data_aquisicao >= '{$date_data_aquisicao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_aquisicao_fim ) )
		{
			$filtros .= "{$whereAnd} e.data_aquisicao <= '{$date_data_aquisicao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_titulo ) )
		{
			$filtros .= "{$whereAnd} a.titulo LIKE '%{$str_titulo}%'";
			$whereAnd = " AND ";
		}
		if (is_numeric($int_tombo))
		{
			$filtros .= "{$whereAnd} e.tombo = {$int_tombo}";
			$whereAnd = " AND ";
		}


		/**
		 * INICIO  - PESQUISAS EXTRAS
		 */
		$whereAnd2 = " AND ";
		$filtros_extra = null;

		if( is_string( $str_titulo_livro ) )
		{
			$filtros_extra .= "{$whereAnd2} to_ascii(a.titulo) ilike to_ascii('%{$date_data_aquisicao_fim}%') ";
			$whereAnd2 = " AND ";
		}

		if( is_numeric( $int_ref_exemplar_tipo ) )
		{
			$filtros_extra .= "{$whereAnd} a.ref_cod_exemplar_tipo = $int_ref_exemplar_tipo";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ref_cod_biblioteca ) )
		{
			$filtros_extra .= "{$whereAnd} a.ref_cod_biblioteca = $int_ref_cod_biblioteca";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros_extra .= "{$whereAnd} b.ref_cod_instituicao = $int_ref_cod_instituicao";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros_extra .= "{$whereAnd} b.ref_cod_escola = $int_ref_cod_escola";
			$whereAnd = " AND ";
		}

		if($filtros_extra)
			$filtros .= "{$whereAnd} exists (SELECT 1 FROM pmieducar.acervo a where a.cod_acervo = e.ref_cod_acervo {$filtros_extra} )";
		/**
		 * FIM  - PESQUISAS EXTRAS
		 */


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b {$filtros}" );

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

	function retorna_tombo_maximo($bibliotecaId, $exceptExemplarId = null) {
    if (empty($bibliotecaId))
      throw new Exception("Deve ser enviado um argumento '\$bibliotecaId' ao método 'retorna_tombo_maximo'");

    // sem esta regra ao editar o ultimo exemplar sem informar o tombo, seria pego o proprio tombo.
    if (! empty($exceptExemplarId))
      $exceptExemplar = " and exemplar.cod_exemplar !=  $exceptExemplarId";
    else
      $exceptExemplar = '';

		$sql = "SELECT MAX(tombo) as tombo_max FROM pmieducar.exemplar, pmieducar.acervo WHERE exemplar.ativo = 1 and exemplar.ref_cod_acervo = acervo.cod_acervo and acervo.ref_cod_biblioteca = $bibliotecaId $exceptExemplar";

		$db = new clsBanco();
		return $db->CampoUnico($sql);
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista_com_acervos( $int_cod_exemplar = null, $int_ref_cod_fonte = null, $int_ref_cod_motivo_baixa = null, $int_ref_cod_acervo = null, $int_ref_cod_situacao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_permite_emprestimo = null, $int_preco = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $date_data_aquisicao_ini = null, $date_data_aquisicao_fim = null, $int_ref_exemplar_tipo = null, $str_titulo_livro = null,$int_ref_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $int_ref_cod_acervo_colecao = null, $int_ref_cod_acervo_editora = null, $tombo)	{
		$sql = "SELECT {$this->_campos_lista}, a.ref_cod_biblioteca, a.titulo FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b";

		$whereAnd = " AND";
		$filtros = " WHERE e.ref_cod_acervo = a.cod_acervo AND a.ref_cod_biblioteca = b.cod_biblioteca ";

		if( is_numeric( $int_cod_exemplar ) )
		{
			$filtros .= "{$whereAnd} e.cod_exemplar = '{$int_cod_exemplar}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_fonte ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_fonte = '{$int_ref_cod_fonte}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_motivo_baixa ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_motivo_baixa = '{$int_ref_cod_motivo_baixa}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acervo ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_acervo = '{$int_ref_cod_acervo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_situacao ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_situacao = '{$int_ref_cod_situacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} e.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} e.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_permite_emprestimo ) )
		{
			$filtros .= "{$whereAnd} e.permite_emprestimo = '{$int_permite_emprestimo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_preco ) )
		{
			$filtros .= "{$whereAnd} e.preco = '{$int_preco}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} e.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} e.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} e.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} e.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} e.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} e.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_aquisicao_ini ) )
		{
			$filtros .= "{$whereAnd} e.data_aquisicao >= '{$date_data_aquisicao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_aquisicao_fim ) )
		{
			$filtros .= "{$whereAnd} e.data_aquisicao <= '{$date_data_aquisicao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_titulo_livro ) )
		{
			$filtros .= "{$whereAnd} to_ascii(a.titulo) LIKE to_ascii('%{$str_titulo_livro}%')";
			$whereAnd = " AND ";
		}

		if (is_numeric($tombo)) {
			$filtros .= "{$whereAnd} e.tombo = $tombo";
			$whereAnd = " AND ";
		}

		/**
		 * INICIO  - PESQUISAS EXTRAS
		 */
		$whereAnd2 = " AND ";
		$filtros_extra = null;

		if( is_numeric( $int_ref_exemplar_tipo ) )
		{
			$filtros_extra .= "{$whereAnd} a.ref_cod_exemplar_tipo = $int_ref_exemplar_tipo";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ref_cod_biblioteca ) )
		{
			$filtros_extra .= "{$whereAnd} a.ref_cod_biblioteca = $int_ref_cod_biblioteca";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros_extra .= "{$whereAnd} b.ref_cod_instituicao = $int_ref_cod_instituicao";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros_extra .= "{$whereAnd} b.ref_cod_escola = $int_ref_cod_escola";
			$whereAnd = " AND ";
		}
		if (is_numeric($int_ref_cod_acervo_colecao))
		{
			$filtros_extra .= "{$whereAnd} a.ref_cod_acervo_colecao = {$int_ref_cod_acervo_colecao}";
			$whereAnd = " AND ";
		}
		if (is_numeric($int_ref_cod_acervo_editora))
		{
			$filtros_extra .= "{$whereAnd} a.ref_cod_acervo_editora = {$int_ref_cod_acervo_editora}";
			$whereAnd = " AND ";
		}

		if($filtros_extra)
			$filtros .= "{$whereAnd} exists (SELECT 1 FROM pmieducar.acervo a where a.cod_acervo = e.ref_cod_acervo {$filtros_extra} )";
		/**
		 * FIM  - PESQUISAS EXTRAS
		 */


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b {$filtros}" );

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
		if( is_numeric( $this->cod_exemplar ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} e WHERE e.cod_exemplar = '{$this->cod_exemplar}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function existe()
	{
		if( is_numeric( $this->cod_exemplar ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_exemplar = '{$this->cod_exemplar}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
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
		if( is_numeric( $this->cod_exemplar ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_exemplar = '{$this->cod_exemplar}'" );
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
