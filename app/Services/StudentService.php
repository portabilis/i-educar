<?php

namespace App\Services;

use App\Models\LegacyIndividual;
use App\Models\LegacyStudent;

class StudentService
{
    private const AUTHORIZED_FIELDS = [
        'autorizado_um',
        'parentesco_um',
        'autorizado_dois',
        'parentesco_dois',
        'autorizado_tres',
        'parentesco_tres',
        'autorizado_quatro',
        'parentesco_quatro',
        'autorizado_cinco',
        'parentesco_cinco',
    ];

    public function create(array $data): ?int
    {
        if (!is_numeric($data['ref_idpes'] ?? null)) {
            return null;
        }

        if (!LegacyIndividual::query()->whereKey($data['ref_idpes'])->exists()) {
            return null;
        }

        $attributes = [
            'ref_idpes' => $data['ref_idpes'],
            'data_cadastro' => now(),
            'ativo' => 1,
            'utiliza_transporte_rural' => (bool) ($data['utiliza_transporte_rural'] ?? false),
        ];

        if (is_numeric($data['ref_cod_religiao'] ?? null)) {
            $attributes['ref_cod_religiao'] = $data['ref_cod_religiao'];
        }

        if (is_string($data['rota_transporte'] ?? null)) {
            $attributes['rota_transporte'] = $data['rota_transporte'];
        }

        if (is_numeric($data['tipo_transporte'] ?? null)) {
            $attributes['tipo_transporte'] = $data['tipo_transporte'];
        }

        if (is_numeric($data['ref_usuario_cad'] ?? null)) {
            $attributes['ref_usuario_cad'] = $data['ref_usuario_cad'];
        }

        if (is_numeric($data['analfabeto'] ?? null)) {
            $attributes['analfabeto'] = $data['analfabeto'];
        }

        if ($data['emancipado'] ?? false) {
            $attributes['emancipado'] = true;
        }

        if (is_string($data['tipo_responsavel'] ?? null) && mb_strlen($data['tipo_responsavel']) <= 1) {
            $attributes['tipo_responsavel'] = $data['tipo_responsavel'];
        }

        if ($data['aluno_estado_id'] ?? false) {
            $attributes['aluno_estado_id'] = $data['aluno_estado_id'];
        }

        if (is_string($data['recursos_prova_inep'] ?? null)) {
            $attributes['recursos_prova_inep'] = $data['recursos_prova_inep'];
        }

        if (is_numeric($data['recebe_escolarizacao_em_outro_espaco'] ?? null)) {
            $attributes['recebe_escolarizacao_em_outro_espaco'] = $data['recebe_escolarizacao_em_outro_espaco'];
        }

        if (is_numeric($data['justificativa_falta_documentacao'] ?? null)) {
            $attributes['justificativa_falta_documentacao'] = $data['justificativa_falta_documentacao'];
        }

        if (is_string($data['url_documento'] ?? null) && $data['url_documento'] != '') {
            $attributes['url_documento'] = $data['url_documento'];
        }

        if (is_string($data['url_laudo_medico'] ?? null) && $data['url_laudo_medico'] != '') {
            $attributes['url_laudo_medico'] = $data['url_laudo_medico'];
        }

        if (is_string($data['codigo_sistema'] ?? null)) {
            $attributes['codigo_sistema'] = $data['codigo_sistema'];
        }

        if (is_string($data['veiculo_transporte_escolar'] ?? null)) {
            $attributes['veiculo_transporte_escolar'] = '{' . $data['veiculo_transporte_escolar'] . '}';
        }

        foreach (self::AUTHORIZED_FIELDS as $field) {
            if (is_string($data[$field] ?? null) && $data[$field] != 'NULL') {
                $attributes[$field] = $data[$field];
            }
        }

        return LegacyStudent::query()->create($attributes)->cod_aluno;
    }

    public function update(int $student, array $data): bool
    {
        $attributes = [];

        if (is_numeric($data['ref_cod_religiao'] ?? null) || ($data['ref_cod_religiao'] ?? null) === 'NULL') {
            $attributes['ref_cod_religiao'] = is_numeric($data['ref_cod_religiao']) ? $data['ref_cod_religiao'] : null;
        }

        if (is_numeric($data['ref_usuario_exc'] ?? null)) {
            $attributes['ref_usuario_exc'] = $data['ref_usuario_exc'];
        }

        if (is_string($data['rota_transporte'] ?? null)) {
            $attributes['rota_transporte'] = $data['rota_transporte'];
        }

        if (isset($data['utiliza_transporte_rural'])) {
            $attributes['utiliza_transporte_rural'] = (bool) $data['utiliza_transporte_rural'];
        }

        if (is_numeric($data['tipo_transporte'] ?? null)) {
            $attributes['tipo_transporte'] = $data['tipo_transporte'];
        }

        if (is_numeric($data['ref_usuario_cad'] ?? null)) {
            $attributes['ref_usuario_cad'] = $data['ref_usuario_cad'];
        }

        if (is_numeric($data['ref_idpes'] ?? null) && LegacyIndividual::query()->whereKey($data['ref_idpes'])->exists()) {
            $attributes['ref_idpes'] = $data['ref_idpes'];
        }

        if (is_string($data['data_cadastro'] ?? null)) {
            $attributes['data_cadastro'] = $data['data_cadastro'];
        }

        if (is_numeric($data['analfabeto'] ?? null)) {
            $attributes['analfabeto'] = $data['analfabeto'];
        }

        if (isset($data['emancipado'])) {
            $attributes['emancipado'] = (bool) $data['emancipado'];
        }

        if (is_string($data['tipo_responsavel'] ?? null) && mb_strlen($data['tipo_responsavel']) <= 1) {
            $attributes['tipo_responsavel'] = $data['tipo_responsavel'];
        } elseif (($data['tipo_responsavel'] ?? null) == '') {
            $attributes['tipo_responsavel'] = null;
        }

        $attributes['aluno_estado_id'] = ($data['aluno_estado_id'] ?? false) ?: null;

        if (is_string($data['recursos_prova_inep'] ?? null)) {
            $attributes['recursos_prova_inep'] = $data['recursos_prova_inep'];
        } elseif (array_key_exists('recursos_prova_inep', $data) && $data['recursos_prova_inep'] !== false) {
            $attributes['recursos_prova_inep'] = null;
        }

        if (is_numeric($data['recebe_escolarizacao_em_outro_espaco'] ?? null)) {
            $attributes['recebe_escolarizacao_em_outro_espaco'] = $data['recebe_escolarizacao_em_outro_espaco'];
        }

        if (is_numeric($data['justificativa_falta_documentacao'] ?? null)) {
            $attributes['justificativa_falta_documentacao'] = $data['justificativa_falta_documentacao'];
        } elseif (array_key_exists('justificativa_falta_documentacao', $data) && $data['justificativa_falta_documentacao'] !== false) {
            $attributes['justificativa_falta_documentacao'] = null;
        }

        if (is_string($data['url_documento'] ?? null) && $data['url_documento'] != '') {
            $attributes['url_documento'] = $data['url_documento'];
        }

        if (is_string($data['url_laudo_medico'] ?? null) && $data['url_laudo_medico'] != '') {
            $attributes['url_laudo_medico'] = $data['url_laudo_medico'];
        }

        if (is_string($data['codigo_sistema'] ?? null)) {
            $attributes['codigo_sistema'] = $data['codigo_sistema'];
        }

        if (is_string($data['veiculo_transporte_escolar'] ?? null)) {
            $attributes['veiculo_transporte_escolar'] = '{' . $data['veiculo_transporte_escolar'] . '}';
        } elseif (array_key_exists('veiculo_transporte_escolar', $data) && $data['veiculo_transporte_escolar'] !== false) {
            $attributes['veiculo_transporte_escolar'] = null;
        }

        foreach (self::AUTHORIZED_FIELDS as $field) {
            if (is_string($data[$field] ?? null) && $data[$field] != 'NULL') {
                $attributes[$field] = $data[$field];
            } elseif (($data[$field] ?? null) == 'NULL') {
                $attributes[$field] = null;
            }
        }

        return (bool) LegacyStudent::query()->find($student, ['cod_aluno'])?->update($attributes);
    }

    public function delete(int $student, int $user): bool
    {
        return (bool) LegacyStudent::query()->find($student, ['cod_aluno'])?->update([
            'ativo' => 0,
            'data_exclusao' => now(),
            'ref_usuario_exc' => $user,
        ]);
    }

    public function enable(int $student, int $user): bool
    {
        return (bool) LegacyStudent::query()->find($student, ['cod_aluno'])?->update([
            'ativo' => 1,
            'ref_usuario_exc' => $user,
        ]);
    }
}
