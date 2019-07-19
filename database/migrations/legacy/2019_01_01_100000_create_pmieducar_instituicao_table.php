<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarInstituicaoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.instituicao_cod_instituicao_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.instituicao (
                    cod_instituicao integer DEFAULT nextval(\'pmieducar.instituicao_cod_instituicao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_idtlog character varying(20) NOT NULL,
                    ref_sigla_uf character(2) NOT NULL,
                    cep numeric(8,0) NOT NULL,
                    cidade character varying(60) NOT NULL,
                    bairro character varying(40) NOT NULL,
                    logradouro character varying(255) NOT NULL,
                    numero numeric(6,0),
                    complemento character varying(50),
                    nm_responsavel character varying(255) NOT NULL,
                    ddd_telefone numeric(2,0),
                    telefone numeric(11,0),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    nm_instituicao character varying(255) NOT NULL,
                    data_base_remanejamento date,
                    data_base_transferencia date,
                    controlar_espaco_utilizacao_aluno smallint,
                    percentagem_maxima_ocupacao_salas numeric(5,2),
                    quantidade_alunos_metro_quadrado integer,
                    exigir_vinculo_turma_professor smallint,
                    gerar_historico_transferencia boolean,
                    matricula_apenas_bairro_escola boolean,
                    restringir_historico_escolar boolean,
                    coordenador_transporte character varying,
                    restringir_multiplas_enturmacoes boolean,
                    permissao_filtro_abandono_transferencia boolean,
                    data_base_matricula date,
                    multiplas_reserva_vaga boolean DEFAULT false NOT NULL,
                    reserva_integral_somente_com_renda boolean DEFAULT false NOT NULL,
                    data_expiracao_reserva_vaga date,
                    data_fechamento date,
                    componente_curricular_turma boolean,
                    reprova_dependencia_ano_concluinte boolean,
                    controlar_posicao_historicos boolean,
                    data_educacenso date,
                    bloqueia_matricula_serie_nao_seguinte boolean,
                    permitir_carga_horaria boolean DEFAULT false,
                    exigir_dados_socioeconomicos boolean DEFAULT false,
                    altera_atestado_para_declaracao boolean,
                    orgao_regional integer,
                    obrigar_campos_censo boolean,
                    obrigar_documento_pessoa boolean DEFAULT false,
                    exigir_lancamentos_anteriores boolean DEFAULT false,
                    exibir_apenas_professores_alocados boolean DEFAULT false,
	                bloquear_vinculo_professor_sem_alocacao_escola bool NOT NULL DEFAULT false
                );

                ALTER TABLE ONLY pmieducar.instituicao
                    ADD CONSTRAINT instituicao_pkey PRIMARY KEY (cod_instituicao);

                SELECT pg_catalog.setval(\'pmieducar.instituicao_cod_instituicao_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.instituicao');

        DB::unprepared('DROP SEQUENCE pmieducar.instituicao_cod_instituicao_seq;');
    }
}
