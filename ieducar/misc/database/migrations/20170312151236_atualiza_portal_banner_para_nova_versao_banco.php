<?php

use Phinx\Migration\AbstractMigration;

class AtualizaPortalBannerParaNovaVersaoBanco extends AbstractMigration
{
  public function change()
  {
      $this->execute("CREATE SEQUENCE portal_banner_cod_portal_banner_seq
                             START WITH 1
                             INCREMENT BY 1
                             NO MAXVALUE
                             MINVALUE 0
                             CACHE 1;
                      CREATE TABLE portal_banner (
                          cod_portal_banner integer DEFAULT nextval('portal_banner_cod_portal_banner_seq'::regclass) NOT NULL,
                          ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                          caminho character varying(255) DEFAULT ''::character varying NOT NULL,
                          title character varying(255),
                          prioridade integer DEFAULT 0 NOT NULL,
                          link character varying(255) DEFAULT ''::character varying NOT NULL,
                          lateral_ smallint DEFAULT (1)::smallint NOT NULL);");
  }
}
