<?php

use Phinx\Migration\AbstractMigration;

class MigraIdsDoCartorioInep extends AbstractMigration
{
    public function change()
    {
        $this->execute(
            'ALTER TABLE cadastro.documento ALTER COLUMN cartorio_cert_civil_inep TYPE INTEGER USING cartorio_cert_civil_inep::int;

            CREATE INDEX tmp_idx_documento_cartorio ON cadastro.documento(cartorio_cert_civil_inep);
            CREATE INDEX tmp_idx_cartorio_inep_1 ON cadastro.codigo_cartorio_inep(id_cartorio, cod_municipio);
            CREATE INDEX tmp_idx_cartorio_inep_2 ON cadastro.codigo_cartorio_inep(cod_serventia, cod_municipio);
        
            UPDATE cadastro.documento
            SET cartorio_cert_civil_inep =
            COALESCE(
                (SELECT id
                FROM cadastro.codigo_cartorio_inep
                WHERE id_cartorio = documento.cartorio_cert_civil_inep
                AND municipio.cod_ibge = cod_municipio
                LIMIT 1
                ),
                (SELECT id
                FROM cadastro.codigo_cartorio_inep
                WHERE cod_serventia = documento.cartorio_cert_civil_inep
                AND municipio.cod_ibge = cod_municipio
                LIMIT 1
                ),
                (SELECT id
                FROM cadastro.codigo_cartorio_inep
                WHERE id_cartorio = documento.cartorio_cert_civil_inep
                LIMIT 1
                ),
                (SELECT id
                FROM cadastro.codigo_cartorio_inep
                WHERE cod_serventia = documento.cartorio_cert_civil_inep
                LIMIT 1
                )
            )
            
            FROM cadastro.fisica
            LEFT JOIN public.municipio
            ON municipio.idmun = fisica.idmun_nascimento
            WHERE cartorio_cert_civil_inep is not null
            AND fisica.idpes = documento.idpes;
            
            DROP INDEX cadastro.tmp_idx_cartorio_inep_2;
            DROP INDEX cadastro.tmp_idx_cartorio_inep_1;
            DROP INDEX cadastro.tmp_idx_documento_cartorio;
            
            ALTER TABLE cadastro.documento
            ADD CONSTRAINT cartorio_cert_civil_inep_fk
            FOREIGN KEY(cartorio_cert_civil_inep)
            REFERENCES cadastro.codigo_cartorio_inep(id)
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT;'
        );
    }
}
