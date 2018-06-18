<?php

/**
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
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Portal
 *
 * @since     Arquivo disponível desde a versão 1.0.0
 *
 * @version   $Id$
 */

/**
 * clsPortalSmsProntuario class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Portal
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class clsPortalSmsProntuario
{
    public $cod_prontuario;
    public $ref_pessoa_exc;
    public $ref_pessoa_cad;
    public $num_prontuario;
    public $nm_paciente;
    public $data_nascimento;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    /**
     * Armazena o total de resultados obtidos na última chamada ao método lista().
     *
     * @var int
     */
    public $_total;

    /**
     * Nome do schema.
     *
     * @var string
     */
    public $_schema;

    /**
     * Nome da tabela.
     *
     * @var string
     */
    public $_tabela;

    /**
     * Lista separada por vírgula, com os campos que devem ser selecionados na
     * próxima chamado ao método lista().
     *
     * @var string
     */
    public $_campos_lista;

    /**
     * Lista com todos os campos da tabela separados por vírgula, padrão para
     * seleção no método lista.
     *
     * @var string
     */
    public $_todos_campos;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo método lista().
     *
     * @var int
     */
    public $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no método lista().
     *
     * @var int
     */
    public $_limite_offset;

    /**
     * Define o campo para ser usado como padrão de ordenação no método lista().
     *
     * @var string
     */
    public $_campo_order_by;

    /**
     * Construtor.
     */
    public function __construct(
      $cod_prontuario = null,
      $ref_pessoa_exc = null,
    $ref_pessoa_cad = null,
      $num_prontuario = null,
      $nm_paciente = null,
    $data_nascimento = null,
      $data_cadastro = null,
      $data_exclusao = null,
      $ativo = null
  ) {
        $db = new clsBanco();
        $this->_schema = 'portal.';
        $this->_tabela = "{$this->_schema}sms_prontuario";

        $this->_campos_lista = $this->_todos_campos = 'cod_prontuario, ref_pessoa_exc, ref_pessoa_cad, num_prontuario, nm_paciente, data_nascimento, data_cadastro, data_exclusao, ativo';

        if (is_numeric($cod_prontuario)) {
            $this->cod_prontuario = $cod_prontuario;
        }

        if (is_numeric($ref_pessoa_exc)) {
            $tmp_obj = new clsFuncionario($ref_pessoa_exc);
            if ($tmp_obj->detalhe()) {
                $this->ref_pessoa_exc = $ref_pessoa_exc;
            }
        }

        if (is_numeric($ref_pessoa_cad)) {
            $tmp_obj = new clsFuncionario($ref_pessoa_cad);
            if ($tmp_obj->detalhe()) {
                $this->ref_pessoa_cad = $ref_pessoa_cad;
            }
        }

        if (is_numeric($num_prontuario)) {
            $this->num_prontuario = $num_prontuario;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }

        if (is_string($nm_paciente)) {
            $this->nm_paciente = $nm_paciente;
        }

        if (is_string($data_nascimento)) {
            $this->data_nascimento = $data_nascimento;
        }

        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }

        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_pessoa_cad) && is_numeric($this->num_prontuario) &&
      is_numeric($this->ativo) && is_string($this->nm_paciente)) {
            $db = new clsBanco();

            $campos  = '';
            $valores = '';
            $gruda   = '';

            if (is_numeric($this->cod_prontuario)) {
                $campos .= "{$gruda}cod_prontuario";
                $valores .= "{$gruda}'{$this->cod_prontuario}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_pessoa_exc)) {
                $campos .= "{$gruda}ref_pessoa_exc";
                $valores .= "{$gruda}'{$this->ref_pessoa_exc}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_pessoa_cad)) {
                $campos .= "{$gruda}ref_pessoa_cad";
                $valores .= "{$gruda}'{$this->ref_pessoa_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->num_prontuario)) {
                $campos .= "{$gruda}num_prontuario";
                $valores .= "{$gruda}'{$this->num_prontuario}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_paciente)) {
                $campos .= "{$gruda}nm_paciente";
                $valores .= "{$gruda}'{$this->nm_paciente}'";
                $gruda = ', ';
            }

            if (is_string($this->data_nascimento)) {
                $campos .= "{$gruda}data_nascimento";
                $valores .= "{$gruda}'{$this->data_nascimento}'";
                $gruda = ', ';
            }

            $campos  .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda    = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_prontuario_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_prontuario)) {
            $db  = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_pessoa_exc)) {
                $set .= "{$gruda}ref_pessoa_exc = '{$this->ref_pessoa_exc}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_pessoa_cad)) {
                $set .= "{$gruda}ref_pessoa_cad = '{$this->ref_pessoa_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->num_prontuario)) {
                $set .= "{$gruda}num_prontuario = '{$this->num_prontuario}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_paciente)) {
                $set .= "{$gruda}nm_paciente = '{$this->nm_paciente}'";
                $gruda = ', ';
            }

            if (is_string($this->data_nascimento)) {
                $set .= "{$gruda}data_nascimento = '{$this->data_nascimento}'";
                $gruda = ', ';
            }

            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_prontuario = '{$this->cod_prontuario}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
      $int_cod_prontuario = null,
      $int_ref_pessoa_exc = null,
    $int_ref_pessoa_cad = null,
      $int_num_prontuario = null,
      $int_ativo = null,
    $str_nm_paciente = null,
      $str_data_nascimento = null,
    $str_data_cadastro_inicio = null,
      $str_data_cadastro_fim = null,
    $str_data_exclusao_inicio = null,
      $str_data_exclusao_fim = null
  ) {
        $db = new clsBanco();
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_prontuario)) {
            $filtros .= "{$whereAnd} cod_prontuario = '{$int_cod_prontuario}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_pessoa_exc)) {
            $filtros .= "{$whereAnd} ref_pessoa_exc = '{$int_ref_pessoa_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_pessoa_cad)) {
            $filtros .= "{$whereAnd} ref_pessoa_cad = '{$int_ref_pessoa_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_num_prontuario)) {
            $filtros .= "{$whereAnd} num_prontuario = '{$int_num_prontuario}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} ativo = '{$int_ativo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_paciente)) {
            $filtros .= "{$whereAnd} (nm_paciente) ILIKE  ('%{$str_nm_paciente}%')";
            $whereAnd = ' AND ';
        }

        if (is_string($str_data_nascimento)) {
            $filtros .= "{$whereAnd} data_nascimento = '{$str_data_nascimento}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_data_cadastro_inicio)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$str_data_cadastro_inicio}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$str_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_data_exclusao_inicio)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$str_data_exclusao_inicio}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$str_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");
        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_prontuario)) {
            if ($this->ativo != false) {
                $where = ' and ativo = 1 ';
            }

            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_prontuario = '{$this->cod_prontuario}' {$where}");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_prontuario)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Define quais campos da tabela serão selecionados no método Lista().
     */
    public function setCamposLista($str_campos)
    {
        $this->_campos_lista = $str_campos;
    }

    /**
     * Define que o método Lista() deverpa retornar todos os campos da tabela.
     */
    public function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    /**
     * Define limites de retorno para o método Lista().
     */
    public function setLimite($intLimiteQtd, $intLimiteOffset = null)
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    /**
     * Retorna a string com o trecho da query responsável pelo limite de
     * registros retornados/afetados.
     *
     * @return string
     */
    public function getLimite()
    {
        if (is_numeric($this->_limite_quantidade)) {
            $retorno = " LIMIT {$this->_limite_quantidade}";
            if (is_numeric($this->_limite_offset)) {
                $retorno .= " OFFSET {$this->_limite_offset} ";
            }

            return $retorno;
        }

        return '';
    }

    /**
     * Define o campo para ser utilizado como ordenação no método Lista().
     */
    public function setOrderby($strNomeCampo)
    {
        if (is_string($strNomeCampo) && $strNomeCampo) {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    /**
     * Retorna a string com o trecho da query responsável pela Ordenação dos
     * registros.
     *
     * @return string
     */
    public function getOrderby()
    {
        if (is_string($this->_campo_order_by)) {
            return " ORDER BY {$this->_campo_order_by} ";
        }

        return '';
    }
}
