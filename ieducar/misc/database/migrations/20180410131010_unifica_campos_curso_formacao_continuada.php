<?php

use Phinx\Migration\AbstractMigration;

class UnificaCamposCursoFormacaoContinuada extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_formacao_continuada INTEGER[];');
        $this->execute('UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 1)
                         WHERE curso_creche = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 2)
                         WHERE curso_pre_escola = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 3)
                         WHERE curso_anos_iniciais = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 4)
                         WHERE curso_anos_finais = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 5)
                         WHERE curso_ensino_medio = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 6)
                         WHERE curso_eja = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 7)
                         WHERE curso_educacao_especial = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 8)
                         WHERE curso_educacao_indigena = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 9)
                         WHERE curso_educacao_campo = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 10)
                         WHERE curso_educacao_ambiental = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 11)
                         WHERE curso_educacao_direitos_humanos = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 12)
                         WHERE curso_genero_diversidade_sexual = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 13)
                         WHERE curso_direito_crianca_adolescente = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 14)
                         WHERE curso_relacoes_etnicorraciais = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 15)
                         WHERE curso_outros = 1;

                        UPDATE pmieducar.servidor
                           SET curso_formacao_continuada = array_append(curso_formacao_continuada, 16)
                         WHERE curso_nenhum = 1;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_creche;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_pre_escola;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_anos_iniciais;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_anos_finais;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_ensino_medio;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_eja;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_especial;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_indigena;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_campo;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_ambiental;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_direitos_humanos;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_genero_diversidade_sexual;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_direito_crianca_adolescente;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_relacoes_etnicorraciais;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_outros;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_nenhum;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN curso_formacao_continuada;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_creche SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_pre_escola SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_anos_iniciais SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_anos_finais SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_ensino_medio SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_eja SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_especial SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_indigena SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_campo SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_ambiental SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_direitos_humanos SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_genero_diversidade_sexual SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_direito_crianca_adolescente SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_relacoes_etnicorraciais SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_outros SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN curso_nenhum SMALLINT;');
    }
}
