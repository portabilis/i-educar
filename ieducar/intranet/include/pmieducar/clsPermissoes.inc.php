<?php

require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsMenuFuncionario.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaUsuario.inc.php';
require_once 'lib/Portabilis/Array/Utils.php';

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

class clsPermissoes
{
    /**
     * Verifica se um usuário tem permissão para cadastrar baseado em um
     * identificador de processo.
     *
     * @param int $int_processo_ap Identificador de processo
     * @param int $int_idpes_usuario Identificador do usuário
     * @param int $int_soma_nivel_acesso
     * @param string $str_pagina_redirecionar Caminho para o qual a requisição será encaminhada caso o usuário não tenha privilégios suficientes para a operação de cadastro
     * @param bool $super_usuario TRUE para verificar se o usuário é super usuário
     * @param bool $int_verifica_usuario_biblioteca TRUE para verificar se o usuário possui cadastro em alguma biblioteca
     *
     * @return bool|void
     */
    public function permissao_cadastra(
        $int_processo_ap,
        $int_idpes_usuario,
        $int_soma_nivel_acesso,
        $str_pagina_redirecionar = null,
        $super_usuario = null,
        $int_verifica_usuario_biblioteca = false
    ) {
        $obj_usuario = new clsFuncionario($int_idpes_usuario);
        $detalhe_usuario = $obj_usuario->detalhe();

        // Verifica se é super usuário
        if ($detalhe_usuario['ativo']) {
            $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario, false, false, 0);
            $detalhe_super_usuario = $obj_menu_funcionario->detalhe();
        }

        if (!$detalhe_super_usuario) {
            $obj_menu_tipo_usuario = new clsPmieducarMenuTipoUsuario();
            $detalhe = $obj_menu_tipo_usuario->detalhePorUsuario($int_idpes_usuario, $int_processo_ap);
        }

        $nivel = $this->nivel_acesso($int_idpes_usuario);
        $ok = false;

        // Alterado, pois super_usuário sempre deve ter acesso a tudo
        if (($detalhe_super_usuario) || $nivel && $int_soma_nivel_acesso) {
            $ok = true;
        }

        if (!$detalhe_super_usuario && !$detalhe['cadastra']) {
            $ok = false;
        }

        // Se for usuario tipo biblioteca ou escola
        // ($int_verifica_usuario_biblioteca = true), verifica se possui
        // cadastro na tabela usuario biblioteca
        if (
            (
                $nivel == 8
                || ($nivel == 4 && $int_verifica_usuario_biblioteca == true)
            )
            && $int_soma_nivel_acesso > 3
            && !$detalhe_super_usuario
        ) {
            $ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? false : true;

            if (!$ok && $nivel == 8) {
                throw new HttpResponseException(
                    new RedirectResponse('index.php?negado=1')
                );
            }
        }

        if (!$ok) {
            if ($str_pagina_redirecionar) {
                throw new HttpResponseException(
                    new RedirectResponse($str_pagina_redirecionar)
                );
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica se um usuário tem permissão para cadastrar baseado em um
     * identificador de processo.
     *
     * @param int $int_processo_ap Identificador de processo
     * @param int $int_idpes_usuario Identificador do usuário
     * @param int $int_soma_nivel_acesso
     * @param string $str_pagina_redirecionar Caminho para o qual a requisição será encaminhada caso o usuário não tenha privilégios suficientes para a operação de cadastro
     * @param bool $super_usuario TRUE para verificar se o usuário é super usuário
     * @param bool $int_verifica_usuario_biblioteca TRUE para verificar se o usuário possui cadastro em alguma biblioteca
     *
     * @return bool|void
     */
    public function permissao_excluir(
        $int_processo_ap,
        $int_idpes_usuario,
        $int_soma_nivel_acesso,
        $str_pagina_redirecionar = null,
        $super_usuario = null,
        $int_verifica_usuario_biblioteca = false
    ) {
        $obj_usuario = new clsFuncionario($int_idpes_usuario);
        $detalhe_usuario = $obj_usuario->detalhe();

        // Verifica se é super usuário
        if ($super_usuario != null && $detalhe_usuario['ativo']) {
            $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario, false, false, 0);
            $detalhe_super_usuario = $obj_menu_funcionario->detalhe();
        }

        if (!$detalhe_super_usuario) {
            $obj_menu_tipo_usuario = new clsPmieducarMenuTipoUsuario();
            $detalhe = $obj_menu_tipo_usuario->detalhePorUsuario($int_idpes_usuario, $int_processo_ap);
        }

        $nivel = $this->nivel_acesso($int_idpes_usuario);
        $ok = false;

        if (($super_usuario && $detalhe_super_usuario) || $nivel & $int_soma_nivel_acesso) {
            $ok = true;
        }

        if ((!$detalhe['exclui'] && !$detalhe_super_usuario)) {
            $ok = false;
        }

        /*
         * Se for usuario tipo biblioteca ou escola
         * ($int_verifica_usuario_biblioteca = true), verifica se possui cadastro na
         * tabela usuario biblioteca
         */
        if (
            (
                $nivel == 8
                || ($nivel == 4 && $int_verifica_usuario_biblioteca == true)
            ) && $int_soma_nivel_acesso > 3
            && !$detalhe_super_usuario
        ) {
            $ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? false : true;

            if (!$ok && $nivel == 8) {
                throw new HttpResponseException(
                    new RedirectResponse('index.php?negado=1')
                );
            }
        }

        if (!$ok) {
            if ($str_pagina_redirecionar) {
                throw new HttpResponseException(
                    new RedirectResponse($str_pagina_redirecionar)
                );
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Retorna o nível de acesso do usuário, podendo ser:
     *
     * - 1: Poli-institucional
     * - 2: Institucional
     * - 4: Escola
     * - 8: Biblioteca
     *
     * @param int $int_idpes_usuario
     *
     * @return bool|int Retorna FALSE caso o usuário não exista
     */
    public function nivel_acesso($int_idpes_usuario)
    {
        $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
        $detalhe_usuario = $obj_usuario->detalhe();

        if ($detalhe_usuario) {
            $obj_tipo_usuario = new clsPmieducarTipoUsuario($detalhe_usuario['ref_cod_tipo_usuario']);
            $detalhe_tipo_usuario = $obj_tipo_usuario->detalhe();

            return $detalhe_tipo_usuario['nivel'];
        }

        return false;
    }

    /**
     * Retorna o código identificador da instituição ao qual o usuário está
     * vinculado.
     *
     * @param int $int_idpes_usuario
     *
     * @return bool|int Retorna FALSE caso o usuário não exista
     */
    public function getInstituicao($int_idpes_usuario)
    {
        $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
        $detalhe_usuario = $obj_usuario->detalhe();

        if ($detalhe_usuario) {
            return $detalhe_usuario['ref_cod_instituicao'];
        }

        return false;
    }

    /**
     * Retorna o código identificador da escola ao qual o usuário está vinculado.
     *
     * @param int $int_idpes_usuario
     *
     * @return bool|int Retorna FALSE caso o usuário não exista
     */
    public function getEscola($int_idpes_usuario)
    {
        $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
        $detalhe_usuario = $obj_usuario->detalhe();

        if ($detalhe_usuario) {
            return $detalhe_usuario['ref_cod_escola'] ?? false;
        }

        return false;
    }

    /**
     * Retorna lista de código identificador da escola ao qual o usuário está vinculado.
     *
     * @param int $int_idpes_usuario
     *
     * @return bool|array Retorna FALSE caso o usuário não exista
     */
    public function getEscolas($int_idpes_usuario)
    {
        $objEscolaUsuario = new clsPmieducarEscolaUsuario();
        $escolas = $objEscolaUsuario->lista($int_idpes_usuario);

        if (!empty($escolas)) {
            return Portabilis_Array_Utils::arrayColumn($escolas, 'ref_cod_escola');
        }

        return false;
    }

    /**
     * Retorna um array associativo com os códigos identificadores da escola e
     * da instituição ao qual o usuário está vinculado.
     *
     * @param $int_idpes_usuario
     *
     * @return array|bool Retorna FALSE caso o usuário não exista
     */
    public function getInstituicaoEscola($int_idpes_usuario)
    {
        $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
        $detalhe_usuario = $obj_usuario->detalhe();

        if ($detalhe_usuario) {
            return [
                'instituicao' => $detalhe_usuario['ref_cod_instituicao'],
                'escola' => $detalhe_usuario['ref_cod_escola']
            ];
        }

        return false;
    }

    /**
     * Retorna um array com os códigos identificadores das bibliotecas aos quais
     * o usuário está vinculado.
     *
     * @param int $int_idpes_usuario
     *
     * @return array|int Retorna o inteiro "0" caso o usuário não esteja vinculado
     *                   a uma biblioteca
     */
    public function getBiblioteca($int_idpes_usuario)
    {
        $obj_usuario = new clsPmieducarBibliotecaUsuario();
        $lst_usuario_biblioteca = $obj_usuario->lista(null, $int_idpes_usuario);

        if ($lst_usuario_biblioteca) {
            return $lst_usuario_biblioteca;
        } else {
            return 0;
        }
    }
}
