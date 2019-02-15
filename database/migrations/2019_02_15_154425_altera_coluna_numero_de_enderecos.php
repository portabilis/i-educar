<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlteraColunaNumeroDeEnderecos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS cadastro.v_endereco;');
        DB::statement('DROP VIEW IF EXISTS relatorio.view_dados_escola;');
        DB::statement('DROP VIEW IF EXISTS relatorio.view_dados_aluno;');

        Schema::table('cadastro.endereco_pessoa', function (Blueprint $table) {
            $table->decimal('numero', 10, 0)->change();
        });
        Schema::table('historico.endereco_pessoa', function (Blueprint $table) {
            $table->decimal('numero', 10, 0)->change();
        });
        Schema::table('cadastro.endereco_externo', function (Blueprint $table) {
            $table->decimal('numero', 10, 0)->change();
        });
        Schema::table('historico.endereco_externo', function (Blueprint $table) {
            $table->decimal('numero', 10, 0)->change();
        });
        Schema::table('pmieducar.escola_complemento', function (Blueprint $table) {
            $table->decimal('numero', 10, 0)->change();
        });

        DB::statement($this->createViewEndereco());
        DB::statement($this->createViewDadosEscola());
        DB::statement($this->createViewDadosAluno());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS cadastro.v_endereco;');
        DB::statement('DROP VIEW IF EXISTS relatorio.view_dados_escola;');
        DB::statement('DROP VIEW IF EXISTS relatorio.view_dados_aluno;');

        Schema::table('cadastro.endereco_pessoa', function (Blueprint $table) {
            $table->decimal('numero', 6, 0)->change();
        });
        Schema::table('historico.endereco_pessoa', function (Blueprint $table) {
            $table->decimal('numero', 6, 0)->change();
        });
        Schema::table('cadastro.endereco_externo', function (Blueprint $table) {
            $table->decimal('numero', 6, 0)->change();
        });
        Schema::table('historico.endereco_externo', function (Blueprint $table) {
            $table->decimal('numero', 6, 0)->change();
        });
        Schema::table('pmieducar.escola_complemento', function (Blueprint $table) {
            $table->decimal('numero', 6, 0)->change();
        });

        DB::statement($this->createViewEndereco());
        DB::statement($this->createViewDadosEscola());
        DB::statement($this->createViewDadosAluno());
    }

    protected function createViewEndereco()
    {
        return '
            CREATE OR REPLACE VIEW cadastro.v_endereco AS 
                SELECT e.idpes,
                    e.cep,
                    e.idlog,
                    e.numero,
                    e.letra,
                    e.complemento,
                    e.idbai,
                    e.bloco,
                    e.andar,
                    e.apartamento,
                    l.nome AS logradouro,
                    l.idtlog,
                    b.nome AS bairro,
                    m.nome AS cidade,
                    m.sigla_uf,
                    b.zona_localizacao
                FROM cadastro.endereco_pessoa e,
                    public.logradouro l,
                    public.bairro b,
                    public.municipio m
                WHERE e.idlog = l.idlog AND e.idbai = b.idbai AND b.idmun = m.idmun AND e.tipo = 1::numeric
            UNION
                SELECT e.idpes,
                    e.cep,
                    NULL::numeric AS idlog,
                    e.numero,
                    e.letra,
                    e.complemento,
                    NULL::numeric AS idbai,
                    e.bloco,
                    e.andar,
                    e.apartamento,
                    e.logradouro,
                    e.idtlog,
                    e.bairro,
                    e.cidade,
                    e.sigla_uf,
                    e.zona_localizacao
                FROM cadastro.endereco_externo e
                WHERE e.tipo = 1::numeric;
        ';
    }

    protected function createViewDadosEscola()
    {
        return '
            CREATE OR REPLACE VIEW relatorio.view_dados_escola AS 
                SELECT escola.cod_escola,
                    relatorio.get_nome_escola(escola.cod_escola) AS nome,
                    pessoa.email,
                    COALESCE(endereco_pessoa.cep, endereco_externo.cep) AS cep,
                    COALESCE(endereco_pessoa.numero, endereco_externo.numero) AS numero,
                    COALESCE(logradouro.idtlog, endereco_externo.idtlog) AS tipo_logradouro,
                    tipo_logradouro.descricao AS descricao_logradouro,
                    COALESCE(logradouro.nome, endereco_externo.logradouro) AS logradouro,
                    COALESCE(bairro.nome, endereco_externo.bairro) AS bairro,
                    COALESCE(municipio.nome, endereco_externo.cidade) AS municipio,
                    COALESCE(municipio.sigla_uf, endereco_externo.sigla_uf::character varying) AS uf_municipio,
                    educacenso_cod_escola.cod_escola_inep AS inep,
                    relatorio.get_ddd_escola(escola.cod_escola) AS telefone_ddd,
                    relatorio.get_telefone_escola(escola.cod_escola) AS telefone,
                    fone_pessoa.ddd AS celular_ddd,
                    to_char(fone_pessoa.fone, \'99999-9999\'::text) AS celular
                FROM pmieducar.escola
                LEFT JOIN cadastro.pessoa ON escola.ref_idpes::numeric = pessoa.idpes
                LEFT JOIN modules.educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
                LEFT JOIN cadastro.endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
                LEFT JOIN cadastro.endereco_externo ON endereco_externo.idpes = pessoa.idpes
                LEFT JOIN public.logradouro ON logradouro.idlog = endereco_pessoa.idlog
                LEFT JOIN urbano.tipo_logradouro ON tipo_logradouro.idtlog::text = COALESCE(logradouro.idtlog, endereco_externo.idtlog)::text
                LEFT JOIN public.bairro ON bairro.idbai = endereco_pessoa.idbai
                LEFT JOIN public.municipio ON municipio.idmun = bairro.idmun
                LEFT JOIN cadastro.fone_pessoa ON pessoa.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 3::numeric
                LEFT JOIN cadastro.juridica ON juridica.idpes = fone_pessoa.idpes AND juridica.idpes = escola.ref_idpes::numeric;
        ';
    }

    protected function createViewDadosAluno()
    {
        return '
            CREATE OR REPLACE VIEW relatorio.view_dados_aluno AS 
                SELECT pessoa.idpes,
                    fisica.cpf,
                    aluno.cod_aluno,
                    fcn_upper(pessoa.nome::text) AS nome_aluno,
                    endereco_pessoa.cep,
                    logradouro.nome AS nome_logradouro,
                    endereco_pessoa.complemento,
                    endereco_pessoa.numero,
                    bairro.nome AS nome_bairro,
                    municipio.nome AS nome_cidade,
                    uf.nome AS nome_estado,
                    pais.nome,
                    pessoa.email,
                    matricula.cod_matricula,
                    matricula.ano,
                    matricula.ref_ref_cod_escola AS cod_escola,
                    relatorio.get_nome_escola(matricula.ref_ref_cod_escola) AS escola_aluno,
                    curso.cod_curso,
                    curso.nm_curso AS nome_curso,
                    serie.cod_serie,
                    serie.nm_serie AS nome_serie,
                    turma.cod_turma,
                    turma.nm_turma AS nome_turma,
                    fisica.sexo
                FROM pmieducar.matricula
                JOIN pmieducar.matricula_turma ON matricula_turma.ref_cod_matricula = matricula.cod_matricula
                JOIN pmieducar.turma ON turma.cod_turma = matricula_turma.ref_cod_turma AND turma.ref_ref_cod_escola = matricula.ref_ref_cod_escola AND turma.ref_ref_cod_serie = matricula.ref_ref_cod_serie AND turma.ref_cod_curso = matricula.ref_cod_curso AND turma.ano = matricula.ano
                JOIN pmieducar.serie ON serie.cod_serie = matricula.ref_ref_cod_serie
                JOIN pmieducar.curso ON curso.cod_curso = matricula.ref_cod_curso
                JOIN pmieducar.aluno ON aluno.cod_aluno = matricula.ref_cod_aluno
                JOIN cadastro.pessoa ON pessoa.idpes = aluno.ref_idpes::numeric
                JOIN cadastro.fisica ON fisica.idpes = pessoa.idpes
                LEFT JOIN cadastro.endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
                LEFT JOIN public.bairro ON bairro.idbai = endereco_pessoa.idbai
                LEFT JOIN public.logradouro ON logradouro.idlog = endereco_pessoa.idlog
                LEFT JOIN public.municipio ON municipio.idmun = bairro.idmun
                LEFT JOIN public.uf ON uf.sigla_uf::text = municipio.sigla_uf::text
                LEFT JOIN public.pais ON pais.idpais = uf.idpais
                WHERE matricula_turma.sequencial = relatorio.get_max_sequencial_matricula(matricula_turma.ref_cod_matricula);
        ';
    }
}
