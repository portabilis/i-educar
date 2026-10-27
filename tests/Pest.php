<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\LoginFirstUser;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

// Os testes de browser passam por HTTP de verdade e precisam do app Laravel
// bootado. O DatabaseTransactions isola porque o servidor HTTP do Pest 4 é
// in-process: ele roda no mesmo processo do teste.
uses(
    TestCase::class,
    DatabaseTransactions::class,
)->in('Browser');

// A camada de requisição exercita as mesmas telas sem navegador: GET para abrir
// o formulário, POST para gravar. Nunca com WithoutMiddleware — é o
// ConvertEmptyStringsToNull que faz um campo vazio chegar ao legado como null.
uses(
    TestCase::class,
    DatabaseTransactions::class,
)->in('Request');

uses(
    TestCase::class,
    DatabaseTransactions::class,
    LoginFirstUser::class,
    WithoutMiddleware::class,
)->in('Pages');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
