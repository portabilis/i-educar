<?php

namespace iEducar\Modules\Educacenso\Model;

class AcoesAmbientais
{
    public const NENHUMA_DAS_ACOES_LISTADAS = 1;

    public const CONTEUDO_COMPONENTE = 2;

    public const CONTEUDO_CURRICULAR = 3;

    public const EIXO_CURRICULO = 4;

    public const EVENTOS = 5;

    public const PROJETOS_INTERDISCIPLINARES = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::CONTEUDO_COMPONENTE => 'Como conteúdo dos componentes/campos de experiências presentes no currículo',
            self::CONTEUDO_CURRICULAR => 'Como um componente curricular especial, específico, flexível ou eletivo',
            self::EIXO_CURRICULO => 'Como um eixo estruturante do currículo',
            self::EVENTOS => 'Em eventos',
            self::PROJETOS_INTERDISCIPLINARES => 'Em projetos transversais ou interdisciplinares',
            self::NENHUMA_DAS_ACOES_LISTADAS => 'Nenhuma das opções listadas',
        ];
    }
}
