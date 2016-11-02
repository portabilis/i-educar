-- //

--
-- Altera o tipo da coluna carga_horaria de pmieducar.servidor_alocacao para
-- possibilitar a alocação de um servidor por mais de 24h em um período de aula
-- (matutino, vespertino, noturno).
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE pmieducar.servidor_alocacao ALTER carga_horaria TYPE character varying(8);

-- //@UNDO

-- Não é possível converter uma coluna character varying para time. Caso
-- necessário, um script de rotação de dados deverá ser criado.

-- //