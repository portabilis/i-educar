<?php

namespace App\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Validation\Rule;

class ReCaptchaV3 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function passes($attribute, $value)
    {
        $client = new Client();

        try {
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'query' => [
                    'secret' => config('legacy.app.recaptcha_v3.private_key'),
                    'response' => $value,
                    'remoteip' => null,//request()->ip(),
                ],
            ]);
        } catch (BadResponseException $e) {
            return false;
        }

        return $this->getScore($response) >= 1;
    }

    private function getScore($response)
    {
        return json_decode($response->getBody()->getContents(), true)['score'];
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
}
