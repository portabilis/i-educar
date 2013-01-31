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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'clsConfigItajai.inc.php';
require_once 'include/clsCronometro.inc.php';
require_once 'include/clsEmail.inc.php';

require_once 'modules/Error/Mailers/NotificationMailer.php';

/**
 * clsBancoSQL_ abstract class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
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
   * Ponteiro interno para a tupla atual da consulta.
   * @var int
   */
  public $iLinha           = 0;

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
   * Ativa ou desativa funções de depuração
   * @var bool
   */
  public $bDepurar         = FALSE;

  /**
   * '1' para limpar o resultado assim que chegar ao último registro.
   * @var bool
   */
  public $bAuto_Limpa      = FALSE;

  /**
   * Query SQL.
   * @var string
   */
  public $strStringSQL     = '';

  /**
   * @var mixed
   */
  var $transactionBlock    = FALSE;

  /**
   * @var array
   */
  var $savePoints          = array();

  /**
   * @var bool
   */
  var $executandoEcho      = FALSE;

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
   * Conecta com o banco de dados
   *
   * Verifica se o link está inativo e conecta. Se a conexão não obtiver
   * sucesso, interrompe o script
   */
  public function Conecta() {
    // Verifica se o link de conexão está inativo e conecta
    if (0 == $this->bLink_ID) {
      $this->FraseConexao();
      $this->bLink_ID = pg_connect($this->strFraseConexao);

      if (! $this->bLink_ID)
        $this->Interrompe("N&atilde;o foi possivel conectar ao banco de dados");
    }
  }

  /**
   * Executa uma consulta SQL.
   *
   * @param  string  $consulta    Consulta SQL.
   * @param  bool    $reescrever  (Opcional) SQL é reescrita para transformar
   *   sintaxe MySQL em PostgreSQL.
   * @return bool|resource FALSE em caso de erro ou o identificador da consulta
   *   em caso de sucesso.
   */
  public function Consulta($consulta, $reescrever = true)
  {
    $cronometro = new clsCronometro();
    $cronometro->marca('inicio');

    if (empty($consulta)) {
      return FALSE;
    }
    else {
      $this->strStringSQL = $consulta;
    }

    $this->strStringSQLOriginal = $this->strStringSQL;

    $this->Conecta();

    if ($this->bDepurar) {
      printf("<br>Depurar: Frase de Consulta = %s<br>\n", $this->strStringSQL);
    }

    // Alterações de padrão MySQL para PostgreSQL
    if ($reescrever) {
      // Altera o Limit
      $this->strStringSQL = eregi_replace( "LIMIT[ ]{0,3}([0-9]+)[ ]{0,3},[ ]{0,3}([0-9]+)", "LIMIT \\2 OFFSET \\1", $this->strStringSQL );

      // Altera selects com YEAR( campo ) ou MONTH ou DAY
      $this->strStringSQL = eregi_replace( "(YEAR|MONTH|DAY)[(][ ]{0,3}(([a-z]|_|[0-9])+)[ ]{0,3}[)]", "EXTRACT( \\1 FROM \\2 )", $this->strStringSQL );

      // Remove os ORDER BY das querys COUNT()
      // Altera os LIKE para ILIKE (ignore case)
      $this->strStringSQL = eregi_replace(" LIKE ", " ILIKE ", $this->strStringSQL);

      $this->strStringSQL = eregi_replace("([a-z_0-9.]+) +ILIKE +'([^']+)'", "to_ascii(\\1) ILIKE to_ascii('\\2')", $this->strStringSQL);

      $this->strStringSQL = eregi_replace("fcn_upper_nrm", "to_ascii", $this->strStringSQL);
    }

    $temp = explode("'", $this->strStringSQL);

    for ($i = 0; $i < count($temp); $i++) {
      // Ignora o que está entre aspas
      if (! ($i % 2)) {
        // Fora das aspas, verifica se há algo errado no SQL
        if (eregi("(--|#|/\*)", $temp[$i])) {
          $erroMsg = 'Proteção contra injection: ' . date( "Y-m-d H:i:s" );
          echo "<!-- {$this->strStringSQL} -->";
          $this->Interrompe($erroMsg);
        }
      }
    }

    // Executa a Consulta
    if ($this->executandoEcho) {
      echo $this->strStringSQL."\n<br>";
    }

    $this->bConsulta_ID = pg_query($this->bLink_ID, $this->strStringSQL);
    $this->strErro = pg_result_error($this->bConsulta_ID);
    $this->bErro_no = ($this->strErro == '') ? FALSE : TRUE;
    $this->iLinha   = 0;

    if (!$this->bConsulta_ID) {
      if ($this->getThrowException()) {
        $message  = $this->bErro_no ? "($this->bErro_no) " . $this->strErro :
                                      pg_last_error($this->bLink_ID);

        $message .= PHP_EOL . $this->strStringSQL;

        throw new Exception($message);
      }
      else
      {
        $erroMsg = "SQL invalido: {$this->strStringSQL}<br>\n";
        throw new Exception("Erro ao executar uma ação no banco de dados: $erroMsg");
      }
    }


    $cronometro->marca('fim');

    $tempoTotal = $cronometro->getTempoTotal();

    $objConfig = new clsConfig();
    if ($tempoTotal > $objConfig->arrayConfig['intSegundosQuerySQL']) {
      $conteudo = "<table border=\"1\" width=\"100%\">";
      $conteudo .= "<tr><td><b>Data</b>:</td><td>" . date( "d/m/Y H:i:s", time() ) . "</td></tr>";
      $conteudo .= "<tr><td><b>Script</b>:</td><td>{$_SERVER["PHP_SELF"]}</td></tr>";
      $conteudo .= "<tr><td><b>Tempo da query</b>:</td><td>{$tempoTotal} segundos</td></tr>";
      $conteudo .= "<tr><td><b>Tempo max permitido</b>:</td><td>{$objConfig->arrayConfig["intSegundosQuerySQL"]} segundos</td></tr>";
      $conteudo .= "<tr><td><b>SQL Query Original</b>:</td><td>{$this->strStringSQLOriginal}</td></tr>";
      $conteudo .= "<tr><td><b>SQL Query Executado</b>:</td><td>{$this->strStringSQL}</td></tr>";
      $conteudo .= "<tr><td><b>URL get</b>:</td><td>{$_SERVER['QUERY_STRING']}</td></tr>";
      $conteudo .= "<tr><td><b>Metodo</b>:</td><td>{$_SERVER["REQUEST_METHOD"]}</td></tr>";

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $conteudo .= "<tr><td><b>POST vars</b>:</td><td>";

        foreach ($_POST as $var => $val) {
          $conteudo .= "{$var} => {$val}<br>";
        }
        $conteudo .= "</td></tr>";
      }
      elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $conteudo .= "<tr><td><b>GET vars</b>:</td><td>";

        foreach ($_GET as $var => $val) {
          $conteudo .= "{$var} => {$val}<br>";
        }
        $conteudo .= "</td></tr>";
      }

      if ($_SERVER['HTTP_REFERER']) {
        $conteudo .= "<tr><td><b>Referrer</b>:</td><td>{$_SERVER["HTTP_REFERER"]}</td></tr>";
      }

      $conteudo .= "</table>";

      $objEmail = new clsEmail($objConfig->arrayConfig['ArrStrEmailsAdministradores'],
        "[INTRANET - PMI] Desempenho de query", $conteudo);
      $objEmail->envia();
    }

    return $this->bConsulta_ID;
  }

  /**
   * Inicia um bloco de transação (transaction block).
   * @return bool
   */
  function begin()
  {
    if (!$this->transactionBlock) {
      $this->Consulta( "BEGIN" );
      $this->transactionBlock = TRUE;

      // Reseta os savePoints.
      $this->savePoints = array();
      return TRUE;
    }

    // Tratamento de erro informando que está dentro de um transaction block
    return FALSE;
  }

  /**
   * Processa umbloco de transacao (transaction block)
   *
    * @return bool
   */
  function commit()
  {
    if ($this->transactionBlock) {
      $this->Consulta('COMMIT');
      $this->transactionBlock = FALSE;

      // Reseta os savePoints
      $this->savePoints = array();
      return TRUE;
    }

    // Tratamento de erro informando que está dentro de um transaction block
    return FALSE;
  }

  /**
   * Cria um novo savePoint.
   * @param  string $strSavePointName Nome do savePoint a ser criado.
   * @return bool
   */
  function savePoint($strSavePointName = FALSE)
  {
    if ($this->transactionBlock) {
      if ($strSavePointName) {
        foreach ($this->savePoints as $key => $nome) {
          // Não podemos ter dois savepoints com o mesmo nome
          if ($nome == $strSavePointName) {
            return FALSE;
          }
        }

        $this->savePoints[] = $strSavePointName;
        $this->Consulta("SAVEPOINT $strSavePointName");
        return TRUE;
      }
      else {
        $nome = 'save_' . count($this->savePoints);
        $this->savePoints[] = $nome;
        $this->Consulta("SAVEPOINT $nome");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Cria um novo savePoint.
   * @param  string $strSavePointName Nome do savePoint onde se deseja voltar,
   *   se não for definido volta ao último savepoint criado
   * @return bool
   */
  function rollBack($strSavePointName = FALSE)
  {
    if ($this->transactionBlock) {
      if (count($this->savePoints)) {
        if ($strSavePointName) {
          foreach ($this->savePoints as $key => $nome) {
            // Se achar é porque tem o savePoint
            if ($nome == $strSavePointName) {
              $this->savePoints = array_slice($this->savePoints, 0, $key);
              $this->Consulta("ROLLBACK TO {$strSavePointName}");
              return TRUE;
            }
          }
        }
        else {
          // Se não tem um nome definido ele volta ao último savePoint
          $lastPos = count($this->savePoints) - 1;
          $strSavePointName = $this->savePoints[$lastPos];
          $this->savePoints = array_slice($this->savePoints, 0, ($lastPos - 1));
          $this->Consulta("ROLLBACK TO {$strSavePointName}");
        }
      }
    }

    return FALSE;
  }

  /**
   * Retorna o último ID gerado por uma sequence.
   * @param  string $str_sequencia
   * @return bool|string
   */
  function InsertId($str_sequencia = FALSE)
  {
    if ($str_sequencia) {
      $this->Consulta("SELECT currval('{$str_sequencia}'::text)");
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
    // Fetch do resultado
    if ($this->getFetchMode() == self::FETCH_ARRAY) {
      $this->arrayStrRegistro = @pg_fetch_array($this->bConsulta_ID);
    }
    elseif ($this->getFetchMode() == self::FETCH_ASSOC) {
      $this->arrayStrRegistro = @pg_fetch_assoc($this->bConsulta_ID);
    }

    // Verifica se houve erros
    $this->strErro = @pg_result_error($this->bConsulta_ID);
    $this->bErro_no = ($this->strErro == '') ? FALSE : TRUE;

    // Testa se está vazio e verifica se Auto_Limpa é TRUE
    $stat = is_array($this->arrayStrRegistro);
    if ($this->bDepurar && $stat) {
      printf("<br>Depurar: Registro : %s <br>\n", implode($this->arrayStrRegistro));
    }

    if (!$stat && $this->bAuto_Limpa)
    {
      $this->Libera();
    }

    return $stat;
  }

  /**
   * Setter para índice de um registro do array de resultados retornado por
   * Consulta().
   * @param int $Pos
   */
  function Procura($Pos)
  {
    $this->iLinha = $Pos;
  }

  /**
   * Retorna a quantidade de linhas afetadas por queries INSERT, UPDATE e DELETE.
   */
  function Linhas_Afetadas()
  {
    return @pg_affected_rows($this->bConsulta_ID);
  }

  /**
   * Libera os resultados da memória.
   */
  function Libera()
  {
    pg_free_result($this->bConsulta_ID);
    $this->bConsulta_ID = 0;
    $this->strStringSQL = '';
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
    return pg_num_rows($this->bConsulta_ID);
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
    return pg_num_fields($this->bConsulta_ID);
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
      list ($campo) = $this->Tupla();
      $this->Libera();
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
   * Retorna um único registro de uma query SELECT.
   * @param  string $consulta
   * @return mixed
   */
  function UnicaTupla($consulta)
  {
    $this->Consulta($consulta);
    $this->ProximoRegistro();
    $tupla = $this->Tupla();
    $this->Libera();
    return $tupla;
  }

  /**
   * @see clsBancoSQL_#UnicaTupla()
   */
  function TuplaUnica($consulta)
  {
    return $this->UnicaTupla;
  }

  /**
   * Mostra a mensagem de erro e interrompe a execução do script.
   * @param  string $msg
   * @param  bool   $getError
   */
  function Interrompe($appErrorMsg, $getError = FALSE)
  {
    $lastError = error_get_last();

    @session_start();
    $_SESSION['last_php_error_message'] = $lastError['message'];
    $_SESSION['last_php_error_line']    = $lastError['line'];
    $_SESSION['last_php_error_file']    = $lastError['file'];
    @session_write_close();

    $pgErrorMsg = $getError ? pg_result_error($this->bConsulta_ID) : '';
    NotificationMailer::unexpectedDataBaseError($appErrorMsg, $pgErrorMsg, $this->strStringSQL);

    die("<script>document.location.href = '/module/Error/unexpected';</script>");
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
      $this->Conecta();
      $dbConn = $this->bLink_ID;

      if (! is_array($params))
        $params = array($params);

      $this->bConsulta_ID = @pg_query_params($dbConn, $query, $params);
      $resultError = @pg_result_error($this->bConsulta_ID);
      $errorMsgs .= trim($resultError) != '' ? $resultError : @pg_last_error($this->bConsulta_ID);

      }
      catch(Exception $e) {
        $errorMsgs .= "Exception: " . $e->getMessage();
      }

      if ($this->bConsulta_ID == false || trim($errorMsgs) != '')
        throw new Exception("Erro ao preparar consulta ($query) no banco de dados: $errorMsgs");

      return $this->bConsulta_ID;
  }
}
