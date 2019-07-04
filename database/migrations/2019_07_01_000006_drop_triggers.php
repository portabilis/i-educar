<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropTriggers extends Migration
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
                SET search_path = cadastro, pg_catalog;
                
                DROP TRIGGER IF EXISTS trg_aft_fisica ON fisica;
                
                DROP TRIGGER IF EXISTS trg_aft_fisica_provisorio ON fisica;
                
                DROP TRIGGER IF EXISTS trg_aft_fisica_cpf_provisorio ON fisica_cpf;

                SET search_path = pmieducar, pg_catalog;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo_acervo_assunto;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo_acervo_autor;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo_assunto;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo_autor;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo_colecao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo_editora;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON acervo_idioma;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON aluno;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON aluno_beneficio;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON ano_letivo_modulo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON avaliacao_desempenho;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON biblioteca;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON biblioteca_dia;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON biblioteca_feriados;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON biblioteca_usuario;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON calendario_ano_letivo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON calendario_anotacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON calendario_dia;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON calendario_dia_anotacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON calendario_dia_motivo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON cliente;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON cliente_suspensao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON cliente_tipo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON cliente_tipo_cliente;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON cliente_tipo_exemplar_tipo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON coffebreak_tipo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON curso;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON disciplina;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON disciplina_topico;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON escola;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON escola_ano_letivo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON escola_complemento;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON escola_curso;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON escola_localizacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON escola_rede_ensino;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON escola_serie;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON exemplar;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON exemplar_emprestimo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON exemplar_tipo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON falta_atraso;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON falta_atraso_compensado;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON fonte;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON funcao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON habilitacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON habilitacao_curso;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON historico_disciplinas;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON historico_escolar;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON infra_comodo_funcao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON infra_predio;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON infra_predio_comodo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON instituicao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON material_didatico;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON material_tipo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON matricula;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON matricula_ocorrencia_disciplinar;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON menu_tipo_usuario;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON modulo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON motivo_afastamento;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON motivo_baixa;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON motivo_suspensao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON nivel_ensino;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON operador;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON pagamento_multa;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON pre_requisito;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON quadro_horario;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON religiao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON reserva_vaga;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON reservas;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON sequencia_serie;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON serie;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON serie_pre_requisito;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON servidor;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON servidor_afastamento;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON servidor_alocacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON servidor_curso;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON servidor_formacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON servidor_titulo_concurso;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON situacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON tipo_avaliacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON tipo_avaliacao_valores;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON tipo_dispensa;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON tipo_ensino;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON tipo_ocorrencia_disciplinar;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON tipo_regime;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON tipo_usuario;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON transferencia_solicitacao;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON transferencia_tipo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON turma;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON turma_modulo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON turma_tipo;
                
                DROP TRIGGER IF EXISTS fcn_aft_update ON usuario;
                
                SET search_path = public, pg_catalog;
            '
        );
    }
}
