CREATE TABLE pmiacervo.fotos (
  cod_pmiacervo_fotos INTEGER NOT NULL AUTO_INCREMENT,
  ref_cod_imagem INTEGER NOT NULL,
  ref_idmun INTEGER NOT NULL,
  ref_idbai INTEGER,
  ref_cod_pessoa_exc INTEGER NULL,
  ref_cod_pessoa_cad INTEGER NOT NULL,
  coloracao CHAR(1) NULL,
  legibilidade FLOAT NULL,
  origem CHAR(1) NULL,
  nm_fotografo VARCHAR(255) NOT NULL DEFAULT 'anônimo',
  ano_foto VARCHAR(15) NULL DEFAULT 's/d',
  historico TEXT NULL,
  proprietario VARCHAR(255) NOT NULL DEFAULT 'anônimo',
  ano_aquisicao VARCHAR(15) NULL DEFAULT 's/d',
  localidade VARCHAR(255) NULL,
  localizacao VARCHAR(255) NULL,
  responsavel VARCHAR(255) NULL,
  ano_tombo VARCHAR(15) NULL DEFAULT 's/d',
  fundo VARCHAR(255) NULL,
  numero_tombo VARCHAR(255) NULL,
  ativo SMALLINT NOT NULL DEFAULT '1',
  data_cadastro TIMESTAMP NOT NULL,
  data_exclusao TIMESTAMP NULL,
  altura FLOAT NULL,
  largura FLOAT NULL,
  caminho VARCHAR(255) NULL,
  nm_foto VARCHAR(255) NULL,
  PRIMARY KEY(cod_pmiacervo_fotos),
  FOREIGN KEY(ref_cod_pessoa_exc)
    REFERENCES portal.funcionario(ref_cod_pessoa_fj)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT,
  FOREIGN KEY(ref_cod_pessoa_cad)
    REFERENCES portal.funcionario(ref_cod_pessoa_fj)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT,
  FOREIGN KEY(ref_idbai)
    REFERENCES public.bairro(idbai)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT,
  FOREIGN KEY(ref_idmun)
    REFERENCES public.municipio(idmun)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT,
  FOREIGN KEY(ref_cod_imagem)
    REFERENCES portal.imagem(cod_imagem)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT
)
CREATE TABLE pmiacervo.fotos_integridade (
  cod_integridade SERIAL NOT NULL,
  ref_cod_pmiacervo_fotos INTEGER NOT NULL,
  integridade CHAR(1) NULL,
  PRIMARY KEY(cod_integridade),
  FOREIGN KEY(ref_cod_pmiacervo_fotos)
    REFERENCES pmiacervo.fotos(cod_pmiacervo_fotos)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
CREATE TABLE pmiacervo.fotos_suporte (
  cod_fotos_suporte SERIAL NOT NULL,
  ref_cod_pmiacervo_fotos INTEGER NOT NULL,
  suporte CHAR(1) NOT NULL,
  PRIMARY KEY(cod_fotos_suporte),
  FOREIGN KEY(ref_cod_pmiacervo_fotos)
    REFERENCES pmiacervo.fotos(cod_pmiacervo_fotos)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)



