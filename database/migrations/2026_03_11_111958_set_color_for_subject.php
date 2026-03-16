<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::unprepared($this->getSql());
    }

    private function getSql(): string
    {
        return <<<'SQL'
with componentes as (
  select
    id,
    regexp_replace(
      regexp_replace(
        lower(unaccent(nome)),
        '[^a-z0-9\-_]+', ' ', 'gi'
      ),
      '^-+|-+$', '', 'g'
    ) as slug
  from modules.componente_curricular
)

update modules.componente_curricular c
set color = coalesce(

  -- cores fixas para disciplinas principais
  case

    -- português
    when s.slug ~ '^(lingua portuguesa|portugues|leitura|redacao|producao textual|leitura e escrita)' then '#FF9DA5'

    -- matemática
    when s.slug ~ '^(matematica|geometria|raciocinio logico|jogos matematicos)' then '#8EC5FF'

    -- ciências
    when s.slug ~ '^(ciencias|ciencias naturais|ciencia)' then '#BBF451'

    -- história
    when s.slug ~ '^historia' then '#46ECD5'

    -- geografia
    when s.slug ~ '^geografia' then '#B3F5D1'

    -- arte
    when s.slug ~ '^(arte|artes|artesanato|desenho|pintura|grafite|arte e cultura|artes plasticas|artes visuais)' then '#FCCEE8'

    -- educação física
    when s.slug ~ '^(educacao fisica|ed fisica|esporte|esportes|futebol|futsal|basquete|voleibol|handebol|karate|capoeira|judo|jiu|rugby|atletismo|natacao)' then '#FFD6A8'

    -- língua estrangeira
    when s.slug ~ '^(ingles|lingua inglesa|espanhol|lingua espanhola|alemao|frances|italiano|japones|lingua estrangeira)' then '#A2F4FD'

    -- filosofia
    when s.slug ~ '^filosofia' then '#E9D4FF'

    -- sociologia
    when s.slug ~ '^sociologia' then '#FF6900'

    -- ensino religioso
    when s.slug ~ '^(ensino religioso|religiao|educacao religiosa|orientacao crista)' then '#F4A8FF'

    -- física
    when s.slug ~ '^fisica' then '#2B7FFF'

    -- química
    when s.slug ~ '^quimica' then '#C4B4FF'

    -- biologia
    when s.slug ~ '^biologia' then '#C6D2FF'

    -- literatura
    when s.slug ~ '^literatura' then '#FF3B4C'

    -- informática / tecnologia
    when s.slug ~ '^(informatica|computacao|robotica|tecnologia|programacao|pacote office)' then '#615FFF'

    -- música
    when s.slug ~ '^(musica|canto coral|fanfarra|banda|musicalizacao)' then '#8E51FF'

    -- dança / teatro
    when s.slug ~ '^(danca|dancas|teatro|bale|hip hop)' then '#E12AFB'

    -- meio ambiente
    when s.slug ~ '(ambiental|meio ambiente)' then '#7CCF00'

    -- empreendedorismo / projeto de vida
    when s.slug ~ '(empreendedorismo|projeto de vida)' then '#00BBA7'

    -- libras
    when s.slug ~ '^libras' then '#F6339A'

  end,

  -- fallback usando hash determinístico
  (
    array[
        '#00C16A',
        '#00B8DB',
        '#AD46FF',
        '#A50F1C',
        '#CA3500',
        '#3D6300',
        '#007F45',
        '#005F5A',
        '#007595',
        '#193CB8',
        '#432DD7',
        '#5D0EC0',
        '#8200DB',
        '#8A0194',
        '#C6005C'
    ]
  )[ (abs(hashtext(s.slug)) % 15) + 1 ]

)
from componentes s
where s.id = c.id;
SQL;
    }
};
