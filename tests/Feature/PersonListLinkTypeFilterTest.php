<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $_GET = [];
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('all link types are available with todos as default', function () {
    $response = $this->get('/intranet/atendidos_lst.php')->assertOk();

    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $options = (new DOMXPath($document))->query('//select[@name="link_type"]/option');

    expect($options)->toHaveCount(6);
    expect($options->item(0)->textContent)->toBe('Todos');
    expect($options->item(0)->getAttribute('value'))->toBe('');
    expect(array_map(fn ($option) => $option->getAttribute('value'), iterator_to_array($options)))->toBe(['', 'student', 'employee', 'mother', 'father', 'responsible']);
});

test('selected link type filters people and is preserved in pagination', function () {
    $mother = LegacyIndividualFactory::new()->withName('FiltroVinculoTeste1211Mae')->create()->person;
    $unlinked = LegacyIndividualFactory::new()->withName('FiltroVinculoTeste1211SemVinculo')->create()->person;
    LegacyIndividualFactory::new()->create(['idpes_mae' => $mother->getKey()]);

    $response = $this->get('/intranet/atendidos_lst.php?nm_pessoa=FiltroVinculoTeste1211&link_type=mother')
        ->assertOk()
        ->assertSee("atendidos_det.php?cod_pessoa={$mother->getKey()}", false)
        ->assertDontSee("atendidos_det.php?cod_pessoa={$unlinked->getKey()}", false);

    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);

    expect($xpath->query('//select[@name="link_type" and not(@multiple)]/option[@value="mother" and @selected]'))->toHaveCount(1);
    expect($xpath->query('//a[contains(@class,"nvp_paginador") and contains(@href,"link_type=mother")]')->length)->toBeGreaterThan(0);
});
