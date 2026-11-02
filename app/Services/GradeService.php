<?php

namespace App\Services;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use DateTime;
use DateTimeInterface;
use RegraAvaliacao_Model_RegraDataMapper;

class GradeService
{
    public function create(array $data): ?LegacyGrade
    {
        if (
            !is_numeric($data['ref_usuario_cad'] ?? null)
            || !is_numeric($data['ref_cod_curso'] ?? null)
            || !is_string($data['nm_serie'] ?? null)
            || !is_numeric($data['etapa_curso'] ?? null)
            || !is_numeric($data['concluinte'] ?? null)
            || !is_numeric($data['carga_horaria'] ?? null)
            || !is_numeric($data['dias_letivos'] ?? null)
        ) {
            return null;
        }

        $attributes = [
            'ref_usuario_cad' => $data['ref_usuario_cad'],
            'ref_cod_curso' => $data['ref_cod_curso'],
            'nm_serie' => $data['nm_serie'],
            'etapa_curso' => $data['etapa_curso'],
            'concluinte' => $data['concluinte'],
            'carga_horaria' => $data['carga_horaria'],
            'dias_letivos' => $data['dias_letivos'],
            'data_cadastro' => now(),
            'ativo' => 1,
            'alerta_faixa_etaria' => (bool) ($data['alerta_faixa_etaria'] ?? false),
            'bloquear_matricula_faixa_etaria' => (bool) ($data['bloquear_matricula_faixa_etaria'] ?? false),
            'exigir_inep' => (bool) ($data['exigir_inep'] ?? false),
            'importar_serie_pre_matricula' => (bool) ($data['importar_serie_pre_matricula'] ?? false),
        ];

        foreach (['idade_inicial', 'idade_final', 'idade_ideal', 'etapa_educacenso'] as $field) {
            if (is_numeric($data[$field] ?? null)) {
                $attributes[$field] = $data[$field];
            }
        }

        foreach (['descricao', 'observacao_historico'] as $field) {
            if (is_string($data[$field] ?? null)) {
                $attributes[$field] = $data[$field];
            }
        }

        $attributes += $this->evaluationRules($data);

        return LegacyGrade::query()->create($attributes);
    }

    public function update(int|string $gradeId, array $data): bool
    {
        if (!is_numeric($gradeId) || !is_numeric($data['ref_usuario_exc'] ?? null)) {
            return false;
        }

        $attributes = [
            'ref_usuario_exc' => $data['ref_usuario_exc'],
            'alerta_faixa_etaria' => (bool) ($data['alerta_faixa_etaria'] ?? false),
            'bloquear_matricula_faixa_etaria' => (bool) ($data['bloquear_matricula_faixa_etaria'] ?? false),
            'exigir_inep' => (bool) ($data['exigir_inep'] ?? false),
            'importar_serie_pre_matricula' => (bool) ($data['importar_serie_pre_matricula'] ?? false),
        ];

        // Guard que falha preserva o valor do banco
        foreach (['ref_cod_curso', 'etapa_curso', 'concluinte', 'carga_horaria', 'ativo', 'dias_letivos'] as $field) {
            if (is_numeric($data[$field] ?? null)) {
                $attributes[$field] = $data[$field];
            }
        }

        if (is_string($data['nm_serie'] ?? null)) {
            $attributes['nm_serie'] = $data['nm_serie'];
        }

        foreach (['descricao', 'observacao_historico'] as $field) {
            if (is_string($data[$field] ?? null)) {
                $attributes[$field] = $data[$field];
            }
        }

        // Guard que falha grava NULL
        foreach (['idade_inicial', 'idade_final', 'idade_ideal', 'etapa_educacenso'] as $field) {
            $attributes[$field] = is_numeric($data[$field] ?? null) ? $data[$field] : null;
        }

        $attributes += $this->evaluationRules($data, clearDifferentiatedWhenMissing: true);

        LegacyGrade::query()->whereKey($gradeId)->update($attributes);

        return true;
    }

    /**
     * Exclusão lógica: mantém a configuração da série
     */
    public function delete(int|string $gradeId, int|string $userId): bool
    {
        if (!is_numeric($gradeId) || !is_numeric($userId)) {
            return false;
        }

        LegacyGrade::query()->whereKey($gradeId)->update([
            'ativo' => 0,
            'ref_usuario_exc' => $userId,
            'data_exclusao' => now(),
        ]);

        return true;
    }

    /**
     * Verifica a idade na data base de matrícula da instituição, quando ela existe
     *
     * Série sem faixa etária só aceita idade zero, porque range(null, null) é [0]
     */
    public function isAgeWithinRange(int|string $gradeId, DateTimeInterface|string|null $birthDate, int|string $year): bool
    {
        $grade = LegacyGrade::query()->find($gradeId, [
            'cod_serie',
            'ref_cod_curso',
            'idade_inicial',
            'idade_final',
        ]);

        if ($grade === null) {
            return true;
        }

        $enrollmentBaseDate = LegacyInstitution::query()
            ->whereKey(LegacyCourse::query()->whereKey($grade->ref_cod_curso)->value('ref_cod_instituicao'))
            ->value('data_base_matricula');

        if (!is_string($enrollmentBaseDate)) {
            return true;
        }

        [, $month, $day] = explode('-', $enrollmentBaseDate);

        $birth = $birthDate ?: 'now';

        if (!$birth instanceof DateTimeInterface) {
            $birth = new DateTime($birth);
        }

        $age = $birth->diff(new DateTime("{$year}-{$month}-{$day}"))->y;

        return in_array($age, range($grade->idade_inicial, $grade->idade_final));
    }

    private function evaluationRules(array $data, bool $clearDifferentiatedWhenMissing = false): array
    {
        $attributes = [];

        if ($this->evaluationRuleExists($data['regra_avaliacao_id'] ?? null)) {
            $attributes['regra_avaliacao_id'] = $data['regra_avaliacao_id'];
        }

        $differentiated = $data['regra_avaliacao_diferenciada_id'] ?? null;

        if ($this->evaluationRuleExists($differentiated)) {
            $attributes['regra_avaliacao_diferenciada_id'] = $differentiated;
        } elseif ($clearDifferentiatedWhenMissing) {
            $attributes['regra_avaliacao_diferenciada_id'] = null;
        }

        return $attributes;
    }

    /**
     * O mapper lança quando a regra não existe, e a gravação inteira para
     */
    private function evaluationRuleExists($ruleId): bool
    {
        if (!is_numeric($ruleId)) {
            return false;
        }

        (new RegraAvaliacao_Model_RegraDataMapper)->find($ruleId);

        return true;
    }
}
