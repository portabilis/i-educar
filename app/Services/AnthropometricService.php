<?php

namespace App\Services;

use DateTime;

/**
 * Serviço antropométrico WHO 2007
 * - IMC e classificação (OMS)
 * - Z-score por idade/sexo via LMS (OMS 2007)
 * - Circunferência de cintura (adultos: pontos de corte fixos; pediatria: ≥P90)
 * - Retorno com código e rótulo (PT-BR)
 *
 * REQUER dados WHO 2007 carregados no banco de dados via:
 * - php artisan migrate (criar tabelas)
 * - php artisan anthropometric:load (carregar dados XLSX)
 * - $service->carregarReferenciasDoBanco() (usar dados)
 */
class AnthropometricService
{
    /** Dados LMS e P90 carregados do banco de dados */
    private array $lms = [];

    private array $waistP90 = [];

    /** Flag simples para saber se referências externas foram carregadas/injetadas */
    private bool $refsCarregadas = false;

    /** Cache de dados carregados do banco */
    private array $lmsCache = [];

    private array $waistCache = [];

    private string $currentSource = 'WHO_2007';

    private bool $usingDatabaseData = false;

    /**
     * Carrega referências do banco de dados (método preferencial).
     * Usa cache para melhor performance.
     */
    public function carregarReferenciasDoBanco(string $source = 'WHO_2007'): bool
    {
        try {
            $this->currentSource = $source;

            $hasLmsData = false;
            $hasWaistData = false;

            // Carregar dados Z-scores do banco
            if (class_exists('\App\Models\AnthropometricZScore')) {
                $this->lmsCache = \App\Models\AnthropometricZScore::getAllGroupedForCache($source);
                if (!empty($this->lmsCache)) {
                    $this->lms = $this->lmsCache;
                    $hasLmsData = true;
                }
            }

            // Carregar dados de cintura P90 da tabela de percentis
            if (class_exists('\App\Models\AnthropometricPercentile')) {
                $this->waistCache = \App\Models\AnthropometricPercentile::getAllP90ForWaistCache($source);
                if (!empty($this->waistCache)) {
                    $this->waistP90 = $this->waistCache;
                    $hasWaistData = true;
                }
            }

            // Só marcar como carregado se realmente veio do banco
            $this->usingDatabaseData = $hasLmsData || $hasWaistData;
            $this->refsCarregadas = $this->usingDatabaseData;

            return $this->usingDatabaseData;

        } catch (\Exception $e) {
            // Log do erro mas não falha - usa dados de fallback
            if (function_exists('logger')) {
                logger()->warning('Falha ao carregar dados antropométricos do banco', [
                    'source' => $source,
                    'error' => $e->getMessage(),
                ]);
            }
            $this->usingDatabaseData = false;

            return false;
        }
    }

    /**
     * Verifica se está usando dados do banco de dados (não fallback).
     */
    public function isUsingDatabaseData(): bool
    {
        return $this->usingDatabaseData;
    }

    /** Retorna LMS interpolado para idade/sexo ou null */
    private function obterLMS(int $idadeMeses, string $sexo): ?array
    {
        $sexo = strtoupper($sexo);
        if (!in_array($sexo, ['M', 'F'], true)) {
            return null;
        }

        // Usar dados carregados do banco - se não há dados, retorna null
        if (isset($this->lms[$idadeMeses][$sexo])) {
            return $this->lms[$idadeMeses][$sexo];
        }

        // Interpolação entre idades disponíveis
        $idades = array_keys($this->lms);
        if (empty($idades)) {
            return null;
        }

        sort($idades);
        $menor = null;
        $maior = null;
        foreach ($idades as $i) {
            if ($i < $idadeMeses) {
                $menor = $i;
            } elseif ($i > $idadeMeses) {
                $maior = $i;
                break;
            }
        }

        if ($menor === null && $maior === null) {
            return null;
        }
        if ($menor === null) {
            return $this->lms[$maior][$sexo] ?? null;
        }
        if ($maior === null) {
            return $this->lms[$menor][$sexo] ?? null;
        }

        $a = $this->lms[$menor][$sexo] ?? null;
        $b = $this->lms[$maior][$sexo] ?? null;
        if (!$a || !$b) {
            return null;
        }

        $t = ($idadeMeses - $menor) / ($maior - $menor);

        return [
            'L' => $a['L'] + ($b['L'] - $a['L']) * $t,
            'M' => $a['M'] + ($b['M'] - $a['M']) * $t,
            'S' => $a['S'] + ($b['S'] - $a['S']) * $t,
        ];
    }

    /** Retorna P90 interpolado para idade/sexo a partir dos dados do banco */
    private function getP90(int $idadeAnos, string $sexo): ?float
    {
        $sexo = strtoupper($sexo);
        if (!in_array($sexo, ['M', 'F'], true)) {
            return null;
        }

        // Usar dados P90 carregados do banco - se não há dados, retorna null
        if (isset($this->waistP90[$idadeAnos][$sexo])) {
            return (float) $this->waistP90[$idadeAnos][$sexo];
        }

        // Interpolação entre idades disponíveis
        $idades = array_keys($this->waistP90);
        if (empty($idades)) {
            return null;
        }

        sort($idades);
        $menor = null;
        $maior = null;
        foreach ($idades as $i) {
            if ($i < $idadeAnos) {
                $menor = $i;
            } elseif ($i > $idadeAnos) {
                $maior = $i;
                break;
            }
        }

        if ($menor === null && $maior === null) {
            return null;
        }
        if ($menor === null) {
            return isset($this->waistP90[$maior][$sexo]) ? (float) $this->waistP90[$maior][$sexo] : null;
        }
        if ($maior === null) {
            return isset($this->waistP90[$menor][$sexo]) ? (float) $this->waistP90[$menor][$sexo] : null;
        }

        if (!isset($this->waistP90[$menor][$sexo]) || !isset($this->waistP90[$maior][$sexo])) {
            return null;
        }

        $a = (float) $this->waistP90[$menor][$sexo];
        $b = (float) $this->waistP90[$maior][$sexo];
        $t = ($idadeAnos - $menor) / ($maior - $menor);

        return $a + ($b - $a) * $t;
    }

    /** Calcula IMC (peso em kg, altura em cm) */
    public function calcularIMC(float $peso, float $altura): ?float
    {
        if ($peso <= 0 || $altura <= 0) {
            return null;
        }
        if ($peso < 5 || $peso > 300) {
            return null;
        }
        if ($altura < 50 || $altura > 250) {
            return null;
        }

        $m = $altura / 100.0;

        return round($peso / ($m * $m), 2);
    }

    /** Classificação do IMC em adultos (OMS) */
    public function obterClassificacaoIMCAdulto(float $imc): array
    {
        if ($imc < 18.5) {
            return ['code' => 'underweight', 'label' => 'Baixo peso'];
        }
        if ($imc < 25) {
            return ['code' => 'normal',      'label' => 'Peso normal'];
        }
        if ($imc < 30) {
            return ['code' => 'overweight',  'label' => 'Sobrepeso'];
        }
        if ($imc < 35) {
            return ['code' => 'obesity_class_1', 'label' => 'Obesidade grau I'];
        }
        if ($imc < 40) {
            return ['code' => 'obesity_class_2', 'label' => 'Obesidade grau II'];
        }

        return ['code' => 'obesity_class_3', 'label' => 'Obesidade grau III (grave)'];
    }

    /**
     * Classificação do IMC para crianças/adolescentes (5–19 anos) via Z-score OMS 2007
     */
    public function obterClassificacaoIMCCrianca(float $imc, int $idadeEmMeses, string $sexo): array
    {
        // Só calcular classificação de criança se estiver usando dados do banco
        if (!$this->usingDatabaseData) {
            return ['code' => 'no_reference_data', 'label' => 'Dados de referência não disponíveis'];
        }

        $sexo = $this->normalizarSexo($sexo);
        if ($sexo === null) {
            return ['code' => 'not_evaluable', 'label' => 'Não avaliável'];
        }

        $z = $this->calcularEscoreZIMC($imc, $idadeEmMeses, $sexo);
        if ($z === null) {
            return ['code' => 'not_evaluable', 'label' => 'Não avaliável'];
        }

        if ($z < -3) {
            return ['code' => 'severe_thinness',   'label' => 'Magreza acentuada', 'z' => $z];
        }
        if ($z < -2) {
            return ['code' => 'thinness',          'label' => 'Magreza',           'z' => $z];
        }
        if ($z <= 1) {
            return ['code' => 'normal',            'label' => 'Eutrofia',          'z' => $z];
        }
        if ($z <= 2) {
            return ['code' => 'overweight_risk',   'label' => 'Risco de sobrepeso', 'z' => $z];
        }
        if ($z <= 3) {
            return ['code' => 'obesity',           'label' => 'Obesidade',         'z' => $z];
        }

        return ['code' => 'severe_obesity', 'label' => 'Obesidade grave', 'z' => $z];
    }

    /**
     * Z-score do IMC (OMS 2007) usando parâmetros LMS
     * Fórmula: Z = ((IMC/M)^L - 1) / (L*S)
     */
    public function calcularEscoreZIMC(float $imc, int $idadeEmMeses, string $sexo): ?float
    {
        $sexo = $this->normalizarSexo($sexo);
        if ($sexo === null) {
            return null;
        }

        $lms = $this->obterLMS($idadeEmMeses, $sexo);
        if ($lms === null) {
            return null;
        }

        [$L, $M, $S] = [$lms['L'], $lms['M'], $lms['S']];
        if ($M <= 0 || $S <= 0) {
            return null;
        }

        $z = ($L == 0.0)
            ? (log($imc / $M) / $S)                               // caso limite quando L≈0
            : ((pow(($imc / $M), $L) - 1.0) / ($L * $S));

        return round($z, 2);
    }

    /**
     * Classificação de risco por circunferência da cintura
     * - Adultos: pontos de corte (OMS/IDF)
     * - Pediatria: ≥P90 (tabela de referência)
     */
    public function obterClassificacaoRiscoCircunferenciaCintura(float $circCinturaCm, string $sexo, ?int $idadeAnos = null): array
    {
        $sexo = $this->normalizarSexo($sexo);
        if ($sexo === null) {
            return ['code' => 'not_evaluable', 'label' => 'Não avaliável'];
        }

        if ($idadeAnos === null || $idadeAnos >= 18) {
            return $this->obterRiscoCinturaAdulto($circCinturaCm, $sexo);
        }

        return $this->obterRiscoCinturaCrianca($circCinturaCm, $sexo, $idadeAnos);
    }

    private function obterRiscoCinturaAdulto(float $circCinturaCm, string $sexo): array
    {
        if ($sexo === 'M') {
            if ($circCinturaCm < 94) {
                return ['code' => 'no_risk',  'label' => 'Sem risco'];
            }
            if ($circCinturaCm < 102) {
                return ['code' => 'increased', 'label' => 'Risco aumentado'];
            }

            return ['code' => 'high', 'label' => 'Risco muito aumentado'];
        }
        // sexo F
        if ($circCinturaCm < 80) {
            return ['code' => 'no_risk',  'label' => 'Sem risco'];
        }
        if ($circCinturaCm < 88) {
            return ['code' => 'increased', 'label' => 'Risco aumentado'];
        }

        return ['code' => 'high', 'label' => 'Risco muito aumentado'];
    }

    private function obterRiscoCinturaCrianca(float $circCinturaCm, string $sexo, int $idadeAnos): array
    {
        // Só calcular risco de criança se estiver usando dados do banco
        if (!$this->usingDatabaseData) {
            return ['code' => 'no_reference_data', 'label' => 'Dados de referência não disponíveis'];
        }

        $p90 = $this->getP90($idadeAnos, $sexo);
        if ($p90 === null) {
            return ['code' => 'no_reference_data', 'label' => 'Dados de referência não disponíveis'];
        }

        if ($circCinturaCm >= $p90) {
            return ['code' => 'increased', 'label' => 'Risco aumentado (≥ P90)', 'p90' => $p90];
        }

        return ['code' => 'no_risk', 'label' => 'Sem risco (< P90)', 'p90' => $p90];
    }

    /** Normaliza sexo para 'M' ou 'F' */
    private function normalizarSexo(string $sexo): ?string
    {
        $s = strtoupper(trim($sexo));
        if (in_array($s, ['M', 'F'], true)) {
            return $s;
        }
        if (in_array($s, ['MALE', 'MASCULINO'], true)) {
            return 'M';
        }
        if (in_array($s, ['FEMALE', 'FEMININO'], true)) {
            return 'F';
        }

        return null;
    }

    /** Idade em meses (inteiros, como a WHO usa) */
    public function calcularIdadeEmMeses(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        $dn = $dataNascimento instanceof DateTime ? clone $dataNascimento : new DateTime($dataNascimento);
        $ref = $dataReferencia ? ($dataReferencia instanceof DateTime ? clone $dataReferencia : new DateTime($dataReferencia)) : new DateTime;
        $i = $dn->diff($ref);

        return ($i->y * 12) + $i->m;
    }

    /** Idade em anos (inteiros) */
    public function calcularIdadeEmAnos(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        $dn = $dataNascimento instanceof DateTime ? clone $dataNascimento : new DateTime($dataNascimento);
        $ref = $dataReferencia ? ($dataReferencia instanceof DateTime ? clone $dataReferencia : new DateTime($dataReferencia)) : new DateTime;

        return $dn->diff($ref)->y;
    }

    /** Avaliação completa — estrutura consolidada */
    public function obterAvaliacaoCompleta(
        float $peso,
        float $altura,
        ?float $circunferenciaCintura,
        string|DateTime $dataNascimento,
        string $sexo,
        string|DateTime|null $dataAvaliacao = null
    ): array {
        $imc = $this->calcularIMC($peso, $altura);
        $idadeAnos = $this->calcularIdadeEmAnos($dataNascimento, $dataAvaliacao);
        $idadeMeses = $this->calcularIdadeEmMeses($dataNascimento, $dataAvaliacao);
        $sexoNorm = $this->normalizarSexo($sexo);

        $out = [
            'peso_kg' => $peso,
            'altura_cm' => $altura,
            'imc' => $imc,
            'idade_anos' => $idadeAnos,
            'idade_meses' => $idadeMeses,
            'sexo' => $sexoNorm ?? $sexo,
        ];

        if ($imc !== null && $sexoNorm !== null) {
            if ($idadeAnos >= 18) {
                $out['imc_classificacao'] = $this->obterClassificacaoIMCAdulto($imc);
            } else {
                $out['imc_classificacao'] = $this->obterClassificacaoIMCCrianca($imc, $idadeMeses, $sexoNorm);
                $out['imc_zscore'] = $out['imc_classificacao']['z'] ?? $this->calcularEscoreZIMC($imc, $idadeMeses, $sexoNorm);
            }
        } else {
            $out['imc_classificacao'] = ['code' => 'not_evaluable', 'label' => 'Não avaliável'];
        }

        if ($circunferenciaCintura !== null) {
            $out['cintura_cm'] = $circunferenciaCintura;
            $out['cintura_classificacao'] = $this->obterClassificacaoRiscoCircunferenciaCintura(
                $circunferenciaCintura,
                $sexoNorm ?? $sexo,
                $idadeAnos
            );
        }

        return $out;
    }
}
