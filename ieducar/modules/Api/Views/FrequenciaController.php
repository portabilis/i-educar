<?php

class FrequenciaController extends ApiCoreController
{
    protected function getTipoPresenca()
    {
        $id = $this->getRequest()->id;

        if (is_numeric($id)) {
            $turma = new clsPmieducarTurma();
            $turma->cod_turma = $id;
            $turma = $turma->detalhe();

            foreach ($turma as $k => $v) {
                if (is_numeric($k)) {
                    unset($turma[$k]);
                }
            }

            if (isset($turma) && !empty($turma)) {
                $db = new clsBanco();
                $sql = "
                        SELECT
                            r.tipo_presenca
                        FROM
                            modules.regra_avaliacao_serie_ano s
                        JOIN modules.regra_avaliacao r
                            ON (s.regra_avaliacao_id = r.id)
                        WHERE s.serie_id = {$turma['ref_ref_cod_serie']}
               ";

                $db->Consulta($sql);

                $db->ProximoRegistro();
                return ['tipo_presenca' => $db->Campo('tipo_presenca')];
            }

            return [];
        }

        return [];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'getTipoPresenca')) {
            $this->appendResponse($this->getTipoPresenca());
        }
    }
}
