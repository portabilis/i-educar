<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\CategoriaEscolaPrivada as Categoria;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola as Dependencia;
use iEducar\Modules\Educacenso\Model\FormasContratacaoPoderPublico as Forma;
use iEducar\Modules\Educacenso\Validator\FormasContratacaoEscolaValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FormasContratacaoEscolaValidatorTest extends TestCase
{
    public static function escolas(): array
    {
        return [
            'municipal com termo de cooperação técnica' => [Dependencia::MUNICIPAL, null, [Forma::TERMO_COOPERACAO_TECNICA], true],
            'municipal com contrato de prestação de serviço' => [Dependencia::MUNICIPAL, null, [Forma::CONTRATO_PRESTACAO_SERVICO], false],
            'comunitária com termo de fomento' => [Dependencia::PRIVADA, Categoria::COMUNITARIA, [Forma::TERMO_FOMENTO], true],
            'filantrópica com consórcio' => [Dependencia::PRIVADA, Categoria::FILANTROPICA, [Forma::CONTRATO_CONSORCIO], false],
            'particular com prestação de serviço' => [Dependencia::PRIVADA, Categoria::PARTICULAR, [Forma::CONTRATO_PRESTACAO_SERVICO], true],
            'particular com termo de colaboração' => [Dependencia::PRIVADA, Categoria::PARTICULAR, [Forma::TERMO_COLABORACAO], false],
            'privada sem categoria' => [Dependencia::PRIVADA, null, [Forma::TERMO_COLABORACAO], true],
        ];
    }

    #[DataProvider('escolas')]
    public function test_aceita_as_formas_de_contratacao_da_dependencia_e_da_categoria(int $dependencia, ?int $categoria, array $formas, bool $valido): void
    {
        $validator = new FormasContratacaoEscolaValidator($dependencia, $categoria, $formas);

        $this->assertSame($valido, $validator->isValid());
        $this->assertSame('Verifique se o INEP possui 12 dígitos', $validator->getMessage());
    }
}
