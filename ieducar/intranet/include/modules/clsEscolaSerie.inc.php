<?php

require_once "include/clsBanco.inc.php";

class clsEscolaSerie
{
    protected $escola;
    protected $serie;
    protected $db;

    public function __construct($escola, $serie)
    {
        if ($escola == null || $serie == null) {
            throw new Exception("Escola e série são obrigatórios para o construtor da classe clsEscolaSerie");
        }

        $this->escola = $escola;
        $this->serie = $serie;

        $this->db = new clsBanco();
    }

    public function validaExclusaoComponentes(array $listaComponentesSelecionados)
    {
        try {
            $this->validaDispensaComponente(join(",", $listaComponentesSelecionados));
        } catch(Exception $err) {
            throw $err;
        }
    }

    protected function validaDispensaComponente($componentesSelecionados)
    {
        $sql = "
            SELECT dd.ref_cod_disciplina AS cod_disciplina, cc.nome AS componente, dd.ref_cod_matricula AS matricula
            FROM pmieducar.dispensa_disciplina AS dd
            INNER JOIN modules.componente_curricular AS cc ON cc.id = dd.ref_cod_disciplina
            WHERE dd.ref_cod_escola = 2
            AND dd.ref_cod_serie = 38
            AND dd.ref_cod_disciplina NOT IN ({$componentesSelecionados})
        ";

        $this->db->Consulta($sql);
        $numLinhas = $this->db->Num_Linhas();

        if ($numLinhas > 0) {
            $listaDispensaComponentes = array();

            while($this->db->ProximoRegistro()) {
                $dispensa = $this->db->Tupla();
                $listaDispensaComponentes[$dispensa["cod_disciplina"]] = $dispensa["componente"];
            }

            $dispensaComponentes = join(",", $listaDispensaComponentes);
            throw new Exception("Alguns dos componentes que não serão mais utilizados tem dispensas lançadas no sistema:<br />{$dispensaComponentes}");
        }
    }
}