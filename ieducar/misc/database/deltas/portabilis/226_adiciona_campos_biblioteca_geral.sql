--
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


 ALTER TABLE pmieducar.acervo ADD COLUMN dimencao VARCHAR(255);
 ALTER TABLE pmieducar.acervo ADD COLUMN material_ilustrativo VARCHAR(255);
 ALTER TABLE pmieducar.acervo ADD COLUMN dimencao_ilustrativo VARCHAR(255);
 ALTER TABLE pmieducar.acervo ADD COLUMN local VARCHAR(255);
 ALTER TABLE pmieducar.acervo ALTER COLUMN isbn type VARCHAR(255);
 ALTER TABLE pmieducar.acervo ADD COLUMN ref_cod_tipo_autor int;
 ALTER TABLE pmieducar.acervo ADD COLUMN tipo_autor VARCHAR(255);
CREATE TABLE pmieducar.tipo_autor(codigo int,tipo_autor varchar(255));
INSERT INTO pmieducar.tipo_autor VALUES (1, 'Autor');
INSERT INTO pmieducar.tipo_autor VALUES (2, 'Evento');
INSERT INTO pmieducar.tipo_autor VALUES (3, 'Entidade coletiva');
INSERT INTO pmieducar.tipo_autor VALUES (4, 'Anônimo');
 ALTER TABLE pmieducar.acervo ALTER COLUMN volume drop not null;
 ALTER TABLE pmieducar.acervo ALTER COLUMN num_edicao drop not null;
 ALTER TABLE pmieducar.acervo ALTER COLUMN ano drop not null;
 ALTER TABLE pmieducar.acervo ALTER COLUMN num_paginas drop not null;
      update pmieducar.acervo set ref_cod_tipo_autor = 1 
       where cod_acervo in(select ref_cod_acervo 
        from pmieducar.acervo_acervo_autor);
 --undo

 ALTER TABLE pmieducar.acervo DROP COLUMN dimencao;
 ALTER TABLE pmieducar.acervo DROP COLUMN material_ilustrativo;
 ALTER TABLE pmieducar.acervo DROP COLUMN dimencao_ilustrativo;
 ALTER TABLE pmieducar.acervo DROP COLUMN local;
 ALTER TABLE pmieducar.acervo DROP COLUMN ref_cod_tipoautor;
 ALTER TABLE pmieducar.acervo ALTER COLUMN isbn type int;
  DROP TABLE pmieducar.tipo_autor;
 ALTER TABLE pmieducar.acervo ALTER COLUMN volume type int set not null;
 ALTER TABLE pmieducar.acervo ALTER COLUMN num_edicao type int set not null;
 ALTER TABLE pmieducar.acervo ALTER COLUMN ano type int set not null;
 ALTER TABLE pmieducar.acervo ALTER COLUMN num_paginas type int set not null;
      update pmieducar.acervo set ref_cod_tipo_autor = null
       where cod_acervo in(select ref_cod_acervo 
        from pmieducar.acervo_acervo_autor);
