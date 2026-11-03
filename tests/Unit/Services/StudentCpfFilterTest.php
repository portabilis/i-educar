<?php

use App\Services\StudentCpfFilter;

// ---------- Ciclo 1 ----------

it('test_bloqueia_a_busca_quando_o_cpf_e_invalido', function () {
    $esperado = ['cpf' => null, 'error' => 'CPF inválido.', 'blocked' => true];

    expect(StudentCpfFilter::resolve('123'))->toBe($esperado);
    expect(StudentCpfFilter::resolve('111.111.111-11'))->toBe($esperado);
    expect(StudentCpfFilter::resolve('529.982.247-24'))->toBe($esperado);
});

// ---------- Ciclo 2 ----------

it('test_libera_a_busca_quando_o_cpf_e_valido_com_ou_sem_mascara', function () {
    expect(StudentCpfFilter::resolve('529.982.247-25'))
        ->toBe(['cpf' => '52998224725', 'error' => null, 'blocked' => false]);

    expect(StudentCpfFilter::resolve('11144477735'))
        ->toBe(['cpf' => '11144477735', 'error' => null, 'blocked' => false]);
});

it('test_nao_bloqueia_a_busca_quando_o_cpf_nao_e_informado', function () {
    $esperado = ['cpf' => null, 'error' => null, 'blocked' => false];

    expect(StudentCpfFilter::resolve(''))->toBe($esperado);
    expect(StudentCpfFilter::resolve(null))->toBe($esperado);
});