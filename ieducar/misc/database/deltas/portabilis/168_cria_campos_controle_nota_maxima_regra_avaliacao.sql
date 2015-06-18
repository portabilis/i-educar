--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE modules.regra_avaliacao ADD COLUMN nota_maxima_geral INTEGER NOT NULL;
ALTER TABLE modules.regra_avaliacao ADD COLUMN nota_maxima_exame_final INTEGER NOT NULL;
ALTER TABLE modules.regra_avaliacao ADD COLUMN qtd_casas_decimais INTEGER NOT NULL;
UPDATE modules.regra_avaliacao SET nota_maxima_geral = 10;
UPDATE modules.regra_avaliacao SET nota_maxima_exame_final = 10;
UPDATE modules.regra_avaliacao SET qtd_casas_decimais = 2;
ALTER TABLE modules.nota_componente_curricular_media ALTER COLUMN media_arredondada TYPE CHARACTER VARYING(10);
ALTER TABLE modules.nota_componente_curricular_media ALTER COLUMN media TYPE numeric(8, 4);
ALTER TABLE modules.nota_componente_curricular ALTER COLUMN nota TYPE numeric(8, 4);
ALTER TABLE modules.nota_componente_curricular ALTER COLUMN nota_arredondada TYPE CHARACTER VARYING(10);
ALTER TABLE modules.nota_componente_curricular ALTER COLUMN nota_recuperacao TYPE CHARACTER VARYING(10);
ALTER TABLE modules.nota_componente_curricular ALTER COLUMN nota_original TYPE CHARACTER VARYING(10);
