<?php

use App\Rules\ReCaptchaV3;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    request()->server->set('REMOTE_ADDR', '127.0.0.1');

    $this->configureRecaptcha = function (): void {
        config([
            'legacy.app.recaptcha_v3.public_key' => 'public-key',
            'legacy.app.recaptcha_v3.private_key' => 'private-key',
            'legacy.app.recaptcha_v3.minimum_score' => 0.5,
        ]);
    };
});

test('passes returns true when recaptcha is not configured', function () {
    config([
        'legacy.app.recaptcha_v3.public_key' => null,
        'legacy.app.recaptcha_v3.private_key' => null,
        'legacy.app.recaptcha_v3.minimum_score' => 0.5,
    ]);

    $client = $this->createMock(Client::class);
    $client->expects($this->never())
        ->method('post');

    $rule = new ReCaptchaV3($client);

    $this->assertTrue($rule->passes('grecaptcha', 'token'));
});

test('passes validates score threshold', function (array $responsePayload, bool $expected) {
    ($this->configureRecaptcha)();

    $client = $this->createMock(Client::class);
    $client->expects($this->once())
        ->method('post')
        ->with(
            'https://www.google.com/recaptcha/api/siteverify',
            $this->callback(function (array $options): bool {
                return $options['query']['secret'] === 'private-key'
                    && $options['query']['response'] === 'token'
                    && array_key_exists('remoteip', $options['query']);
            })
        )
        ->willReturn(new Response(200, [], json_encode($responsePayload)));

    $rule = new ReCaptchaV3($client);

    $this->assertSame($expected, $rule->passes('grecaptcha', 'token'));
})->with([
    'score above threshold' => [['score' => 0.9], true],
    'score at threshold' => [['score' => 0.5], true],
    'score below threshold' => [['score' => 0.2], false],
    'missing score defaults to zero' => [[], false],
]);

test('passes returns false when recaptcha rejects the request', function () {
    ($this->configureRecaptcha)();

    $client = $this->createMock(Client::class);
    $exceptionClass = 'GuzzleHttp\\Exception\\BadResponseException';
    /** @var Throwable $exception */
    $exception = new $exceptionClass(
        'Invalid response',
        new Request('POST', 'https://www.google.com/recaptcha/api/siteverify'),
        new Response(400)
    );

    $client->expects($this->once())
        ->method('post')
        ->willThrowException($exception);

    $rule = new ReCaptchaV3($client);

    $this->assertFalse($rule->passes('grecaptcha', 'token'));
});

test('passes returns true when recaptcha is temporarily unavailable', function () {
    ($this->configureRecaptcha)();

    $client = $this->createMock(Client::class);
    $client->expects($this->once())
        ->method('post')
        ->willThrowException(new RuntimeException('Timeout'));

    $rule = new ReCaptchaV3($client);

    $this->assertTrue($rule->passes('grecaptcha', 'token'));
});

test('a mensagem de erro fala da verificacao do recaptcha', function () {
    $this->assertSame('A verificação do reCAPTCHA falhou', (new ReCaptchaV3)->message());
});

test('mensagem de falha', function () {
    $this->assertSame('A verificação do reCAPTCHA falhou', (new ReCaptchaV3)->message());
});
