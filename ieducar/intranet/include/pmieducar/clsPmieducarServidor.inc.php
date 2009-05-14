<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * clsPmieducarServidor class
 *
 * Implementa o mapeamento com a tabela pmieducar.servidor. Contém métodos para
 * busca de servidores utilizando os relacionamentos estrangeiros que a tabela
 * possui.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Servidor
 * @since       Classe disponível desde a versão 1.0.0
 * @version     $Id$
 */


require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarServidor {

	var $cod_servidor;
	var $ref_cod_deficiencia;
	var $ref_idesco;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;
	var $ref_cod_subnivel;

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
	var $_campos_lista2;

	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;
	var $_todos_campos2;

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


	/**ord
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
  public function clsPmieducarServidor(
    $cod_servidor = NULL, $ref_cod_deficiencia = NULL, $ref_idesco = NULL,
    $carga_horaria = NULL, $data_cadastro = NULL, $data_exclusao = NULL,
    $ativo = NULL, $ref_cod_instituicao = NULL, $ref_cod_subnivel = NULL) {

    $db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}servidor";

		$this->_campos_lista = $this->_todos_campos = "cod_servidor, ref_cod_deficiencia, ref_idesco, carga_horaria, data_cadastro, data_exclusao, ativo, ref_cod_instituicao,ref_cod_subnivel";
		$this->_campos_lista2 = $this->_todos_campos2 = "s.cod_servidor, s.ref_cod_deficiencia, s.ref_idesco, s.carga_horaria, s.data_cadastro, s.data_exclusao, s.ativo, s.ref_cod_instituicao,s.ref_cod_subnivel";

		if( is_numeric( $ref_cod_deficiencia ) )
		{
			if( class_exists( "clsCadastroDeficiencia" ) )
			{
				$tmp_obj = new clsCadastroDeficiencia( $ref_cod_deficiencia );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_deficiencia = $ref_cod_deficiencia;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_deficiencia = $ref_cod_deficiencia;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.deficiencia WHERE cod_deficiencia = '{$ref_cod_deficiencia}'" ) )
				{
					$this->ref_cod_deficiencia = $ref_cod_deficiencia;
				}
			}
		}
		if( is_numeric( $ref_idesco ) )
		{
			if( class_exists( "clsCadastroEscolaridade" ) )
			{
				$tmp_obj = new clsCadastroEscolaridade( $ref_idesco );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_idesco = $ref_idesco;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_idesco = $ref_idesco;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.escolaridade WHERE idesco = '{$ref_idesco}'" ) )
				{
					$this->ref_idesco = $ref_idesco;
				}
			}
		}
		if( is_numeric( $cod_servidor ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $cod_servidor );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->cod_servidor = $cod_servidor;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->cod_servidor = $cod_servidor;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$cod_servidor}'" ) )
				{
					$this->cod_servidor = $cod_servidor;
				}
			}
		}


		if( is_numeric( $carga_horaria ) )
		{
			$this->carga_horaria = $carga_horaria;
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

		if( is_numeric( $ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$tmp_obj = new clsPmieducarInstituicao( $ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_instituicao = $ref_cod_instituicao;
				}
			}
		}

		if( is_numeric( $ref_cod_subnivel) )
		{
			if( class_exists( "clsPmieducarSubnivel" ) )
			{
				$tmp_obj = new clsPmieducarSubnivel( $ref_cod_subnivel );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_subnivel = $ref_cod_subnivel;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_subnivel = $ref_cod_subnivel;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.subnivel WHERE cod_subnivel = '{$ref_cod_subnivel}'" ) )
				{
					$this->ref_cod_subnivel = $ref_cod_subnivel;
				}
			}
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->ref_cod_instituicao ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";
			if( is_numeric( $this->cod_servidor ) )
			{
				$campos .= "{$gruda}cod_servidor";
				$valores .= "{$gruda}'{$this->cod_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_deficiencia ) )
			{
				$campos .= "{$gruda}ref_cod_deficiencia";
				$valores .= "{$gruda}'{$this->ref_cod_deficiencia}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idesco ) )
			{
				$campos .= "{$gruda}ref_idesco";
				$valores .= "{$gruda}'{$this->ref_idesco}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$campos .= "{$gruda}carga_horaria";
				$valores .= "{$gruda}'{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_subnivel ) )
			{
				$campos .= "{$gruda}ref_cod_subnivel";
				$valores .= "{$gruda}'{$this->ref_cod_subnivel}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $this->cod_servidor;
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

		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_deficiencia ) )
			{
				$set .= "{$gruda}ref_cod_deficiencia = '{$this->ref_cod_deficiencia}'";
				$gruda = ", ";
			}
			else {
				$set .= "{$gruda}ref_cod_deficiencia = NULL";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idesco ) )
			{
				$set .= "{$gruda}ref_idesco = '{$this->ref_idesco}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
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
			if( is_numeric( $this->ref_cod_subnivel ) )
			{
				$set .= "{$gruda}ref_cod_subnivel = '{$this->ref_cod_subnivel}'";
				$gruda = ", ";
			}
			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
				return true;
			}
		}
		return false;
	}



  /**
   * Retorna um array com resultados de uma pesquisa parametrizada
   *
   * O array retornado contém em cada um de seus items um array associativo onde
   * as chaves correspondem aos campos da tabela indicados por
   * $this->_campos_lista.
   *
   * A pesquisa SELECT realizada é afetada por diversos parâmetros disponíveis.
   * Alguns dos parâmetros induzem a subqueries para a avaliação de diferentes
   * funcionalidades do sistema.
   *
   * @see intranet/educar_pesquisa_servidor_lst.php  Listagem de busca de
   *  servidores
   * @see intranet/educar_quadro_horario_horarios_cad.php  Cadastro de horário
   *  de aula para uma turma
   * @see intranet/educar_turma_cad.php  Cadastro de turma
   *
   * @param  int         $int_cod_servidor             Código do servidor
   * @param  int         $int_ref_cod_deficiencia      Código da deficiência do servidor
   * @param  int         $int_ref_idesco               Código da escolaridade do servidor
   * @param  int         $int_carga_horaria            Carga horária do servidor
   * @param  string      $date_data_cadastro_ini       Data de cadastro inicial (busca por intervalo >= ao valor)
   * @param  string      $date_data_cadastro_fim       Data de cadastro final (busca por intervalo <= ao valor)
   * @param  string      $date_data_exclusao_ini       Data da exclusão inicial (busca por intervalo >= ao valor)
   * @param  string      $date_data_exclusao_fim       Data da exclusão final (busca por intervalo <= ao valor)
   * @param  int         $int_ativo                    '1' para buscar apenas por servidores ativos
   * @param  int         $int_ref_cod_instituicao      Código da instituição do servidor
   * @param  string      $str_tipo                     'livre' para buscar apenas por servidores não alocados (subquery)
   * @param  array       $array_horario                Busca por horário de alocação do servidor (subquery)
   * @param  int         $str_not_in_servidor          Código de servidor a excluir
   * @param  string      $str_nome_servidor            Busca do tipo LIKE pelo padrão de nome do servidor (subquery)
   * @param  int|string  $boo_professor                Qualquer valor que avalie para TRUE para buscar por servidores professores (subquery)
   * @param  string      $str_horario                  'S' para buscar se o servidor está alocado em um dos horários (indicados $matutino, $vespertino ou $noturno) (subquery)
   * @param  bool        $bool_ordena_por_nome         TRUE para ordenar os resultados pelo campo nome por ordem alfabética crescente
   * @param  string      $lst_matriculas               Verifica se o servidor não está na lista de matriculas (string com inteiros separados por vírgula: 54, 55, 60).
   *                                                   Apenas verifica quando a buscar por horário de alocação é realizada
   * @param  bool        $matutino                     Busca por professores com horário livre no período matutino
   * @param  bool        $vespertino                   Busca por professores com horário livre no período vespertino
   * @param  bool        $noturno                      Busca por professores com horário livre no período noturno
   * @param  int         $int_ref_cod_escola           Código da escola para verificar se o servidor está alocado nela (usado em várias das subqueries)
   * @param  string      $str_hr_mat                   Duração da aula (formato HH:MM) para o período matutino
   * @param  string      $str_hr_ves                   Duração da aula (formato HH:MM) para o período vespertino
   * @param  string      $str_hr_not                   Duração da aula (formato HH:MM) para o período noturno
   * @param  int         $int_dia_semana               Inteiro para o dia da semana (1 = domingo, 7 = sábado)
   * @param  int         $alocacao_escola_instituicao  Código da instituição ao qual o servidor deve estar cadastrado (subquery)
   * @param  int         $int_identificador            Campo identificado para busca na tabela pmieducar.quadro_horario_horarios_aux (subquery)
   * @param  int         $int_ref_cod_curso            Código do curso que o professor deve estar cadastrado (subquery)
   * @param  int         $int_ref_cod_disciplina       Código da disciplina que o professor deve ser habilitado (subquery).
   *                                                   Somente verifica quando o curso passado por $int_ref_cod_curso não
   *                                                   possui sistema de falta globalizada
   * @param  int         $int_ref_cod_subnivel         Código de subnível que o servidor deve possuir
   * @return array|bool  Array com os resultados da query SELECT ou FALSE caso
   *                     nenhum registro tenha sido encontrado
   */
  public function lista(
    $int_cod_servidor            = NULL,
    $int_ref_cod_deficiencia     = NULL,
    $int_ref_idesco              = NULL,
    $int_carga_horaria           = NULL,
    $date_data_cadastro_ini      = NULL,
    $date_data_cadastro_fim      = NULL,
    $date_data_exclusao_ini      = NULL,
    $date_data_exclusao_fim      = NULL,
    $int_ativo                   = NULL,
    $int_ref_cod_instituicao     = NULL,
    $str_tipo                    = NULL,
    $array_horario               = NULL,
    $str_not_in_servidor         = NULL,
    $str_nome_servidor           = NULL,
    $boo_professor               = FALSE,
    $str_horario                 = NULL,
    $bool_ordena_por_nome        = FALSE,
    $lst_matriculas              = NULL,
    $matutino                    = FALSE,
    $vespertino                  = FALSE,
    $noturno                     = FALSE,
    $int_ref_cod_escola          = NULL,
    $str_hr_mat                  = NULL,
    $str_hr_ves                  = NULL,
    $str_hr_not                  = NULL,
    $int_dia_semana              = NULL,
    $alocacao_escola_instituicao = NULL,
    $int_identificador           = NULL,
    $int_ref_cod_curso           = NULL,
    $int_ref_cod_disciplina      = NULL,
    $int_ref_cod_subnivel        = NULL
    ) {

    $whereAnd     = ' WHERE ';
    $filtros      = '';
    $tabela_compl = '';

    if (is_bool($bool_ordena_por_nome)) {
      $tabela_compl         .= ', cadastro.pessoa p';
      $this->_campos_lista2 .= ', p.nome';
      $filtros              .= $whereAnd . ' s.cod_servidor = p.idpes ';
      $whereAnd              = ' AND ';
      $this->setOrderby('nome');
    }
    else {
      $this->_campos_lista2 = $this->_todos_campos2;
      $this->setOrderby(' 1 ');
    }

    $sql = "SELECT {$this->_campos_lista2} FROM {$this->_schema}servidor s{$tabela_compl}";

    if (is_numeric($int_cod_servidor)) {
      $filtros .= "{$whereAnd} s.cod_servidor = '{$int_cod_servidor}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_deficiencia)) {
      $filtros .= "{$whereAnd} s.ref_cod_deficiencia = '{$int_ref_cod_deficiencia}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_idesco)) {
      $filtros .= "{$whereAnd} s.ref_idesco = '{$int_ref_idesco}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_carga_horaria)) {
      $filtros .= "{$whereAnd} s.carga_horaria = '{$int_carga_horaria}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} s.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} s.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} s.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} s.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} s.ativo = '1'";
      $whereAnd = " AND ";
    }
    else {
      $filtros .= "{$whereAnd} s.ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} s.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    // Busca tipo LIKE pelo nome do servidor
    if (is_string($str_nome_servidor)) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1
  FROM cadastro.pessoa p
  WHERE cod_servidor = p.idpes
  AND to_ascii(p.nome) LIKE to_ascii('%$str_nome_servidor%')) ";
      $whereAnd = " AND ";
    }

    // Seleciona apenas servidores que tenham a carga atual maior ou igual ao
    // do servidor atual
    if (is_string($str_tipo)) {
      switch ($str_tipo) {
        case "livre":
          if (is_numeric($int_ref_cod_instituicao)) {
            $where  = " AND s.ref_cod_instituicao      = '{$int_ref_cod_instituicao}' ";
            $where2 = " AND sa.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ";
          }

          $filtros .= "
  {$whereAnd} NOT EXISTS
    (SELECT 1
    FROM pmieducar.servidor_alocacao sa
    WHERE sa.ref_cod_servidor = s.cod_servidor $where2)";

          $filtros .= "
  {$whereAnd} (s.carga_horaria::text || ':00:00') >= COALESCE(
    (SELECT SUM(carga_horaria)::text
    FROM pmieducar.servidor_alocacao saa
    WHERE saa.ref_cod_servidor = {$str_not_in_servidor}),'00:00') $where";

          $whereAnd = " AND ";
          break;
      }

      $whereAnd = " AND ";
    }

    if (is_numeric($alocacao_escola_instituicao)) {
      $filtros .= "
  {$whereAnd} s.cod_servidor IN
    (SELECT a.ref_cod_servidor
      FROM pmieducar.servidor_alocacao a
      WHERE ";

      if (is_numeric($int_ref_cod_instituicao)) {
        $filtros .=  " a.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      }

      if (is_numeric($int_ref_cod_escola)) {
        $filtros .=  " and ref_cod_escola = '{$int_ref_cod_escola}' ";
      }

      $filtros .= ') ';
    }

    if (is_array($array_horario)) {
      $cond = "";
      if (is_numeric($int_ref_cod_instituicao)) {
        $where .= " {$cond} a.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ";
        $cond   = "AND";
      }

      if (is_numeric($int_ref_cod_escola)) {
        $where .= " {$cond} a.ref_cod_escola = '{$int_ref_cod_escola}' ";
        $cond   = "AND";
      }

      $where .= " {$cond} a.ativo = '1'";
      $cond   = "AND";

      $hora_ini = explode(":", $array_horario[1]);
      $hora_fim = explode(":", $array_horario[2]);
      $horas    = sprintf("%02d", (int) abs($hora_fim[0]) - abs($hora_ini[0]));
      $minutos  = sprintf("%02d", (int) abs($hora_fim[1]) - abs($hora_ini[1]));

      if ($matutino) {
        if (is_string($str_horario) && $str_horario == "S") {
          // A somatória retorna nulo
          $filtros .= "
  {$whereAnd} s.cod_servidor IN (SELECT a.ref_cod_servidor
    FROM pmieducar.servidor_alocacao a
    WHERE $where
    AND a.periodo = 1
    AND a.carga_horaria >= COALESCE(
    (SELECT SUM(qhh.hora_final - qhh.hora_inicial)
      FROM pmieducar.quadro_horario_horarios qhh
      WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
      AND qhh.ref_cod_escola = '$int_ref_cod_escola'
      AND hora_inicial >= '08:00'
      AND hora_inicial <= '12:00'
      AND qhh.ativo = '1'
      AND qhh.dia_semana <> '$int_dia_semana'
      AND qhh.ref_servidor = a.ref_cod_servidor
      GROUP BY qhh.ref_servidor) ,'00:00')  + '$str_hr_mat' + COALESCE(
      (SELECT SUM( qhha.hora_final - qhha.hora_inicial )
        FROM pmieducar.quadro_horario_horarios_aux qhha
        WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
        AND qhha.ref_cod_escola = $int_ref_cod_escola
        AND hora_inicial >= '08:00'
        AND hora_inicial <= '12:00'
        AND qhha.ref_servidor = a.ref_cod_servidor
        AND identificador = '$int_identificador'
        GROUP BY qhha.ref_servidor),'00:00'))";
        }
        else {
          $filtros .= "
    {$whereAnd} s.cod_servidor NOT IN (SELECT a.ref_cod_servidor
      FROM pmieducar.servidor_alocacao a
      WHERE $where
      AND a.periodo = 1)";
        }
      }

      if ($vespertino) {
        if (is_string($str_horario) && $str_horario == "S") {
            $filtros .= "
    {$whereAnd} s.cod_servidor IN
      (SELECT a.ref_cod_servidor
        FROM pmieducar.servidor_alocacao a
        WHERE $where
        AND a.periodo = 2
        AND a.carga_horaria >= COALESCE(
          (SELECT SUM( qhh.hora_final - qhh.hora_inicial )
          FROM pmieducar.quadro_horario_horarios qhh
          WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
          AND qhh.ref_cod_escola = '$int_ref_cod_escola'
          AND qhh.ativo = '1'
          AND hora_inicial >= '12:00'
          AND hora_inicial <= '18:00'
          AND qhh.dia_semana <> '$int_dia_semana'
          AND qhh.ref_servidor = a.ref_cod_servidor
          GROUP BY qhh.ref_servidor ),'00:00') + '$str_hr_ves' +  COALESCE(
          (SELECT SUM( qhha.hora_final - qhha.hora_inicial )
            FROM pmieducar.quadro_horario_horarios_aux qhha
            WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
            AND qhha.ref_cod_escola = '$int_ref_cod_escola'
            AND qhha.ref_servidor = a.ref_cod_servidor
            AND hora_inicial >= '12:00'
            AND hora_inicial <= '18:00'
            AND identificador = '$int_identificador'
            GROUP BY qhha.ref_servidor),'00:00') )";
        }
        else {
          $filtros .= "
    {$whereAnd} s.cod_servidor NOT IN ( SELECT a.ref_cod_servidor
      FROM pmieducar.servidor_alocacao a
      WHERE $where
      AND a.periodo = 2 )";
        }
      }

      if ($noturno) {
        if (is_string($str_horario) && $str_horario == "S") {
          $filtros .= "
    {$whereAnd} s.cod_servidor IN ( SELECT a.ref_cod_servidor
      FROM pmieducar.servidor_alocacao a
      WHERE $where
      AND a.periodo = 3
      AND a.carga_horaria >= COALESCE(
      (SELECT SUM(qhh.hora_final - qhh.hora_inicial)
        FROM pmieducar.quadro_horario_horarios qhh
        WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
        AND qhh.ref_cod_escola = '$int_ref_cod_escola'
        AND qhh.ativo = '1'
        AND hora_inicial >= '18:00'
        AND hora_inicial <= '23:00'
        AND qhh.dia_semana <> '$int_dia_semana'
        GROUP BY qhh.ref_servidor ),'00:00')  + '$str_hr_not' +  COALESCE(
          (SELECT SUM( qhha.hora_final - qhha.hora_inicial )
          FROM pmieducar.quadro_horario_horarios_aux qhha
          WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
          AND qhha.ref_cod_escola = '$int_ref_cod_escola'
          AND qhha.ref_servidor = a.ref_cod_servidor
          AND hora_inicial >= '18:00'
          AND hora_inicial <= '23:00'
          AND identificador = '$int_identificador'
          GROUP BY qhha.ref_servidor),'00:00') )";
        }
        else {
          $filtros .= "
    {$whereAnd} s.cod_servidor NOT IN (
    SELECT a.ref_cod_servidor
      FROM pmieducar.servidor_alocacao a
      WHERE $where
      AND a.periodo = 3 )";
        }
      }

      if (is_string($str_horario) && $str_horario == "S") {
      }
      else {
        $filtros .= "
    {$whereAnd} s.carga_horaria >= COALESCE(
      (SELECT sum(hora_final - hora_inicial) + '" . abs($horas) . ":" . abs($minutos)."'
        FROM pmieducar.servidor_alocacao sa
        WHERE sa.ref_cod_servidor = s.cod_servidor
        AND sa.ref_ref_cod_instituicao ='{$int_ref_cod_instituicao}'),'00:00') ";
      }
    }

    if ((is_array($array_horario) && $str_not_in_servidor) || (is_string($str_tipo) && $str_not_in_servidor)) {
      $filtros .= "{$whereAnd} s.cod_servidor NOT IN ( {$str_not_in_servidor} )";
      $whereAnd = " AND ";
    }

    $obj_curso = new clsPmieducarCurso($int_ref_cod_curso);
    $det_curso = $obj_curso->detalhe();

    if ($det_curso['falta_ch_globalizada']) {
      // Busca professores independentemente da disciplina, somente verifica
      // se é professor e se ministra a disciplina para o curso
      $filtros .= "
    {$whereAnd} EXISTS
      (SELECT 1
        FROM pmieducar.servidor_curso_ministra scm
        WHERE scm.ref_cod_curso = $int_ref_cod_curso AND
        scm.ref_ref_cod_instituicao = s.ref_cod_instituicao AND
        s.cod_servidor = scm.ref_cod_servidor)";
      $whereAnd = " AND ";
    }
    else {
      // Verifica se o professor está habilitado para ministrar a disciplina
      if (is_numeric($int_ref_cod_disciplina)) {
        $filtros .= "
    {$whereAnd} EXISTS
      (SELECT 1
        FROM pmieducar.servidor_disciplina sd
        WHERE sd.ref_cod_disciplina = $int_ref_cod_disciplina AND
        sd.ref_ref_cod_instituicao = s.ref_cod_instituicao AND
        s.cod_servidor = sd.ref_cod_servidor)";
      $whereAnd = " AND ";
      }
    }

    // Seleciona apenas servidor cuja uma de suas funções seja a de professor
    if ($boo_professor) {
      $filtros .= "
    {$whereAnd} EXISTS
      (SELECT 1
        FROM pmieducar.servidor_funcao sf, pmieducar.funcao f
        WHERE f.cod_funcao = sf.ref_cod_funcao AND
        f.professor = 1 AND
        sf.ref_ref_cod_instituicao = s.ref_cod_instituicao AND
        s.cod_servidor = sf.ref_cod_servidor)";
      $whereAnd = " AND ";
    }

    if (is_string($str_horario) && $str_horario == "S") {
      $filtros .= "
    {$whereAnd} s.cod_servidor NOT IN
      (SELECT DISTINCT qhh.ref_servidor
        FROM pmieducar.quadro_horario_horarios qhh
        WHERE qhh.ref_servidor = s.cod_servidor
        AND qhh.ref_cod_instituicao_servidor = s.ref_cod_instituicao
        AND qhh.dia_semana = '{$array_horario[0]}'
        AND qhh.hora_inicial >= '{$array_horario[1]}'
        AND qhh.hora_final <= '{$array_horario[2]}'
        AND qhh.ativo = '1'";

      if (is_string($lst_matriculas)) {
        $filtros .= "AND qhh.ref_servidor NOT IN ({$lst_matriculas})";
      }

      $filtros .= " )";

      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_subnivel)) {
      $filtros .= "{$whereAnd} s.ref_cod_subnivel = '{$int_ref_cod_subnivel}'";
      $whereAnd = " AND ";
    }

    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $db = new clsBanco();

    $sql = "SELECT {$this->_campos_lista2} FROM {$this->_schema}servidor s{$tabela_compl} {$filtros}" .
      $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_schema}servidor s{$tabela_compl} {$filtros}");

    // Executa a query
    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }



	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
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
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
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
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}



  /**
   * Retorna array com as funções do servidor
   *
   * Exemplo de array de retorno:
   * <code>
   * array(
   *   '2' => array(
   *     'cod_funcao' => 2,
   *     'nm_funcao' => 'Professor',
   *     'professor' => 1
   *   )
   * );
   * <code>
   *
   * @since   Método disponível desde a versão 1.0.2
   * @return  array  Array associativo com a primeira chave sendo o código da
   *   função. O array interno contém o nome da função e se a função desempenha
   *   um papel de professor
   */
  public function getServidorFuncoes() {
    $db = new clsBanco();

    $sql  = 'SELECT t2.cod_funcao, t2.nm_funcao, t2.professor FROM pmieducar.servidor_funcao AS t1, pmieducar.funcao AS t2 ';
    $sql .= 'WHERE t1.ref_cod_servidor = \'%d\' AND t1.ref_ref_cod_instituicao = \'%d\' ';
    $sql .= 'AND t1.ref_cod_funcao = t2.cod_funcao';

    $sql = sprintf($sql, $this->cod_servidor, $this->ref_cod_instituicao);
    $db->Consulta($sql);

    $funcoes = array();

    while ($db->ProximoRegistro() != FALSE) {
      $row = $db->Tupla();
      $funcoes[$row['cod_funcao']] = array(
        'cod_funcao' => $row['cod_funcao'],
        'nm_funcao'  => $row['nm_funcao'],
        'professor'  => $row['professor'],
      );
    }

    return $funcoes;
  }



  /**
   * Retorna um array com as disciplinas alocadas ao servidor no quadro de
   * horários
   *
   * @since   Método disponível desde a versão 1.0.2
   * @param   int  $codServidor     Código do servidor, caso não seja informado,
   *   usa o código disponível no objeto atual
   * @param   int  $codInstituicao  Código da instituição, caso não seja
   *   informado, usa o código disponível no objeto atual
   * @return  array|bool  Array com códigos das disciplinas ordenados ou FALSE
   *   caso o servidor não tenha disciplinas
   */
  public function getServidorDisciplinasQuadroHorarioHorarios($codServidor = NULL,
    $codInstituicao = NULL) {

    $codServidor = $codServidor != NULL ? $codServidor : $this->cod_servidor;
    $codInstituicao = $codInstituicao != NULL ? $codInstituicao : $this->ref_cod_instituicao;

    $sql  = 'SELECT DISTINCT(qhh.ref_cod_disciplina) AS ref_cod_disciplina ';
    $sql .= 'FROM pmieducar.quadro_horario_horarios qhh, pmieducar.servidor s ';
    $sql .= 'WHERE qhh.ref_servidor = s.cod_servidor AND ';
    $sql .= 'qhh.ref_servidor = \'%d\' AND qhh.ref_cod_instituicao_servidor = \'%d\'';

    $sql = sprintf($sql, $codServidor, $codInstituicao);

    $db = new clsBanco();
    $db->Consulta($sql);

    $disciplinas = array();

    while ($db->ProximoRegistro() != FALSE) {
      $row = $db->Tupla();
      $disciplinas[] = $row['ref_cod_disciplina'];
    }

    if (count($disciplinas)) {
      return asort($disciplinas);
    }

    return FALSE;
  }



  /**
   * Retorna um array com os códigos das disciplinas do servidor
   *
   * @since   Método disponível desde a versão 1.0.2
   * @param   int  $codServidor     Código do servidor, caso não seja informado,
   *   usa o código disponível no objeto atual
   * @param   int  $codInstituicao  Código da instituição, caso não seja
   *   informado, usa o código disponível no objeto atual
   * @return  array|bool  Array com códigos das disciplinas ordenados ou FALSE
   *   caso o servidor não tenha disciplinas
   */
  public function getServidorDisciplinas($codServidor = NULL,
    $codInstituicao = NULL) {

    $codServidor = $codServidor != NULL ? $codServidor : $this->cod_servidor;
    $codInstituicao = $codInstituicao != NULL ? $codInstituicao : $this->ref_cod_instituicao;

    // Se códigos não forem fornecidos, nem pela classe nem pelo código cliente,
    // retorna FALSE
    if ($codServidor == NULL || $codInstituicao == NULL) {
      return FALSE;
    }

    $sql  = 'SELECT DISTINCT(sd.ref_cod_disciplina) AS ref_cod_disciplina ';
    $sql .= 'FROM pmieducar.servidor_disciplina sd, pmieducar.servidor s ';
    $sql .= 'WHERE sd.ref_cod_servidor = s.cod_servidor AND ';
    $sql .= 'sd.ref_cod_servidor = \'%d\' AND sd.ref_ref_cod_instituicao = \'%d\'';

    $sql = sprintf($sql, $codServidor, $codInstituicao);

    $db = new clsBanco();
    $db->Consulta($sql);

    $disciplinas = array();

    while ($db->ProximoRegistro() != FALSE) {
      $row = $db->Tupla();
      $disciplinas[] = $row['ref_cod_disciplina'];
    }

    if (count($disciplinas)) {
      return asort($disciplinas);
    }

    return FALSE;
  }

  /**
   * Verifica se um servidor desempenha a função de professor.
   *
   * Primeiro, recuperamos todas as funções do servidor e procuramos
   * por um dos itens que tenha o índice professor igual a 1.
   *
   * @since   Método disponível desde a versão 1.0.2
   * @return  bool  TRUE caso o servidor desempenhe a função de professor
   */
  public function isProfessor() {
    $funcoes = $this->getServidorFuncoes();

    foreach ($funcoes as $funcao) {
      if (1 == $funcao['professor']) {
        return TRUE;
        break;
      }
    }

    return FALSE;
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

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function qtdhoras( $int_cod_servidor, $int_cod_escola, $int_ref_cod_instituicao, $dia_semana )
	{
		$db = new clsBanco();
		$db->Consulta( "SELECT EXTRACT( HOUR FROM ( SUM( hora_final - hora_inicial ) ) ) AS hora,
						       EXTRACT( MINUTE FROM ( SUM( hora_final - hora_inicial ) ) ) AS min
						  FROM pmieducar.servidor_alocacao
						 WHERE ref_cod_servidor        = {$int_cod_servidor}
						   AND ref_cod_escola	       = {$int_cod_escola}
						   AND ref_ref_cod_instituicao = {$int_ref_cod_instituicao}
						   AND dia_semana              = {$dia_semana}" );
		$db->ProximoRegistro();
		return $db->Tupla();
	}

}