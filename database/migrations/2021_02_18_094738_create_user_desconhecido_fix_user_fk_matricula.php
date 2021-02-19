<?php

use App\Models\LegacyEmployee;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyPerson;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use App\Support\Database\UnknowUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserDesconhecidoFixUserFkMatricula extends Migration
{

    use UnknowUser;

    public function up()
    {
        if (!$this->checkUnknowUserExists()) {
            $this->createDesconhecidoUser();
        }
        $this->fixCodUserMissedOnMatriculaTurmaTable();
    }

    private function createDesconhecidoUser()
    {

        $person = LegacyPerson::create([
            'nome' => 'Desconhecido',
            'tipo' => 'F',
        ]);

        $individual = LegacyIndividual::create([
            'idpes' => $person->getKey(),
        ]);

        $employee = LegacyEmployee::create([
            'ref_cod_pessoa_fj' => $individual->getKey(),
            'matricula' => 'desconhecido',
            'senha' => Hash::make(time()),
            'ativo' => 1,
            'force_reset_password' => false,
        ]);

        LegacyUser::create([
            'cod_usuario' => $employee->getKey(),
            'ref_cod_instituicao' => app(LegacyInstitution::class)->getKey(),
            'ref_funcionario_cad' => 1,
            'ref_cod_tipo_usuario' => LegacyUserType::LEVEL_ADMIN,
            'data_cadastro' => now(),
            'ativo' => 1,
        ]);
    }

    private function fixCodUserMissedOnMatriculaTurmaTable()
    {

        $unknowUserId = $this->getUnknowUserId();

        $sSql = <<<SQL
        SET "audit.context" = '{"user_id" : 0, "user_name" : "Rodrigo Cabral", "origin": "Issue 8218"}';
        UPDATE pmieducar.matricula_turma
        set ref_usuario_cad = $unknowUserId
        where NOT EXISTS
        (SELECT 1 FROM pmieducar.usuario where cod_usuario = ref_usuario_cad);

        UPDATE pmieducar.matricula_turma
        set ref_usuario_exc = $unknowUserId
        where NOT EXISTS
        (SELECT 1 FROM pmieducar.usuario where cod_usuario = ref_usuario_exc)
        and ref_usuario_exc is not null;
SQL;

        DB::unprepared($sSql);
    }
}
