-- //

--
-- Corrige sequences que apresentaram valores incorretos devido a limpeza do
-- banco de dados. Isso ocorre quando uma instrução SQL INSERT informa o valor
-- da chave primária. O PostgreSQL não executa a chamada a nextval() dos campos
-- de tipo serial e nem atualiza a sequence com setval(), apesar de esse
-- comportamento estar presente nos tipos autoincrement do MySQL.
--
-- O problema que ocorre é a execução de um SQL INSERT sem passar um valor para
-- a chave primária (sequence). Se o próximo valor da sequence já estiver sendo
-- utilizado, um erro de integridade da chave é lançada.
--
-- Exemplo de SQL que resultaria em erro (observe o valor 1):
-- <code>
-- INSERT INTO acesso.sistema(
--          idsis, nome, descricao, contexto, situacao)
--  VALUES (1, ?, ?, ?, ?);
-- <code>
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
-- @version  $Id$
--

SELECT setval('acesso.sistema_idsis_seq', MAX(idsis)) FROM acesso.sistema WHERE TRUE;
SELECT setval('pmicontrolesis.menu_cod_menu_seq', MAX(cod_menu)) FROM pmicontrolesis.menu WHERE TRUE;
SELECT setval('pmicontrolesis.tutormenu_cod_tutormenu_seq', MAX(cod_tutormenu)) FROM pmicontrolesis.tutormenu WHERE TRUE;
SELECT setval('pmieducar.instituicao_cod_instituicao_seq', MAX(cod_instituicao)) FROM pmieducar.instituicao WHERE TRUE;
SELECT setval('pmieducar.tipo_usuario_cod_tipo_usuario_seq', MAX(cod_tipo_usuario)) FROM pmieducar.tipo_usuario WHERE TRUE;
SELECT setval('portal.imagem_cod_imagem_seq', MAX(cod_imagem)) FROM portal.imagem WHERE TRUE;
SELECT setval('portal.imagem_tipo_cod_imagem_tipo_seq', MAX(cod_imagem_tipo)) FROM portal.imagem_tipo WHERE TRUE;
SELECT setval('portal.menu_menu_cod_menu_menu_seq', MAX(cod_menu_menu)) FROM portal.menu_menu WHERE TRUE;
SELECT setval('portal.menu_submenu_cod_menu_submenu_seq', MAX(cod_menu_submenu)) FROM portal.menu_submenu WHERE TRUE;

-- //@UNDO

-- //
