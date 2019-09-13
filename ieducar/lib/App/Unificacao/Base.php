<?php

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
    protected $transacao;

    public function __construct($codigoUnificador, $codigosDuplicados, $codPessoaLogada, clsBanco $db, $transacao = true)
    {
        $this->codigoUnificador = $codigoUnificador;
        $this->codigosDuplicados = $codigosDuplicados;
        $this->codPessoaLogada = $codPessoaLogada;
        $this->db = $db;
        $this->transacao = $transacao;
    }

    public function unifica()
    {
        $this->validaParametros();

        $this->desabilitaTodasTriggers();
        $this->habilitaTriggersNecessarias();
        $this->processaChavesDeletarDuplicados();
        $this->processaChavesManterPrimeiroVinculo();
        $this->processaChavesManterTodosVinculos();
        $this->habilitaTodasTriggers();
    }

    protected function processaChavesDeletarDuplicados()
    {
        $stringCodigosDuplicados = implode(',', $this->codigosDuplicados);

        foreach ($this->chavesDeletarDuplicados as $key => $value) {
            try {
                $this->db->Consulta(
                    "
                        UPDATE {$value['tabela']}
                        SET {$value['coluna']} = {$this->codigoUnificador}
                        WHERE {$value['coluna']} IN ({$stringCodigosDuplicados})
                    "
                );
            } catch (Exception $e) {
                $this->db->Consulta(
                    "
                        DELETE FROM {$value['tabela']}
                        WHERE {$value['coluna']} IN ({$stringCodigosDuplicados})
                    "
                );
            }
        }
    }

    protected function processaChavesManterTodosVinculos()
    {
        $stringCodigosDuplicados = implode(',', $this->codigosDuplicados);

        foreach ($this->chavesManterTodosVinculos as $key => $value) {
            $this->db->Consulta(
                "
                    UPDATE {$value['tabela']}
                    SET {$value['coluna']} = {$this->codigoUnificador}
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
}
