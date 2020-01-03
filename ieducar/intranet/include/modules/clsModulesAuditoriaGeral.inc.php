<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Utils/SafeJson.php';

class clsModulesAuditoriaGeral extends Model
{
    const OPERACAO_INCLUSAO = 1;
    const OPERACAO_ALTERACAO = 2;
    const OPERACAO_EXCLUSAO = 3;

    public $id;
    public $usuario_id;
    public $codigo;
    public $rotina;

    public function __construct($rotina, $usuario_id, $codigo = 'null', $id = null)
    {
        $this->_campos_lista = 'id,
                            codigo,
                            usuario_id,
                            operacao,
                            rotina,
                            valor_novo,
                            valor_antigo,
                            data_hora';
        $this->_tabela = 'modules.auditoria_geral';

        $this->rotina = $rotina;
        $this->usuario_id = $usuario_id;
        $this->codigo = $codigo;
        $this->id = $id;

        // Seta usuário admin quando não houver usuário pois pode ser API/Novo educação
        if (!$this->usuario_id) {
            $this->usuario_id = 1;
        }
    }

    public function removeKeyNaoNumerica($dados)
    {
        foreach ($dados as $key => $value) {
            if (is_int($key)) {
                unset($dados[$key]);
            }
        }

        return $dados;
    }

    public function removeKeysDesnecessarias($dados)
    {
        $keysDesnecessarias = ['ref_usuario_exc',
            'ref_usuario_cad',
            'data_cadastro',
            'data_exclusao'];
        foreach ($dados as $key => $value) {
            if (in_array($key, $keysDesnecessarias)) {
                unset($dados[$key]);
            }
        }

        return $dados;
    }

    public function converteArrayDadosParaJson($dados)
    {
        $dados = $this->removeKeyNaoNumerica($dados);
        $dados = $this->removeKeysDesnecessarias($dados);
        $dados = SafeJson::encode($dados);
        $dados = str_replace('\'', '\'\'', $dados);

        return $dados;
    }

    public function insereAuditoria($operacao, $valorAntigo, $valorNovo)
    {
        if (!$valorAntigo && !$valorNovo) {
            return;
        }

        if ($valorAntigo) {
            $valorAntigo = '\'' . $this->converteArrayDadosParaJson($valorAntigo) . '\'';
        } else {
            $valorAntigo = 'NULL';
        }

        if ($valorNovo) {
            $valorNovo = '\'' . $this->converteArrayDadosParaJson($valorNovo) . '\'';
        } else {
            $valorNovo = 'NULL';
        }

        $sql = "INSERT INTO modules.auditoria_geral (codigo,
                                                 usuario_id,
                                                 operacao,
                                                 rotina,
                                                 valor_antigo,
                                                 valor_novo,
                                                 data_hora)
                 VALUES ('{$this->codigo}',
                         {$this->usuario_id},
                         {$operacao},
                         '{$this->rotina}',
                         {$valorAntigo},
                         {$valorNovo},
                         NOW())";

        $db = new clsBanco();
        $db->Consulta($sql);
    }

    public function inclusao($dados)
    {
        $this->insereAuditoria(self::OPERACAO_INCLUSAO, null, $dados);
    }

    public function alteracao($valorAntigo, $valorNovo)
    {
        $this->insereAuditoria(self::OPERACAO_ALTERACAO, $valorAntigo, $valorNovo);
    }

    public function exclusao($dados)
    {
        $this->insereAuditoria(self::OPERACAO_EXCLUSAO, $dados, null);
    }

    public function lista($rotina, $usuario, $dataInicial, $dataFinal, $horaInicial, $horaFinal, $operacao, $codigo)
    {
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($this->id)) {
            $filtros .= "{$whereAnd} id = {$this->id}";
            $whereAnd = ' AND ';
        }

        if (is_string($rotina)) {
            $filtros .= "{$whereAnd} rotina ILIKE '%{$rotina}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($operacao)) {
            $filtros .= "{$whereAnd} operacao = {$operacao}";
            $whereAnd = ' AND ';
        }

        if (is_string($codigo)) {
            $filtros .= "{$whereAnd} codigo = '{$codigo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($usuario)) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM portal.funcionario
                                        WHERE funcionario.ref_cod_pessoa_fj = auditoria_geral.usuario_id
                                          AND funcionario.matricula = '{$usuario}')";
            $whereAnd = ' AND ';
        }

        if (is_string($dataInicial)) {
            $filtros .= "{$whereAnd} data_hora::date >= '{$dataInicial}'";
            $whereAnd = ' AND ';
        }

        if (is_string($dataFinal)) {
            $filtros .= "{$whereAnd} data_hora::date <= '{$dataFinal}'";
            $whereAnd = ' AND ';
        }

        if (is_string($horaInicial)) {
            $filtros .= "{$whereAnd} data_hora::time >= '{$horaInicial}'";
            $whereAnd = ' AND ';
        }

        if (is_string($horaFinal)) {
            $filtros .= "{$whereAnd} data_hora::time <= '{$horaFinal}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} ";
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
}
