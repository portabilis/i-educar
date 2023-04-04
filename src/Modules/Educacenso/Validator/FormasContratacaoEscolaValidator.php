<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\CategoriaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\FormasContratacaoPoderPublico;

class FormasContratacaoEscolaValidator implements EducacensoValidator
{
    private $dependenciaAdministrativa;
    private $categoriaEscolaPrivada;
    private $formasContratacao;

    public function __construct(
        $dependenciaAdministrativa,
        $categoriaEscolaPrivada,
        $formasContratacao
    ) {
        $this->dependenciaAdministrativa = $dependenciaAdministrativa;
        $this->categoriaEscolaPrivada = $categoriaEscolaPrivada;
        $this->formasContratacao = $formasContratacao;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->validaDependenciaAdministrativa() && $this->validaCategoriaEscolaPrivada();
    }

    /**
     * @return bool
     */
    private function validaDependenciaAdministrativa()
    {
        $dependenciasAdimistrativas = [
            DependenciaAdministrativaEscola::FEDERAL,
            DependenciaAdministrativaEscola::ESTADUAL,
            DependenciaAdministrativaEscola::MUNICIPAL,
        ];

        if (in_array($this->dependenciaAdministrativa, $dependenciasAdimistrativas)) {
            $opcoesValidas = [
                FormasContratacaoPoderPublico::TERMO_COOPERACAO_TECNICA,
                FormasContratacaoPoderPublico::CONTRATO_CONSORCIO,
            ];

            return count(array_diff($this->formasContratacao, $opcoesValidas)) === 0;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function validaCategoriaEscolaPrivada()
    {
        $categorias = [
            CategoriaEscolaPrivada::COMUNITARIA,
            CategoriaEscolaPrivada::CONFESSIONAL,
            CategoriaEscolaPrivada::FILANTROPICA,
        ];

        if (in_array($this->categoriaEscolaPrivada, $categorias)) {
            $opcoesValidas = [
                FormasContratacaoPoderPublico::TERMO_COLABORACAO,
                FormasContratacaoPoderPublico::TERMO_FOMENTO,
                FormasContratacaoPoderPublico::ACORDO_COOPERACAO,
                FormasContratacaoPoderPublico::CONTRATO_PRESTACAO_SERVICO,
            ];

            return count(array_diff($this->formasContratacao, $opcoesValidas)) === 0;
        }

        if ($this->categoriaEscolaPrivada === CategoriaEscolaPrivada::PARTICULAR) {
            $opcoesValidas = [
                FormasContratacaoPoderPublico::CONTRATO_PRESTACAO_SERVICO,
            ];

            return count(array_diff($this->formasContratacao, $opcoesValidas)) === 0;
        }

        return true;
    }

    public function getMessage()
    {
        return 'Verifique se o INEP possui 12 dígitos';
    }
}
