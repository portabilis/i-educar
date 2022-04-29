<?php

use iEducar\Legacy\Model;

class clsPmieducarCategoriaAcervo extends Model
{
    public $id;
    public $descricao;
    public $observacoes;

    public function __construct($id = null, $descricao = null, $observacoes = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}categoria_obra";

        $this->_campos_lista = $this->_todos_campos = 'id, descricao, observacoes';

        if (is_numeric($id)) {
            $this->id = $id;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_string($observacoes)) {
            $this->observacoes = $observacoes;
        }
    }

    public function deletaCategoriaDaObra($acervoId)
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM pmieducar.relacao_categoria_acervo WHERE ref_cod_acervo = {$acervoId}");

        return true;
    }

    public function cadastraCategoriaParaObra($acervoId, $categoriaId)
    {
        $db = new clsBanco();
        $db->Consulta("INSERT INTO pmieducar.relacao_categoria_acervo (ref_cod_acervo, categoria_id) VALUES ({$acervoId},{$categoriaId})");

        return true;
    }

    public function listaCategoriasPorObra($acervoId)
    {
        $db = new clsBanco();
        $resultado = [];
        $db->Consulta("SELECT pmieducar.relacao_categoria_acervo.*,
                              categoria_obra.descricao as descricao
                         FROM pmieducar.acervo
                   INNER JOIN relacao_categoria_acervo ON (acervo.cod_acervo = relacao_categoria_acervo.ref_cod_acervo)
                   INNER JOIN categoria_obra on (relacao_categoria_acervo.categoria_id = categoria_obra.id)
                        WHERE acervo.cod_acervo = {$acervoId}");

        while ($db->ProximoRegistro()) {
            $resultado[] = $db->Tupla();
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }
}
