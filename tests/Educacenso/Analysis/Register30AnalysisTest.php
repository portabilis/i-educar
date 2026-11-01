<?php

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\Registro60;
use App\Models\LegacyInstitution;
use Database\Factories\LegacyInstitutionFactory;
use iEducar\Modules\Educacenso\Analysis\Register30CommonDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30ManagerDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30StudentDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30TeacherAndManagerDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30TeacherAndStudentDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30TeacherDataAnalysis;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaPessoa;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // A análise usa a data de referência da instituição ativa.
    LegacyInstitution::query()->update(['ativo' => 0]);
    LegacyInstitutionFactory::new()->create(['ativo' => 1, 'data_educacenso' => '2030-05-29']);

    // A descrição da etapa vem de um JSON lido com caminho relativo à intranet,
    // a pasta em que o LegacyController roda as telas.
    $this->cwd = getcwd();
    chdir(base_path('ieducar/intranet'));
});

afterEach(function () {
    chdir($this->cwd);
});

function pessoaDoCenso(array $attributes = [], array $tipos = []): Registro30
{
    $pessoa = new Registro30;
    $pessoa->nomeEscola = 'Escola Aurora';
    $pessoa->nomePessoa = 'Ana Alves';
    $pessoa->codigoPessoa = 1;
    $pessoa->codigoServidor = 1;
    $pessoa->codigoAluno = 1;
    $pessoa->dataNascimento = '2010-03-01';
    $pessoa->sexo = 2;
    $pessoa->raca = 1;
    $pessoa->nacionalidade = Nacionalidade::BRASILEIRA;
    $pessoa->paisNacionalidade = 76;
    $pessoa->municipioNascimento = 4205407;
    $pessoa->municipioResidencia = 4205407;
    $pessoa->arrayDeficiencias = '{}';
    $pessoa->cpf = '12345678909';
    $pessoa->email = 'ana@escola.br';
    $pessoa->paisResidencia = PaisResidencia::BRASIL;
    $pessoa->localizacaoResidencia = 1;
    $pessoa->posGraduacoes = [];
    $pessoa->tipos = $tipos;

    foreach ($attributes as $key => $value) {
        $pessoa->$key = $value;
    }

    return $pessoa;
}

function dadosDoAluno(array $attributes): Registro60
{
    $aluno = new Registro60;

    foreach ($attributes + ['etapaTurma' => 14, 'tipoAtendimentoTurma' => 0, 'modalidadeCurso' => 1, 'turmaClasseEspecial' => 0] as $key => $value) {
        $aluno->$key = $value;
    }

    return $aluno;
}

function mensagensDaAnalise(string $analysis, Registro30 $pessoa, ?int $year = null): array
{
    $analysis = new $analysis($pessoa);

    if ($year && method_exists($analysis, 'setYear')) {
        $analysis->setYear($year);
    }

    $analysis->run();

    return array_map(fn ($message) => "{$message['text']} {$message['linkPath']}", $analysis->getMessages());
}

test('uma pessoa com o cadastro completo passa em todas as análises', function () {
    $pessoa = pessoaDoCenso([
        'escolaridade' => Escolaridade::ENSINO_FUNDAMENTAL,
        'countFormacaoContinuada' => 1,
        'dadosAluno' => dadosDoAluno([]),
        'recursosProvaInep' => '{}',
    ]);

    foreach ([
        Register30CommonDataAnalysis::class,
        Register30ManagerDataAnalysis::class,
        Register30TeacherDataAnalysis::class,
        Register30TeacherAndStudentDataAnalysis::class,
        Register30TeacherAndManagerDataAnalysis::class,
        Register30StudentDataAnalysis::class,
    ] as $analysis) {
        expect(mensagensDaAnalise($analysis, $pessoa, 2030))->toBe([]);
    }
});

test('os dados comuns apontam nome, filiação, nascimento, sexo, raça, nacionalidade, endereço e deficiências', function () {
    $longo = str_repeat('Ana ', 30);

    $messages = mensagensDaAnalise(Register30CommonDataAnalysis::class, pessoaDoCenso([
        'nomePessoa' => $longo,
        'filiacao1' => $longo,
        'filiacao2' => 'Joãoooo 2',
        'dataNascimento' => '2031-01-01',
        'sexo' => null,
        'raca' => null,
        'povoIndigena' => 10,
        'nacionalidade' => Nacionalidade::ESTRANGEIRA,
        'paisNacionalidade' => 76,
        'cep' => '88000000',
        'municipioResidencia' => null,
        'arrayDeficiencias' => '{1,2}',
    ]));

    expect(implode(PHP_EOL, $messages))
        ->toContain('Insira no máximo 100 letras no nome da pessoa')
        ->toContain('O nome da pessoa')
        ->toContain('não pode ser maior que a data de referência do Educacenso (29/05/2030)')
        ->toContain('Insira no máximo 100 letras no nome da mãe')
        ->toContain('O nome da mãe de')
        ->toContain('O nome do pai de')
        ->toContain('Verifique se o sexo da pessoa')
        ->toContain('Verifique se a raça da pessoa')
        ->toContain('portanto o povo indígena não deve ser informado')
        ->toContain('portanto o país de origem não pode ser Brasil')
        ->toContain('portanto é necessário informar o município de residência')
        ->toContain('possui o campo de deficiências preenchido incorretamente')
        ->toContain('/intranet/educar_servidor_cad.php?cod_servidor=1');
});

test('os dados comuns cobram nascimento, povo indígena, nacionalidade, país, naturalidade e o caminho do aluno', function () {
    $messages = mensagensDaAnalise(Register30CommonDataAnalysis::class, pessoaDoCenso([
        'filiacao2' => str_repeat('Pai ', 30),
        'dataNascimento' => null,
        'raca' => '5',
        'nacionalidade' => null,
        'municipioNascimento' => null,
        'arrayDeficiencias' => '{1,2}',
    ], [Registro30::TIPO_STUDENT => true]));

    $estrangeira = mensagensDaAnalise(Register30CommonDataAnalysis::class, pessoaDoCenso([
        'nacionalidade' => Nacionalidade::ESTRANGEIRA,
        'paisNacionalidade' => null,
    ]));

    $brasileira = mensagensDaAnalise(Register30CommonDataAnalysis::class, pessoaDoCenso(['municipioNascimento' => null]));

    expect(implode(PHP_EOL, $messages))
        ->toContain('Insira no máximo 100 letras no nome do pai')
        ->toContain('Verifique se a data de nascimento da pessoa')
        ->toContain('Verifique se o povo indígena da pessoa')
        ->toContain('Verifique se o nacionalidade da pessoa')
        ->toContain('/module/Cadastro/aluno?id=1')
        ->and(implode(PHP_EOL, $estrangeira))->toContain('portanto o país de origem deve ser informado')
        ->and(implode(PHP_EOL, $brasileira))->toContain('Verifique se a naturalidade da pessoa');
});

test('gestor e docente brasileiros precisam de CPF, e o gestor de e-mail', function () {
    $pessoa = pessoaDoCenso(['cpf' => null, 'email' => null]);

    expect(mensagensDaAnalise(Register30ManagerDataAnalysis::class, $pessoa))->toHaveCount(2)
        ->and(mensagensDaAnalise(Register30TeacherDataAnalysis::class, $pessoa))->toHaveCount(1);
});

test('docente e aluno no Brasil precisam da zona de residência compatível com a localização diferenciada', function () {
    $semZona = mensagensDaAnalise(Register30TeacherAndStudentDataAnalysis::class, pessoaDoCenso(['localizacaoResidencia' => null]));
    $assentamentoUrbano = mensagensDaAnalise(Register30TeacherAndStudentDataAnalysis::class, pessoaDoCenso([
        'localizacaoDiferenciada' => LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO,
    ]));

    expect($semZona)->toHaveCount(1)
        ->and(implode(PHP_EOL, $assentamentoUrbano))->toContain('não pode ser área de assentamento');
});

test('a escolaridade de docentes e gestores cobra curso, pós-graduação, formação continuada e anos de conclusão', function () {
    $semEscolaridade = mensagensDaAnalise(Register30TeacherAndManagerDataAnalysis::class, pessoaDoCenso(['countFormacaoContinuada' => 0]));
    $ensinoMedio = mensagensDaAnalise(Register30TeacherAndManagerDataAnalysis::class, pessoaDoCenso([
        'escolaridade' => Escolaridade::ENSINO_MEDIO,
        'countFormacaoContinuada' => 2,
        'formacaoContinuadaEducacaoNenhum' => 1,
    ]));
    $superior = mensagensDaAnalise(Register30TeacherAndManagerDataAnalysis::class, pessoaDoCenso([
        'escolaridade' => Escolaridade::EDUCACAO_SUPERIOR,
        'countFormacaoContinuada' => 1,
        'posGraduacoes' => [(object) ['type_id' => 1, 'area_id' => null, 'completion_year' => null]],
        'formacaoCurso' => ['999990'],
        'formacaoAnoConclusao' => [1930, 2999],
        'formacaoInstituicao' => [],
    ]));

    expect($semEscolaridade)->toHaveCount(2)
        ->and($ensinoMedio)->toHaveCount(2)
        ->and($superior)->toHaveCount(6);
});

test('o aluno precisa de CPF na etapa, deficiência no AEE, recursos de prova coerentes e certidão válida', function () {
    $messages = mensagensDaAnalise(Register30StudentDataAnalysis::class, pessoaDoCenso([
        'cpf' => null,
        'arrayDeficiencias' => '{}',
        'recursosProvaInep' => '{}',
        'certidaoNascimento' => '123',
        'dadosAluno' => dadosDoAluno(['etapaTurma' => 69, 'tipoAtendimentoTurma' => TipoAtendimentoTurma::AEE, 'turmaClasseEspecial' => 1]),
    ]), 2030);

    $comDeficiencia = mensagensDaAnalise(Register30StudentDataAnalysis::class, pessoaDoCenso([
        'arrayDeficiencias' => '{1}',
        'recursosProvaInep' => '{}',
        'certidaoNascimento' => '1234567890abcdefghij1234567890AB',
        'dadosAluno' => dadosDoAluno(['modalidadeCurso' => ModalidadeCurso::EDUCACAO_ESPECIAL]),
    ]), 2030);

    $recursoInvalido = mensagensDaAnalise(Register30StudentDataAnalysis::class, pessoaDoCenso([
        'arrayDeficiencias' => '{1}',
        'recursosProvaInep' => '{14,1}',
        'dadosAluno' => dadosDoAluno([]),
    ]), 2030);

    expect($messages)->toHaveCount(5)
        ->and($comDeficiencia)->toHaveCount(3)
        ->and($recursoInvalido)->toHaveCount(1);
});
