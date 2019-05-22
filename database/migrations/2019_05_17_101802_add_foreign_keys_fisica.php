<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysFisica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getTables() as $table) {
            $columnsForeignKey = $table['columns_foreign_key'];
            try {
                Schema::table($table['table_name'], function (Blueprint $table) use ($columnsForeignKey) {
                    $table->foreign($columnsForeignKey)->references('idpes')->on('cadastro.fisica');
                });
            } catch (\Throwable $exception) {
                // Trata inconsistências
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function getTables()
    {
        return [
            [
                'table_name' => 'cadastro.fisica_sangue',
                'columns_foreign_key' => ['idpes'],
            ],
            [
                'table_name' => 'cadastro.aviso_nome',
                'columns_foreign_key' => ['idpes'],
            ],
            [
                'table_name' => 'cadastro.documento',
                'columns_foreign_key' => ['idpes'],
            ],
            [
                'table_name' => 'cadastro.fisica_cpf',
                'columns_foreign_key' => ['idpes'],
            ],
            [
                'table_name' => 'cadastro.funcionario',
                'columns_foreign_key' => ['idpes'],
            ],
            [
                'table_name' => 'cadastro.fisica_deficiencia',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'cadastro.fisica_raca',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'cadastro.religiao',
                'columns_foreign_key' => ['idpes_exc'],
            ],
            [
                'table_name' => 'cadastro.religiao',
                'columns_foreign_key' => ['idpes_cad'],
            ],
            [
                'table_name' => 'modules.empresa_transporte_escolar',
                'columns_foreign_key' => ['ref_resp_idpes'],
            ],
            [
                'table_name' => 'modules.motorista',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'modules.pessoa_transporte',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'pmidrh.setor',
                'columns_foreign_key' => ['ref_idpes_resp'],
            ],
            [
                'table_name' => 'pmieducar.aluno',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'pmieducar.cliente',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'pmiotopic.grupopessoa',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'pmiotopic.notas',
                'columns_foreign_key' => ['ref_idpes'],
            ],
            [
                'table_name' => 'portal.foto_portal',
                'columns_foreign_key' => ['ref_ref_cod_pessoa_fj'],
            ],
            [
                'table_name' => 'portal.funcionario',
                'columns_foreign_key' => ['ref_cod_pessoa_fj'],
            ],
            [
                'table_name' => 'pmieducar.responsaveis_aluno',
                'columns_foreign_key' => ['ref_idpes'],
            ],
        ];
    }
}
