<?php

namespace App\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Validation\Rule;
use Throwable;

class ReCaptchaV3 implements Rule
{
    /**
     * @var mixed
     */
    private $client;

    public function __construct($client = null)
    {
        $this->client = $client ?? app(Client::class);
    }

    /**
     * Create a new rule instance.
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$this->checkConfig()) {
            return true;
        }

        try {
            $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
                'query' => [
                    'secret' => config('legacy.app.recaptcha_v3.private_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ],
            ]);

            return $this->getScore($response) >= config('legacy.app.recaptcha_v3.minimum_score');
        } catch (BadResponseException $e) {
            return false;
        } catch (Throwable $e) {
            return true;
        }
    }

    /**
     * Retorna o score do recaptcha
     *
     *
     * @return float
     */
    private function getScore($response)
    {
        return json_decode($response->getBody()->getContents(), true)['score'] ?? 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A verificação do reCAPTCHA falhou';
    }

    /**
     * Verifica se as configurações do recaptcha estão presentes
     *
     * @return bool
     */
    private function checkConfig()
    {
        return config('legacy.app.recaptcha_v3.public_key') && config('legacy.app.recaptcha_v3.private_key');
    }
}
