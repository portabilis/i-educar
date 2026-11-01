<?php

use App\Models\LegacyGeneralConfiguration;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('os utilitários de array juntam, filtram, ordenam e extraem valores', function () {
    $pessoas = [['id' => 2, 'nome' => 'Érica'], ['id' => 1, 'nome' => 'Ana']];

    expect((new Portabilis_Array_Utils)->mergeValues([[1, 2], [2, 3]]))->toBe([1 => 1, 2 => 2, 3 => 3])
        ->and((new Portabilis_Array_Utils)->mergeValues(collect([5])))->toBe([5 => 5])
        ->and(Portabilis_Array_Utils::insertIn('', 'Selecione', [1 => 'Um']))->toBe(['' => 'Selecione', 1 => 'Um'])
        ->and(Portabilis_Array_Utils::filterSet([], 'id'))->toBe([])
        ->and(Portabilis_Array_Utils::filterSet($pessoas, ['id', 'nome' => 'name']))->toBe([['id' => 2, 'name' => 'Érica'], ['id' => 1, 'name' => 'Ana']])
        ->and(Portabilis_Array_Utils::filter(['id' => 3], 'id'))->toBe(['id' => 3])
        ->and(Portabilis_Array_Utils::filterSet(collect(['id' => 4]), 'id'))->toBe([['id' => 4]])
        ->and(Portabilis_Array_Utils::setAsIdValue([], 'id', 'nome'))->toBe([])
        ->and(Portabilis_Array_Utils::setAsIdValue([['id' => 2, 'nome' => 'Bia'], ['id' => 1, 'nome' => 'Ana']], 'id', 'nome'))->toBe([1 => 'Ana', 2 => 'Bia'])
        ->and(Portabilis_Array_Utils::setAsIdValue(collect(['id' => 3, 'nome' => 'Caio']), 'id', 'nome'))->toBe([3 => 'Caio'])
        ->and(Portabilis_Array_Utils::sortByKey('id', $pessoas + [2 => ['id' => 1, 'nome' => 'Bia']]))->toHaveCount(3)
        ->and(Portabilis_Array_Utils::sortByValue(['a' => 'Ana', 'b' => 'Ana', 'c' => 3, 'd' => 1]))->toBe(['d' => 1, 'c' => 3, 'a' => 'Ana', 'b' => 'Ana'])
        ->and(Portabilis_Array_Utils::arrayColumn($pessoas, 'nome'))->toBe(['Érica', 'Ana']);
});

test('os utilitários de objeto filtram atributos e montam pares de id e valor', function () {
    $ana = (object) ['id' => 1, 'nome' => 'Ana'];

    expect(Portabilis_Object_Utils::filterSet($ana, ['id', 'nome' => 'name']))->toBe([['id' => 1, 'name' => 'Ana']])
        ->and(Portabilis_Object_Utils::filter($ana, 'nome'))->toBe(['nome' => 'Ana'])
        ->and(Portabilis_Object_Utils::asIdValue($ana, 'id', 'nome'))->toBe([1 => 'Ana']);
});

test('o validador avisa e lança exceção para valores ausentes, não numéricos, fora da lista ou fora do banco', function () {
    $messenger = new Portabilis_Messenger;
    $validator = new Portabilis_Validator($messenger);
    $vazio = '';
    $texto = 'abc';
    $lista = 'x';
    $escola = LegacySchoolFactory::new()->create()->getKey();
    $inexistente = 0;
    $pessoas = [1];

    expect($validator->validatesPresenceOf($vazio, 'nome'))->toBeFalse()
        ->and(fn () => $validator->validatesPresenceOf($vazio, 'nome', true, 'Nome obrigatório'))->toThrow(CoreExt_Exception::class, 'Nome obrigatório')
        ->and($validator->validatesValueIsNumeric($texto, 'idade'))->toBeFalse()
        ->and(fn () => $validator->validatesValueIsNumeric($texto, 'idade', true))->toThrow(CoreExt_Exception::class)
        ->and($validator->validatesValueIsArray($texto, 'itens'))->toBeFalse()
        ->and(fn () => $validator->validatesValueIsArray($texto, 'itens', true))->toThrow(CoreExt_Exception::class)
        ->and($validator->validatesValueInSetOf($lista, ['a', 'b'], 'opcao'))->toBeFalse()
        ->and(fn () => $validator->validatesValueInSetOf($lista, ['a'], 'opcao', true))->toThrow(CoreExt_Exception::class)
        ->and($validator->validatesValueInSetOf($lista, ['x'], 'opcao'))->toBeTrue()
        ->and($validator->validatesValueIsNumeric($escola, 'escola'))->toBeTrue()
        ->and($validator->validatesValueIsArray($pessoas, 'pessoas'))->toBeTrue()
        ->and(fn () => $validator->validatesValueIsInBd('cod_escola', $inexistente, 'pmieducar', 'escola'))->toThrow(CoreExt_Exception::class)
        ->and($validator->validatesValueNotInBd('cod_escola', $inexistente, 'pmieducar', 'escola'))->toBeTrue()
        ->and($validator->validatesValueNotInBd('cod_escola', $escola, 'pmieducar', 'escola', false))->toBeFalse()
        ->and(fn () => $validator->validatesValueNotInBd('cod_escola', $escola, 'pmieducar', 'escola'))->toThrow(CoreExt_Exception::class, 'já existe no banco de dados')
        ->and($messenger->getMsgs())->not->toBeEmpty();
});

test('os utilitários de texto e de data separam, testam vazio, convertem e validam', function () {
    expect(Portabilis_String_Utils::split([';', ','], 'a , b'))->toBe(['a', 'b'])
        ->and(Portabilis_String_Utils::split(',', 'a, b', ['trim' => false]))->toBe(['a', ' b'])
        ->and(Portabilis_String_Utils::split(',', 'sem divisor'))->toBe(['sem divisor'])
        ->and(Portabilis_String_Utils::emptyOrNull(null))->toBeTrue()
        ->and(Portabilis_String_Utils::emptyOrNull('x'))->toBeFalse()
        ->and(Portabilis_Date_Utils::pgSQLToBr('2030-02-10 08:30:00'))->toBe('10/02/2030 08:30:00')
        ->and(Portabilis_Date_Utils::pgSQLToBr('2030-02-10 08:30:00.123456'))->toBe('10/02/2030 08:30:00')
        ->and(Portabilis_Date_Utils::pgSQLToBr_ddmm('2030-02-10'))->toBe('10/02')
        ->and(Portabilis_Date_Utils::pgSQLToBr_ddmm('data'))->toBeNull()
        ->and(Portabilis_Date_Utils::validaData('10/02/2030'))->toBeTrue()
        ->and(Portabilis_Date_Utils::validaData('2030-02-10'))->toBeFalse()
        ->and(Portabilis_Date_Utils::isDateValid('2030-02-10'))->toBeTrue();
});

test('os rótulos personalizados vêm da configuração da instituição ativa', function () {
    $instance = new ReflectionProperty(CustomLabel::class, 'instance');
    $instance->setValue(null, null);
    LegacyGeneralConfiguration::query()->update(['custom_labels' => null]);
    LegacyGeneralConfigurationFactory::new()->create(['custom_labels' => json_encode(['Aluno' => 'Estudante'])]);

    try {
        $labels = CustomLabel::getInstance();

        expect($labels)->toBe(CustomLabel::getInstance())
            ->and($labels->customize('Aluno'))->toBe('Estudante')
            ->and($labels->customize('Escola'))->toBe('Escola')
            ->and($labels->getDefaults())->toBe(['Aluno' => 'Estudante']);
    } finally {
        $instance->setValue(null, null);
    }
});

test('o e-mail, o reCAPTCHA, a API XML antiga e o locale', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    config(['legacy.app.recaptcha.public_key' => 'publica', 'legacy.app.recaptcha.options.lang' => 'pt-BR', 'legacy.app.recaptcha.private_key' => 'privada']);
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $email = new CoreExt_Validate_Email;

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    expect($email->isValid('ana@escola.br'))->toBeTrue()
        ->and(fn () => $email->isValid('ana'))->toThrow(Exception::class, 'Email inválido.')
        ->and(Portabilis_Utils_ReCaptcha::getWidget())->toContain('data-sitekey="publica"')->toContain('hl=pt-BR')
        ->and(Portabilis_Utils_ReCaptcha::check(''))->toBeFalse();

    $locale = CoreExt_Locale::getInstance();
    $locale->setCulture('xx_XX')->setLocale();

    expect($locale->getCulture())->toBe('xx_XX')
        ->and($locale->getCultureInfo('decimal_point'))->toBe('.')
        ->and($locale->getCultureInfo())->toBeArray();

    $locale->setCulture('en_US');
    $locale->resetLocale();

    // Sem o locale pedido instalado, volta para o C e tenta só o separador numérico.
    $semLocale = new class extends CoreExt_Locale
    {
        public array $tentativas = [];

        public function __construct() {}

        protected function _setLocale($category, array $locale)
        {
            $this->tentativas[] = $locale[0];

            return $locale === ['C'] ? 'C' : false;
        }
    };
    $semLocale->setLocale('xx_XX');

    expect($semLocale->actualCulture)->toBe(['LC_ALL' => 'C', 'LC_NUMERIC' => false])
        ->and($semLocale->tentativas)->toBe(['xx_XX.', 'C', 'xx_XX.UTF-8']);
});
