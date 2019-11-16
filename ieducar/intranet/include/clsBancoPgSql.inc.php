<?php

use iEducar\Modules\ErrorTracking\TrackerFactory;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

abstract class clsBancoSQL_
{
  /**
   * Opções de fetch de uma consulta pgsql.
   * @var int
   */
  const FETCH_ARRAY = 1;
  const FETCH_ASSOC = 2;

  /**
   * Nome ou endereço IP do servidor do banco de dados.
   * @var string
   */
  protected $strHost       = NULL;

  /**
   * Nome do banco de dados.
   * @var string
   */
  protected $strBanco      = NULL;

  /**
   * Usuário devidamente autorizado a acessar o banco.
   * @var string
   */
  protected $strUsuario    = NULL;

  /**
   * Senha do usuário do banco
   * @var string
   */
  protected $strSenha      = NULL;

  /**
   * Porta do servidor de banco de dados.
   * @var string
   */
  protected $strPort       = NULL;

  /**
   * Identificador da conexão.
   * @var int
   */
  public $bLink_ID         = 0;

  /**
   * Identificador do resultado da consulta.
   * @var int
   */
  public $bConsulta_ID     = 0;

  /**
   * Tupla resultante de uma consulta.
   * @var array
   */
  public $arrayStrRegistro = array();

  /**
   * Se ocorreu erro na consulta, retorna FALSE.
   * @var int
   */
  public $bErro_no         = 0;

  /**
   * Frase de descrição do erro retornado
   * @var string
   */
  public $strErro          = '';

  /**
   * Query SQL.
   * @var string
   */
  public $strStringSQL     = '';

  /**
   * Define se serão lançadas exceções como respostas a erros da extensão
   * ext/pgsql. Implementado no método Consulta().
   * @see clsBancoSQL_#Consulta()
   * @var bool
   */
  protected $_throwException = FALSE;

  /**
   * Opções de fetch de resultado de pg_query() disponíveis.
   * @var array
   */
  protected $_fetchOptions = array(
    self::FETCH_ARRAY,
    self::FETCH_ASSOC
  );

  /**
   * Modo de fetch a ser utilizado.
   * @var int
   */
  protected $_fetchMode    = self::FETCH_ARRAY;


  /**
   * Construtor.
   * @link http://svn.softwarepublico.gov.br/trac/ieducar/ticket/58 Remover parâmetro não utilizado do construtor de clsBanco (ticket 58)
   */
  public function __construct($options)
  {
    // Verifica se o parâmetro é do tipo array, para evitar problemas enquanto
    // o ticket 58 não é resolvido.
    if (is_array($options)) {
      if (0 < count($options)) {
        // Verifica por fetchMode
        if (isset($options['fetchMode'])) {
          $this->setFetchMode($options['fetchMode']);
        }
      }
    }
  }

  /**
   * Setter.
   * @param string $v O nome do host onde está o banco de dados.
   */
  public function setHost($v) {
    $this->strHost = (string) $v;
  }

  /**
   * Setter.
   * @param string $v O nome do banco de dados a conectar.
   */
  public function setDbname($v) {
    $this->strBanco = (string) $v;
  }

  /**
   * Setter.
   * @param string $v O nome do usuário do banco de dados.
   */
  public function setUser($v) {
    $this->strUsuario = (string) $v;
  }

  /**
   * Setter.
   * @param string $v A senha de acesso para o usuário do banco de dados.
   */
  public function setPassword($v) {
    $this->strSenha = (string) $v;
  }

  /**
   * Setter.
   * @param string $v A porta em que o banco de dados está escutando.
   */
  public function setPort($v) {
    $this->strPort = (string) $v;
  }

  /**
   * Getter.
   * @return string
   */
  public function getHost() {
    return $this->strHost;
  }

  /**
   * Getter.
   * @return string
   */
  public function getDbname() {
    return $this->strBanco;
  }

  /**
   * Getter.
   * @return string
   */
  public function getUser() {
    return $this->strUsuario;
  }

  /**
   * Getter.
   * @return string
   */
  public function getPassword() {
    return $this->strSenha;
  }

  /**
   * Getter.
   * @return string
   */
  public function getPort() {
    return $this->strPort;
  }

  /**
   * Setter para o modo de fetch de resultados do banco de dados.
   * @param int $fetchMode
   * @return clsBancoSQL_ Provê interface fluída
   */
  public function setFetchMode($fetchMode = self::FETCH_ARRAY)
  {
    if (in_array($fetchMode, $this->_fetchOptions)) {
      $this->_fetchMode = $fetchMode;
    }
    else {
      $this->_fetchMode = self::FETCH_ARRAY;
    }
    return $this;
  }

  /**
   * Getter.
   * @return int
   */
  public function getFetchMode()
  {
    return $this->_fetchMode;
  }

  /**
   * Setter.
   * @param bool $throw
   * @return clsBancoSQL_ Provê interface fluída
   */
  public function setThrowException($throw = FALSE)
  {
    $this->_throwException = (bool) $throw;
    return $this;
  }

  /**
   * Getter.
   * @return bool
   */
  public function getThrowException()
  {
    return $this->_throwException;
  }

  /**
   * Getter.
   * @return string
   */
  public function getFraseConexao() {
    return $this->strFraseConexao;
  }

  /**
   * Constrói a string de conexão de banco de dados.
   */
  public function FraseConexao() {
    $this->strFraseConexao = "";
    if (!empty($this->strHost)) {
      $this->strFraseConexao .= "host={$this->strHost}";
    }
    if (!empty($this->strBanco)) {
      $this->strFraseConexao .= " dbname={$this->strBanco}";
    }
    if (!empty($this->strUsuario)) {
      $this->strFraseConexao .= " user={$this->strUsuario}";
    }
    if (!empty($this->strSenha)) {
      $this->strFraseConexao .= " password={$this->strSenha}";
    }
    if (!is_null($this->strPort)) {
      $this->strFraseConexao .= " port={$this->strPort}";
    }
  }

    /**
     * Executa uma consulta SQL.
     *
     * @param string $consulta Consulta SQL.
     * @param bool $reescrever (Opcional) SQL é reescrita para transformar
     *   sintaxe MySQL em PostgreSQL.
     * @param bool $forceUseWritePdo Força o uso da conexão de escrita
     * @return bool|resource FALSE em caso de erro ou o identificador da consulta
     *   em caso de sucesso.
     *
     * @throws Exception
     */
  public function Consulta($consulta, $reescrever = true, $forceUseWritePdo = false)
  {
    if (empty($consulta)) {
      return FALSE;
    }
    else {
      $this->strStringSQL = $consulta;
    }

    // Alterações de padrão MySQL para PostgreSQL
    if ($reescrever) {
      // Altera o Limit
      $this->strStringSQL = preg_replace( "/LIMIT[ ]{0,3}([0-9]+)[ ]{0,3},[ ]{0,3}([0-9]+)/i", "LIMIT \\2 OFFSET \\1", $this->strStringSQL );

      // Altera selects com YEAR( campo ) ou MONTH ou DAY
      $this->strStringSQL = preg_replace( "/(YEAR|MONTH|DAY)[(][ ]{0,3}(([a-z]|_|[0-9])+)[ ]{0,3}[)]/i", "EXTRACT( \\1 FROM \\2 )", $this->strStringSQL );

      // Remove os ORDER BY das querys COUNT()
      // Altera os LIKE para ILIKE (ignore case)
      $this->strStringSQL = preg_replace("/ LIKE /i", " ILIKE ", $this->strStringSQL);

      $this->strStringSQL = preg_replace("/fcn_upper_nrm/i", "", $this->strStringSQL);
    }

    $temp = explode("'", $this->strStringSQL);

    for ($i = 0; $i < count($temp); $i++) {
      // Ignora o que está entre aspas
      if (! ($i % 2)) {
        // Fora das aspas, verifica se há algo errado no SQL
        if (preg_match("/(--|#|\/\*)/", $temp[$i])) {
          throw new Exception('Erro na query executada no banco de dados');
        }
      }
    }

    $this->run($this->strStringSQL, [], $forceUseWritePdo);

    if (!$this->bConsulta_ID) {
        $erroMsg = "SQL invalido: {$this->strStringSQL}<br>\n";
        throw new Exception("Erro ao executar uma ação no banco de dados: $erroMsg");
    }

    return $this->bConsulta_ID;
  }

  /**
   * Retorna o último ID gerado por uma sequence.
   * @param  string $str_sequencia
   * @return bool|string
   */
  function InsertId($str_sequencia = FALSE)
  {
    if ($str_sequencia) {
      $this->Consulta("SELECT currval('{$str_sequencia}'::text)", true, true);
      $this->ProximoRegistro();
      list($valor) = $this->Tupla();
      return $valor;
    }
    return FALSE;
  }

  /**
   * @see clsBancoSQL_#InsertId($str_sequencia = FALSE)
   */
  function UltimoID($str_sequencia = FALSE)
  {
    return $this->InsertId($str_sequencia);
  }

  /**
   * Avança um registro no resultado da consulta corrente, retorna FALSE quando
   * exaure a lista de resultados. É necessário chamar essa função antes de
   * tentar acessar o primeiro registro com uma chamada a Tupla(), para mover
   * o ponteiro interno do array de resultados.
   *
   * @return bool|mixed
   */
  function ProximoRegistro()
  {
      if (empty($this->bConsulta_ID)) {
          $this->strErro = 'Nenhuma conexão informada.';
          $this->bErro_no = false;

          return false;
      }

      $this->arrayStrRegistro = $this->bConsulta_ID->fetch();

    // Testa se está vazio e verifica se Auto_Limpa é TRUE
    $stat = is_array($this->arrayStrRegistro);

    return $stat;
  }

  /**
   * Retorna a quantidade de linhas afetadas por queries INSERT, UPDATE e DELETE.
   */
  function Linhas_Afetadas()
  {
    return @pg_affected_rows($this->bConsulta_ID);
  }

  /**
   * @see clsBancoSQL_#numLinhas()
   */
  function Num_Linhas()
  {
    return $this->numLinhas();
  }

  /**
   * Retorna o número de linhas do resultado de uma chamada a Consulta().
   * @return int
   */
  function numLinhas()
  {
    return $this->bConsulta_ID->rowCount();
  }

  /**
   * @see clsBancoSQL_#numCampos()
   */
  function Num_Campos()
  {
    return $this->numCampos();
  }

  /**
   * Retorna o número de campos do resultado de uma chamada a Consulta().
   * @return int
   */
  function numCampos()
  {
    return $this->bConsulta_ID->columnCount();
  }

  /**
   * Retorna o valor de um campo retornado por uma consulta SELECT.
   * @param  string $Nome
   * @return mixed
   */
  function Campo($Nome)
  {
    return $this->arrayStrRegistro[$Nome];
  }

  /**
   * Retorna o registro atual do array de resultados.
   * @return mixed
   */
  function Tupla()
  {
    return $this->arrayStrRegistro;
  }

  /**
   * Retorna o valor de um campo em uma query SELECT.
   * @param unknown_type $consulta
   * @return unknown_type
   */
  function UnicoCampo($consulta)
  {
    $this->Consulta($consulta);
    if ($this->ProximoRegistro()) {
        $campo = $this->Tupla();
        $campo = array_shift($campo);

      return $campo;
    }
    return FALSE;
  }

  /**
   * @see clsBancoSQL_#UnicoCampo()
   */
  function CampoUnico($consulta)
  {
    return $this->UnicoCampo($consulta);
  }

  /**
   * Executa uma consulta SQL preparada ver: http://php.net/manual/en/function.pg-prepare.php
   * ex: $db->execPreparedQuery("select * from portal.funcionario where matricula = $1 and senha = $2", array('admin', '123'))
   *
   * @param  string $name    nome da consulta
   * @param  string $query   sql para ser preparado
   * @param  array  $params  parametros para consulta
   *
   * @return bool|resource FALSE em caso de erro ou o identificador da consulta
   *   em caso de sucesso.
   */
  public function execPreparedQuery($query, $params = array()) {
    try {
      $errorMsgs = '';

      if (! is_array($params))
        $params = array($params);

        $this->run($query, $params);
      }
      catch(Exception $e) {
        $errorMsgs .= "Exception: " . $e->getMessage();
      }

      if ($this->bConsulta_ID == false || trim($errorMsgs) != '')
        throw new Exception("Erro ao preparar consulta ($query) no banco de dados: $errorMsgs");

      return $this->bConsulta_ID;
  }

    /**
     * Método mockavel para execução de query
     *
     * @param string $query
     * @param array $params
     * @param bool $forceUseWritePdo Força o método publicRun a usar a conexão de escrita
     */
    private function run(string $query, $params = [], $forceUseWritePdo = false)
    {
        if (is_numeric(key($params))) {
            $params = array_combine(range('a', chr(96 + count($params))), $params);
            $query = preg_replace_callback('/(\$\d{1,})/', function ($matches) {
                return ':' . chr(substr($matches[0], 1) + 96);
            }, $query);
        }

        if ($this->getFetchMode() == self::FETCH_ARRAY) {
            DB::setFetchMode(PDO::FETCH_BOTH);
        } else {
            DB::setFetchMode($this->getFetchMode());
        }

        $this->bConsulta_ID = DB::publicRun($query, $params, $forceUseWritePdo);
    }
}
