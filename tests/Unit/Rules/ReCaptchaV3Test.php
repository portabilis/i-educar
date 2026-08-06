<?php

namespace Tests\Unit\Rules;

use App\Rules\ReCaptchaV3;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class ReCaptchaV3Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        request()->server->set('REMOTE_ADDR', '127.0.0.1');
    }

    public function test_passes_returns_true_when_recaptcha_is_not_configured(): void
    {
        config([
            'legacy.app.recaptcha_v3.public_key' => null,
            'legacy.app.recaptcha_v3.private_key' => null,
            'legacy.app.recaptcha_v3.minimum_score' => 0.5,
        ]);

        $client = $this->createMock('GuzzleHttp\\Client');
        $client->expects($this->never())
            ->method('post');

        $rule = new ReCaptchaV3($client);

        $this->assertTrue($rule->passes('grecaptcha', 'token'));
    }

    #[DataProvider('scoreProvider')]
    public function test_passes_validates_score_threshold(array $responsePayload, bool $expected): void
    {
        $this->configureRecaptcha();

        $client = $this->createMock('GuzzleHttp\\Client');
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
    }

    public function test_passes_returns_false_when_recaptcha_rejects_the_request(): void
    {
        $this->configureRecaptcha();

        $client = $this->createMock('GuzzleHttp\\Client');
        $exceptionClass = 'GuzzleHttp\\Exception\\BadResponseException';
        /** @var \Throwable $exception */
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
    }

    public function test_passes_returns_true_when_recaptcha_is_temporarily_unavailable(): void
    {
        $this->configureRecaptcha();

        $client = $this->createMock('GuzzleHttp\\Client');
        $client->expects($this->once())
            ->method('post')
            ->willThrowException(new RuntimeException('Timeout'));

        $rule = new ReCaptchaV3($client);

        $this->assertTrue($rule->passes('grecaptcha', 'token'));
    }

    public static function scoreProvider(): array
    {
        return [
            'score above threshold' => [['score' => 0.9], true],
            'score at threshold' => [['score' => 0.5], true],
            'score below threshold' => [['score' => 0.2], false],
            'missing score defaults to zero' => [[], false],
        ];
    }

    private function configureRecaptcha(): void
    {
        config([
            'legacy.app.recaptcha_v3.public_key' => 'public-key',
            'legacy.app.recaptcha_v3.private_key' => 'private-key',
            'legacy.app.recaptcha_v3.minimum_score' => 0.5,
        ]);
    }
}
