--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


alter table cadastro.fisica add column falecido boolean;
 --undo

alter table cadastro.fisica drop column falecido;
