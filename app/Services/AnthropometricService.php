<?php

namespace App\Services;

use DateTime;

/**
 * Serviço antropométrico
 * - IMC e classificação (OMS)
 * - Z-score por idade/sexo via LMS (OMS 2007) — usando referências internas
 * - Circunferência de cintura (adultos: pontos de corte fixos; pediatria: ≥P90)
 * - Retorno com código e rótulo (PT-BR) para facilitar i18n
 */
class AnthropometricService
{
    /**
     * //TODO: Referências internas (subconjuntos ilustrativos) — em produção, carregar uma tabela completa WHO 2007.
     * - LMS do IMC por idade (meses) e sexo
     * - P90 de circunferência da cintura por idade (anos) e sexo
     */
    private array $lms = [
        120 => ['M' => ['L' => -1.0, 'M' => 16.80, 'S' => 0.0800], 'F' => ['L' => -1.0, 'M' => 16.70, 'S' => 0.0850]],
        144 => ['M' => ['L' => -1.0, 'M' => 17.55, 'S' => 0.0801], 'F' => ['L' => -1.0, 'M' => 17.90, 'S' => 0.0820]],
        180 => ['M' => ['L' => -1.0, 'M' => 20.10, 'S' => 0.0900], 'F' => ['L' => -1.0, 'M' => 20.30, 'S' => 0.0950]],
    ];

    private array $waistP90 = [
        5 => ['M' => 56, 'F' => 55], 6 => ['M' => 58, 'F' => 57], 7 => ['M' => 60, 'F' => 59],
        8 => ['M' => 62, 'F' => 61], 9 => ['M' => 64, 'F' => 63], 10 => ['M' => 67, 'F' => 66],
        11 => ['M' => 70, 'F' => 69], 12 => ['M' => 73, 'F' => 72], 13 => ['M' => 76, 'F' => 75],
        14 => ['M' => 79, 'F' => 78], 15 => ['M' => 82, 'F' => 81], 16 => ['M' => 85, 'F' => 83], 17 => ['M' => 88, 'F' => 85],
    ];

    /** Retorna LMS interpolado para idade/sexo ou null */
    private function obterLMS(int $idadeMeses, string $sexo): ?array
    {
        $sexo = strtoupper($sexo);
        if (!in_array($sexo, ['M', 'F'], true)) {
            return null;
        }
        if (isset($this->lms[$idadeMeses][$sexo])) {
            return $this->lms[$idadeMeses][$sexo];
        }
        $idades = array_keys($this->lms);
        sort($idades);
        $menor = null;
        $maior = null;
        foreach ($idades as $i) {
            if ($i < $idadeMeses) {
                $menor = $i;
            } if ($i > $idadeMeses) {
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

        return ['L' => $a['L'] + ($b['L'] - $a['L']) * $t, 'M' => $a['M'] + ($b['M'] - $a['M']) * $t, 'S' => $a['S'] + ($b['S'] - $a['S']) * $t];
    }

    /** Retorna P90 interpolado para idade/sexo */
    private function getP90(int $idadeAnos, string $sexo): float
    {
        $sexo = strtoupper($sexo);
        if (!in_array($sexo, ['M', 'F'], true)) {
            return 90.0;
        }
        if (isset($this->waistP90[$idadeAnos][$sexo])) {
            return (float) $this->waistP90[$idadeAnos][$sexo];
        }
        $idades = array_keys($this->waistP90);
        sort($idades);
        $menor = null;
        $maior = null;
        foreach ($idades as $i) {
            if ($i < $idadeAnos) {
                $menor = $i;
            } if ($i > $idadeAnos) {
                $maior = $i;
                break;
            }
        }
        if ($menor === null && $maior === null) {
            return 90.0;
        }
        if ($menor === null) {
            return (float) $this->waistP90[$maior][$sexo];
        }
        if ($maior === null) {
            return (float) $this->waistP90[$menor][$sexo];
        }
        $a = (float) $this->waistP90[$menor][$sexo];
        $b = (float) $this->waistP90[$maior][$sexo];
        $t = ($idadeAnos - $menor) / ($maior - $menor);

        return $a + ($b - $a) * $t;
    }

    /**
     * Calcula IMC (peso em kg, altura em cm)
     */
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

    /**
     * Classificação do IMC em adultos (OMS) — retorna código e rótulo PT-BR
     */
    public function obterClassificacaoIMCAdulto(float $imc): array
    {
        if ($imc < 18.5) {
            return ['code' => 'underweight', 'label' => 'Baixo peso'];
        }
        if ($imc < 25) {
            return ['code' => 'normal', 'label' => 'Peso normal'];
        }
        if ($imc < 30) {
            return ['code' => 'overweight', 'label' => 'Sobrepeso'];
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
     * Retorna código e rótulo em PT-BR
     */
    public function obterClassificacaoIMCCrianca(float $imc, int $idadeEmMeses, string $sexo): array
    {
        $sexo = $this->normalizarSexo($sexo);
        if ($sexo === null) {
            return ['code' => 'not_evaluable', 'label' => 'Não avaliável'];
        }

        $z = $this->calcularEscoreZIMC($imc, $idadeEmMeses, $sexo);
        if ($z === null) {
            return ['code' => 'not_evaluable', 'label' => 'Não avaliável'];
        }

        if ($z < -3) {
            return ['code' => 'severe_thinness', 'label' => 'Magreza acentuada', 'z' => $z];
        }
        if ($z < -2) {
            return ['code' => 'thinness', 'label' => 'Magreza', 'z' => $z];
        }
        if ($z <= 1) {
            return ['code' => 'normal', 'label' => 'Eutrofia', 'z' => $z];
        }
        if ($z <= 2) {
            return ['code' => 'overweight_risk', 'label' => 'Risco de sobrepeso', 'z' => $z];
        }
        if ($z <= 3) {
            return ['code' => 'obesity', 'label' => 'Obesidade', 'z' => $z];
        }

        return ['code' => 'severe_obesity', 'label' => 'Obesidade grave', 'z' => $z];
    }

    /**
     * Z-score do IMC (OMS 2007) usando parâmetros LMS internos
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

        if ($L == 0.0) {
            // caso limite quando L ~ 0, usar log
            $z = log($imc / $M) / $S;
        } else {
            $z = ((pow(($imc / $M), $L) - 1.0) / ($L * $S));
        }

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
                return ['code' => 'no_risk', 'label' => 'Sem risco'];
            }
            if ($circCinturaCm < 102) {
                return ['code' => 'increased', 'label' => 'Risco aumentado'];
            }

            return ['code' => 'high', 'label' => 'Risco muito aumentado'];
        }
        // F
        if ($circCinturaCm < 80) {
            return ['code' => 'no_risk', 'label' => 'Sem risco'];
        }
        if ($circCinturaCm < 88) {
            return ['code' => 'increased', 'label' => 'Risco aumentado'];
        }

        return ['code' => 'high', 'label' => 'Risco muito aumentado'];
    }

    private function obterRiscoCinturaCrianca(float $circCinturaCm, string $sexo, int $idadeAnos): array
    {
        $p90 = $this->getP90($idadeAnos, $sexo);
        if ($circCinturaCm >= $p90) {
            return ['code' => 'increased', 'label' => 'Risco aumentado (≥ P90)', 'p90' => $p90];
        }

        return ['code' => 'no_risk', 'label' => 'Sem risco (< P90)', 'p90' => $p90];
    }

    /**
     * Utilitário: normaliza sexo para 'M' ou 'F'
     */
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

    /**
     * Idade em meses
     */
    public function calcularIdadeEmMeses(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        $dn = $dataNascimento instanceof DateTime ? clone $dataNascimento : new DateTime($dataNascimento);
        $ref = $dataReferencia ? ($dataReferencia instanceof DateTime ? clone $dataReferencia : new DateTime($dataReferencia)) : new DateTime;
        $i = $dn->diff($ref);

        return ($i->y * 12) + $i->m; // WHO usa meses inteiros
    }

    /**
     * Idade em anos (inteiros)
     */
    public function calcularIdadeEmAnos(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        $dn = $dataNascimento instanceof DateTime ? clone $dataNascimento : new DateTime($dataNascimento);
        $ref = $dataReferencia ? ($dataReferencia instanceof DateTime ? clone $dataReferencia : new DateTime($dataReferencia)) : new DateTime;

        return $dn->diff($ref)->y;
    }

    /**
     * Avaliação completa — retorna estrutura consolidada com códigos + rótulos PT-BR
     */
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
