  --
  -- Cria coluna referenciando servidor funcao na alocação do servidor
  -- @author   Alan Felipe Farias <alan@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.instituicao ADD COLUMN permissao_filtro_abandono_transferencia boolean;

  -- //