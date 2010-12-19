-- //

--
-- Remove a obrigatoriedade de Fórmula de Média para uma Regra de Avaliação,
-- possibilitando que o campo contenha o valor NULL.
--
-- @author   Eriksen Costa <eriksencosta@gmail.com>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id SET DEFAULT NULL;
ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id DROP NOT NULL;

-- //@UNDO

-- Não é o ideal, já que esse pode ser de uma instituição diferente. Mas como a
-- necessidade de um rollback é muito remota e precisamos satisfazer uma
-- foreign key, pegamos o primeiro id disponível.
UPDATE
  modules.regra_avaliacao
SET
  formula_media_id = (SELECT id FROM modules.formula_media OFFSET 0 LIMIT 1)
WHERE
  formula_media_id IS NULL;

ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id DROP DEFAULT;
ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id SET NOT NULL;

-- //