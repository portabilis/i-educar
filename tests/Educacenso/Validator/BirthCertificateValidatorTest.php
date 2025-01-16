<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use Tests\TestCase;

class BirthCertificateValidatorTest extends TestCase
{
    public function test_wrong_length_number()
    {
        $number = '123';
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
    }

    public function test_x_digits_before31_digit()
    {
        $certificateYear = date('Y');
        $number = "1234567890{$certificateYear}5678901234567X9012";
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
    }

    public function test_x_digits_on31_digit()
    {
        $certificateYear = date('Y');
        $number = "1234567890{$certificateYear}5678901234567890X2";
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertTrue($validator->isValid());
    }

    public function test_forbidden_digits_before31_digit()
    {
        $certificateYear = date('Y');
        $number = "1234567890{$certificateYear}5678901234567Z9012";
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
    }

    public function test_forbidden_digit_on31_digit()
    {
        $certificateYear = date('Y');
        $number = "1234567890{$certificateYear}5678901234567890Z2";
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
    }

    public function test_certificate_year_before_birth_date()
    {
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $certificateYear = substr($birthDate, 0, 4) - 1;
        $number = "1234567890{$certificateYear}5678901234567890XX";

        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) foi preenchido com o ano inválido. O número localizado nas posições de 11 a 14, não pode ser anterior ao ano de nascimento e nem posterior ao ano corrente', $validator->getMessage());
    }

    public function test_certificate_year_after_today_year()
    {
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $certificateYear = date('Y') + 1;
        $number = "1234567890{$certificateYear}5678901234567890XX";

        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) foi preenchido com o ano inválido. O número localizado nas posições de 11 a 14, não pode ser anterior ao ano de nascimento e nem posterior ao ano corrente', $validator->getMessage());
    }

    public function test_certificate_year_at_same_year_of_birth_date()
    {
        $birthDate = date('Y-m-d', strtotime('-10 years'));
        $certificateYear = substr($birthDate, 0, 4);
        $number = "1234567890{$certificateYear}5678901234567890XX";

        $validator = new BirthCertificateValidator($number, $birthDate);

        $this->assertTrue($validator->isValid());
    }
}
