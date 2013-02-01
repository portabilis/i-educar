<?php

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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_DataMapper
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

/**
 * CoreExt_DataMapper abstract class.
 *
 * Implementa alguns dos conceitos do pattern Data Mapper de forma simples. A
 * intenção é o de tornar o mapeamento objeto-relacional mais simples,
 * permitindo a criação de objetos de domínio novos que interajam com objetos
 * de domínio legados.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_DataMapper
 * @since     Classe disponível desde a versão 1.1.0
 * @todo      Refactoring dos métodos get*Statment() com extract method
 * @todo      Testes para ordenação em findAll()
 * @version   @@package_version@@
 */
abstract class CoreExt_DataMapper
{
  /**
   * Classe CoreExt_Entity para este data mapper.
   * @var string
   */
  protected $_entityClass = '';

  /**
   * Mapeamento objeto-relacional, atributos-campos.
   * @var array
   */
  protected $_attributeMap = array();

  /**
   * Atributos não-persistíveis.
   * @var array
   */
  protected $_notPersistable = array();

  /**
   * Define as chaves primárias da tabela. Configurada automaticamente para
   * usar o campo identidade de CoreExt_Entity.
   * @see CoreExt_Entity::_createIdentityField()
   * @var array
   */
  protected $_primaryKey = array('id');

  /**
   * Objeto de conexão com o banco de dados.
   * @var clsBanco
   */
  protected $_dbAdapter = NULL;

  /**
   * Instância padrão para uso em objetos CoreExt_DataMapper. Isso possibilita
   * que a mesma instância do adapter de conexão com o banco de dados seja
   * reutilizado em várias instâncias de CoreExt_DataMapper.
   *
   * @var clsBanco
   */
  protected static $_defaultDbAdapter = NULL;

  /**
   * Nome da tabela em que o objeto é mapeado.
   * @var string
   */
  protected $_tableName = '';

  /**
   * Nome do schema da tabela.
   * @var string
   */
  protected $_tableSchema = '';

  /**
   * @var CoreExt_Locale
   */
  protected $_locale = NULL;

  /**
   * Construtor.
   * @param clsBanco $db
   */
  public function __construct(clsBanco $db = NULL)
  {
    if (!is_null($db)) {
      $this->_setDbAdapter($db);
    }
  }

  /**
   * Setter para configuração de um adapter de banco de dados padrão usado
   * nas instâncias concretas de CoreExt_DataMapper quando nenhuma instância de
   * clsBanco é passada ao construtor.
   * @param clsBanco $db
   */
  public static function setDefaultDbAdapter(clsBanco $db = NULL)
  {
    self::$_defaultDbAdapter = $db;
  }

  /**
   * Reseta o adapter padrão, fazendo com que CoreExt_DataMapper instancie
   * automaticamente uma instância de clsBanco quando necessário.
   */
  public static function resetDefaultDbAdapter()
  {
    self::setDefaultDbAdapter(NULL);
  }

  /**
   * Setter para o objeto de adapter responsável pela interação com o banco de
   * dados.
   *
   * @param  clsBanco $db
   * @return CoreExt_DataMapper Provê interface fluída
   */
  protected function _setDbAdapter(clsBanco $db)
  {
    $this->_dbAdapter = $db;
    return $this;
  }

  /**
   * Getter para o objeto de adapter de banco de dados.
   *
   * Se nenhuma instância foi explicitamente passada ao construtor,
   * tenta atribuir uma instância por padrão, na seguinte ordem:
   *
   * - Usando o adapter provido pelo método estático setDefaultDbAdapter
   * (útil para usar várias instâncias de CoreExt_DataMapper sem a instanciação
   * da classe clsBanco)
   * - Ou, instanciando a classe clsBanco
   *
   * Usar o setter estático tem a vantagem de reduzir o overhead causado pela
   * instanciação a clsBanco a cada novo objeto CoreExt_DataMapper.
   *
   * @return clsBanco
   */
  protected function _getDbAdapter()
  {
    if (is_null($this->_dbAdapter)) {
      if (!is_null(self::$_defaultDbAdapter)) {
        $adapter = self::$_defaultDbAdapter;
      }
      else {
        $adapter = new clsBanco(array('fetchMode' => clsBanco::FETCH_ASSOC));
      }
      $this->_setDbAdapter($adapter);
    }
    return $this->_dbAdapter;
  }

  /**
   * Getter público para o objeto de adapter de banco de dados.
   * @return clsBanco
   */
  public function getDbAdapter()
  {
    return $this->_getDbAdapter();
  }

  /**
   * Retorna o nome da tabela. Se o $_tableSchema for informado, retorna o
   * nome da tabela no formato 'schema.table'.
   *
   * @return string
   */
  protected function _getTableName()
  {
    return $this->_tableSchema != '' ?
      $this->_tableSchema . '.' . $this->_tableName : $this->_tableName;
  }

  /**
   * Retorna o nome do recurso, isto é o nome da tabela sem '_',
   * Ex: transporte_aluno => transporte aluno.
   *
   * @return string
   */
  public function resourceName()
  {
    return strtolower(str_replace('_', ' ', $this->_tableName));
  }

  /**
   * Retorna os nomes das colunas da tabela em um array, de acordo com o array
   * de dados associativo $data.
   *
   * Caso nenhum array seja passado, é usado o array de atributos da classe
   * CoreExt_Entity ao qual o data mapper mapeia.
   *
   * @param array $data
   * @return array
   */
  protected function _getTableColumnsArray(array $data = array())
  {
    $columns = array();

    if (0 == count($data)) {
      $tempEntity = new $this->_entityClass();
      $data = $tempEntity->toDataArray();
    }

    $tempColumns = array_map(array($this, '_getTableColumn'), array_keys($data));

    // Remove colunas não-persistíveis
    foreach ($tempColumns as $key => $column) {
      if (is_null($column)) {
        continue;
      }
      $columns[] = $column;
    }

    return $columns;
  }

  /**
   * Retorna o nome do campo da tabela caso o identificador do atributo
   * esteja mapeado em $_attributeMap.
   *
   * Caso contrário, retorna o próprio identificador do atributo.
   *
   * @param string $key
   * @return string|NULL NULL para coluna não persistível
   */
  protected function _getTableColumn($key)
  {
    if (in_array($key, $this->_notPersistable)) {
      return NULL;
    }

    if (array_key_exists($key, $this->_attributeMap)) {
      return $this->_attributeMap[$key];
    }
    return $key;
  }

  /**
   * Retorna os nomes das colunas da tabela separados por vírgula e espaço (', ').
   *
   * @param array $data
   * @return string
   */
  protected function _getTableColumns(array $data = array())
  {
    return join(', ', $this->_getTableColumnsArray($data));
  }

  /**
   * Retorna uma query SQL de recuperação de todos os registros de uma tabela.
   *
   * @param array $data
   * @param array $where
   * @param array $orderBy
   * @return string
   */
  protected function _getFindAllStatment(array $data = array(), array $where = array(),
    array $orderBy = array())
  {
    $whereArg = $where;
    $where    = array();
    $order    = array();

    if (0 < count($whereArg)) {
      foreach ($whereArg as $key => $value) {
        $whereName = $this->_getTableColumn($key);

        preg_match('/[<,=,>]/', $value, $matches);
        $hasComparisonSign = ! empty($matches);

        // Caso $value contenha <, > ou =, ex: '> $1', não adiciona sinal de igual.
        if($hasComparisonSign)
          $where[] = sprintf("%s %s", $whereName, $value);

        // Caso $value contenha parametros para consulta preparada ($1, $2...), não adiciona $value entre aspas.
        elseif(strpos($value, '$') > -1)
          $where[] = sprintf("%s = %s", $whereName, $value);

        else
          $where[] = sprintf("%s = '%s'", $whereName, $value);
      }
    }
    else {
      $where[] = '1 = 1';
    }

    if (0 < count($orderBy)) {
      foreach ($orderBy as $key => $value) {
        $order[] = sprintf('%s %s', $this->_getTableColumn($key), $value);
      }
      $order = sprintf('ORDER BY %s', implode(',', $order));
    }
    else {
      $order = '';
    }

    return sprintf("SELECT %s FROM %s WHERE %s %s", $this->_getTableColumns($data),
      $this->_getTableName(), implode(' AND ', $where), $order);
  }

  /**
   * Retorna uma query SQL de recuperação de registro baseada na identidade.
   *
   * Converte o argumento $pkey para float, como forma de
   * evitar os problemas do tipo int em ambientes 32 bit (mais especificamente,
   * a ausência de um tipo long).
   *
   * @link   http://php.net/manual/en/language.types.integer.php
   * @link   http://php.net/manual/en/function.intval.php
   * @param  array|long $pkey
   * @return string
   */
  protected function _getFindStatment($pkey)
  {
    $where = array();

    if (!is_array($pkey)) {
      $where[] = sprintf("id = '%d'", floatval($pkey));
    }
    elseif (is_array($pkey)) {
      foreach ($pkey as $key => $pk) {
        if (is_int($key)) {
          $whereName = $this->_getTableColumn($this->_primaryKey[$key]);
        }
        elseif (is_string($key)) {
          $whereName = $this->_getTableColumn($key);
        }
        $where[] = sprintf("%s = '%d'", $whereName, floatval($pk));
      }
    }

    return sprintf("SELECT %s FROM %s WHERE %s", $this->_getTableColumns(),
      $this->_getTableName(), implode(' AND ', $where));
  }

  /**
   * Retorna uma query SQL para a operação INSERT. Utiliza para isso os
   * atributos da instância CoreExt_Entity, com o cuidado de remover o
   * campo identidade.
   *
   * Uma query gerada por esse método segue a forma:
   * <code>
   * INSERT INTO [schema.]table (column) VALUES ('value');
   * </code>
   *
   * @param CoreExt_Entity $instance
   * @return string
   */
  protected function _getSaveStatment(CoreExt_Entity $instance)
  {
    $sql = 'INSERT INTO %s (%s) VALUES (%s)';
    $data = $this->_getDbAdapter()->formatValues($instance->toDataArray());

    // Remove o campo identidade e campos não-persistentes
    $data = $this->_cleanData($data);

    // Pega apenas os valores do array
    $values = array_values($data);

    // Colunas para formar a query
    $columns = $this->_getTableColumns($data);

    // Trata os valores NULL diferentemente dos outros, para evitar erro
    // de execução query
    $valuesStmt = array();
    for ($i = 0, $count = count($values); $i < $count; $i++) {
      $value = $values[$i];
      if (is_null($value)) {
        $value = "NULL";
        $replaceString = "%s";
      }
      else {
        $replaceString = "'%s'";
      }
      $valuesStmt[] = sprintf($replaceString, $value);
    }

    $valuesStmt = join(", ", $valuesStmt);
    return sprintf($sql, $this->_getTableName(), $columns, $valuesStmt);
  }

  /**
   * Retorna uma query SQL para a operação UPDATE. Utiliza para isso os
   * atributos da instância CoreExt_Entity, usando o atributo identidade
   * para especificar qual registro atualizar na tabela.
   *
   * Uma query gerada por esse método segue a forma:
   * <code>
   * UPDATE [schema.]table SET column='value' WHERE id = 'idValue';
   * </code>
   *
   * @param CoreExt_Entity $instance
   * @return string
   */
  protected function _getUpdateStatment(CoreExt_Entity $instance)
  {
    $sql = 'UPDATE %s SET %s WHERE %s';
    $data = $this->_getDbAdapter()->formatValues($instance->toDataArray());

    // Remove o campo identidade e campos não-persistentes
    $data = $this->_cleanData($data);

    // Trata os valores NULL diferentemente dos outros, para evitar erro
    // de execução query
    $columns = array();
    foreach ($data as $key => $value) {
      $columnName = $this->_getTableColumn($key);
      if (is_null($value)) {
        $value = "NULL";
        $replaceString = "%s = %s";
      }
      else {
        $replaceString = "%s = '%s'";
      }
      $columns[] = sprintf($replaceString, $columnName, $value);
    }

    $where = array();
    foreach ($this->_primaryKey as $pk) {
      $whereName = $this->_getTableColumn($pk);
      $where[] = sprintf("%s = '%d'", $whereName, $instance->get($pk));
    }

    return sprintf($sql, $this->_getTableName(), implode(', ', $columns),
      implode(' AND ', $where));
  }

  /**
   * Retorna uma query SQL para a operação DELETE. Utiliza para isso o
   * atributo identidade "id" (caso seja passada uma instância de CoreExt_Entity
   * como parâmetro) ou o valor inteiro passado como parâmetro.
   *
   * Uma query gerada por esse método segue a forma:
   * <code>
   * DELETE FROM [schema.]table WHERE id = 'idValue';
   * </code>
   *
   * @param mixed $instance
   * @return string
   */
  protected function _getDeleteStatment($instance)
  {
    $sql = 'DELETE FROM %s WHERE %s';

    $where = array();
    if ($instance instanceof CoreExt_Entity) {
      foreach ($this->_primaryKey as $pk) {
        $whereName = $this->_getTableColumn($pk);
        //$where[] = sprintf("%s = '%d'", $whereName, $instance->get($pk)); estoura o decimal. valor 9801762824 retornando 1211828232
        $where[] = sprintf("%s = '%s'", $whereName, $instance->get($pk));
      }
    }
    elseif (is_numeric($instance)) {
      $where[] = sprintf("%s = '%d'", 'id', floatval($instance));
    }
    elseif (is_array($instance)) {
      foreach ($this->_primaryKey as $pk) {
        $whereName = $this->_getTableColumn($pk);
        $where[] = sprintf("%s = '%d'", $whereName, $instance[$pk]);
      }
    }

    return sprintf($sql, $this->_getTableName(), implode(' AND ', $where));
  }

  /**
   * Retorna todos os registros como objetos CoreExt_Entity retornados pela
   * query de _getFindAllStatment().
   *
   * @param  array $columns Atributos a serem carregados. O atributo id é sempre carregado.
   * @param  array $where
   * @param  array $orderBy
   * @param  array $addColumnIdIfNotSet Se true, adiciona a coluna 'id' caso não esteja definido no array $columns
   * @return array
   * @todo   Problema potencial com busca em registros com compount key. Testar.
   */
  public function findAll(array $columns = array(), array $where = array(), array $orderBy = array(), $addColumnIdIfNotSet = true)
  {
    // Inverte chave valor, permitindo array simples como array('nome')
    if (0 < count($columns)) {
      $columns = array_flip($columns);
      if (!isset($columns['id']) && $addColumnIdIfNotSet) {
        $columns['id'] = TRUE;
      }
    }

    // Reseta o locale para o default (en_US)
    $this->getLocale()->resetLocale();

    $this->_getDbAdapter()->Consulta($this->_getFindAllStatment($columns, $where, $orderBy));
    $list = array();

    // Retorna o locale para o usado no restante da aplicação
    $this->getLocale()->setLocale();

    while ($this->_getDbAdapter()->ProximoRegistro()) {
      $list[] = $this->_createEntityObject($this->_getDbAdapter()->Tupla());
    }

    return $list;
  }


  /**
   * Retorna todos os registros como objetos CoreExt_Entity retornados pela
   * query de _getFindAllStatment() (usando consulta preparada, util para evitar sql injection).
   *
   * @param  array $columns Atributos a serem carregados. O atributo id é sempre carregado.
   * @param  array $where   Condicoes preparadas ex: array('arg1 = $1', 'arg2 = $2');
   * @param  array $params  Valor das condiçoes ($1, $2 ...) ex: array('1', '3');
   * @param  array $orderBy
   * @param  array $addColumnIdIfNotSet Se true, adiciona a coluna 'id' caso não esteja definido no array $columns
   * @return array
   * @todo
   */
  public function findAllUsingPreparedQuery(array $columns = array(), array $where = array(), array $params = array(), array $orderBy = array(), $addColumnIdIfNotSet = true) {
    $list = array();

    // Inverte chave valor, permitindo array simples como array('nome')
    if (0 < count($columns)) {
      $columns = array_flip($columns);
      if (!isset($columns['id']) && $addColumnIdIfNotSet) {
        $columns['id'] = TRUE;
      }
    }

    // Reseta o locale para o default (en_US)
    $this->getLocale()->resetLocale();

    $sql = $this->_getFindAllStatment($columns, $where, $orderBy);

    if ($this->_getDbAdapter()->execPreparedQuery($sql, $params) != false) {
      // Retorna o locale para o usado no restante da aplicação
      $this->getLocale()->setLocale();

      while ($this->_getDbAdapter()->ProximoRegistro()) {
        $list[] = $this->_createEntityObject($this->_getDbAdapter()->Tupla());
      }
    }

    return $list;
  }


  /**
   * Retorna um registro que tenha como identificador (chave única ou composta)
   * o valor dado por $pkey.
   *
   * @param  array|long $pkey
   * @return CoreExt_Entity
   * @throws Exception
   */
  public function find($pkey)
  {
    $this->_getDbAdapter()->Consulta($this->_getFindStatment($pkey));
    if (FALSE === $this->_getDbAdapter()->ProximoRegistro()) {
      throw new Exception('Nenhum registro encontrado com a(s) chaves(s) informada(s).');
    }
    return $this->_createEntityObject($this->_getDbAdapter()->Tupla());
  }

  /**
   * Salva ou atualiza um registro através de uma instância de CoreExt_Entity.
   *
   * @param  CoreExt_Entity $instance
   * @return bool
   * @throws CoreExt_DataMapper_Exception|Exception
   */
  public function save(CoreExt_Entity $instance)
  {
    if (!$instance->isValid()) {
      throw new Exception('A instânca de "' . get_class($instance) . '" contém erros de validação.');
    }

    // Coumpound key, todos os valores precisam estar setados, seja para
    // INSERT ou UPDATE. A instância precisa ser marcada explicitamente
    // como "old" para que UPDATE seja chamado.
    if (1 < count($this->_primaryKey)) {
      foreach ($this->_primaryKey as $pk) {
        $value = $instance->get($pk);
        if (!isset($value)) {
          require_once 'CoreExt/DataMapper/Exception.php';
          throw new CoreExt_DataMapper_Exception('Erro de compound key. Uma das primary keys tem o valor NULL: "' . $pk . '"');
        }
      }
    }
    // Field identity, se estiver presente, marca instância como "old".
    elseif (1 == count($this->_primaryKey)) {
      if (isset($instance->id)) {
        $instance->markOld();
      }
    }

    // Reseta o locale para o default (en_US)
    $this->getLocale()->resetLocale();

    if ($instance->isNew()) {
      return $this->_getDbAdapter()->Consulta($this->_getSaveStatment($instance));
    }
    else {
      return $this->_getDbAdapter()->Consulta($this->_getUpdateStatment($instance));
    }

    // Retorna o locale para o usado no restante da aplicação
    $this->getLocale()->setLocale();
  }

  /**
   * Apaga um registro através de uma instância CoreExt_Entity. Pode apagar
   * recebendo uma instância com as chaves primárias setadas ou um array
   * associativo de chaves primárias e seus valores.
   *
   * Exemplo:
   * <code>
   * <?php
   * $instance   = new CoreExt_Entity(array('pk1' => 1, 'pk2' => 2));
   * $dataMapper = new CoreExt_DataMapper();
   *
   * // Por valor do campo identidade 'id'
   * $dataMapper->delete(1);
   *
   * // Pelas chaves primárias de uma instância
   * $dataMapper->delete($instance);
   *
   * // Por um array de chaves associativas de chaves primárias
   * $dataMapper->delete(array('pk1' => 1, 'pk2' => 2));
   * </code>
   *
   * @param mixed $instance
   * @return bool
   */
  public function delete($instance)
  {
      return $this->_getDbAdapter()->Consulta($this->_getDeleteStatment($instance));
  }

  /**
   * Retorna uma nova instância de CoreExt_Entity. A instância criada não
   * produz efeito algum no comportamento de CoreExt_DataMapper.
   *
   * @return CoreExt_Entity
   */
  public function createNewEntityInstance(array $data = array())
  {
    return new $this->_entityClass($data);
  }

  /**
   */
  protected function _cleanData(array $data)
  {
    if (array_key_exists('id', $data)) {
      unset($data['id']);
    }

    // Remove dados não-persistíveis
    foreach ($this->_notPersistable as $field) {
      if (array_key_exists($field, $data)) {
        unset($data[$field]);
      }
    }

    return $data;
  }

  /**
   * Cria um objeto CoreExt_Entity com os valores dos campos relacionais
   * mapeados para os atributos da instância.
   *
   * @param array $data
   * @return CoreExt_Entity
   */
  protected function _createEntityObject(array $data)
  {
    $instance = $this->createNewEntityInstance();
    $instance->markOld();
    $instance = $this->_mapData($data, $instance);
    return $instance;
  }

  /**
   * Mapeia os campos relacionais para os atributos de uma instância de
   * CoreExt_Entity.
   *
   * @param  array $data
   * @param  CoreExt_Entity $instance
   * @return CoreExt_Entity A instância com os atributos mapeados
   */
  protected function _mapData($data, CoreExt_Entity $instance)
  {
    foreach ($data as $key => $value) {
      try {
        $instance->$key = $value;
      }
      catch (CoreExt_Exception_InvalidArgumentException $e) {
        // Caso o campo não tenha um atributo correspondente, procura no
        // mapa de atributos pelo equivalente e atribue.
        if (FALSE !== ($index = array_search($key, $this->_attributeMap))) {
          $instance->$index = $value;
        }
      }
    }
    return $instance;
  }

  /**
   * Setter.
   * @param CoreExt_Locale $instance
   * @return CoreExt_DataMapper Provê interface fluída
   */
  public function setLocale(CoreExt_Locale $instance)
  {
    $this->_locale = $instance;
    return $this;
  }

  /**
   * Getter.
   * @return CoreExt_Locale
   */
  public function getLocale()
  {
    if (is_null($this->_locale)) {
      require_once 'CoreExt/Locale.php';
      $this->setLocale(CoreExt_Locale::getInstance());
    }
    return $this->_locale;
  }
}
