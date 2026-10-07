<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\UniformDistributionFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');

    $this->distribution = fn (array $attributes = []) => UniformDistributionFactory::new()->create($attributes + [
        'student_id' => $this->student,
        'school_id' => $this->scenario->school,
        'year' => 2030,
        'type' => 'Entregue',
        'distribution_date' => '15/03/2030',
        'complete_kit' => false,
        'coat_jacket_qty' => 11,
        'coat_pants_qty' => 12,
        'shirt_short_qty' => 13,
        'shirt_long_qty' => 14,
        'kids_shirt_qty' => 15,
        'pants_jeans_qty' => 16,
        'pants_fem_qty' => 17,
        'pants_mas_qty' => 18,
        'socks_qty' => 19,
        'skirt_qty' => 20,
        'shorts_skirt_qty' => 21,
        'shorts_tactel_qty' => 22,
        'shorts_coton_qty' => 23,
        'sneakers_qty' => null,
    ]);

    $this->keys = fn ($distribution) => "ref_cod_aluno={$this->student->getKey()}&cod_distribuicao_uniforme={$distribution->getKey()}";
    $this->url = fn ($distribution) => '/intranet/educar_distribuicao_uniforme_det.php?' . ($this->keys)($distribution);
});

test('mostra os dados da distribuição e a quantidade de cada peça', function () {
    $distribution = ($this->distribution)();

    $this->get(($this->url)($distribution))
        ->assertOk()
        ->assertSeeInOrder([
            'Aluno', 'Ana Alves',
            'Ano', '2030',
            'Tipo', 'Entregue',
            'Data da distribuição', '15/03/2030',
            'Escola fornecedora', 'Escola Aurora',
            'Recebeu kit completo', 'Não',
            'Quantidade de agasalhos (jaqueta)', '11',
            'Quantidade de agasalhos (calça)', '12',
            'Quantidade de camisetas (manga curta)', '13',
            'Quantidade de camisetas (manga longa)', '14',
            'Quantidade de camisetas infantis (sem manga)', '15',
            'Quantidade de calça jeans', '16',
            'Quantidade de calça feminina (tecidos diversos)', '17',
            'Quantidade de calça masculina (tecidos diversos)', '18',
            'Quantidade de meias', '19',
            'Quantidade de saias', '20',
            'Quantidade de shorts saias', '21',
            'Bermuda masculina (tecidos diversos)', '22',
            'Bermuda feminina (tecidos diversos)', '23',
            'Quantidade de tênis', '0',
        ])
        ->assertSee('educar_distribuicao_uniforme_cad.php?ref_cod_aluno=' . $this->student->getKey(), false)
        ->assertSee('educar_distribuicao_uniforme_cad.php?' . ($this->keys)($distribution), false)
        ->assertSee('educar_distribuicao_uniforme_lst.php?ref_cod_aluno=' . $this->student->getKey(), false);
});

test('com o kit completo não mostra as quantidades', function () {
    $distribution = ($this->distribution)(['complete_kit' => true]);

    $this->get(($this->url)($distribution))
        ->assertOk()
        ->assertSeeInOrder(['Recebeu kit completo', 'Sim'])
        ->assertDontSee('Quantidade de meias');
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $distribution = ($this->distribution)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get(($this->url)($distribution))
        ->assertOk()
        ->assertSee('Ana Alves')
        ->assertDontSee('educar_distribuicao_uniforme_cad.php', false);
});

test('volta para a listagem do aluno quando a distribuição não existe', function () {
    $this->get('/intranet/educar_distribuicao_uniforme_det.php?ref_cod_aluno=' . $this->student->getKey() . '&cod_distribuicao_uniforme=0')
        ->assertRedirect('educar_distribuicao_uniforme_lst.php?ref_cod_aluno=' . $this->student->getKey());
});
