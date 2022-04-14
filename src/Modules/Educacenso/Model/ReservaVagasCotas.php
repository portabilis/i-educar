<?php

namespace iEducar\Modules\Educacenso\Model;

class ReservaVagasCotas
{
    public const NAO_POSSUI = 1;
    public const AUTODECLARACAO_PPI = 2;
    public const CONDICAO_RENDA = 3;
    public const ESCOLA_PUBLICA = 4;
    public const PCD = 5;
    public const OUTROS = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::AUTODECLARACAO_PPI => 'Autodeclarado preto, pardo ou indígena (PPI)',
            self::CONDICAO_RENDA => 'Condição de Renda',
            self::ESCOLA_PUBLICA => 'Oriundo de escola pública',
            self::PCD => 'Pessoa com deficiência (PCD)',
            self::OUTROS => 'Outros grupos que não os listados',
            self::NAO_POSSUI => 'Sem reservas de vagas para sistema de cotas (ampla concorrência)',
        ];
    }
}
