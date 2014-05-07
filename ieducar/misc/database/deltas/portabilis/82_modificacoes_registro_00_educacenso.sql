  -- //

  -- Cria tabela distrito e colunas necessárias para atender o registro 00 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
  
  ALTER TABLE pmieducar.escola ADD COLUMN situacao_funcionamento INTEGER DEFAULT '1';

  ALTER TABLE public.uf ADD COLUMN cod_ibge NUMERIC (6,0);

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_administrativa INTEGER DEFAULT '3';

  ALTER TABLE pmieducar.escola ADD COLUMN regulamentacao INTEGER DEFAULT '1';

  ALTER TABLE pmieducar.escola ADD COLUMN longitude CHARACTER VARYING(20);

  ALTER TABLE pmieducar.escola ADD COLUMN latitude CHARACTER VARYING(20);

  CREATE SEQUENCE public.seq_distrito
    INCREMENT 1
    MINVALUE 0
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;

  CREATE TABLE public.distrito
  (
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    iddis numeric(6,0) NOT NULL DEFAULT nextval(('public.seq_distrito'::text)::regclass),
    nome character varying(80) NOT NULL,
    cod_ibge numeric(6,0),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT pk_distrito PRIMARY KEY (iddis ),
    CONSTRAINT fk_distrito_municipio FOREIGN KEY (idmun)
        REFERENCES public.municipio (idmun) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT fk_distrito_sistema_idpes_cad FOREIGN KEY (idpes_cad)
        REFERENCES cadastro.pessoa (idpes) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE SET NULL,
    CONSTRAINT fk_distrito_sistema_idpes_rev FOREIGN KEY (idpes_rev)
        REFERENCES cadastro.pessoa (idpes) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE SET NULL,
    CONSTRAINT fk_distrito_sistema_idsis_cad FOREIGN KEY (idsis_cad)
        REFERENCES acesso.sistema (idsis) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE SET NULL,
    CONSTRAINT fk_distrito_sistema_idsis_rev FOREIGN KEY (idsis_rev)
        REFERENCES acesso.sistema (idsis) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE SET NULL,
    CONSTRAINT ck_distrito_operacao CHECK (operacao = 'I'::bpchar OR operacao = 'A'::bpchar OR operacao = 'E'::bpchar),
    CONSTRAINT ck_distrito_origem_gravacao CHECK (origem_gravacao = 'M'::bpchar OR origem_gravacao = 'U'::bpchar OR origem_gravacao = 'C'::bpchar OR origem_gravacao = 'O'::bpchar)
  )
  WITH (
    OIDS=TRUE
  );

  CREATE OR REPLACE FUNCTION public.cria_distritos()
  RETURNS void AS
  $BODY$
  DECLARE 
  cur_log RECORD;  
  sequence_val INTEGER;
  begin 

    FOR cur_log IN (SELECT idmun, nome, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad 
                      FROM public.municipio ORDER BY idmun ASC) LOOP

      INSERT INTO public.distrito (idmun, iddis, nome, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad)
                  VALUES(cur_log.idmun, cur_log.idmun, cur_log.nome, cur_log.idpes_cad, cur_log.data_cad,
                         cur_log.origem_gravacao, cur_log.operacao, cur_log.idsis_cad);
    END LOOP;    
    sequence_val := (SELECT max(iddis)+1 FROM public.distrito)::INT;
    PERFORM setval('public.seq_distrito', sequence_val);
        
  end;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

  SELECT public.cria_distritos();

  ALTER TABLE public.bairro ADD COLUMN iddis INTEGER;

  UPDATE public.bairro SET iddis = idmun;

  ALTER TABLE public.bairro ALTER COLUMN iddis SET NOT NULL;

  ALTER TABLE public.bairro ADD CONSTRAINT fk_bairro_distrito FOREIGN KEY (iddis)
                                REFERENCES public.distrito (iddis) MATCH SIMPLE; 

  INSERT INTO portal.menu_submenu VALUES (759, 68, 2, 'Distrito', 'public_distrito_lst.php', null, 3);                                

  -- //@UNDO

  ALTER TABLE pmieducar.escola DROP COLUMN situacao_funcionamento;

  ALTER TABLE public.uf DROP COLUMN cod_ibge;
  
  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_administrativa;

  ALTER TABLE pmieducar.escola DROP COLUMN longitude;

  ALTER TABLE pmieducar.escola DROP COLUMN latitude;
  
  ALTER TABLE pmieducar.escola DROP COLUMN regulamentacao;

  ALTER TABLE public.bairro DROP CONSTRAINT fk_bairro_distrito;

  ALTER TABLE public.bairro DROP COLUMN iddis;

  DROP FUNCTION public.cria_distritos();

  DROP TABLE public.distrito;

  DROP SEQUENCE public.seq_distrito;

  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 759;

  -- //