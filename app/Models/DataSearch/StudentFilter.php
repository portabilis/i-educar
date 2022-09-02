<?php

namespace App\Models\DataSearch;

class StudentFilter
{
    public function __construct(
        public readonly ?int $codAluno = null,
        public readonly ?string $inep = null,
        public readonly ?string $redeEstatual = null,
        public readonly ?string $nomeAluno = null,
        public readonly ?string $dataNascimento = null,
        public readonly ?string $cpf = null,
        public readonly ?string $rg = null,
        public readonly ?string $nomePai = null,
        public readonly ?string $nomeMae = null,
        public readonly ?string $nomeResponsavel = null,
        public readonly ?int $ano = null,
        public readonly ?int $instituicao = null,
        public readonly ?int $escola = null,
        public readonly ?int $curso = null,
        public readonly ?int $serie = null,
        public readonly ?int $perPage = null,
        public readonly ?string $pageName = null,
    ) {}
}
