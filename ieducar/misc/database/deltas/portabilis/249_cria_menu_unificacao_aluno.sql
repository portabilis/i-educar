
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999847,55,2,'Unificação de alunos','educar_unifica_aluno.php',NULL,3);
insert into pmicontrolesis.menu values(999847,999847,21171,'Unificação de alunos',0,'educar_unifica_aluno.php','_self',1,15,1);
insert into pmieducar.menu_tipo_usuario values(1,999847,1,1,1);
ALTER TABLE pmieducar.historico_disciplinas DROP CONSTRAINT historico_disciplinas_ref_ref_cod_aluno_fkey;
ALTER TABLE pmieducar.historico_disciplinas ADD CONSTRAINT historico_disciplinas_ref_ref_cod_aluno_fkey FOREIGN KEY (ref_ref_cod_aluno, ref_sequencial)
REFERENCES pmieducar.historico_escolar (ref_cod_aluno, sequencial) MATCH SIMPLE
ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE pmieducar.historico_escolar DROP CONSTRAINT historico_escolar_ref_cod_aluno_fkey;
ALTER TABLE pmieducar.historico_escolar ADD CONSTRAINT historico_escolar_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno)
REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE
ON UPDATE CASCADE ON DELETE CASCADE;



--UNDO

delete from pmicontrolesis.menu where cod_menu = 999847;
delete from portal.menu_submenu where cod_menu_submenu = 999847;
delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999847;
ALTER TABLE pmieducar.historico_disciplinas DROP CONSTRAINT historico_disciplinas_ref_ref_cod_aluno_fkey;
ALTER TABLE pmieducar.historico_disciplinas ADD CONSTRAINT historico_disciplinas_ref_ref_cod_aluno_fkey FOREIGN KEY (ref_ref_cod_aluno, ref_sequencial)
REFERENCES pmieducar.historico_escolar (ref_cod_aluno, sequencial) MATCH SIMPLE
ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE pmieducar.historico_escolar DROP CONSTRAINT historico_escolar_ref_cod_aluno_fkey;
ALTER TABLE pmieducar.historico_escolar ADD CONSTRAINT historico_escolar_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno)
REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE
ON UPDATE RESTRICT ON DELETE RESTRICT;








