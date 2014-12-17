  -- //

  --
  -- Cria tabela que contêm número de vagas alienadas em aplicações externas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE TABLE pmieducar.quantidade_reserva_externa(
    ref_cod_instituicao INTEGER NOT NULL, 
    ref_cod_escola INTEGER NOT NULL, 
    ref_cod_curso INTEGER NOT NULL, 
    ref_cod_serie INTEGER NOT NULL, 
    ref_turma_turno_id INTEGER NOT NULL,
    ano INTEGER NOT NULL, 
    qtd_alunos INTEGER NOT NULL,
    CONSTRAINT quantidade_reserva_externa_pkey PRIMARY KEY (ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, ref_turma_turno_id, ano)
  );

  -- //