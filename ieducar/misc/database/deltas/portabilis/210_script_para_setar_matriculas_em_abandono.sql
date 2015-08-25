--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
 -- Remove funções desnecessárias

UPDATE pmieducar.matricula_turma
   SET abandono = TRUE
  FROM (SELECT ref_cod_matricula AS matricula, max(sequencial) AS sequencial
          FROM pmieducar.matricula_turma
         WHERE ref_cod_matricula IN (SELECT cod_matricula FROM pmieducar.matricula WHERE matricula.aprovado = 6)
         GROUP BY ref_cod_matricula) enturmacao_abandono
 WHERE matricula_turma.ref_cod_matricula = enturmacao_abandono.matricula
   AND matricula_turma.sequencial = enturmacao_abandono.sequencial