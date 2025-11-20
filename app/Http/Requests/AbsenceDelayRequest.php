<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest para validação de Falta/Atraso de Servidor
 *
 * Este Request implementa as regras de validação para os campos
 * do formulário de cadastro/edição de falta/atraso de servidor.
 *
 * Funcionalidades implementadas:
 * - Validação de valores não-negativos para horas e minutos
 * - Validação de campos obrigatórios
 * - Mensagens de erro personalizadas e amigáveis
 *
 * @package App\Http\Requests
 * @author Sistema I-Educar
 * @version 1.0.0
 */
class AbsenceDelayRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * Por padrão, retorna true. Pode ser customizado para incluir
     * lógica de autorização específica se necessário.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtém as regras de validação que devem ser aplicadas à requisição.
     *
     * Regras implementadas:
     * - tipo: obrigatório e deve ser inteiro
     * - qtd_horas: opcional, mas se fornecido deve ser inteiro >= 0
     * - qtd_min: opcional, mas se fornecido deve ser inteiro >= 0
     * - ref_cod_servidor_funcao: obrigatório e deve ser inteiro
     * - data_falta_atraso: obrigatório e deve estar no formato dd/mm/yyyy
     *
     * @return array<string, array<int, string|int>> Array associativo com as regras de validação
     */
    public function rules(): array
    {
        return [
            'tipo' => ['required', 'integer'],
            'qtd_horas' => ['nullable', 'integer', 'min:0'],
            'qtd_min' => ['nullable', 'integer', 'min:0'],
            'ref_cod_servidor_funcao' => ['required', 'integer'],
            'data_falta_atraso' => ['required', 'date_format:d/m/Y'],
        ];
    }

    /**
     * Obtém as mensagens de validação personalizadas.
     *
     * Fornece mensagens de erro claras e em português para melhorar
     * a experiência do usuário ao identificar problemas de validação.
     *
     * @return array<string, string> Array associativo com as mensagens personalizadas
     */
    public function messages(): array
    {
        return [
            // Mensagens para validação de horas
            'qtd_horas.integer' => 'O campo Quantidade de Horas deve ser um número inteiro.',
            'qtd_horas.min' => 'O campo Quantidade de Horas não pode ser negativo.',

            // Mensagens para validação de minutos
            'qtd_min.integer' => 'O campo Quantidade de Minutos deve ser um número inteiro.',
            'qtd_min.min' => 'O campo Quantidade de Minutos não pode ser negativo.',

            // Mensagens para outros campos obrigatórios
            'tipo.required' => 'O campo Tipo é obrigatório.',
            'data_falta_atraso.required' => 'O campo Dia é obrigatório.',
            'data_falta_atraso.date_format' => 'O campo Dia deve estar no formato dia/mês/ano (ex: 01/01/2024).',
            'ref_cod_servidor_funcao.required' => 'O campo Função é obrigatório.',
        ];
    }

    /**
     * Obtém os nomes personalizados dos atributos para mensagens de validação.
     *
     * Define nomes amigáveis para os campos que serão usados nas
     * mensagens de validação automáticas do Laravel.
     *
     * @return array<string, string> Array associativo com os nomes dos atributos
     */
    public function attributes(): array
    {
        return [
            'qtd_horas' => 'Quantidade de Horas',
            'qtd_min' => 'Quantidade de Minutos',
            'tipo' => 'Tipo',
            'data_falta_atraso' => 'Dia',
            'ref_cod_servidor_funcao' => 'Função',
        ];
    }
}
