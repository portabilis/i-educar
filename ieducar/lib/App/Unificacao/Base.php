<?php

use App\Services\UnificationService;
use Illuminate\Support\Facades\DB;

class App_Unificacao_Base
{
    protected $chavesManterPrimeiroVinculo = [];
    protected $chavesManterTodosVinculos = [];
    protected $chavesDeletarDuplicados = [];
    protected $triggersNecessarias = [];
    protected $codigoUnificador;
    protected $codigosDuplicados;
    protected $codPessoaLogada;
    protected $db;
    protected $unificationId;

    /**
     * @var UnificationService
     */
    protected $unificationService;

    public function __construct($codigoUnificador, $codigosDuplicados, $codPessoaLogada, clsBanco $db, $unificationId)
    {
        $this->codigoUnificador = $codigoUnificador;
        $this->codigosDuplicados = $codigosDuplicados;
        $this->codPessoaLogada = $codPessoaLogada;
        $this->db = $db;
        $this->unificationId = $unificationId;
        $this->unificationService = new UnificationService();
    }

    public function unifica()
    {
        try {
            $this->validaParametros();
            $this->desabilitaTodasTriggers();
            $this->habilitaTriggersNecessarias();
            $this->processaChavesDeletarDuplicados();
            $this->processaChavesManterPrimeiroVinculo();
            $this->processaChavesManterTodosVinculos();
            $this->habilitaTodasTriggers();
        } catch (CoreExt_Exception $e) {
            throw new CoreExt_Exception('Não foi possível realizar este processo de unificação. Por favor, entre em contato com o suporte. '.$e->getMessage());
        }
    }

    protected function processaChavesDeletarDuplicados()
    {
        $stringCodigosDuplicados = implode(',', $this->codigosDuplicados);

        foreach ($this->chavesDeletarDuplicados as $key => $value) {
            $oldKeys = explode(',', $stringCodigosDuplicados);
            $this->storeLogOldDataByKeys($oldKeys, $value['tabela'], $value['coluna']);
            try {
                $this->db->Consulta("SELECT 1 FROM {$value['tabela']} WHERE {$value['coluna']} IN ({$stringCodigosDuplicados})");

                if ($this->db->ProximoRegistro()) {
                    $this->db->Consulta(
                        "
                        DELETE FROM {$value['tabela']}
                        WHERE {$value['coluna']} IN ({$stringCodigosDuplicados})
                    "
                    );
                } else {
                    $this->db->Consulta(
                        "
                        UPDATE {$value['tabela']}
                        SET {$value['coluna']} = {$this->codigoUnificador}
                        WHERE {$value['coluna']} IN ({$stringCodigosDuplicados})
                    "
                    );
                }
            } catch (Exception) {
                throw new Exception('Erro ao deletar registros duplicados. Por favor, entre em contato com suporte.');
            }
        }
    }

    protected function processaChavesManterTodosVinculos()
    {
        $stringCodigosDuplicados = implode(',', $this->codigosDuplicados);

        foreach ($this->chavesManterTodosVinculos as $key => $value) {
            $oldKeys = explode(',', $stringCodigosDuplicados);
            $this->storeLogOldDataByKeys($oldKeys, $value['tabela'], $value['coluna']);
            $addSql = $this->buildSqlExtraBeforeUnification($value['tabela']);

            $this->db->Consulta(
                "
                    UPDATE {$value['tabela']}
                    SET {$value['coluna']} = {$this->codigoUnificador}
                    {$addSql}
                    WHERE {$value['coluna']} IN ({$stringCodigosDuplicados})
                "
            );
        }
    }

    protected function processaChavesManterPrimeiroVinculo()
    {
        $chavesConsultar = $this->codigosDuplicados;
        $chavesConsultar[] = $this->codigoUnificador;
        $chavesConsultarString = implode(',', $chavesConsultar);

        foreach ($this->chavesManterPrimeiroVinculo as $key => $value) {
            $oldKeys = explode(',', $chavesConsultarString);
            $this->storeLogOldDataByKeys($oldKeys, $value['tabela'], $value['coluna']);

            $this->db->Consulta(
                "
                    DELETE FROM {$value['tabela']}
                    WHERE {$value['coluna']} <>
                    (
                        SELECT {$value['coluna']}
                        from {$value['tabela']}
                        WHERE {$value['coluna']} in ({$chavesConsultarString})
                        ORDER BY {$value['coluna']} = {$this->codigoUnificador} DESC
                        LIMIT 1
                    )
                    AND {$value['coluna']} in ({$chavesConsultarString})
                "
            );

            $this->db->Consulta(
                "
                    UPDATE {$value['tabela']}
                    SET {$value['coluna']} = {$this->codigoUnificador}
                    WHERE {$value['coluna']} IN ({$chavesConsultarString})
                "
            );
        }
    }

    protected function tabelasEnvolvidas()
    {
        $todasChaves = array_merge($this->chavesManterPrimeiroVinculo, $this->chavesManterTodosVinculos);
        $todasTabelas = [];

        foreach ($todasChaves as $key => $value) {
            $todasTabelas[$value['tabela']] = $value['tabela'];
        }

        return array_values($todasTabelas);
    }

    protected function habilitaTriggersNecessarias()
    {
        foreach ($this->triggersNecessarias as $triggerNecessaria) {
            $this->db->Consulta("ALTER TABLE {$triggerNecessaria['tabela']} ENABLE TRIGGER {$triggerNecessaria['trigger']}");
        }
    }

    protected function desabilitaTodasTriggers()
    {
        $tabelasEnvolvidas = $this->tabelasEnvolvidas();

        foreach ($tabelasEnvolvidas as $key => $tabela) {
            $this->db->Consulta("ALTER TABLE {$tabela} DISABLE TRIGGER ALL");
        }
    }

    protected function habilitaTodasTriggers()
    {
        $tabelasEnvolvidas = $this->tabelasEnvolvidas();

        foreach ($tabelasEnvolvidas as $key => $tabela) {
            $this->db->Consulta("ALTER TABLE {$tabela} ENABLE TRIGGER ALL");
        }
    }

    protected function validaParametros()
    {
        if ($this->codigoUnificador != (int) $this->codigoUnificador) {
            throw new CoreExt_Exception('Parâmetro 1 deve ser o código unificador');
        }

        if (!is_array($this->codigosDuplicados) || !count($this->codigosDuplicados)) {
            throw new CoreExt_Exception('Parâmetro 2 deve ser um array de códigos duplicados');
        }

        if ($this->codPessoaLogada != (int) $this->codPessoaLogada) {
            throw new CoreExt_Exception('Parâmetro 3 deve ser um inteiro');
        }
    }

    /**
     * Grava log das tabelas alteradas pela unificação
     *
     * @param $oldKeys
     * @param $table
     * @param $columnKey
     */
    private function storeLogOldDataByKeys($oldKeys, $table, $columnKey)
    {
        foreach ($oldKeys as $key) {
            $data = $this->getOldData($table, $columnKey, $key);

            if ($data->isEmpty()) {
                continue;
            }

            $this->unificationService->storeLogOldData(
                $this->unificationId,
                $table,
                [$columnKey => $key],
                $data->toArray()
            );
        }
    }

    /**
     * Retorna dados da tabela de acordo com a chave informada
     *
     * @param $table
     * @param $key
     * @param $value
     *
     * @return \Illuminate\Support\Collection
     */
    private function getOldData($table, $key, $value)
    {
        return DB::table($table)->whereIn($key, [$value])->get();
    }

    /**
     * @param $tableName string
     *
     * @return string
     */
    private function buildSqlExtraBeforeUnification(string $tableName)
    {
        $addSql = '';

        if ($tableName === 'pmieducar.servidor_afastamento') {
            $addSql .= ', sequencial = (
                select
                COALESCE(max(sequencial)+1,1)
                from pmieducar.servidor_afastamento
                where ref_cod_servidor = ' . $this->codigoUnificador . '
                ) ';
        }

        return $addSql;
    }
}
