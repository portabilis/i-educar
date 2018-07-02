<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'lib/Portabilis/Utils/User.php';

/**
 * Portabilis_Utils_Validation class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Utils_Validation {

  public static function validatesCpf($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);

    if (strlen($cpf) != 11)
      return false;

    $cpfsInvalidos = array(
      '00000000000',
      '11111111111',
      '22222222222',
      '33333333333',
      '44444444444',
      '55555555555',
      '66666666666',
      '77777777777',
      '88888888888',
      '99999999999'
    );

    if (in_array($cpf, $cpfsInvalidos)) {
      return false;
    }

    // calcula primeiro dígito verificador
    $soma = 0;

    for ($i = 0; $i < 9; $i++)
      $soma += ((10 - $i) * $cpf[$i]);

    $primeiroDigito = 11 - ($soma % 11);

    if ($primeiroDigito >= 10)
      $primeiroDigito = 0;


    // calcula segundo dígito verificador
    $soma = 0;

    for ($i = 0; $i < 10; $i++)
      $soma += ((11 - $i) * $cpf[$i]);

    $segundoDigito = 11 - ($soma % 11);

    if ($segundoDigito >= 10)
      $segundoDigito = 0;



    return ($primeiroDigito == $cpf[9] && $segundoDigito == $cpf[10]);
  }

  /**
   * Valida os novos formatos de Certidão Civil.
   *
   * @author Thieres Tembra <tdt@mytdt.com.br>
   *
   * @param  string  $certidao Certidão a ser validada.
   * @param  bool  $motivo optional Se deseja retornar o motivo em string.
   * @return bool|string Se !$motivo retorna true/false.
   *                     Se $motivo retorna uma string com o motivo da falha ou uma string vazia (sucesso).
   */
  public static function validatesCertidaoNovoFormato($certidao, $motivo = false) {
    // invalida se não tiver 32 caracteres
    if (strlen($certidao) !== 32) {
        if ($motivo) {
            return 'Tamanho inválido';
        }
        return false;
    }

    // converte para maiúsculas
    $certidao = mb_strtoupper($certidao);

    // obtém primeiros 30 dígitos
    $numeros = substr($certidao, 0, 30);

    // obtém 1º e 2º digito verificador
    $primeiroDigito = substr($certidao, 30, 1);
    $segundoDigito = substr($certidao, 31, 1);

    /**
     * se após retirar todos os dígitos dos primeiros 30 caracteres ainda sobrar alguma coisa
     * ou se após retirar todos os dígitos e caractere X dos 1º e 2º dígito, ainda sobrar alguma coisa
     * invalida, pois existem caracteres inválidos (aceita somente dígitos e X nos últimos 2 caracteres)
     */
    if (strlen(preg_replace('/\d/', '', $numeros)) !== 0
        || strlen(preg_replace('/\d|X/', '', $primeiroDigito . $segundoDigito)) !== 0) {
        if ($motivo) {
            return 'Caractere(s) inválido(s)';
        }
        return false;
    }

    // define iterações que irá realizar
    $iteracoes = [
        [
            'numeros' => $numeros,
            'digito' => $primeiroDigito,
        ],
        [
            'numeros' => $numeros . $primeiroDigito,
            'digito' => $segundoDigito,
        ],
    ];

    foreach ($iteracoes as $iteracao) {
        // reseta a soma
        $soma = 0;

        // define multiplicador inicial
        $multiplicador = 9;

        // calcula módulo 11
        for ($i = strlen($iteracao['numeros']) - 1; $i >= 0; $i--) {
            $soma += $iteracao['numeros'][$i] * $multiplicador--;
            if ($multiplicador === -1) {
                $multiplicador = 10;
            }
        }
        $mod = $soma % 11;

        /**
         * invalida nos seguintes casos:
         *   - se o módulo 11 não for 10 e o dígito for diferente do valor do módulo 11
         *   - se o módulo 11 for 10 e o dígito não for 1 ou X
         */
        if (($mod !== 10 && intval($iteracao['digito']) !== $mod)
            || ($mod === 10 && !in_array($iteracao['digito'], ['1', 'X']) )) {
            if ($motivo) {
                return 'Valor inválido';
            }
            return false;
        }
    }

    // se passou por tudo finalmente retorna sucesso
    if ($motivo) {
        return '';
    }
    return true;
  }
}
