<?php

namespace Tests\Feature;

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PersonListLinkTypeFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $_GET = [];
        $this->actingAs(LegacyUserFactory::new()->admin()->create());
    }

    public function test_all_link_types_are_available_with_todos_as_default(): void
    {
        $response = $this->get('/intranet/atendidos_lst.php')->assertOk();

        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $options = (new DOMXPath($document))->query('//select[@name="link_type"]/option');

        $this->assertCount(6, $options);
        $this->assertSame('Todos', $options->item(0)->textContent);
        $this->assertSame('', $options->item(0)->getAttribute('value'));
        $this->assertSame(['', 'student', 'employee', 'mother', 'father', 'responsible'],
            array_map(fn ($option) => $option->getAttribute('value'), iterator_to_array($options)));
    }

    public function test_selected_link_type_filters_people_and_is_preserved_in_pagination(): void
    {
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

        $this->assertCount(1, $xpath->query('//select[@name="link_type" and not(@multiple)]/option[@value="mother" and @selected]'));
        $this->assertGreaterThan(0, $xpath->query('//a[contains(@class,"nvp_paginador") and contains(@href,"link_type=mother")]')->length);
    }
}
