<?php

use App\Support\Database\UnknowUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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

        $this->insertIntoPessoaTable();
        $this->insertIntoFisicaTable();
        $this->insertIntoFuncionarioTable();
        $this->insertIntoUsuarioTable();
    }

    private function insertIntoPessoaTable()
    {
        DB::statement("
        INSERT INTO cadastro.pessoa
        SELECT
          (SELECT nextval(('cadastro.seq_pessoa'::text)::regclass)),
             'Desconhecido',
             idpes_cad ,
             now() ,
             url ,
             tipo ,
             idpes_rev ,
             NULL,
             email,
             situacao,
             origem_gravacao,
             operacao ,
             'desconhecido'
        FROM cadastro.pessoa
        WHERE idpes = 1 ;
        ");
    }

    private function insertIntoFisicaTable()
    {
        DB::statement("
        INSERT INTO cadastro.fisica
        SELECT
          (SELECT idpes
           FROM cadastro.pessoa
           WHERE nome = 'Desconhecido'),
             data_nasc ,
             sexo ,
             idpes_mae ,
             idpes_pai ,
             idpes_responsavel ,
             idesco ,
             ideciv ,
             idpes_con ,
             data_uniao ,
             data_obito ,
             nacionalidade ,
             idpais_estrangeiro ,
             data_chegada_brasil ,
             idmun_nascimento ,
             ultima_empresa ,
             idocup ,
             nome_mae ,
             nome_pai ,
             nome_conjuge ,
             nome_responsavel ,
             justificativa_provisorio,
             idpes_rev ,
             data_rev ,
             origem_gravacao ,
             idpes_cad ,
             now() AS data_cad ,
             operacao ,
             ref_cod_sistema ,
             cpf ,
             ref_cod_religiao ,
             nis_pis_pasep ,
             sus ,
             ocupacao ,
             empresa ,
             pessoa_contato ,
             renda_mensal ,
             data_admissao ,
             ddd_telefone_empresa ,
             telefone_empresa ,
             falecido ,
             ativo ,
             ref_usuario_exc ,
             data_exclusao ,
             zona_localizacao_censo ,
             tipo_trabalho ,
             local_trabalho ,
             horario_inicial_trabalho,
             horario_final_trabalho ,
             nome_social ,
             pais_residencia ,
             localizacao_diferenciada
        FROM cadastro.fisica
        WHERE idpes = 1;
        ");
    }

    private function insertIntoFuncionarioTable()
    {
        DB::statement("
        INSERT INTO portal.funcionario
        SELECT
            (SELECT idpes
             FROM cadastro.pessoa
             WHERE nome = 'Desconhecido'),
            'desconhecido' as admin     ,
            '@6j$34@sQ8Iw2BEYs4IOCeTKFYb4.Fs/e9pl.c80CTrYSWCe2JEUSOK5CsFy' as senha,
            ativo                       ,
            ref_sec                     ,
            ramal                       ,
            sequencial                  ,
            opcao_menu                  ,
            ref_cod_setor               ,
            ref_cod_funcionario_vinculo ,
            tempo_expira_senha          ,
            tempo_expira_conta          ,
            data_troca_senha            ,
            data_reativa_conta          ,
            ref_ref_cod_pessoa_fj       ,
            proibido                    ,
            ref_cod_setor_new           ,
            matricula_new               ,
            matricula_permanente        ,
            tipo_menu                   ,
            NULL AS ip_logado                   ,
            NULL AS data_login                  ,
            email                       ,
            status_token                ,
            matricula_interna           ,
            receber_novidades           ,
            atualizou_cadastro          ,
            data_expiracao              ,
            force_reset_password
        FROM portal.funcionario
        WHERE ref_cod_pessoa_fj = 1;
        ");
    }

    private function insertIntoUsuarioTable()
    {
        DB::statement("
        INSERT INTO pmieducar.usuario
        SELECT
            (SELECT idpes
             FROM cadastro.pessoa
             WHERE nome = 'Desconhecido'),
             ref_cod_instituicao  ,
             ref_funcionario_cad  ,
             ref_funcionario_exc  ,
             ref_cod_tipo_usuario ,
             NOW()        ,
             NULL        ,
             0
        FROM pmieducar.usuario
        where cod_usuario = 1;
        ");
    }

    private function fixCodUserMissedOnMatriculaTurmaTable()
    {

        $unknowUserId = $this->getUnknowUserId();

        DB::statement("
        UPDATE pmieducar.matricula_turma
        set ref_usuario_cad = {$unknowUserId}
        where NOT EXISTS (SELECT 1 FROM pmieducar.usuario where cod_usuario = ref_usuario_cad);
        ");
        DB::statement("
        UPDATE pmieducar.matricula_turma
        set ref_usuario_exc = {$unknowUserId}
        where NOT EXISTS (SELECT 1 FROM pmieducar.usuario where cod_usuario = ref_usuario_exc) and ref_usuario_exc is not null;
        ");
    }
}
