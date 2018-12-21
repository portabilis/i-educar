<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchPessoa extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = '
                select
                (
                    case
                        when fisica.nome_social not like \'\' then fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else pessoa.nome
                    end
                ) as nome
                from
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                and pessoa.idpes = $1
                and fisica.idpes = pessoa.idpes
            ';

            $options = ['params' => $id, 'return_only' => 'first-field'];
            $nome = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return Portabilis_String_Utils::toLatin1($nome, ['transform' => true, 'escape' => false]);
        }
    }

    public function simpleSearchPessoa($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'pessoa',
            'apiController' => 'Pessoa',
            'apiResource' => 'pessoa-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o nome, código, CPF ou RG da pessoa';
    }
}
