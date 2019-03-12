<?php

class clsModulesComponenteCurricularAnoEscolar
{
    public $componente_curricular_id;
    public $ano_escolar_id;
    public $carga_horaria;
    public $tipo_nota;
    public $componentes;
    public $updateInfo;

    // propriedades padrao
    public $_total; // Armazena o total de resultados obtidos na ultima chamada ao metodo lista
    public $_schema; // Nome do schema
    public $_tabela; // Nome da tabela
    public $_campos_lista; // Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
    public $_todos_campos; // Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
    public $_limite_quantidade; // Valor que define a quantidade de registros a ser retornada pelo metodo lista
    public $_limite_offset; // Define o valor de offset no retorno dos registros no metodo lista
    public $_campo_order_by; // Define o campo padrao para ser usado como padrao de ordenacao no metodo lista

    public function __construct(
        $componente_curricular_id = null,
        $ano_escolar_id = null,
        $carga_horaria = null,
        $tipo_nota = null,
        $componentes = null,
        $updateInfo = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}componente_curricular_ano_escolar";

        $campos = [
            'componente_curricular_id',
            'ano_escolar_id',
            'carga_horaria',
            'array_to_json(anos_letivos) as anos_letivos',
        ];

        $this->_campos_lista = join(', ', $campos) . ' ';
        $this->_todos_campos = $this->_campos_lista;

        if (is_numeric($componente_curricular_id)) {
            $this->componente_curricular_id = $componente_curricular_id;
        }

        if (is_numeric($ano_escolar_id)) {
            $this->ano_escolar_id = $ano_escolar_id;
        }

        if (is_numeric($carga_horaria)) {
            $this->$carga_horaria = $carga_horaria;
        }

        if (is_numeric($tipo_nota)) {
            $this->$tipo_nota = $tipo_nota;
        }

        if (is_array($componentes)) {
            $this->componentes = $componentes;
        }

        if (is_array($updateInfo)) {
            $this->updateInfo = $updateInfo;
        }
    }

    public function atualizaComponentesDaSerie()
    {
        $this->updateInfo();

        if ($this->updateInfo['update']) {
            foreach ($this->updateInfo['update'] as $componenteUpdate) {
                $this->editaComponente(
                    intval($componenteUpdate['id']),
                    intval($componenteUpdate['carga_horaria']),
                    intval($componenteUpdate['tipo_nota']),
                    $componenteUpdate['anos_letivos']
                );
            }
        }

        if ($this->updateInfo['insert']) {
            foreach ($this->updateInfo['insert'] as $componenteInsert) {
                $this->cadastraComponente(
                    intval($componenteInsert['id']),
                    intval($componenteInsert['carga_horaria']),
                    intval($componenteInsert['tipo_nota']),
                    $componenteInsert['anos_letivos']
                );
            }
        }

        if ($this->updateInfo['delete']) {
            foreach ($this->updateInfo['delete'] as $componenteDelete) {
                $this->excluiComponente(intval($componenteDelete));
            }
        }

        return true;
    }

    public function updateInfo()
    {
        $c = $u = $i = $d = 0;

        foreach ($this->componentes as $componente) {
            $componentesArray[$c] = $componente['id'];
            $c++;

            if (in_array($componente['id'], $this->getComponentesSerie())) {
                $anosLetivosDiff = $this->getAnosLetivosDiff($componente['id'], $componente['anos_letivos']);

                $this->updateInfo['update'][$u]['id'] = $componente['id'];
                $this->updateInfo['update'][$u]['carga_horaria'] = $componente['carga_horaria'];
                $this->updateInfo['update'][$u]['tipo_nota'] = $componente['tipo_nota'];
                $this->updateInfo['update'][$u]['anos_letivos'] = $componente['anos_letivos'];
                $this->updateInfo['update'][$u]['anos_letivos_inseridos'] = $anosLetivosDiff['inseridos'];
                $this->updateInfo['update'][$u]['anos_letivos_removidos'] = $anosLetivosDiff['removidos'];
                $u++;
            } else {
                $this->updateInfo['insert'][$i]['id'] = $componente['id'];
                $this->updateInfo['insert'][$i]['carga_horaria'] = $componente['carga_horaria'];
                $this->updateInfo['insert'][$i]['tipo_nota'] = $componente['tipo_nota'];
                $this->updateInfo['insert'][$i]['anos_letivos'] = $componente['anos_letivos'];
                $i++;
            }
        }

        foreach ($this->getComponentesSerie() as $componente) {
            if (!in_array($componente, $componentesArray)) {
                $this->updateInfo['delete'][$d] = $componente;
                $d++;
            }
        }

        return $this->updateInfo;
    }

    public function getComponentesSerie()
    {
        $sql = "
            SELECT componente_curricular_id
            FROM {$this->_tabela}
            WHERE ano_escolar_id = {$this->ano_escolar_id}
        ";

        $db = new clsBanco();
        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $componentesSerie[] = $tupla['componente_curricular_id'];
        }

        if ($componentesSerie) {
            return $componentesSerie;
        }

        return false;
    }

    private function getAnosLetivosDiff($componenteCurricularId, $arrayAnosLetivos)
    {
        $retorno = [
            'inseridos' => [],
            'removidos' => []
        ];

        $sql = <<<SQL
            SELECT array_to_json(anos_letivos) as anos_letivos
            FROM {$this->_tabela}
            WHERE ano_escolar_id = {$this->ano_escolar_id}
            AND componente_curricular_id = {$componenteCurricularId}
SQL;

        $db = new clsBanco();
        $db->Consulta($sql);
        $db->ProximoRegistro();

        $resultado = $db->Tupla();

        if (empty($resultado) || !isset($resultado['anos_letivos'])) {
            return $retorno;
        }

        $anosLetivosExistentes = json_decode($resultado['anos_letivos'], true);

        foreach (array_diff($arrayAnosLetivos, $anosLetivosExistentes) as $ano) {
            $retorno['inseridos'][] = $ano;
        }

        foreach (array_diff($anosLetivosExistentes, $arrayAnosLetivos) as $ano) {
            $retorno['removidos'][] = $ano;
        }

        return $retorno;
    }

    private function cadastraComponente(
        $componente_curricular_id = null,
        $carga_horaria = null,
        $tipo_nota = null,
        $anosLetivos = null
    ) {
        if (is_numeric($componente_curricular_id) && is_numeric($carga_horaria)) {
            $db = new clsBanco();
            $tipo_nota = (int) $tipo_nota;
            $tipo_nota = $tipo_nota === 0 ? 'NULL' : $tipo_nota;
            $anosLetivosFormatados = Portabilis_Utils_Database::arrayToPgArray($anosLetivos);

            $sql = "
                INSERT INTO
                    {$this->_tabela}
                VALUES(
                    $componente_curricular_id,
                    $this->ano_escolar_id,
                    $carga_horaria,
                    $tipo_nota,
                    $anosLetivosFormatados
            )";

            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    private function editaComponente(
        $componente_curricular_id = null,
        $carga_horaria = null,
        $tipo_nota = null,
        $anosLetivos = null
    ) {
        $db = new clsBanco();
        $set = '';
        $gruda = '';

        if (is_numeric($componente_curricular_id)) {
            if (is_numeric($carga_horaria)) {
                $set .= "{$gruda}carga_horaria = {$carga_horaria}";
                $gruda = ', ';
            }

            if (is_numeric($tipo_nota)) {
                $tipo_nota = (int) $tipo_nota;
                $tipo_nota = $tipo_nota === 0 ? 'NULL' : $tipo_nota;

                $set .= "{$gruda}tipo_nota = {$tipo_nota}";
                $gruda = ', ';
            }

            if (is_array($anosLetivos)) {
                $set .= "{$gruda}anos_letivos = " . Portabilis_Utils_Database::arrayToPgArray($anosLetivos) . ' ';
                $gruda = ', ';
            }

            if ($set) {
                $sql = "
                    UPDATE {$this->_tabela}
                    SET $set
                    WHERE componente_curricular_id = {$componente_curricular_id}
                    AND ano_escolar_id = {$this->ano_escolar_id}
                ";

                $db->Consulta($sql);

                return true;
            }
        }

        return false;
    }

    private function excluiComponente($componente_curricular_id = null)
    {
        if (is_numeric($componente_curricular_id)) {
            $db = new clsBanco();

            $sql = "
                DELETE FROM {$this->_tabela}
                WHERE componente_curricular_id = {$componente_curricular_id}
                AND ano_escolar_id = {$this->ano_escolar_id}
            ";

            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    public function cadastra()
    {
        if (is_numeric($this->componente_curricular_id) && is_numeric($this->ano_escolar_id)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ano_escolar_id)) {
                $campos .= "{$gruda}ano_escolar_id";
                $valores .= "{$gruda}'{$this->ano_escolar_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->componente_curricular_id)) {
                $campos .= "{$gruda}componente_curricular_id";
                $valores .= "{$gruda}'{$this->componente_curricular_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->carga_horaria)) {
                $campos .= "{$gruda}carga_horaria";
                $valores .= "{$gruda}'{$this->carga_horaria}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_nota) && (int)$tipo_nota !== 0) {
                $campos .= "{$gruda}tipo_nota";
                $valores .= "{$gruda}'{$this->tipo_nota}'";
                $gruda = ', ';
            }

            $sql = "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )";

            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    public function exclui()
    {
        if (is_numeric($this->ano_escolar_id)) {
            $db = new clsBanco();

            $sql = "
                DELETE FROM {$this->_tabela}
                WHERE ano_escolar_id = {$this->ano_escolar_id}
            ";

            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    // Retorna uma lista filtrados de acordo com os parametros
    public function lista(
        $componente_curricular_id = null,
        $ano_escolar_id = null,
        $carga_horaria = null,
        $tipo_nota = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';
        $whereAnd = ' WHERE ';

        if (is_numeric($componente_curricular_id)) {
            $filtros .= "{$whereAnd} componente_curricular_id = {$componente_curricular_id}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($ano_escolar_id)) {
            $filtros .= "{$whereAnd} ano_escolar_id = {$ano_escolar_id}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($carga_horaria)) {
            $filtros .= "{$whereAnd} carga_horaria = {$carga_horaria}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($tipo_nota)) {
            $filtros .= "{$whereAnd} tipo_$tipo_nota = {$tipo_nota}";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
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

    // Define quais campos da tabela serao selecionados na invocacao do metodo lista
    public function setCamposLista($str_campos)
    {
        $this->_campos_lista = $str_campos;
    }

    // Define que o metodo Lista devera retornoar todos os campos da tabela
    public function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    // Define limites de retorno para o metodo lista
    public function setLimite($intLimiteQtd, $intLimiteOffset = null)
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    // Retorna a string com o trecho da query resposavel pelo Limite de registros
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

    // Define campo para ser utilizado como ordenacao no metolo lista
    public function setOrderby($strNomeCampo)
    {
        // limpa a string de possiveis erros (delete, insert, etc)
        // $strNomeCampo = eregi_replace();

        if (is_string($strNomeCampo) && $strNomeCampo) {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    // Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
    public function getOrderby()
    {
        if (is_string($this->_campo_order_by)) {
            return " ORDER BY {$this->_campo_order_by} ";
        }

        return '';
    }
}
