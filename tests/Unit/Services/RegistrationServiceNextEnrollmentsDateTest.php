<?php

namespace Tests\Unit\Services;

use App\Services\RegistrationService;
use DateTime;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use PHPUnit\Framework\TestCase;

class RegistrationServiceNextEnrollmentsDateTest extends TestCase
{
    private const TARGET_DATE = '2024-03-01';

    private const EARLIER_DATE = '2024-02-01';

    private function makeServiceWithoutConstructor(): RegistrationService
    {
        $ref = new \ReflectionClass(RegistrationService::class);
        /** @var RegistrationService $service */
        $service = $ref->newInstanceWithoutConstructor();

        if ($ref->hasProperty('user')) {
            $prop = $ref->getProperty('user');
            $prop->setAccessible(true);
            $prop->setValue($service, null); // ou um stub de User, se necessário
        }

        return $service;
    }

    private function callUpdateNext(EloquentCollection $coll, string $date): void
    {
        $service = $this->makeServiceWithoutConstructor();
        $ref = new \ReflectionMethod($service, 'updateNextEnrollmentsRegistrationDate');
        $ref->setAccessible(true);
        $ref->invoke($service, $coll, $date);
    }

    private static function asYmd($value): ?string
    {
        $result = null;

        if ($value instanceof DateTime) {
            $result = $value->format('Y-m-d');
        } elseif (is_string($value)) {
            $result = (new DateTime($value))->format('Y-m-d');
        }

        return $result;
    }

    public function test_ct1_entradas_validas(): void
    {
        $enrollment = new EnrollmentStub(self::EARLIER_DATE, self::EARLIER_DATE);

        $this->callUpdateNext(new EloquentCollection([$enrollment]), self::TARGET_DATE);

        $this->assertSame(self::TARGET_DATE, self::asYmd($enrollment->data_enturmacao));
        $this->assertSame(self::TARGET_DATE, self::asYmd($enrollment->data_exclusao));
    }

    public function test_ct2_enturmacao_anterior_exc_nula(): void
    {
        $enrollment = new EnrollmentStub(self::EARLIER_DATE, null);

        $this->callUpdateNext(new EloquentCollection([$enrollment]), self::TARGET_DATE);

        $this->assertSame(self::TARGET_DATE, self::asYmd($enrollment->data_enturmacao));
        $this->assertNull(self::asYmd($enrollment->data_exclusao));
    }

    public function test_ct3_exclusao_anterior_enturmacao_posterior(): void
    {
        $enrollment = new EnrollmentStub('2024-04-01', self::EARLIER_DATE);

        $this->callUpdateNext(new EloquentCollection([$enrollment]), self::TARGET_DATE);

        $this->assertSame('2024-04-01', self::asYmd($enrollment->data_enturmacao));
        $this->assertSame(self::TARGET_DATE, self::asYmd($enrollment->data_exclusao));
    }
}

class EnrollmentStub
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
}
