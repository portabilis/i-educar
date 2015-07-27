--
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

update pmieducar.servidor_alocacao
set
    ref_cod_funcionario_vinculo = portal.funcionario.ref_cod_funcionario_vinculo
from  portal.funcionario
where pmieducar.servidor_alocacao.ref_cod_servidor = portal.funcionario.ref_cod_pessoa_fj;