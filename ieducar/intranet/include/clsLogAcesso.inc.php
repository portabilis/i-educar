<?php

class clsLogAcesso
{
    public $cod_acesso;
    public $ip_externo;
    public $ip_interno;
    public $data_hora;
    public $cod_pessoa;
    public $obs;
    public $sucesso;
    public $tabela;

    /**
     * Construtor
     *
     * @return Object:clsLogAcesso
     */
    public function __construct($cod_acesso=false, $ip_externo=false, $ip_interno=false, $cod_pessoa=false, $obs=false, $sucesso=null)
    {
        $this->cod_acesso = $cod_acesso;
        $this->ip_externo = $ip_externo;
        $this->ip_interno = $ip_interno;
        $this->cod_pessoa = $cod_pessoa;
        $this->obs = $obs;
        $this->sucesso = $sucesso;
        $this->tabela = 'portal.acesso';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->cod_pessoa)) {
            $db = new clsBanco();
            // verificacoes de campos obrigatorios para insercao
            $campos = '';
            $valores = '';
            if (is_string($this->obs)) {
                $campos .= ', obs';
                $valores .= ", '{$this->obs}'";
            }
            if (! is_null($this->sucesso)) {
                $campos .= ', sucesso';
                if ($this->sucesso) {
                    $valores .= ', \'t\'';
                } else {
                    $valores .= ', \'f\'';
                }
            }
            $db->Consulta("INSERT INTO {$this->tabela} ( ip_externo, ip_interno, data_hora, cod_pessoa $campos ) VALUES ( '{$this->ip_externo}', '{$this->ip_interno}', NOW(), '{$this->cod_pessoa}' $campos )");
            // define o ID do registro
            $this->cod_acesso = $db->InsertId("{$this->tabela}_cod_acesso_seq");

            return $this->cod_acesso;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_cod_pessoa=false, $str_ip_interno=false, $str_ip_externo=false, $date_inicio=false, $date_fim=false, $str_obs=false, $str_order_by='data_hora DESC', $int_limit_ini=0, $int_limit_qtd=20, $sucesso=null)
    {
        // verificacoes de filtros a serem usados
        $whereAnd = 'WHERE ';
        $where = '';
        if (is_array($int_cod_pessoa)) {
            foreach ($int_cod_pessoa as $cod) {
                if ($cod) {
                    $where .= "{$whereAnd}cod_pessoa = '$cod'";
                    $whereAnd = ' OR ';
                }
            }
        } elseif (is_numeric($int_cod_pessoa)) {
            $where .= "{$whereAnd}cod_pessoa = '$int_cod_pessoa'";
            $whereAnd = ' AND ';
        }

        if ($whereAnd == ' OR ') {
            $whereAnd = ' AND ';
        }
        if (is_string($str_ip_interno)) {
            $where .= "{$whereAnd}ip_interno = '$str_ip_interno'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_ip_externo)) {
            $where .= "{$whereAnd}ip_externo = '$str_ip_externo'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_obs)) {
            $where .= "{$whereAnd}obs = '$str_obs'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_fim)) {
            $where .= "{$whereAnd}data_cadastro <= '$date_fim'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_inicio)) {
            $where .= "{$whereAnd}data_cadastro >= '$date_inicio'";
            $whereAnd = ' AND ';
        }
        if (! is_null($sucesso)) {
            if ($sucesso) {
                $where .= "{$whereAnd}sucesso = 't'";
            } else {
                $where .= "{$whereAnd}sucesso = 'f'";
            }
            $whereAnd = ' AND ';
        }

        $limit = '';
        if (is_numeric($int_limit_ini) && is_numeric($int_limit_qtd)) {
            $limit = "LIMIT $int_limit_ini,$int_limit_qtd";
        }
        $orderBy = '';
        if (is_string($str_order_by)) {
            $orderBy = "ORDER BY $str_order_by";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');
        $db->Consulta("SELECT cod_acesso, ip_interno, ip_externo, data_hora, obs, cod_pessoa, sucesso FROM {$this->tabela} $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['total'] = $total;
            $resultado[] = $tupla;
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os detalhes do objeto
     *
     * @return Array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_acesso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT cod_acesso, ip_interno, ip_externo, data_hora, obs, cod_pessoa, sucesso FROM {$this->tabela} WHERE cod_acesso='{$this->cod_acesso}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }
}
