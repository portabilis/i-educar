<?php

use Phinx\Migration\AbstractMigration;

class CriaCamposConfiguracoesFerramentaAdm extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD mostrar_codigo_inep_aluno SMALLINT DEFAULT 1  NULL;
COMMENT ON COLUMN pmieducar.configuracoes_gerais.mostrar_codigo_inep_aluno IS \'Mostrar código INEP do aluno nas telas de cadastro\'');

        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD justificativa_falta_documentacao_obrigatorio SMALLINT DEFAULT 1  NULL;
COMMENT ON COLUMN pmieducar.configuracoes_gerais.justificativa_falta_documentacao_obrigatorio IS \'Campo "Justificativa para a falta de documentação" obrigatório no cadastro de alunos\';');

        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD tamanho_min_rede_estadual INT DEFAULT null NULL;
COMMENT ON COLUMN pmieducar.configuracoes_gerais.tamanho_min_rede_estadual IS \'Tamanho mínimo do campo "Código rede estadual"\';');

        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD modelo_boletim_professor INT DEFAULT 1 NULL;
COMMENT ON COLUMN pmieducar.configuracoes_gerais.modelo_boletim_professor IS \'Modelo do boletim do professor. 1 - Padrão, 2 - Modelo recuperação por etapa, 3 - Modelo recuperação paralela\';');
    }
}
