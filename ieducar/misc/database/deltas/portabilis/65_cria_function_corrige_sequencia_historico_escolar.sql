  -- //

  --
  -- Retira menus de Lançamento de notas por Aluno e por Turma, deixando o caminho no menu Faltas/Notas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE OR REPLACE FUNCTION modules.corrige_sequencial_historico()
    RETURNS void AS
  $BODY$
  DECLARE 
  cur_aluno RECORD;
  cur_hist RECORD;  
  contador integer;  
  begin           

  ALTER TABLE pmieducar.historico_escolar DISABLE TRIGGER USER;
  ALTER TABLE pmieducar.historico_escolar DROP CONSTRAINT historico_escolar_pkey cascade;
  FOR cur_aluno IN (SELECT cod_aluno as id FROM pmieducar.aluno) LOOP
    update pmieducar.historico_escolar set sequencial = sequencial + 100 where ref_cod_aluno = cur_aluno.id;
    contador:=1;
    FOR cur_hist IN (SELECT sequencial FROM pmieducar.historico_escolar WHERE ref_cod_aluno = cur_aluno.id ORDER BY ano, nm_serie, data_cadastro) LOOP
      update pmieducar.historico_escolar set sequencial = contador where ref_cod_aluno = cur_aluno.id and sequencial = cur_hist.sequencial; 
      contador:=contador+1;
    END LOOP;
  END LOOP;
  ALTER TABLE pmieducar.historico_escolar ADD CONSTRAINT historico_escolar_pkey PRIMARY KEY(ref_cod_aluno, sequencial);
  ALTER TABLE pmieducar.historico_escolar ENABLE TRIGGER USER;
  end;$BODY$
    LANGUAGE 'plpgsql' VOLATILE;

-- //@UNDO

DROP FUNCTION modules.corrige_sequencial_historico();

-- //
