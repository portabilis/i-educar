<?php

use Phinx\Migration\AbstractMigration;

class AdicionaNomeSocialNaViewDePessoaFisica extends AbstractMigration
{
    public function change()
    {
        $this->execute("DROP VIEW cadastro.v_pessoa_fisica;");
        $this->execute("
            CREATE OR REPLACE VIEW cadastro.v_pessoa_fisica AS 
                SELECT p.idpes,
                p.nome,
                p.url,
                p.email,
                p.situacao,
                f.nome_social,
                f.data_nasc,
                f.sexo,
                f.cpf,
                f.ref_cod_sistema,
                f.idesco,
                f.ativo
                FROM cadastro.pessoa p,
                cadastro.fisica f
                WHERE p.idpes = f.idpes;
         ");
         $this->execute("ALTER TABLE cadastro.v_pessoa_fisica OWNER TO ieducar;");
    }
}
