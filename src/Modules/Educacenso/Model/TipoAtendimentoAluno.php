<?php

namespace iEducar\Modules\Educacenso\Model;

class TipoAtendimentoAluno
{
    const DESENVOLVIMENTO_FUNCOES_COGNITIVAS = 1;
    const DESENVOLVIMENTO_VIDA_AUTONOMA = 2;
    const ENRIQUECIMENTO_CURRICULAR = 3;
    const ENSINO_INFORMATICA_ACESSIVEL = 4;
    const ENSINO_LIBRAS = 5;
    const ENSINO_LINGUA_PORTUGUESA = 6;
    const ENSINO_SOROBAN = 7;
    const ENSINO_BRAILE = 8;
    const ENSINO_ORIENTACAO_MOBILIDADE = 9;
    const ENSINO_CAA = 10;
    const ENSINO_RECURSOS_OPTICOS_E_NAO_OPTICOS = 11;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::DESENVOLVIMENTO_FUNCOES_COGNITIVAS => 'Desenvolvimento de funções cognitivas',
            self::DESENVOLVIMENTO_VIDA_AUTONOMA => 'Desenvolvimento de vida autônoma',
            self::ENRIQUECIMENTO_CURRICULAR => 'Enriquecimento curricular',
            self::ENSINO_INFORMATICA_ACESSIVEL => 'Ensino da informática acessível',
            self::ENSINO_LIBRAS => 'Ensino da Língua Brasileira de Sinais (Libras)',
            self::ENSINO_LINGUA_PORTUGUESA => 'Ensino da Língua Portuguesa como segunda língua',
            self::ENSINO_SOROBAN => 'Ensino das técnicas do cálculo no Soroban',
            self::ENSINO_BRAILE => 'Ensino de Sistema Braille',
            self::ENSINO_ORIENTACAO_MOBILIDADE => 'Ensino de técnicas para orientação e mobilidade',
            self::ENSINO_CAA => 'Ensino de uso da Comunicação Alternativa e Aumentativa (CAA)',
            self::ENSINO_RECURSOS_OPTICOS_E_NAO_OPTICOS => 'Ensino de uso de recursos ópticos e não ópticos',
        ];
    }
}