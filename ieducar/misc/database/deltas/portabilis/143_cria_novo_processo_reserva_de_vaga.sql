  -- //

  --
  -- Cria tabela dos candidatos a reserva de vaga
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE SEQUENCE pmieducar.candidato_reserva_vaga_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

  CREATE TABLE pmieducar.candidato_reserva_vaga (
    cod_candidato_reserva_vaga INTEGER NOT NULL DEFAULT nextval('pmieducar.candidato_reserva_vaga_seq'::regclass),
    ano_letivo INTEGER NOT NULL,
    data_solicitacao DATE NOT NULL,
    ref_cod_aluno INTEGER NOT NULL,
    ref_cod_serie INTEGER NOT NULL,
    ref_cod_turno INTEGER,
    ref_cod_pessoa_cad INTEGER NOT NULL,
    data_cad TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
    data_update TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
    CONSTRAINT cod_candidato_reserva_vaga_pkey PRIMARY KEY (cod_candidato_reserva_vaga),
    CONSTRAINT ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno)
      REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE,
    CONSTRAINT ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie)
      REFERENCES pmieducar.serie (cod_serie) MATCH SIMPLE,
    CONSTRAINT ref_cod_turno_fkey FOREIGN KEY (ref_cod_turno)
      REFERENCES pmieducar.turma_turno (id) MATCH SIMPLE,
    CONSTRAINT ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad)
      REFERENCES cadastro.pessoa (idpes) MATCH SIMPLE
    );

  UPDATE portal.menu_submenu set arquivo = 'educar_candidato_reserva_vaga_lst.php' where cod_menu_submenu = 639;
  UPDATE pmicontrolesis.menu set caminho = 'educar_candidato_reserva_vaga_lst.php' where ref_cod_menu_submenu = 639;
  -- //