<?php

use iEducar\Support\Exceptions\Exception as iEducarException;

require_once 'include/clsBanco.inc.php';
require_once 'Core/Controller/Page/EditController.php';
require_once 'CoreExt/Exception.php';
require_once 'lib/Portabilis/Messenger.php';
require_once 'lib/Portabilis/Validator.php';
require_once 'lib/Portabilis/DataMapper/Utils.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/User.php';
require_once 'lib/Utils/SafeJson.php';

class ApiCoreController extends Core_Controller_Page_EditController
{
    // variaveis usadas apenas em formulários, desnecesário subescrever nos filhos.

    protected $_saveOption = false;

    protected $_deleteOption = false;

    protected $_titulo = '';

    // adicionar classe do data mapper que se deseja usar, em tais casos.

    protected $_dataMapper = null;

    // Variaveis utilizadas pelos validadores validatesAuthorizationToDestroy
    // e validatesAuthorizationToChange.
    // Notar que todos usuários tem autorização para o processo 0, nos
    // controladores em que se deseja verificar permissões, adicionar o
    // processo AP da funcionalidade.

    protected $_processoAp = 0;

    protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;

    public function __construct()
    {
        $this->messenger = new Portabilis_Messenger();
        $this->validator = new Portabilis_Validator($this->messenger);
        $this->response = [];
    }

    protected function getNivelAcesso()
    {
        return Portabilis_Utils_User::getNivelAcesso();
    }

    protected function validatesAccessKey()
    {
        $valid = false;

        if (!is_null($this->getRequest()->access_key)) {
            $accessKey = config('legacy.apis.access_key');
            $valid = $accessKey == $this->getRequest()->access_key;

            if (!$valid) {
                $this->messenger->append('Chave de acesso inválida!');
            }
        }

        return $valid;
    }

    protected function validatesSignature()
    {
        // #TODO implementar validação urls assinadas
        return true;
    }

    protected function validatesUserIsLoggedIn()
    {
        $canAccess = is_numeric($this->getSession()->id_pessoa);

        if (!$canAccess) {
            $canAccess = ($this->validatesAccessKey() && $this->validatesSignature());
        }

        if (!$canAccess) {
            $msg = 'Usuário deve estar logado ou a chave de acesso deve ser enviada!';
            $this->messenger->append($msg, 'error', $encodeToUtf8 = false, $ignoreIfHasMsgWithType = 'error');
        }

        return $canAccess;
    }

    protected function validatesId($resourceName, $options = [])
    {
        $attrName = $resourceName . ($resourceName ? '_id' : 'id');

        return $this->validatesPresenceOf($attrName) &&
            $this->validatesExistenceOf($resourceName, $this->getRequest()->$attrName, $options);
    }

    protected function validatesResourceId()
    {
        return $this->validatesPresenceOf('id')
            && $this->validatesExistenceOf($this->getRequest()->resource, $this->getRequest()->id);
    }

    protected function validatesAuthorizationToDestroy()
    {
        $can = $this->getClsPermissoes()->permissao_excluir(
            $this->getBaseProcessoAp(),
            $this->getSession()->id_pessoa,
            $this->_nivelAcessoOption
        );

        if (!$can) {
            $this->messenger->append("Usuário sem permissão para excluir '{$this->getRequest()->resource}'.");
        }

        return $can;
    }

    protected function validatesAuthorizationToChange()
    {
        $can = $this->getClsPermissoes()->permissao_cadastra(
            $this->getBaseProcessoAp(),
            $this->getSession()->id_pessoa,
            $this->_nivelAcessoOption
        );

        if (!$can) {
            $this->messenger->append("Usuário sem permissão para cadastrar '{$this->getRequest()->resource}'.");
        }

        return $can;
    }

    protected function canAcceptRequest()
    {
        return $this->validatesUserIsLoggedIn()
            && $this->validatesPresenceOf(['oper', 'resource']);
    }

    protected function canGet()
    {
        return $this->validatesResourceId();
    }

    protected function canChange()
    {
        throw new Exception('canChange must be overwritten!');
    }

    protected function canPost()
    {
        return $this->canChange()
            && $this->validatesAuthorizationToChange();
    }

    protected function canPut()
    {
        return $this->canChange()
            && $this->validatesResourceId()
            && $this->validatesAuthorizationToChange();
    }

    protected function canSearch()
    {
        return $this->validatesPresenceOf('query');
    }

    protected function canDelete()
    {
        return $this->validatesResourceId()
            && $this->validatesAuthorizationToDestroy();
    }

    protected function canEnable()
    {
        return $this->validatesResourceId()
            && $this->validatesAuthorizationToChange();
    }

    protected function notImplementedOperationError()
    {
        $this->messenger->append("Operação '{$this->getRequest()->oper}' não implementada para o recurso '{$this->getRequest()->resource}'");
    }

    protected function appendResponse($name, $value = '')
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->response[$k] = $v;
            }
        } elseif (!is_null($name)) {
            $this->response[$name] = $value;
        }
    }

    protected function getAvailableOperationsForResources()
    {
        throw new CoreExt_Exception('É necessário sobrescrever o método "getExpectedOperationsForResources()" de ApiCoreController.');
    }

    protected function isRequestFor($oper, $resource)
    {
        return $this->getRequest()->resource == $resource &&
            $this->getRequest()->oper == $oper;
    }

    protected function prepareResponse()
    {
        try {
            if (isset($this->getRequest()->oper)) {
                $this->appendResponse('oper', $this->getRequest()->oper);
            }

            if (isset($this->getRequest()->resource)) {
                $this->appendResponse('resource', $this->getRequest()->resource);
            }

            $this->appendResponse('msgs', $this->messenger->getMsgs());
            $this->appendResponse('any_error_msg', $this->messenger->hasMsgWithType('error'));

            $response = SafeJson::encode($this->response);
        } catch (Exception $e) {
            error_log("Erro inesperado no metodo prepareResponse da classe ApiCoreController: {$e->getMessage()}");

            $response = [
                'msgs' => [[
                    'msg' => 'Erro inesperado no servidor. Por favor, tente novamente.',
                    'type' => 'error'
                ]]
            ];

            $response = SafeJson::encode($response);
        }

        echo $response;
    }

    public function generate(CoreExt_Controller_Page_Interface $instance)
    {
        header('Content-Type: application/json; charset=UTF-8');

        try {
            if ($this->canAcceptRequest()) {
                $instance->Gerar();
            }
        } catch (iEducarException $exception) {

            // Todos os erros do i-Educar serão pegos neste catch para
            // futuramente movermos para um Exception Handler

            $this->messenger->append($exception->getMessage(), 'error', true);

            $this->appendResponse('error', [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'extra' => $exception->getExtraInfo(),
            ]);

        } catch (Exception $e) {
            $this->messenger->append('Exception: ' . $e->getMessage(), 'error', $encodeToUtf8 = true);
        }

        echo $this->prepareResponse();
    }

    /**
     * @return bool|void
     *
     * @throws CoreExt_Exception
     * @throws iEducarException
     * @throws Exception
     */
    public function Gerar()
    {
        throw new CoreExt_Exception('The method \'Gerar\' must be overwritten!');
    }

    protected function validatesPresenceOf($requiredParamNames)
    {
        if (!is_array($requiredParamNames)) {
            $requiredParamNames = [$requiredParamNames];
        }

        $valid = true;

        foreach ($requiredParamNames as $param) {
            $value = $this->getRequest()->$param;
            if (!$this->validator->validatesPresenceOf($value, $param) and $valid) {
                $valid = false;
            }
        }

        return $valid;
    }

    protected function validatesExistenceOf($resourceName, $value, $options = [])
    {
        $defaultOptions = [
            'schema_name' => 'pmieducar',
            'field_name' => "cod_{$resourceName}",
            'add_msg_on_error' => true
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        if (is_array($value)) {
            $valid = true;

            foreach ($value as $v) {
                $valid = $valid
                    && $this->validator->validatesValueIsInBd(
                        $options['field_name'],
                        $v,
                        $options['schema_name'],
                        $resourceName,
                        $raiseExceptionOnFail = false,
                        $addMsgOnError = $options['add_msg_on_error']
                    );
            }

            return $valid;
        } else {
            return $this->validator->validatesValueIsInBd(
                $options['field_name'],
                $value,
                $options['schema_name'],
                $resourceName,
                $raiseExceptionOnFail = false,
                $addMsgOnError = $options['add_msg_on_error']
            );
        }
    }

    protected function validatesUniquenessOf($resourceName, $value, $options = [])
    {
        $defaultOptions = [
            'schema_name' => 'pmieducar',
            'field_name' => "cod_{$resourceName}",
            'add_msg_on_error' => true
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        return $this->validator->validatesValueNotInBd(
            $options['field_name'],
            $value,
            $options['schema_name'],
            $resourceName,
            $raiseExceptionOnFail = false,
            $addMsgOnError = $options['add_msg_on_error']
        );
    }

    protected function fetchPreparedQuery($sql, $params = [], $hideExceptions = true, $returnOnly = '')
    {
        $options = [
            'params' => $params,
            'show_errors' => !$hideExceptions,
            'return_only' => $returnOnly,
            'messenger' => $this->messenger
        ];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
    }

    protected function getDataMapperFor($packageName, $modelName)
    {
        return Portabilis_DataMapper_Utils::getDataMapperFor($packageName, $modelName);
    }

    protected function getEntityOf($dataMapper, $id)
    {
        return $dataMapper->find($id);
    }

    protected function tryGetEntityOf($dataMapper, $id)
    {
        try {
            $entity = $this->getEntityOf($dataMapper, $id);
        } catch (Exception $e) {
            $entity = null;
        }

        return $entity;
    }

    protected function createEntityOf($dataMapper, $data = [])
    {
        return $dataMapper->createNewEntityInstance($data);
    }

    protected function getOrCreateEntityOf($dataMapper, $id)
    {
        $entity = $this->tryGetEntityOf($dataMapper, $id);

        return (is_null($entity) ? $this->createEntityOf($dataMapper) : $entity);
    }

    protected function deleteEntityOf($dataMapper, $id)
    {
        $entity = $this->tryGetEntityOf($dataMapper, $id);

        return (is_null($entity) ? true : $dataMapper->delete($entity));
    }

    protected function saveEntity($dataMapper, $entity)
    {
        if ($entity->isValid()) {
            $dataMapper->save($entity);
        } else {
            $errors = $entity->getErrors();
            $msgs = [];

            foreach ($errors as $attr => $msg) {
                if (!is_null($msg)) {
                    $msgs[] = "$attr => $msg";
                }
            }

            $msg = 'Erro ao salvar o recurso ' . $dataMapper->resourceName() . ': ' . join(', ', $msgs);

            $this->messenger->append($msg, 'error', true);
        }
    }

    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    protected function toUtf8($str, $options = [])
    {
        return Portabilis_String_Utils::toUtf8($str, $options);
    }

    protected function toLatin1($str, $options = [])
    {
        return Portabilis_String_Utils::toLatin1($str, $options);
    }

    protected function safeString($str, $transform = true)
    {
        return $this->toUtf8($str, ['transform' => $transform]);
    }

    protected function safeStringForDb($str)
    {
        return $this->toLatin1($str);
    }

    protected function defaultSearchOptions()
    {
        $resourceName = Portabilis_String_Utils::underscore($this->getDispatcher()->getActionName());

        return [
            'namespace' => 'pmieducar',
            'table' => $resourceName,
            'idAttr' => "cod_$resourceName",
            'labelAttr' => 'nome',
            'selectFields' => [],
            'sqlParams' => []
        ];
    }

    protected function searchOptions()
    {
        return [];
    }

    protected function sqlsForNumericSearch()
    {
        $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());

        $namespace = $searchOptions['namespace'];
        $table = $searchOptions['table'];
        $idAttr = $searchOptions['idAttr'];
        $labelAttr = $searchOptions['labelAttr'];

        $searchOptions['selectFields'][] = "$idAttr as id, $labelAttr as name";
        $selectFields = join(', ', $searchOptions['selectFields']);

        return "select distinct $selectFields from $namespace.$table where $idAttr::varchar like $1||'%' order by $idAttr limit 15";
    }

    protected function sqlsForStringSearch()
    {
        $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());

        $namespace = $searchOptions['namespace'];
        $table = $searchOptions['table'];
        $idAttr = $searchOptions['idAttr'];
        $labelAttr = $searchOptions['labelAttr'];

        $searchOptions['selectFields'][] = "$idAttr as id, $labelAttr as name";
        $selectFields = join(', ', $searchOptions['selectFields']);

        return "select distinct $selectFields from $namespace.$table where lower($labelAttr) like '%'||lower($1)||'%' order by $labelAttr limit 15";
    }

    protected function sqlParams($query)
    {
        $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());
        $params = [$query];

        foreach ($searchOptions['sqlParams'] as $param) {
            $params[] = $param;
        }

        return $params;
    }

    protected function loadResourcesBySearchQuery($query)
    {
        $results = [];
        $numericQuery = preg_replace('/[^0-9]/', '', $query);

        if (!empty($numericQuery)) {
            $sqls = $this->sqlsForNumericSearch();
            $params = $this->sqlParams($numericQuery);
        } else {
            $query = Portabilis_String_Utils::toLatin1($query, ['escape' => false]);

            $sqls = $this->sqlsForStringSearch();
            $params = $this->sqlParams($query);
        }

        if (!is_array($sqls)) {
            $sqls = [$sqls];
        }

        foreach ($sqls as $sql) {
            $_results = $this->fetchPreparedQuery($sql, $params, false);

            foreach ($_results as $result) {
                if (!isset($results[$result['id']])) {
                    $results[$result['id']] = $this->formatResourceValue($result);
                }
            }
        }

        return $results;
    }

    protected function formatResourceValue($resource)
    {
        return $resource['id'] . ' - ' . $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function search()
    {
        if ($this->canSearch()) {
            $resources = $this->loadResourcesBySearchQuery($this->getRequest()->query);
        }

        if (empty($resources)) {
            $resources = ['' => 'Sem resultados.'];
        }

        return ['result' => $resources];
    }
}
