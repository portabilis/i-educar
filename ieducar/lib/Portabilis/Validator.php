<?php

class Portabilis_Validator
{
    public function __construct(&$messenger)
    {
        $this->messenger = $messenger;
    }

    public function validatesPresenceOf(&$value, $name, $raiseExceptionOnFail = false, $msg = '', $addMsgOnEmpty = true)
    {
        if (!isset($value) || (empty($value) && !is_numeric($value))) {
            if ($addMsgOnEmpty) {
                $msg = empty($msg) ? "É necessário receber uma variavel '$name'" : $msg;
                $this->messenger->append($msg);
            }

            if ($raiseExceptionOnFail) {
                throw new CoreExt_Exception($msg);
            }

            return false;
        }

        return true;
    }

    public function validatesValueIsNumeric(&$value, $name, $raiseExceptionOnFail = false, $msg = '', $addMsgOnError = true)
    {
        if (!is_numeric($value)) {
            if ($addMsgOnError) {
                $msg = empty($msg) ? "O valor recebido para variavel '$name' deve ser numerico" : $msg;
                $this->messenger->append($msg);
            }

            if ($raiseExceptionOnFail) {
                throw new CoreExt_Exception($msg);
            }

            return false;
        }

        return true;
    }

    public function validatesValueIsArray(&$value, $name, $raiseExceptionOnFail = false, $msg = '', $addMsgOnError = true)
    {
        if (!is_array($value)) {
            if ($addMsgOnError) {
                $msg = empty($msg) ? "Deve ser recebido uma lista de '$name'" : $msg;
                $this->messenger->append($msg);
            }

            if ($raiseExceptionOnFail) {
                throw new CoreExt_Exception($msg);
            }

            return false;
        }

        return true;
    }

    public function validatesValueInSetOf(&$value, $setExpectedValues, $name, $raiseExceptionOnFail = false, $msg = '')
    {
        if (!empty($setExpectedValues) && !in_array($value, $setExpectedValues)) {
            $msg = empty($msg) ? "Valor recebido na variavel '$name' é invalido" : $msg;
            $this->messenger->append($msg);

            if ($raiseExceptionOnFail) {
                throw new CoreExt_Exception($msg);
            }

            return false;
        }

        return true;
    }

    public function validatesValueIsInBd($fieldName, &$value, $schemaName, $tableName, $raiseExceptionOnFail = true, $addMsgOnError = true)
    {
        $sql = "select 1 from $schemaName.$tableName where $fieldName = $1 limit 1";

        if (Portabilis_Utils_Database::selectField($sql, $value) != 1) {
            if ($addMsgOnError) {
                $msg = "O valor informado {$value} para $tableName, não esta presente no banco de dados.";
                $this->messenger->append($msg);
            }

            if ($raiseExceptionOnFail) {
                throw new CoreExt_Exception($msg);
            }

            return false;
        }

        return true;
    }

    public function validatesValueNotInBd($fieldName, &$value, $schemaName, $tableName, $raiseExceptionOnFail = true, $addMsgOnError = true)
    {
        $sql = "select 1 from $schemaName.$tableName where $fieldName = $1 limit 1";

        if (Portabilis_Utils_Database::selectField($sql, $value) == 1) {
            if ($addMsgOnError) {
                $msg = "O valor informado {$value} para $tableName já existe no banco de dados.";
                $this->messenger->append($msg);
            }

            if ($raiseExceptionOnFail) {
                throw new CoreExt_Exception($msg);
            }

            return false;
        }

        return true;
    }
}
