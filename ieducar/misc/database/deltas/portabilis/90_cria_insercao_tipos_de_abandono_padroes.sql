  -- //

  --
  -- Cria migração para cadastrar tipos padrões de tipo de abandono
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  INSERT INTO pmieducar.abandono_tipo (ref_cod_instituicao, ref_usuario_cad, nome, data_cadastro, ativo) 
  VALUES ((select cod_instituicao from pmieducar.instituicao where ativo = 1 limit 1),1,'Desistência', now(), 1);
  INSERT INTO pmieducar.abandono_tipo (ref_cod_instituicao, ref_usuario_cad, nome, data_cadastro, ativo) 
  VALUES ((select cod_instituicao from pmieducar.instituicao where ativo = 1 limit 1),1,'Falecimento', now(), 1);

  -- //@UNDO

  UPDATE pmieducar.matricula  SET ref_cod_abandono_tipo = null
  WHERE ref_cod_abandono_tipo in (SELECT cod_abandono_tipo FROM pmieducar.abandono_tipo WHERE nome in ('Desistência', 'Falecimento'));

  DELETE FROM pmieducar.abandono_tipo 
  WHERE cod_abandono_tipo in (SELECT cod_abandono_tipo FROM pmieducar.abandono_tipo WHERE nome in ('Desistência', 'Falecimento'));

  -- //