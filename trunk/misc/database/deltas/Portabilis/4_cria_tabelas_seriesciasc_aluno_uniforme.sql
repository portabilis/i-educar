 	-- //

 	--
 	-- Cria tabela serieciasc.aluno_uniforme 
 	-- modules.educacenso_cod_turma.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
 	--


    CREATE TABLE serieciasc.aluno_uniforme
    (
      ref_cod_aluno integer NOT NULL,
      data_recebimento timestamp without time zone NOT NULL,
      camiseta character(2),
      quantidade_camiseta integer,
      bermuda character(2),
      quantidade_bermuda integer,
      jaqueta character(2),
      quantidade_jaqueta integer,
      calca character(2),
      quantidade_calca integer,
      meia character(2),
      quantidade_meia integer,
      tenis character(2),
      quantidade_tenis integer,
      created_at timestamp without time zone NOT NULL,
      updated_at timestamp without time zone
    )WITH (OIDS=FALSE);
	
	ALTER TABLE serieciasc.aluno_uniforme ADD
 	  CONSTRAINT aluno_uniforme_ref_cod_aluno_pk
 	  PRIMARY KEY (ref_cod_aluno, data_recebimento);


    ALTER TABLE serieciasc.aluno_uniforme
      ADD CONSTRAINT aluno_uniforme_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno)
      REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;
	  
 	-- //@UNDO
 	
 	ALTER TABLE serieciasc.aluno_uniforme DROP
 	  CONSTRAINT aluno_uniforme_ref_cod_aluno_fkey;
 	
 	DROP TABLE serieciasc.aluno_uniforme;

 	-- //		  
