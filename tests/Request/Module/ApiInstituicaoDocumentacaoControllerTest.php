<?php

use App\Models\LegacyInstitutionDocument;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = LegacySchoolFactory::new()->create();

    // A tela de documentação padrão chama os três recursos por GET, inclusive
    // os que gravam e apagam.
    $this->api = fn (string $resource, array $params = []) => $this->get('/module/Api/InstituicaoDocumentacao?' . http_build_query([
        'oper' => 'get',
        'resource' => $resource,
        'access_key' => config('legacy.apis.access_key'),
        'secret_key' => config('legacy.apis.secret_key'),
    ] + $params));

    $this->document = fn (string $title) => [
        'instituicao_id' => $this->user->ref_cod_instituicao,
        'titulo_documento' => $title,
        'url_documento' => "https://exemplo.test/{$title}.pdf",
        'ref_usuario_cad' => $this->user->getKey(),
        'ref_cod_escola' => $this->school->getKey(),
    ];
});

test('grava um documento da instituição e devolve o código dele', function () {
    $response = ($this->api)('insertDocuments', ($this->document)('regimento'))
        ->assertOk()
        ->assertJsonPath('resource', 'insertDocuments');

    $document = LegacyInstitutionDocument::query()->where('titulo_documento', 'regimento')->sole();

    expect($response->json('id'))->toBe($document->getKey())
        ->and($document->instituicao_id)->toBe($this->user->ref_cod_instituicao)
        ->and($document->url_documento)->toBe('https://exemplo.test/regimento.pdf')
        ->and($document->ref_usuario_cad)->toBe($this->user->getKey())
        ->and($document->ref_cod_escola)->toBe($this->school->getKey());
});

test('lista os documentos da instituição, do mais novo para o mais antigo', function () {
    ($this->api)('insertDocuments', ($this->document)('regimento'))->assertOk();
    ($this->api)('insertDocuments', ($this->document)('calendario'))->assertOk();
    ($this->api)('insertDocuments', ['instituicao_id' => LegacyInstitutionFactory::new()->create()->getKey()] + ($this->document)('de-outra-instituicao'))->assertOk();

    $documents = ($this->api)('getDocuments', ['instituicao_id' => $this->user->ref_cod_instituicao])
        ->assertOk()
        ->json('documentos');

    expect(array_column($documents, 'titulo_documento'))->toBe(['calendario', 'regimento'])
        ->and(array_keys($documents[0]))->toBe(['id', 'titulo_documento', 'url_documento', 'ref_usuario_cad', 'ref_cod_escola']);
});

test('apaga um documento da instituição', function () {
    $id = ($this->api)('insertDocuments', ($this->document)('regimento'))->json('id');

    ($this->api)('deleteDocuments', ['id' => $id])
        ->assertOk()
        ->assertJsonPath('resource', 'deleteDocuments');

    expect(LegacyInstitutionDocument::query()->whereKey($id)->exists())->toBeFalse();
});

test('ignora um recurso que não conhece', function () {
    ($this->api)('recurso-que-nao-existe')
        ->assertOk()
        ->assertJsonMissingPath('documentos');
});
