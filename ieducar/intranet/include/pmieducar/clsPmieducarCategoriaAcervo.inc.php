<?php

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarCategoriaAcervo
{
    public $id;
    public $descricao;
    public $observacoes;

    /**
     * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
     *
     * @var int
     */
    public $_total;

    /**
     * Nome do schema
     *
     * @var string
     */
    public $_schema;

    /**
     * Nome da tabela
     *
     * @var string
     */
    public $_tabela;

    /**
     * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
     *
     * @var string
     */
    public $_campos_lista;

    /**
     * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
     *
     * @var string
     */
    public $_todos_campos;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo metodo lista
     *
     * @var int
     */
    public $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no metodo lista
     *
     * @var int
     */
    public $_limite_offset;

    /**
     * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
     *
     * @var string
     */
    public $_campo_order_by;

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
