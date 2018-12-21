<?php

require_once 'CoreExt/Entity.php';
require_once 'App/Model/MatriculaSituacao.php';

class App_Model_Matricula
{
    /**
     * Atualiza os dados da matrícula do aluno, promovendo-o ou retendo-o. Usa
     * uma instância da classe legada clsPmieducarMatricula para tal.
     *
     * @param int  $matricula
     * @param int  $usuario
     * @param bool $aprovado
     *
     * @return bool
     */
    public static function atualizaMatricula($matricula, $usuario, $aprovado = true)
    {
        $instance = CoreExt_Entity::addClassToStorage(
            'clsPmieducarMatricula',
            null,
            'include/pmieducar/clsPmieducarMatricula.inc.php'
        );

        $instance->cod_matricula = $matricula;
        $instance->ref_usuario_cad = $usuario;
        $instance->ref_usuario_exc = $usuario;

        if (is_int($aprovado)) {
            $instance->aprovado = $aprovado;
        } else {
            $instance->aprovado = $aprovado == true
                ? App_Model_MatriculaSituacao::APROVADO
                : App_Model_MatriculaSituacao::REPROVADO;
        }

        return $instance->edita();
    }

    public static function setNovaSituacao($matricula, $novaSituacao)
    {
        $instance = CoreExt_Entity::addClassToStorage(
            'clsPmieducarMatricula',
            null,
            'include/pmieducar/clsPmieducarMatricula.inc.php'
        );

        $instance->cod_matricula = $matricula;
        $instance->aprovado = $novaSituacao;

        return $instance->edita();
    }
}
