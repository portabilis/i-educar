<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->record = fn (string $name, array $attributes = []) => WithdrawalReasonFactory::new()->create($attributes + [
        'nm_motivo' => $name,
        'ref_cod_instituicao' => $this->institution,
    ]);
    $this->detail = fn ($record) => 'educar_motivo_afastamento_det.php?cod_motivo_afastamento=' . $record->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_motivo_afastamento_lst.php?' . http_build_query($query))->assertOk();
});

test('lista em ordem de nome, com a instituição e o botão Novo', function () {
    $beta = ($this->record)('Registro Beta');
    $alfa = ($this->record)('Registro Alfa');

    ($this->list)(['nm_motivo' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Rede Aurora', 'Registro Beta', 'Rede Aurora'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_motivo_afastamento_cad.php")', false);
});

test('filtra pelo nome, por trecho e sem diferenciar maiúsculas', function () {
    $alfa = ($this->record)('Registro Alfa');
    $beta = ($this->record)('Registro Beta');

    ($this->list)(['nm_motivo' => 'tro alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('filtra pela instituição', function () {
    $mine = ($this->record)('Registro Alfa');
    $other = ($this->record)('Registro Beta', ['ref_cod_instituicao' => LegacyInstitutionFactory::new()->create()]);

    ($this->list)(['nm_motivo' => 'Registro', 'ref_cod_instituicao' => $this->institution])
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($other), false);
});

test('não lista registro excluído', function () {
    $deleted = ($this->record)('Registro Alfa');
    $deleted->delete();

    ($this->list)(['nm_motivo' => 'Registro'])->assertDontSee(($this->detail)($deleted), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->record)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nm_motivo' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nm_motivo' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('fora do nível de administrador a instituição não é coluna, e sem permissão não há botão Novo', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $record = ($this->record)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(633, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->list)(['nm_motivo' => 'Registro'])
        ->assertSee(($this->detail)($record), false)
        ->assertDontSee('Rede Aurora</a>', false)
        ->assertDontSee('go("educar_motivo_afastamento_cad.php")', false);
});
