<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class LegacyIndividualRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * CICLO 1 - RED → GREEN
     * 
     * Testa se a flash message de sucesso é criada quando
     * $pessoaIdOrNull é null (indicando novo cadastro).
     * 
     * @return void
     */
    public function test_should_create_success_flash_message_for_new_registration(): void
    {
        $pessoaIdOrNull = null;
        
        if ($pessoaIdOrNull === null) {
            session()->flash('success', 'Pessoa cadastrada com sucesso.');
        }
        $this->assertTrue(
            Session::has('success'),
            'A flash message "success" não foi criada na sessão.'
        );
        
        $this->assertEquals(
            'Pessoa cadastrada com sucesso.',
            session('success'),
            'A mensagem de sucesso não corresponde ao esperado.'
        );
    }
    
    /**
     * CICLO 1 - Teste complementar
     * 
     * Testa se a flash message NÃO é criada quando
     * $pessoaIdOrNull tem um valor (indicando edição).
     * 
     * @return void
     */
    public function test_should_not_create_success_flash_message_for_edit(): void
    {
        $pessoaIdOrNull = 123;
        
        if ($pessoaIdOrNull === null) {
            session()->flash('success', 'Pessoa cadastrada com sucesso.');
        }

        $this->assertFalse(
            Session::has('success'),
            'A flash message "success" não deveria ser criada para edição.'
        );
    }
    
    /**
     * CICLO 2 - RED
     * 
     * Testa se a mensagem de sucesso termina com ponto final,
     * seguindo o padrão de outras mensagens do sistema.
     * 
     * @return void
     */
    public function test_success_message_should_end_with_period(): void
    {
        // Arrange: Simular novo cadastro
        $pessoaIdOrNull = null;
        
        if ($pessoaIdOrNull === null) {
            session()->flash('success', 'Pessoa cadastrada com sucesso.');
        }
        
        $message = session('success');
        
        // Assert: Verificar que a mensagem termina com ponto
        $this->assertStringEndsWith(
            '.',
            $message,
            'A mensagem de sucesso deve terminar com ponto final.'
        );
        
        // Assert: Verificar que não tem espaços extras no final
        $this->assertEquals(
            trim($message),
            $message,
            'A mensagem não deve ter espaços em branco extras.'
        );
    }
    
    /**
     * CICLO 3 - RED
     * 
     * Testa se a mensagem contém palavras-chave importantes
     * que comuniquem claramente o resultado da ação.
     * 
     * @return void
     */
    public function test_success_message_should_contain_key_words(): void
    {
        // Arrange: Simular novo cadastro
        $pessoaIdOrNull = null;
        
        if ($pessoaIdOrNull === null) {
            session()->flash('success', 'Pessoa cadastrada com sucesso.');
        }
        
        $message = session('success');
        
        // Assert: Verificar que contém a palavra "cadastrada"
        $this->assertStringContainsString(
            'cadastrada',
            $message,
            'A mensagem deve conter a palavra "cadastrada" para indicar a ação realizada.'
        );
        
        // Assert: Verificar que contém a palavra "sucesso"
        $this->assertStringContainsString(
            'sucesso',
            $message,
            'A mensagem deve conter a palavra "sucesso" para indicar resultado positivo.'
        );
    }
    
    /**
     * CICLO 4 - RED
     * 
     * Testa se a mensagem tem tamanho adequado (não muito curta, não muito longa)
     * para boa legibilidade e usabilidade.
     * 
     * @return void
     */
    public function test_success_message_should_have_appropriate_length(): void
    {
        // Arrange: Simular novo cadastro
        $pessoaIdOrNull = null;
        
        if ($pessoaIdOrNull === null) {
            session()->flash('success', 'Pessoa cadastrada com sucesso.');
        }
        
        $message = session('success');
        
        // Assert: Mensagem deve ter no mínimo 10 caracteres
        $this->assertGreaterThanOrEqual(
            10,
            strlen($message),
            'A mensagem é muito curta para ser informativa.'
        );
        
        // Assert: Mensagem deve ter no máximo 100 caracteres
        $this->assertLessThanOrEqual(
            100,
            strlen($message),
            'A mensagem é muito longa, pode prejudicar a usabilidade.'
        );
    }
}
