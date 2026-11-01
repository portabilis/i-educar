<?php

use App\Support\Lighthouse\AddSchema;

// Extensão do schema do GraphQL apontando para o arquivo informado.
function extensaoDeSchema(?string $schema): AddSchema
{
    return new class($schema) extends AddSchema
    {
        public function __construct(?string $schema)
        {
            $this->schema = $schema;
        }
    };
}

test('devolve o conteúdo do arquivo do schema e recusa schema ausente ou inexistente', function () {
    $file = tempnam(sys_get_temp_dir(), 'schema');
    file_put_contents($file, 'type Query');

    try {
        expect(extensaoDeSchema($file)->handle())->toBe('type Query')
            ->and(fn () => extensaoDeSchema(null)->handle())->toThrow(Exception::class, 'Schema filename not defined.')
            ->and(fn () => extensaoDeSchema('/nao/existe.graphql')->handle())->toThrow(Exception::class, 'File /nao/existe.graphql does not exists.');
    } finally {
        unlink($file);
    }
});
