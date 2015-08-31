--
-- @author   Alan Felipe Farias <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

 ALTER TABLE pmieducar.aluno ADD COLUMN autorizado_um varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN parentesco_um varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN autorizado_dois varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN parentesco_dois varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN autorizado_tres varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN parentesco_tres varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN autorizado_quatro varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN parentesco_quatro varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN autorizado_cinco varchar(150);
 ALTER TABLE pmieducar.aluno ADD COLUMN parentesco_cinco varchar(150);
 
  -- //@UNDO

 ALTER TABLE pmieducar.aluno DROP COLUMN autorizado_um varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN parentesco_um varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN autorizado_dois varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN parentesco_dois varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN autorizado_tres varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN parentesco_tres varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN autorizado_quatro varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN parentesco_quatro varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN autorizado_cinco varchar(150);
 ALTER TABLE pmieducar.aluno DROP COLUMN parentesco_cinco varchar(150);
