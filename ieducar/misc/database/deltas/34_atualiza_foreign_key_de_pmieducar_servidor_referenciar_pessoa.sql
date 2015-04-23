
-- //

--
-- Atualiza a foreign key constraint de pmieducar.servidor
-- para referenciar cadastro.pessoa.
--
-- Essa atualização é devido a issue #1519 referente a não necessidade do 
-- cadastro de um novo usuário ao criar um novo servidor.
--
-- @author   Iago Effting <iago@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
--
ALTER TABLE pmieducar.servidor DROP CONSTRAINT servidor_cod_servidor_fkey;
ALTER TABLE pmieducar.servidor ADD CONSTRAINT fk_servidor_pessoa FOREIGN KEY (cod_servidor) REFERENCES cadastro.pessoa (idpes);

-- //@UNDO
ALTER TABLE pmieducar.servidor DROP CONSTRAINT fk_servidor_pessoa;
ALTER TABLE pmieducar.servidor ADD CONSTRAINT servidor_cod_servidor_fkey FOREIGN KEY (cod_servidor) REFERENCES portal.funcionario (ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- //
