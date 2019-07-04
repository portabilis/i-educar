<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesFichaMedicaAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                SET default_with_oids = true;
                
                CREATE TABLE modules.ficha_medica_aluno (
                    ref_cod_aluno integer NOT NULL,
                    altura character varying(4),
                    peso character varying(7),
                    grupo_sanguineo character varying(2),
                    fator_rh character varying(1),
                    alergia_medicamento character(1),
                    desc_alergia_medicamento character varying(100),
                    alergia_alimento character(1),
                    desc_alergia_alimento character varying(100),
                    doenca_congenita character(1),
                    desc_doenca_congenita character varying(100),
                    fumante character(1),
                    doenca_caxumba character(1),
                    doenca_sarampo character(1),
                    doenca_rubeola character(1),
                    doenca_catapora character(1),
                    doenca_escarlatina character(1),
                    doenca_coqueluche character(1),
                    doenca_outras character varying(100),
                    epiletico character(1),
                    epiletico_tratamento character(1),
                    hemofilico character(1),
                    hipertenso character(1),
                    asmatico character(1),
                    diabetico character(1),
                    insulina character(1),
                    tratamento_medico character(1),
                    desc_tratamento_medico character varying(100),
                    medicacao_especifica character(1),
                    desc_medicacao_especifica character varying(100),
                    acomp_medico_psicologico character(1),
                    desc_acomp_medico_psicologico character varying(100),
                    restricao_atividade_fisica character(1),
                    desc_restricao_atividade_fisica character varying(100),
                    fratura_trauma character(1),
                    desc_fratura_trauma character varying(100),
                    plano_saude character(1),
                    desc_plano_saude character varying(50),
                    hospital_clinica character varying(100),
                    hospital_clinica_endereco character varying(50),
                    hospital_clinica_telefone character varying(20),
                    responsavel character varying(50),
                    responsavel_parentesco character varying(20),
                    responsavel_parentesco_telefone character varying(20),
                    responsavel_parentesco_celular character varying(20),
                    observacao character varying(255)
                );
                
                ALTER TABLE ONLY modules.ficha_medica_aluno
                    ADD CONSTRAINT ficha_medica_cod_aluno_pkey PRIMARY KEY (ref_cod_aluno);
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.ficha_medica_aluno');
    }
}
