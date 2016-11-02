-- //

--
-- Remove a foreign key de quadro_horario_horarios. Não é mais necessária pois
-- existem as verificações se o componente está habilitado para o ano escolar
-- (dentro do construtor de clsPmieducarQuadroHorarioHorarios), com o DataMapper
-- AnoEscolar.
--
-- A interface se encarrega de exibir apenas os componentes para a turma ou para
-- a escola-série.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
  DROP CONSTRAINT quadro_horario_horarios_ref_cod_serie_fkey;

-- //@UNDO

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
  ADD CONSTRAINT quadro_horario_horarios_ref_cod_serie_fkey
  FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina)
  REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina)
  ON UPDATE RESTRICT
  ON DELETE RESTRICT;

-- //