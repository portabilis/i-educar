<?php

use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use Tests\TestCase;

uses(TestCase::class);

test('wrong length number', function () {
    $number = '123';
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('x digits before31 digit', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}5678901234567X9012";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('x digits on31 digit', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}5678901234567890X2";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('double x on verifying digits', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}5678901234567890XX";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertTrue($validator->isValid());
});

test('numeric verifying digits', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}567890123456789012";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertTrue($validator->isValid());
});

test('comma on verifying digits', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}56789012345678901,";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('space on verifying digits', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}5678901234567890X ";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('lowercase x on verifying digits', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}5678901234567890xx";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('digits validation with short number', function () {
    $number = '12345678901234567890';
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->validateCertificateDigits());
});

test('forbidden digits before31 digit', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}5678901234567Z9012";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('forbidden digit on31 digit', function () {
    $certificateYear = date('Y');
    $number = "1234567890{$certificateYear}5678901234567890Z2";
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) possui valor inválido', $validator->getMessage());
});

test('certificate year before birth date', function () {
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $certificateYear = substr($birthDate, 0, 4) - 1;
    $number = "1234567890{$certificateYear}5678901234567890XX";

    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) foi preenchido com o ano inválido. O número localizado nas posições de 11 a 14, não pode ser anterior ao ano de nascimento e nem posterior ao ano corrente', $validator->getMessage());
});

test('certificate year after today year', function () {
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $certificateYear = date('Y') + 1;
    $number = "1234567890{$certificateYear}5678901234567890XX";

    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Tipo certidão civil (novo formato) foi preenchido com o ano inválido. O número localizado nas posições de 11 a 14, não pode ser anterior ao ano de nascimento e nem posterior ao ano corrente', $validator->getMessage());
});

test('certificate year at same year of birth date', function () {
    $birthDate = date('Y-m-d', strtotime('-10 years'));
    $certificateYear = substr($birthDate, 0, 4);
    $number = "1234567890{$certificateYear}5678901234567890XX";

    $validator = new BirthCertificateValidator($number, $birthDate);

    $this->assertTrue($validator->isValid());
});
