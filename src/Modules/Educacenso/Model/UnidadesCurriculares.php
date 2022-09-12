<?php

namespace iEducar\Modules\Educacenso\Model;

final class UnidadesCurriculares
{
    public const ELETIVAS = 1;
    public const LIBRAS = 2;
    public const LINGUA_INDIGENA = 3;
    public const LINGUA_LITERATURA_ESTRANGEIRA_ESPANHOL = 4;
    public const LINGUA_LITERATURA_ESTRANGEIRA_FRANCES = 5;
    public const LINGUA_LITERATURA_ESTRANGEIRA_OUTRA = 6;
    public const PROJETO_DE_VIDA = 7;
    public const TRILHAS_DE_APROFUNDAMENTO_APRENDIZAGENS = 8;

    public static function getDescriptiveValues()
    {
        return [
            self::ELETIVAS => 'Eletivas',
            self::LIBRAS => 'Libras',
            self::LINGUA_INDIGENA => 'Língua indígena',
            self::LINGUA_LITERATURA_ESTRANGEIRA_ESPANHOL => 'Língua/Literatura estrangeira - Espanhol',
            self::LINGUA_LITERATURA_ESTRANGEIRA_FRANCES => 'Língua/Literatura estrangeira - Francês',
            self::LINGUA_LITERATURA_ESTRANGEIRA_OUTRA => 'Língua/Literatura estrangeira - Outra',
            self::PROJETO_DE_VIDA => 'Projeto de vida',
            self::TRILHAS_DE_APROFUNDAMENTO_APRENDIZAGENS => 'Trilhas de aprofundamento/aprendizagens',
        ];
    }
}
