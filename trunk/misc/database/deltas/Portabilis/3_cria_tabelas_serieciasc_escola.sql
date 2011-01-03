 	-- //

 	--
 	-- Cria as tabelas serieciasc.escola_regulamentacao, serieciasc.escola_lingua_indigena, 
	-- serieciasc.escola_energia, serieciasc.escola_agua, serieciasc.escola_sanitario
	-- serieciasc.escola_lixo e serieciasc.escola_projeto 
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
 	--


     CREATE TABLE serieciasc.escola_regulamentacao
     (            
       ref_cod_escola integer NOT NULL,
	   regulamentacao integer NOT NULL DEFAULT 1,
       situacao integer NOT NULL DEFAULT 1,
       data_criacao date,
       ato_criacao integer DEFAULT 0,
       numero_ato_criacao character varying(20),
       data_ato_criacao date,
       ato_paralizacao integer DEFAULT 0,
       numero_ato_paralizacao character varying(20),
       data_ato_paralizacao date,
       data_extincao date,
       ato_extincao integer DEFAULT 0,
       numero_ato_extincao character varying(20),
       data_ato_extincao date,
	   created_at timestamp without time zone NOT NULL,
       updated_at timestamp without time zone
     )WITH (OIDS=FALSE);
	
	ALTER TABLE serieciasc.escola_regulamentacao ADD
 	  CONSTRAINT educacenso_ref_cod_escola_pk
 	  PRIMARY KEY (ref_cod_escola);

	  
    ALTER TABLE serieciasc.escola_regulamentacao
      ADD CONSTRAINT escola_regulamentacao_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;



    CREATE TABLE serieciasc.escola_lingua_indigena
    (
	  ref_cod_escola integer NOT NULL,
      educacao_indigena integer DEFAULT 0,
      lingua_indigena integer DEFAULT 0,
      lingua_portuguesa integer DEFAULT 0,
      materiais_especificos integer DEFAULT 0,
      ue_terra_indigena integer DEFAULT 0,
      created_at timestamp without time zone NOT NULL,
      updated_at timestamp without time zone
    )WITH (OIDS=FALSE);

	ALTER TABLE serieciasc.escola_lingua_indigena ADD
 	  CONSTRAINT escola_lingua_indigena_pk
 	  PRIMARY KEY (ref_cod_escola);

    ALTER TABLE serieciasc.escola_lingua_indigena
      ADD CONSTRAINT escola_lingua_indigena_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;


    CREATE TABLE serieciasc.escola_energia
    (
      ref_cod_escola integer NOT NULL,
      rede_publica integer DEFAULT 0,
      gerador_proprio integer DEFAULT 0,
      solar integer DEFAULT 0,
      eolica integer DEFAULT 0,
      inexistente integer DEFAULT 0, 
      created_at timestamp without time zone NOT NULL,
      updated_at timestamp without time zone
    )WITH (OIDS=FALSE);

	ALTER TABLE serieciasc.escola_energia ADD
 	  CONSTRAINT escola_energia_ref_cod_escola_pk
 	  PRIMARY KEY (ref_cod_escola);

    ALTER TABLE serieciasc.escola_energia
      ADD CONSTRAINT escola_energia_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;


	CREATE TABLE serieciasc.escola_agua
   (
      ref_cod_escola integer NOT NULL,
      rede_publica integer DEFAULT 0,
      poco_artesiano integer DEFAULT 0,
      cisterna integer DEFAULT 0,
      fonte_rio integer DEFAULT 0,
      inexistente integer DEFAULT 0, 
      created_at timestamp without time zone NOT NULL,
      updated_at timestamp without time zone
    )WITH (OIDS=FALSE);
	
	ALTER TABLE serieciasc.escola_agua ADD
 	  CONSTRAINT escola_agua_ref_cod_escola_pk
 	  PRIMARY KEY (ref_cod_escola);
 
    ALTER TABLE serieciasc.escola_agua
      ADD CONSTRAINT escola_agua_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;



    CREATE TABLE serieciasc.escola_sanitario
    (
      ref_cod_escola integer NOT NULL,
      rede_publica integer DEFAULT 0,
      fossa integer DEFAULT 0,
      inexistente integer DEFAULT 0,
      created_at timestamp without time zone NOT NULL,
      updated_at timestamp without time zone
    )WITH (OIDS=FALSE);

	ALTER TABLE serieciasc.escola_sanitario ADD
 	  CONSTRAINT escola_sanitario_ref_cod_escola_pk
 	  PRIMARY KEY (ref_cod_escola);	

    ALTER TABLE serieciasc.escola_sanitario
      ADD CONSTRAINT escola_sanitario_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;


    CREATE TABLE serieciasc.escola_lixo
    (
      ref_cod_escola integer NOT NULL,
      coleta integer DEFAULT 0,
      queima integer DEFAULT 0,
      outra_area integer DEFAULT 0,
      recicla integer DEFAULT 0,
      reutiliza integer DEFAULT 0,
      enterra integer DEFAULT 0,
      created_at timestamp without time zone NOT NULL,
      updated_at timestamp without time zone
    )WITH (OIDS=FALSE);
	
	ALTER TABLE serieciasc.escola_lixo ADD
 	  CONSTRAINT escola_lixo_ref_cod_escola_pk
 	  PRIMARY KEY (ref_cod_escola);	

    ALTER TABLE serieciasc.escola_lixo
      ADD CONSTRAINT escola_lixo_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;


    CREATE TABLE serieciasc.escola_projeto
    (
      ref_cod_escola integer NOT NULL,
      danca integer DEFAULT 0,
      folclorico integer DEFAULT 0,
      teatral integer DEFAULT 0,
      ambiental integer DEFAULT 0,
      coral integer DEFAULT 0,
      fanfarra integer DEFAULT 0,
      artes_plasticas integer DEFAULT 0,
      integrada integer DEFAULT 0,
      ambiente_alimentacao integer DEFAULT 0,
      created_at timestamp without time zone NOT NULL,
      updated_at timestamp without time zone
    )WITH (OIDS=FALSE);

	ALTER TABLE serieciasc.escola_projeto ADD
 	  CONSTRAINT escola_projeto_ref_cod_escola_pk
 	  PRIMARY KEY (ref_cod_escola);	


    ALTER TABLE serieciasc.escola_projeto
      ADD CONSTRAINT escola_projeto_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;
	  
 	-- //@UNDO
 	
 	ALTER TABLE serieciasc.escola_regulamentacao DROP
 	  CONSTRAINT escola_regulamentacao_ref_cod_escola_fk;
 	
 	DROP TABLE serieciasc.escola_regulamentacao;
	
	ALTER TABLE serieciasc.escola_lingua_indigena DROP
 	  CONSTRAINT escola_lingua_indigena_ref_cod_escola_fk;
 	
 	DROP TABLE serieciasc.escola_lingua_indigena;
	
	ALTER TABLE serieciasc.escola_energia DROP
 	  CONSTRAINT escola_energia_ref_cod_escola_fk;
 	
 	DROP TABLE serieciasc.escola_energia;
	
	ALTER TABLE serieciasc.escola_agua DROP
 	  CONSTRAINT escola_agua_ref_cod_escola_fk;
 	
 	DROP TABLE serieciasc.escola_agua;
	
	ALTER TABLE serieciasc.escola_sanitario DROP
 	  CONSTRAINT escola_sanitario_ref_cod_escola_pk;	
	
 	DROP TABLE serieciasc.escola_sanitario;
	
	ALTER TABLE serieciasc.escola_lixo DROP
 	  CONSTRAINT escola_lixo_ref_cod_escola_fk;	
	  
	DROP TABLE serieciasc.escola_lixo;
	
	ALTER TABLE serieciasc.escola_projeto DROP
 	  CONSTRAINT escola_projeto_ref_cod_escola_fk;
	  
    DROP TABLE serieciasc.escola_projeto;	  
	
 	-- //	  
