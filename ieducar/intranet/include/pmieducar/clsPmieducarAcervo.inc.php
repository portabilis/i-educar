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
* Criado em 14/07/2006 16:58 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarAcervo
{
	var $cod_acervo;
	var $ref_cod_exemplar_tipo;
	var $ref_cod_acervo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_acervo_colecao;
	var $ref_cod_acervo_idioma;
	var $ref_cod_acervo_editora;
	var $titulo;
	var $sub_titulo;
	var $cdu;
	var $cutter;
	var $volume;
	var $num_edicao;
	var $ano;
	var $num_paginas;
	var $isbn;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

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
	function clsPmieducarAcervo( $cod_acervo = null, $ref_cod_exemplar_tipo = null, $ref_cod_acervo = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_acervo_colecao = null, $ref_cod_acervo_idioma = null, $ref_cod_acervo_editora = null, $titulo = null, $sub_titulo = null, $cdu = null, $cutter = null, $volume = null, $num_edicao = null, $ano = null, $num_paginas = null, $isbn = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_biblioteca = null, $cdd = null, $estante = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}acervo";

		$this->_campos_lista = $this->_todos_campos = "a.cod_acervo, a.ref_cod_exemplar_tipo, a.ref_cod_acervo, a.ref_usuario_exc, a.ref_usuario_cad, a.ref_cod_acervo_colecao, a.ref_cod_acervo_idioma, a.ref_cod_acervo_editora, a.titulo, a.sub_titulo, a.cdu, a.cutter, a.volume, a.num_edicao, a.ano, a.num_paginas, a.isbn, a.data_cadastro, a.data_exclusao, a.ativo, a.ref_cod_biblioteca, a.cdd, a.estante";

		if( is_numeric( $ref_cod_biblioteca ) )
		{
			if( class_exists( "clsPmieducarBiblioteca" ) )
			{
				$tmp_obj = new clsPmieducarBiblioteca( $ref_cod_biblioteca );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_biblioteca = $ref_cod_biblioteca;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_biblioteca = $ref_cod_biblioteca;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.biblioteca WHERE cod_biblioteca = '{$ref_cod_biblioteca}'" ) )
				{
					$this->ref_cod_biblioteca = $ref_cod_biblioteca;
				}
			}
		}
		if( is_numeric( $ref_cod_exemplar_tipo ) )
		{
			if( class_exists( "clsPmieducarExemplarTipo" ) )
			{
				$tmp_obj = new clsPmieducarExemplarTipo( $ref_cod_exemplar_tipo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_exemplar_tipo = $ref_cod_exemplar_tipo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_exemplar_tipo = $ref_cod_exemplar_tipo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.exemplar_tipo WHERE cod_exemplar_tipo = '{$ref_cod_exemplar_tipo}'" ) )
				{
					$this->ref_cod_exemplar_tipo = $ref_cod_exemplar_tipo;
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
		}elseif ( $ref_cod_acervo == "NULL")
		{
			$this->ref_cod_acervo = "NULL";
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
		if( is_numeric( $ref_cod_acervo_colecao ) )
		{
			if( class_exists( "clsPmieducarAcervoColecao" ) )
			{
				$tmp_obj = new clsPmieducarAcervoColecao( $ref_cod_acervo_colecao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_acervo_colecao = $ref_cod_acervo_colecao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_acervo_colecao = $ref_cod_acervo_colecao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.acervo_colecao WHERE cod_acervo_colecao = '{$ref_cod_acervo_colecao}'" ) )
				{
					$this->ref_cod_acervo_colecao = $ref_cod_acervo_colecao;
				}
			}
		}
		if( is_numeric( $ref_cod_acervo_idioma ) )
		{
			if( class_exists( "clsPmieducarAcervoIdioma" ) )
			{
				$tmp_obj = new clsPmieducarAcervoIdioma( $ref_cod_acervo_idioma );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_acervo_idioma = $ref_cod_acervo_idioma;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_acervo_idioma = $ref_cod_acervo_idioma;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.acervo_idioma WHERE cod_acervo_idioma = '{$ref_cod_acervo_idioma}'" ) )
				{
					$this->ref_cod_acervo_idioma = $ref_cod_acervo_idioma;
				}
			}
		}
		if( is_numeric( $ref_cod_acervo_editora ) )
		{
			if( class_exists( "clsPmieducarAcervoEditora" ) )
			{
				$tmp_obj = new clsPmieducarAcervoEditora( $ref_cod_acervo_editora );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_acervo_editora = $ref_cod_acervo_editora;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_acervo_editora = $ref_cod_acervo_editora;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.acervo_editora WHERE cod_acervo_editora = '{$ref_cod_acervo_editora}'" ) )
				{
					$this->ref_cod_acervo_editora = $ref_cod_acervo_editora;
				}
			}
		}


		if( is_numeric( $cod_acervo ) )
		{
			$this->cod_acervo = $cod_acervo;
		}
		if( is_string( $titulo ) )
		{
			$this->titulo = $titulo;
		}
		if( is_string( $sub_titulo ) )
		{
			$this->sub_titulo = $sub_titulo;
		}
		if( is_string( $cdu ) )
		{
			$this->cdu = $cdu;
		}
		if( is_string( $cutter ) )
		{
			$this->cutter = $cutter;
		}
		if( is_numeric( $volume ) )
		{
			$this->volume = $volume;
		}
		if( is_numeric( $num_edicao ) )
		{
			$this->num_edicao = $num_edicao;
		}
		if( is_numeric( $ano ) )
		{
			$this->ano = $ano;
		}
		if( is_numeric( $num_paginas ) )
		{
			$this->num_paginas = $num_paginas;
		}
		if( is_numeric( $isbn ) )
		{
			$this->isbn = $isbn;
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

    $this->cdd     = $cdd;
    $this->estante = $estante;
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_exemplar_tipo ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_acervo_idioma ) && is_numeric( $this->ref_cod_acervo_editora ) && is_string( $this->titulo ) && is_numeric( $this->volume ) && is_numeric( $this->num_edicao ) && is_numeric( $this->ano ) && is_numeric( $this->num_paginas ) && is_numeric( $this->ref_cod_biblioteca ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_exemplar_tipo ) )
			{
				$campos .= "{$gruda}ref_cod_exemplar_tipo";
				$valores .= "{$gruda}'{$this->ref_cod_exemplar_tipo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo )  || $this->ref_cod_acervo == "NULL")
			{
				$campos .= "{$gruda}ref_cod_acervo";
				$valores .= "{$gruda}{$this->ref_cod_acervo}";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo_colecao ) )
			{
				$campos .= "{$gruda}ref_cod_acervo_colecao";
				$valores .= "{$gruda}'{$this->ref_cod_acervo_colecao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo_idioma ) )
			{
				$campos .= "{$gruda}ref_cod_acervo_idioma";
				$valores .= "{$gruda}'{$this->ref_cod_acervo_idioma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo_editora ) )
			{
				$campos .= "{$gruda}ref_cod_acervo_editora";
				$valores .= "{$gruda}'{$this->ref_cod_acervo_editora}'";
				$gruda = ", ";
			}
			if( is_string( $this->titulo ) )
			{
				$campos .= "{$gruda}titulo";
				$valores .= "{$gruda}'{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->sub_titulo ) )
			{
				$campos .= "{$gruda}sub_titulo";
				$valores .= "{$gruda}'{$this->sub_titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->cdu ) )
			{
				$campos .= "{$gruda}cdu";
				$valores .= "{$gruda}'{$this->cdu}'";
				$gruda = ", ";
			}
			if( is_string( $this->cutter ) )
			{
				$campos .= "{$gruda}cutter";
				$valores .= "{$gruda}'{$this->cutter}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->volume ) )
			{
				$campos .= "{$gruda}volume";
				$valores .= "{$gruda}'{$this->volume}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_edicao ) )
			{
				$campos .= "{$gruda}num_edicao";
				$valores .= "{$gruda}'{$this->num_edicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$campos .= "{$gruda}ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_paginas ) )
			{
				$campos .= "{$gruda}num_paginas";
				$valores .= "{$gruda}'{$this->num_paginas}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->isbn ) )
			{
				$campos .= "{$gruda}isbn";
				$valores .= "{$gruda}'{$this->isbn}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";

			if( is_numeric( $this->ref_cod_biblioteca ) )
			{
				$campos .= "{$gruda}ref_cod_biblioteca";
				$valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
				$gruda = ", ";
			}

			if( is_string( $this->cdd ) )
			{
				$campos .= "{$gruda}cdd";
				$valores .= "{$gruda}'{$this->cdd}'";
				$gruda = ", ";
			}

			if( is_string( $this->estante ) )
			{
				$campos .= "{$gruda}estante";
				$valores .= "{$gruda}'{$this->estante}'";
				$gruda = ", ";
			}

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_acervo_seq");
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
		if( is_numeric( $this->cod_acervo ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_exemplar_tipo ) )
			{
				$set .= "{$gruda}ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo ) || $this->ref_cod_acervo == "NULL")
			{
				$set .= "{$gruda}ref_cod_acervo = {$this->ref_cod_acervo}";
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
			if( is_numeric( $this->ref_cod_acervo_colecao ) )
			{
				$set .= "{$gruda}ref_cod_acervo_colecao = '{$this->ref_cod_acervo_colecao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo_idioma ) )
			{
				$set .= "{$gruda}ref_cod_acervo_idioma = '{$this->ref_cod_acervo_idioma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo_editora ) )
			{
				$set .= "{$gruda}ref_cod_acervo_editora = '{$this->ref_cod_acervo_editora}'";
				$gruda = ", ";
			}
			if( is_string( $this->titulo ) )
			{
				$set .= "{$gruda}titulo = '{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->sub_titulo ) )
			{
				$set .= "{$gruda}sub_titulo = '{$this->sub_titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->cdu ) )
			{
				$set .= "{$gruda}cdu = '{$this->cdu}'";
				$gruda = ", ";
			}
			if( is_string( $this->cutter ) )
			{
				$set .= "{$gruda}cutter = '{$this->cutter}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->volume ) )
			{
				$set .= "{$gruda}volume = '{$this->volume}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_edicao ) )
			{
				$set .= "{$gruda}num_edicao = '{$this->num_edicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$set .= "{$gruda}ano = '{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_paginas ) )
			{
				$set .= "{$gruda}num_paginas = '{$this->num_paginas}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->isbn ) )
			{
				$set .= "{$gruda}isbn = '{$this->isbn}'";
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
			if( is_numeric( $this->ref_cod_biblioteca ) )
			{
				$set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
				$gruda = ", ";
			}

			if( is_string( $this->cdd ) )
			{
				$set .= "{$gruda}cdd = '{$this->cdd}'";
				$gruda = ", ";
			}

			if( is_string( $this->estante ) )
			{
				$set .= "{$gruda}estante = '{$this->estante}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_acervo = '{$this->cod_acervo}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_cod_acervo = null, $int_ref_cod_exemplar_tipo = null, $int_ref_cod_acervo = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_acervo_colecao = null, $int_ref_cod_acervo_idioma = null, $int_ref_cod_acervo_editora = null, $str_titulo = null, $str_sub_titulo = null, $str_cdu = null, $str_cutter = null, $int_volume = null, $int_num_edicao = null, $int_ano = null, $int_num_paginas = null, $int_isbn = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $str_nm_autor = null )
	{
		$sql = "SELECT {$this->_campos_lista}, aa.cod_acervo_autor FROM {$this->_tabela} a, {$this->_schema}biblioteca b, {$this->_schema}acervo_acervo_autor aaa, {$this->_schema}acervo_autor aa";

		$whereAnd = " AND ";
		$filtros = " WHERE a.ref_cod_biblioteca = b.cod_biblioteca AND a.cod_acervo = aaa.ref_cod_acervo AND aaa.ref_cod_acervo_autor = aa.cod_acervo_autor ";

		if( is_numeric( $int_cod_acervo ) )
		{
			$filtros .= "{$whereAnd} a.cod_acervo = '{$int_cod_acervo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_exemplar_tipo ) )
		{
			$filtros .= "{$whereAnd} a.ref_cod_exemplar_tipo = '{$int_ref_cod_exemplar_tipo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acervo ) )
		{
			$filtros .= "{$whereAnd} a.ref_cod_acervo = '{$int_ref_cod_acervo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} a.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} a.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acervo_colecao ) )
		{
			$filtros .= "{$whereAnd} a.ref_cod_acervo_colecao = '{$int_ref_cod_acervo_colecao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acervo_idioma ) )
		{
			$filtros .= "{$whereAnd} a.ref_cod_acervo_idioma = '{$int_ref_cod_acervo_idioma}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acervo_editora ) )
		{
			$filtros .= "{$whereAnd} a.ref_cod_acervo_editora = '{$int_ref_cod_acervo_editora}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_titulo ) )
		{
			$filtros .= "{$whereAnd} a.titulo LIKE '%{$str_titulo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sub_titulo ) )
		{
			$filtros .= "{$whereAnd} a.sub_titulo LIKE '%{$str_sub_titulo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_cdu ) )
		{
			$filtros .= "{$whereAnd} a.cdu LIKE '%{$str_cdu}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_cutter ) )
		{
			$filtros .= "{$whereAnd} a.cutter LIKE '%{$str_cutter}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_volume ) )
		{
			$filtros .= "{$whereAnd} a.volume = '{$int_volume}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_num_edicao ) )
		{
			$filtros .= "{$whereAnd} a.num_edicao = '{$int_num_edicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ano ) )
		{
			$filtros .= "{$whereAnd} a.ano = '{$int_ano}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_num_paginas ) )
		{
			$filtros .= "{$whereAnd} a.num_paginas = '{$int_num_paginas}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_isbn ) )
		{
			$filtros .= "{$whereAnd} a.isbn = '{$int_isbn}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} a.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} a.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} a.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} a.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} a.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} a.ativo = '0'";
			$whereAnd = " AND ";
		}
		if(is_array($int_ref_cod_biblioteca))
		{
			$bibs = implode(", ", $int_ref_cod_biblioteca);
			$filtros .= "{$whereAnd} (a.ref_cod_biblioteca IN ($bibs) OR a.ref_cod_biblioteca IS NULL)";
			$whereAnd = " AND ";
		}
		elseif (is_numeric($int_ref_cod_biblioteca))
		{
			$filtros .= "{$whereAnd} a.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ))
		{
			$filtro .= "{$whereAnd} b.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ))
		{
			$filtro .= "{$whereAnd} b.ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_autor ) )
		{
			$filtros .= "{$whereAnd} aa.nm_autor LIKE '%{$str_nm_autor}%'";
			$whereAnd = " AND ";
		}
		/*else
		{
			$filtros .= "{$whereAnd} aaa.principal = '1'";
			$whereAnd = " AND ";
		}*/

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} a, {$this->_schema}biblioteca b, {$this->_schema}acervo_acervo_autor aaa, {$this->_schema}acervo_autor aa {$filtros}" );

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


	function listaAcervoBiblioteca($int_ref_cod_biblioteca = null, $str_titulo = null, $ativo = null, $int_ref_cod_acervo_colecao = null,  $int_ref_cod_exemplar_tipo = null, $int_ref_cod_acervo_editora = null)
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} a";

		$whereAnd = " WHERE ";
		if(is_array($int_ref_cod_biblioteca))
		{
			$bibs = implode(", ", $int_ref_cod_biblioteca);
			$filtros .= "{$whereAnd} (ref_cod_biblioteca IN ($bibs) OR ref_cod_biblioteca IS NULL)";
			$whereAnd = " AND ";
		}
		elseif (is_numeric($int_ref_cod_biblioteca))
		{
			$filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
			$whereAnd = " AND ";
		}

		if(is_string($str_titulo))
		{
			$filtros .= "{$whereAnd} titulo LIKE '%{$str_titulo}%'";
			$whereAnd = " AND ";
		}
		if (is_numeric($ativo))
		{
			$filtros .= "{$whereAnd} ativo = {$ativo}";
			$whereAnd = " AND ";
		}
		if (is_numeric($int_ref_cod_acervo_colecao))
		{
			$filtros .= "{$whereAnd} ref_cod_acervo_colecao = {$int_ref_cod_acervo_colecao}";
			$whereAnd = " AND ";
		}
		if (is_numeric($int_ref_cod_exemplar_tipo))
		{
			$filtros .= "{$whereAnd} ref_cod_exemplar_tipo = {$int_ref_cod_exemplar_tipo}";
			$whereAnd = " AND ";
		}
		if (is_numeric($int_ref_cod_acervo_editora))
		{
			$filtros .= "{$whereAnd} ref_cod_acervo_editora = {$int_ref_cod_acervo_editora}";
			$whereAnd = " AND ";
		}

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
		$db = new clsBanco();
		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} a {$filtros}" );

		$db->Consulta( $sql );
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

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
		if( is_numeric( $this->cod_acervo ) )
		{
		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} a WHERE a.cod_acervo = '{$this->cod_acervo}'" );
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
		if( is_numeric( $this->cod_acervo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_acervo = '{$this->cod_acervo}'" );
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
		if( is_numeric( $this->cod_acervo ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_acervo = '{$this->cod_acervo}'" );
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
