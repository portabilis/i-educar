<?php

namespace Database\Seeders;

use App\Models\LegacyUser;
use App\Models\WithdrawalReason;
use Illuminate\Database\Seeder;

class DefaultPmieducarMotivoAfastamentoTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $reasons = [
            'Licença Saúde',
            'Licença Maternidade',
            'Licença Paternidade',
            'Licença por Acidente de Trabalho',
            'Licença Prêmio',
            'Licença para Atividade Política',
            'Licença não Remunerada',
        ];

        foreach ($reasons as $reason) {
            WithdrawalReason::updateOrCreate([
                'nm_motivo' => $reason,
            ], [
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
