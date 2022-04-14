<?php

class Portabilis_Utils_User
{
    public static $_currentUserId;
    public static $_nivelAcesso;
    public static $_permissoes;

    public static function currentUserId()
    {
        if (empty(self::$_currentUserId)) {
            self::$_currentUserId = \Illuminate\Support\Facades\Auth::id();
        }

        return self::$_currentUserId;
    }

    public static function loggedIn()
    {
        return is_numeric(self::currentUserId());
    }

    public static function getClsPermissoes()
    {
        if (!isset(self::$_permissoes)) {
            self::$_permissoes = new clsPermissoes();
        }

        return self::$_permissoes;
    }

    public static function getNivelAcesso()
    {
        if (!isset(self::$_nivelAcesso)) {
            self::$_nivelAcesso = self::getClsPermissoes()->nivel_acesso(self::currentUserId());
        }

        return self::$_nivelAcesso;
    }

    public static function hasNivelAcesso($nivelAcessoType)
    {
        $niveisAcesso = [
            'POLI_INSTITUCIONAL' => 1,
            'INSTITUCIONAL' => 2,
            'SOMENTE_ESCOLA' => 4,
            'SOMENTE_BIBLIOTECA' => 8
        ];

        if (!isset($niveisAcesso[$nivelAcessoType])) {
            throw new CoreExt_Exception("Nivel acesso '$nivelAcessoType' não definido.");
        }

        return self::getNivelAcesso() == $niveisAcesso[$nivelAcessoType];
    }

    public static function canAccessEscola($id)
    {
        if (self::hasNivelAcesso('POLI_INSTITUCIONAL') || self::hasNivelAcesso('INSTITUCIONAL')) {
            return true;
        }

        $escolas = App_Model_IedFinder::getEscolasUser(self::currentUserId());

        foreach ($escolas as $escola) {
            if ($escola['ref_cod_escola'] == $id) {
                return true;
            }
        }

        return false;
    }
}
