  -- //

  --
  -- Cria campos e relacionamentos para o cadastro de tipos de abandono.
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

CREATE SEQUENCE pmieducar.abandono_tipo_cod_abandono_tipo_seq
                increment 1
                minvalue 0
                maxvalue 9223372036854775807
                start 1
                cache 1;

CREATE TABLE pmieducar.abandono_tipo(
  cod_abandono_tipo integer NOT NULL DEFAULT NEXTVAL('abandono_tipo_cod_abandono_tipo_seq'::regclass),
  ref_cod_instituicao integer NOT NULL,
  ref_usuario_exc integer,
  ref_usuario_cad integer,
  nome varchar(255) NOT NULL,
  data_cadastro timestamp,
  data_exclusao timestamp,
  ativo integer,
  CONSTRAINT pk_cod_abandono_tipo primary key(cod_abandono_tipo),
  CONSTRAINT fk_abandono_tipo_instituicao FOREIGN KEY(ref_cod_instituicao)
    REFERENCES pmieducar.instituicao(cod_instituicao),
  CONSTRAINT fk_abandono_tipo_usuario_exc FOREIGN KEY(ref_usuario_exc)
    REFERENCES pmieducar.usuario(cod_usuario),
  CONSTRAINT fk_abandono_tipo_usuario_cad FOREIGN KEY(ref_usuario_cad)
    REFERENCES pmieducar.usuario(cod_usuario)
);

ALTER TABLE pmieducar.matricula ADD ref_cod_abandono_tipo integer;

ALTER TABLE pmieducar.matricula ADD 
        CONSTRAINT fk_matricula_abandono_tipo FOREIGN KEY(ref_cod_abandono_tipo)
          REFERENCES pmieducar.abandono_tipo(cod_abandono_tipo);


  -- //@UNDO

ALTER TABLE pmieducar.matricula DROP CONSTRAINT fk_matricula_abandono_tipo;

DROP TABLE pmieducar.abandono_tipo;

DROP SEQUENCE pmieducar.abandono_tipo_cod_abandono_tipo_seq;

ALTER TABLE pmieducar.matricula DROP ref_cod_abandono_tipo;

  -- //