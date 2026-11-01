<?php

use App\Services\RegistrationService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

beforeEach(function () {
    $this->makeServiceWithoutConstructor = function (): RegistrationService {
        $ref = new ReflectionClass(RegistrationService::class);
        /** @var RegistrationService $service */
        $service = $ref->newInstanceWithoutConstructor();

        if ($ref->hasProperty('user')) {
            $prop = $ref->getProperty('user');
            $prop->setAccessible(true);
            $prop->setValue($service, null); // ou um stub de User, se necessário
        }

        return $service;
    };

    $this->callUpdateNext = function (EloquentCollection $coll, string $date): void {
        $service = ($this->makeServiceWithoutConstructor)();
        $ref = new ReflectionMethod($service, 'updateNextEnrollmentsRegistrationDate');
        $ref->setAccessible(true);
        $ref->invoke($service, $coll, $date);
    };

    $this->asYmd = function ($value): ?string {
        $result = null;

        if ($value instanceof DateTime) {
            $result = $value->format('Y-m-d');
        } elseif (is_string($value)) {
            $result = (new DateTime($value))->format('Y-m-d');
        }

        return $result;
    };

    $this->enrollmentStub = fn (?string $enturmacao, ?string $exclusao) => new class($enturmacao, $exclusao)
    {
        /** @var DateTime|null */
        public $dataEnturmacao;

        /** @var DateTime|null */
        public $dataExclusao;

        public function __construct(?string $enturmacao, ?string $exclusao)
        {
            $this->dataEnturmacao = $enturmacao ? new DateTime($enturmacao) : null;
            $this->dataExclusao = $exclusao ? new DateTime($exclusao) : null;
        }

        public function __set(string $name, $value): void
        {
            $prop = $this->mapProp($name);

            if (in_array($prop, ['dataEnturmacao', 'dataExclusao'], true)) {
                $this->$prop = $value === null
                    ? null
                    : ($value instanceof DateTime ? $value : new DateTime($value));

                return;
            }

            $this->$prop = $value;
        }

        public function __get(string $name)
        {
            $prop = $this->mapProp($name);

            return $this->$prop ?? null;
        }

        private function mapProp(string $name): string
        {
            return match ($name) {
                'data_enturmacao' => 'dataEnturmacao',
                'data_exclusao' => 'dataExclusao',
                'dataEnturmacao', 'dataExclusao' => $name,
                default => $name,
            };
        }

        public function save(): void
        {
            // Intencionalmente vazio no stub de teste:
            // este método só existe para satisfazer a chamada $enrollment->save()
            // feita por App\Services\RegistrationService::updateNextEnrollmentsRegistrationDate.
            // Nos testes, validamos apenas as alterações em memória (sem I/O de banco).
        }
    };
});

test('ct1 entradas validas', function () {
    $enrollment = ($this->enrollmentStub)('2024-02-01', '2024-02-01');

    ($this->callUpdateNext)(new EloquentCollection([$enrollment]), '2024-03-01');

    $this->assertSame('2024-03-01', ($this->asYmd)($enrollment->data_enturmacao));
    $this->assertSame('2024-03-01', ($this->asYmd)($enrollment->data_exclusao));
});

test('ct2 enturmacao anterior exc nula', function () {
    $enrollment = ($this->enrollmentStub)('2024-02-01', null);

    ($this->callUpdateNext)(new EloquentCollection([$enrollment]), '2024-03-01');

    $this->assertSame('2024-03-01', ($this->asYmd)($enrollment->data_enturmacao));
    $this->assertNull(($this->asYmd)($enrollment->data_exclusao));
});

test('ct3 exclusao anterior enturmacao posterior', function () {
    $enrollment = ($this->enrollmentStub)('2024-04-01', '2024-02-01');

    ($this->callUpdateNext)(new EloquentCollection([$enrollment]), '2024-03-01');

    $this->assertSame('2024-04-01', ($this->asYmd)($enrollment->data_enturmacao));
    $this->assertSame('2024-03-01', ($this->asYmd)($enrollment->data_exclusao));
});
