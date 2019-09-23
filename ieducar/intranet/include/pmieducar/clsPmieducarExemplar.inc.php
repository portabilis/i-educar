<?php

use iEducar\Legacy\Model;
use Illuminate\Support\Facades\Session;

require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsPmieducarExemplar extends Model
{
    public $cod_exemplar;
    public $ref_cod_fonte;
    public $ref_cod_motivo_baixa;
    public $ref_cod_acervo;
    public $ref_cod_situacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $permite_emprestimo;
    public $preco;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $data_aquisicao;
    public $tombo;
    public $sequencial;
    public $data_baixa_exemplar;
    public $pessoa_logada;
    public $codUsuario;

    public function __construct($cod_exemplar = null, $ref_cod_fonte = null, $ref_cod_motivo_baixa = null, $ref_cod_acervo = null, $ref_cod_situacao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $permite_emprestimo = null, $preco = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $data_aquisicao = null, $tombo = null, $sequencial = null, $data_baixa_exemplar = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}exemplar";

        $this->pessoa_logada = Session::get('id_pessoa');

        $this->_campos_lista = $this->_todos_campos = 'e.cod_exemplar, e.ref_cod_fonte, e.ref_cod_motivo_baixa, e.ref_cod_acervo, e.ref_cod_situacao, e.ref_usuario_exc, e.ref_usuario_cad, e.permite_emprestimo, e.preco, e.data_cadastro, e.data_exclusao, e.ativo, e.data_aquisicao, e.tombo, e.sequencial, e.data_baixa_exemplar';

        if (is_numeric($ref_cod_fonte)) {
                    $this->ref_cod_fonte = $ref_cod_fonte;
        }
        if (is_numeric($ref_cod_motivo_baixa)) {
                    $this->ref_cod_motivo_baixa = $ref_cod_motivo_baixa;
        }
        if (is_numeric($ref_cod_acervo)) {
                    $this->ref_cod_acervo = $ref_cod_acervo;
        }
        if (is_numeric($ref_cod_situacao)) {
                    $this->ref_cod_situacao = $ref_cod_situacao;
        }
        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_exemplar)) {
            $this->cod_exemplar = $cod_exemplar;
        }
        if (is_numeric($permite_emprestimo)) {
            $this->permite_emprestimo = $permite_emprestimo;
        }
        if (is_numeric($preco)) {
            $this->preco = $preco;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
        if (is_string($data_aquisicao)) {
            $this->data_aquisicao = $data_aquisicao;
        }
        if (is_numeric($tombo)) {
            $this->tombo = $tombo;
        }
        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_string($data_baixa_exemplar)) {
            $this->data_baixa_exemplar = $data_baixa_exemplar;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (!is_numeric($this->preco)) {
            $this->preco = 0.00;
        }

        if (is_numeric($this->ref_cod_fonte) && is_numeric($this->ref_cod_acervo) && is_numeric($this->ref_cod_situacao) && is_numeric($this->ref_usuario_cad) && is_numeric($this->permite_emprestimo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_fonte)) {
                $campos .= "{$gruda}ref_cod_fonte";
                $valores .= "{$gruda}'{$this->ref_cod_fonte}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_motivo_baixa)) {
                $campos .= "{$gruda}ref_cod_motivo_baixa";
                $valores .= "{$gruda}'{$this->ref_cod_motivo_baixa}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_acervo)) {
                $campos .= "{$gruda}ref_cod_acervo";
                $valores .= "{$gruda}'{$this->ref_cod_acervo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_situacao)) {
                $campos .= "{$gruda}ref_cod_situacao";
                $valores .= "{$gruda}'{$this->ref_cod_situacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->permite_emprestimo)) {
                $campos .= "{$gruda}permite_emprestimo";
                $valores .= "{$gruda}'{$this->permite_emprestimo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->preco)) {
                $campos .= "{$gruda}preco";
                $valores .= "{$gruda}'{$this->preco}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tombo)) {
                $campos .= "{$gruda}tombo";
                $valores .= "{$gruda}'{$this->tombo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->sequencial)) {
                $campos .= "{$gruda}sequencial";
                $valores .= "{$gruda}'{$this->sequencial}'";
                $gruda = ', ';
            }
            if (is_string($this->data_baixa_exemplar)) {
                $campos .= "{$gruda}data_baixa_exemplar";
                $valores .= "{$gruda}'{$this->data_baixa_exemplar}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_string($this->data_aquisicao)) {
                $campos .= "{$gruda}data_aquisicao";
                $valores .= "{$gruda}'{$this->data_aquisicao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
            $this->cod_exemplar = $db->InsertId("{$this->_tabela}_cod_exemplar_seq");
            if ($this->cod_exemplar) {
                $detalhe = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('exemplar', $this->pessoa_logada, $this->cod_exemplar);
                $auditoria->inclusao($detalhe);
            }

            return $this->cod_exemplar;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_exemplar) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_fonte)) {
                $set .= "{$gruda}ref_cod_fonte = '{$this->ref_cod_fonte}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_motivo_baixa)) {
                $set .= "{$gruda}ref_cod_motivo_baixa = '{$this->ref_cod_motivo_baixa}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_acervo)) {
                $set .= "{$gruda}ref_cod_acervo = '{$this->ref_cod_acervo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_situacao)) {
                $set .= "{$gruda}ref_cod_situacao = '{$this->ref_cod_situacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->permite_emprestimo)) {
                $set .= "{$gruda}permite_emprestimo = '{$this->permite_emprestimo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->preco)) {
                $set .= "{$gruda}preco = '{$this->preco}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_string($this->data_aquisicao)) {
                $set .= "{$gruda}data_aquisicao = '{$this->data_aquisicao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tombo)) {
                $set .= "{$gruda}tombo = '{$this->tombo}'";
                $gruda = ', ';
            }
            if (is_string($this->data_baixa_exemplar)) {
                $set .= "{$gruda}data_baixa_exemplar = '{$this->data_baixa_exemplar}'";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_exemplar = '{$this->cod_exemplar}'");
                $detalheAtual = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('exemplar', $this->pessoa_logada, $this->cod_exemplar);
                $auditoria->alteracao($detalheAntigo, $detalheAtual);

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
    public function lista($int_cod_exemplar = null, $int_ref_cod_fonte = null, $int_ref_cod_motivo_baixa = null, $int_ref_cod_acervo = null, $int_ref_cod_situacao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_permite_emprestimo = null, $int_preco = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $date_data_aquisicao_ini = null, $date_data_aquisicao_fim = null, $int_ref_exemplar_tipo = null, $str_titulo_livro = null, $int_ref_cod_biblioteca = null, $str_titulo = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $int_tombo = null)
    {
        $sql = "SELECT {$this->_campos_lista}, a.ref_cod_biblioteca, a.titulo FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b";

        $whereAnd = ' AND';
        $filtros = ' WHERE e.ref_cod_acervo = a.cod_acervo AND a.ref_cod_biblioteca = b.cod_biblioteca ';

        if (is_numeric($int_cod_exemplar)) {
            $filtros .= "{$whereAnd} e.cod_exemplar = '{$int_cod_exemplar}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_fonte)) {
            $filtros .= "{$whereAnd} e.ref_cod_fonte = '{$int_ref_cod_fonte}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_motivo_baixa)) {
            $filtros .= "{$whereAnd} e.ref_cod_motivo_baixa = '{$int_ref_cod_motivo_baixa}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_acervo)) {
            $filtros .= "{$whereAnd} e.ref_cod_acervo = '{$int_ref_cod_acervo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_situacao)) {
            $filtros .= "{$whereAnd} e.ref_cod_situacao = '{$int_ref_cod_situacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} e.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} e.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_permite_emprestimo)) {
            $filtros .= "{$whereAnd} e.permite_emprestimo = '{$int_permite_emprestimo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_preco)) {
            $filtros .= "{$whereAnd} e.preco = '{$int_preco}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} e.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} e.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} e.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} e.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} e.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} e.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_aquisicao_ini)) {
            $filtros .= "{$whereAnd} e.data_aquisicao >= '{$date_data_aquisicao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_aquisicao_fim)) {
            $filtros .= "{$whereAnd} e.data_aquisicao <= '{$date_data_aquisicao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_titulo)) {
            $filtros .= "{$whereAnd} a.titulo LIKE '%{$str_titulo}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_tombo)) {
            $filtros .= "{$whereAnd} e.tombo = {$int_tombo}";
            $whereAnd = ' AND ';
        }

        /**
         * INICIO  - PESQUISAS EXTRAS
         */
        $whereAnd2 = ' AND ';
        $filtros_extra = null;

        if (is_string($str_titulo_livro)) {
            $filtros_extra .= "{$whereAnd2} (a.titulo) ilike ('%{$date_data_aquisicao_fim}%') ";
            $whereAnd2 = ' AND ';
        }

        if (is_numeric($int_ref_exemplar_tipo)) {
            $filtros_extra .= "{$whereAnd} a.ref_cod_exemplar_tipo = $int_ref_exemplar_tipo";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros_extra .= "{$whereAnd} a.ref_cod_biblioteca = $int_ref_cod_biblioteca";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros_extra .= "{$whereAnd} b.ref_cod_instituicao = $int_ref_cod_instituicao";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros_extra .= "{$whereAnd} b.ref_cod_escola = $int_ref_cod_escola";
            $whereAnd = ' AND ';
        }

        if ($filtros_extra) {
            $filtros .= "{$whereAnd} exists (SELECT 1 FROM pmieducar.acervo a where a.cod_acervo = e.ref_cod_acervo {$filtros_extra} )";
        }
        /**
         * FIM  - PESQUISAS EXTRAS
         */

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b {$filtros}");

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

    public function retorna_tombo_maximo($bibliotecaId, $exceptExemplarId = null)
    {
        if (empty($bibliotecaId)) {
            throw new Exception('Deve ser enviado um argumento \'$bibliotecaId\' ao método \'retorna_tombo_maximo\'');
        }

        // sem esta regra ao editar o ultimo exemplar sem informar o tombo, seria pego o proprio tombo.
        if (!empty($exceptExemplarId)) {
            $exceptExemplar = " and exemplar.cod_exemplar !=  $exceptExemplarId";
        } else {
            $exceptExemplar = '';
        }

        $sql = "SELECT MAX(tombo) as tombo_max FROM pmieducar.exemplar, pmieducar.acervo WHERE exemplar.ativo = 1 and exemplar.ref_cod_acervo = acervo.cod_acervo and acervo.ref_cod_biblioteca = $bibliotecaId $exceptExemplar";

        $db = new clsBanco();

        return $db->CampoUnico($sql);
    }

    /**
     * Verifica se o tombo a ser cadastrado já não foi cadastrado
     *
     * @return boolean
     */
    public function retorna_tombo_valido($bibliotecaId, $exceptExemplarId = null, $tombo = null)
    {
        if (empty($bibliotecaId)) {
            throw new Exception('Deve ser enviado um argumento \'$bibliotecaId\' ao método \'retorna_tombo_maximo\'');
        }
        if (empty($tombo)) {
            return true;
        }
        // Sem essa regra ao editar e salvar com o mesmo tombo retornaria falso
        if (!empty($exceptExemplarId)) {
            $exceptExemplar = " and exemplar.cod_exemplar !=  $exceptExemplarId";
        } else {
            $exceptExemplar = '';
        }

        $sql = "SELECT tombo FROM pmieducar.exemplar, pmieducar.acervo WHERE exemplar.ativo = 1 and exemplar.ref_cod_acervo =           acervo.cod_acervo and tombo = $tombo and acervo.ref_cod_biblioteca = $bibliotecaId $exceptExemplar";

        $db = new clsBanco();
        $consulta = $db->CampoUnico($sql);
        if ($consulta == $tombo) {
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
    public function lista_com_acervos($int_cod_exemplar = null, $int_ref_cod_fonte = null, $int_ref_cod_motivo_baixa = null, $int_ref_cod_acervo = null, $int_ref_cod_situacao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_permite_emprestimo = null, $int_preco = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $date_data_aquisicao_ini = null, $date_data_aquisicao_fim = null, $int_ref_exemplar_tipo = null, $str_titulo_livro = null, $int_ref_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $int_ref_cod_acervo_colecao = null, $int_ref_cod_acervo_editora = null, $tombo)
    {
        $sql = "SELECT {$this->_campos_lista}, a.ref_cod_biblioteca, a.titulo FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b";

        $whereAnd = ' AND';
        $filtros = ' WHERE e.ref_cod_acervo = a.cod_acervo AND a.ref_cod_biblioteca = b.cod_biblioteca ';

        if (is_numeric($int_cod_exemplar)) {
            $filtros .= "{$whereAnd} e.cod_exemplar = '{$int_cod_exemplar}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_fonte)) {
            $filtros .= "{$whereAnd} e.ref_cod_fonte = '{$int_ref_cod_fonte}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_motivo_baixa)) {
            $filtros .= "{$whereAnd} e.ref_cod_motivo_baixa = '{$int_ref_cod_motivo_baixa}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_acervo)) {
            $filtros .= "{$whereAnd} e.ref_cod_acervo = '{$int_ref_cod_acervo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_situacao)) {
            $filtros .= "{$whereAnd} e.ref_cod_situacao = '{$int_ref_cod_situacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} e.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} e.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_permite_emprestimo)) {
            $filtros .= "{$whereAnd} e.permite_emprestimo = '{$int_permite_emprestimo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_preco)) {
            $filtros .= "{$whereAnd} e.preco = '{$int_preco}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} e.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} e.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} e.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} e.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} e.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} e.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_aquisicao_ini)) {
            $filtros .= "{$whereAnd} e.data_aquisicao >= '{$date_data_aquisicao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_aquisicao_fim)) {
            $filtros .= "{$whereAnd} e.data_aquisicao <= '{$date_data_aquisicao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_titulo_livro)) {
            $filtros .= "{$whereAnd} (a.titulo) LIKE ('%{$str_titulo_livro}%')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($tombo)) {
            $filtros .= "{$whereAnd} e.tombo = $tombo";
            $whereAnd = ' AND ';
        }

        /**
         * INICIO  - PESQUISAS EXTRAS
         */
        $whereAnd2 = ' AND ';
        $filtros_extra = null;

        if (is_numeric($int_ref_exemplar_tipo)) {
            $filtros_extra .= "{$whereAnd} a.ref_cod_exemplar_tipo = $int_ref_exemplar_tipo";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros_extra .= "{$whereAnd} a.ref_cod_biblioteca = $int_ref_cod_biblioteca";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros_extra .= "{$whereAnd} b.ref_cod_instituicao = $int_ref_cod_instituicao";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros_extra .= "{$whereAnd} b.ref_cod_escola = $int_ref_cod_escola";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_usuario
                                              WHERE escola_usuario.ref_cod_escola = b.ref_cod_escola
                                                AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_acervo_colecao)) {
            $filtros_extra .= "{$whereAnd} a.ref_cod_acervo_colecao = {$int_ref_cod_acervo_colecao}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_acervo_editora)) {
            $filtros_extra .= "{$whereAnd} a.ref_cod_acervo_editora = {$int_ref_cod_acervo_editora}";
            $whereAnd = ' AND ';
        }

        if ($filtros_extra) {
            $filtros .= "{$whereAnd} exists (SELECT 1 FROM pmieducar.acervo a where a.cod_acervo = e.ref_cod_acervo {$filtros_extra} )";
        }
        /**
         * FIM  - PESQUISAS EXTRAS
         */

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} e, {$this->_schema}acervo a, {$this->_schema}biblioteca b {$filtros}");

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
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_exemplar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} e WHERE e.cod_exemplar = '{$this->cod_exemplar}'");
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
    public function existe()
    {
        if (is_numeric($this->cod_exemplar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_exemplar = '{$this->cod_exemplar}'");
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
    public function excluir()
    {
        if (is_numeric($this->cod_exemplar) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    public function getProximoSequencialObra($codigoObra)
    {
        $sql = "SELECT MAX(sequencial) AS sequencial
                  FROM pmieducar.exemplar
                 WHERE exemplar.ref_cod_acervo = $codigoObra";

        $db = new clsBanco();
        $ultimoSequencial = $db->CampoUnico($sql);

        return $ultimoSequencial + 1;
    }
}
