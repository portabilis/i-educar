  -- //
  -- Esta migração adiciona o campo que exige o vínculo com a turma para o professor poder lançar notas
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


	CREATE SEQUENCE public.seq_setor_bai
    INCREMENT 1
    MINVALUE 0
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;  

  CREATE TABLE public.setor_bai
  (
    idsetorbai numeric(6,0) NOT NULL DEFAULT nextval(('public.seq_setor_bai'::text)::regclass),
    nome character varying(80) NOT NULL,
    CONSTRAINT pk_setorbai PRIMARY KEY (idsetorbai)
  )
  WITH (
    OIDS=TRUE
  );

  INSERT INTO portal.menu_submenu VALUES (760, 68, 2, 'Setor', 'public_setor_lst.php', null, 3);

  ALTER TABLE public.bairro ADD COLUMN idsetorbai numeric(6,0);

  ALTER TABLE public.bairro ADD CONSTRAINT bairro_idsetorbai_fk FOREIGN KEY (idsetorbai) REFERENCES public.setor_bai (idsetorbai);

  -- //@UNDO

  ALTER TABLE public.bairro DROP CONSTRAINT bairro_idsetorbai_fk;

  ALTER TABLE public.bairro DROP COLUMN idsetorbai;

  DROP TABLE public.setor_bai;

  DROP SEQUENCE public.seq_setor_bai;

  -- //