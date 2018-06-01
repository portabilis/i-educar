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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsModulesFichaMedicaAluno class.
 * 
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     09/2013
 * @version   @@package_version@@
 */
class clsModulesFichaMedicaAluno
{
  var $ref_cod_aluno;
  var $altura;
  var $peso;
  var $grupo_sanguineo;
  var $fator_rh;
  var $alergia_medicamento;
  var $desc_alergia_medicamento;
  var $alergia_alimento;
  var $desc_alergia_alimento;
  var $doenca_congenita;
  var $desc_doenca_congenita;
  var $fumante;
  var $doenca_caxumba;
  var $doenca_sarampo;
  var $doenca_rubeola;
  var $doenca_catapora;
  var $doenca_escarlatina;
  var $doenca_coqueluche;
  var $doenca_outras;
  var $epiletico;
  var $epiletico_tratamento;
  var $hemofilico;
  var $hipertenso;
  var $asmatico;
  var $diabetico;
  var $insulina;
  var $tratamento_medico;
  var $desc_tratamento_medico;
  var $medicacao_especifica;
  var $desc_medicacao_especifica;
  var $acomp_medico_psicologico;
  var $desc_acomp_medico_psicologico;
  var $restricao_atividade_fisica;
  var $desc_restricao_atividade_fisica;
  var $fratura_trauma;
  var $desc_fratura_trauma;
  var $plano_saude;
  var $desc_plano_saude;
  var $hospital_clinica;
  var $hospital_clinica_endereco;
  var $hospital_clinica_telefone;
  var $responsavel;
  var $responsavel_parentesco;
  var $responsavel_parentesco_telefone;
  var $responsavel_parentesco_celular;

  /**
   * @var int
   * Armazena o total de resultados obtidos na última chamada ao método lista().
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por vírgula, com os campos que devem ser selecionados na
   * próxima chamado ao método lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para
   * seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padrão de ordenação no método lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   */
  function __construct( $ref_cod_aluno = NULL , $altura = NULL , $peso = NULL , $grupo_sanguineo = NULL ,
     $fator_rh = NULL , $alergia_medicamento = NULL , $desc_alergia_medicamento = NULL ,
     $alergia_alimento = NULL , $desc_alergia_alimento = NULL ,  $doenca_congenita = NULL ,
     $desc_doenca_congenita = NULL , $fumante = NULL , $doenca_caxumba = NULL , $doenca_sarampo = NULL ,
     $doenca_rubeola = NULL , $doenca_catapora = NULL , $doenca_escarlatina = NULL , $doenca_coqueluche = NULL ,
     $doenca_outras = NULL ,  $epiletico = NULL , $epiletico_tratamento = NULL , $hemofilico = NULL ,
     $hipertenso = NULL , $asmatico = NULL , $diabetico = NULL , $insulina = NULL ,
     $tratamento_medico = NULL , $desc_tratamento_medico = NULL , $medicacao_especifica = NULL ,
     $desc_medicacao_especifica = NULL ,  $acomp_medico_psicologico = NULL , $desc_acomp_medico_psicologico = NULL ,
     $restricao_atividade_fisica = NULL , $desc_restricao_atividade_fisica = NULL , $fratura_trauma = NULL ,
     $desc_fratura_trauma = NULL , $plano_saude = NULL , $desc_plano_saude = NULL , $hospital_clinica = NULL ,
     $hospital_clinica_endereco = NULL , $hospital_clinica_telefone = NULL , $responsavel = NULL ,
     $responsavel_parentesco = NULL , $responsavel_parentesco_telefone = NULL , $responsavel_parentesco_celular = NULL)     
  {
    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}ficha_medica_aluno";

    $this->_campos_lista = $this->_todos_campos = " ref_cod_aluno, altura, peso, grupo_sanguineo,
        fator_rh, alergia_medicamento, desc_alergia_medicamento,alergia_alimento, desc_alergia_alimento,
        doenca_congenita,desc_doenca_congenita, fumante, doenca_caxumba, doenca_sarampo,doenca_rubeola, 
        doenca_catapora, doenca_escarlatina, doenca_coqueluche,doenca_outras,  epiletico, 
        epiletico_tratamento, hemofilico,hipertenso, asmatico, diabetico, insulina,tratamento_medico, 
        desc_tratamento_medico, medicacao_especifica,desc_medicacao_especifica, acomp_medico_psicologico, 
        desc_acomp_medico_psicologico,restricao_atividade_fisica, desc_restricao_atividade_fisica, 
        fratura_trauma,desc_fratura_trauma, plano_saude, desc_plano_saude, hospital_clinica,
        hospital_clinica_endereco, hospital_clinica_telefone, responsavel,responsavel_parentesco, 
        responsavel_parentesco_telefone, responsavel_parentesco_celular"; 

    if (is_numeric($ref_cod_aluno)) {
      $this->ref_cod_aluno = $ref_cod_aluno;
    }

    if (is_string($altura)) {
      $this->altura = $altura;
    }

   if (is_string($peso)) {
      $this->peso = $peso;
    }

   if (is_string($grupo_sanguineo)) {
      $this->grupo_sanguineo = $grupo_sanguineo;
    }

   if (is_string($fator_rh)) {
      $this->fator_rh = $fator_rh;
    }

   if (is_string($alergia_medicamento)) {
      $this->alergia_medicamento = $alergia_medicamento;
    }

   if (is_string($desc_alergia_medicamento)) {
      $this->desc_alergia_medicamento = $desc_alergia_medicamento;
    }

   if (is_string($alergia_alimento)) {
      $this->alergia_alimento = $alergia_alimento;
    }

   if (is_string($desc_alergia_alimento)) {
      $this->desc_alergia_alimento = $desc_alergia_alimento;
    }

   if (is_string($doenca_congenita)) {
      $this->doenca_congenita = $doenca_congenita;
    }

   if (is_string($desc_doenca_congenita)) {
      $this->desc_doenca_congenita = $desc_doenca_congenita;
    }

   if (is_string($fumante)) {
      $this->fumante = $fumante;
    }

   if (is_string($doenca_caxumba)) {
      $this->doenca_caxumba = $doenca_caxumba;
    }

   if (is_string($doenca_sarampo)) {
      $this->doenca_sarampo = $doenca_sarampo;
    }

   if (is_string($doenca_rubeola)) {
      $this->doenca_rubeola = $doenca_rubeola;
    }

   if (is_string($doenca_catapora)) {
      $this->doenca_catapora = $doenca_catapora;
    }

   if (is_string($doenca_escarlatina)) {
      $this->doenca_escarlatina = $doenca_escarlatina;
    }

   if (is_string($doenca_coqueluche)) {
      $this->doenca_coqueluche = $doenca_coqueluche;
    }

   if (is_string($doenca_outras)) {
      $this->doenca_outras = $doenca_outras;
    }

   if (is_string($epiletico)) {
      $this->epiletico = $epiletico;
    }

   if (is_string($epiletico_tratamento)) {
      $this->epiletico_tratamento = $epiletico_tratamento;
    }

   if (is_string($hemofilico)) {
      $this->hemofilico = $hemofilico;
    }

   if (is_string($hipertenso)) {
      $this->hipertenso = $hipertenso;
    }

   if (is_string($asmatico)) {
      $this->asmatico = $asmatico;
    }

   if (is_string($diabetico)) {
      $this->diabetico = $diabetico;
    }

   if (is_string($insulina)) {
      $this->insulina = $insulina;
    }

   if (is_string($tratamento_medico)) {
      $this->tratamento_medico = $tratamento_medico;
    }

   if (is_string($desc_tratamento_medico)) {
      $this->desc_tratamento_medico = $desc_tratamento_medico;
    }

   if (is_string($medicacao_especifica)) {
      $this->medicacao_especifica = $medicacao_especifica;
    }

  if (is_string($desc_medicacao_especifica)) {
      $this->desc_medicacao_especifica = $desc_medicacao_especifica;
    }

  if (is_string($acomp_medico_psicologico)) {
      $this->acomp_medico_psicologico = $acomp_medico_psicologico;
    }

  if (is_string($desc_acomp_medico_psicologico)) {
      $this->desc_acomp_medico_psicologico = $desc_acomp_medico_psicologico;
    }

  if (is_string($restricao_atividade_fisica)) {
      $this->restricao_atividade_fisica = $restricao_atividade_fisica;
    }

  if (is_string($desc_restricao_atividade_fisica)) {
      $this->desc_restricao_atividade_fisica = $desc_restricao_atividade_fisica;
    }

  if (is_string($fratura_trauma)) {
      $this->fratura_trauma = $fratura_trauma;
    }

  if (is_string($desc_fratura_trauma)) {
      $this->desc_fratura_trauma = $desc_fratura_trauma;
    }

  if (is_string($plano_saude)) {
      $this->plano_saude = $plano_saude;
    }

  if (is_string($desc_plano_saude)) {
      $this->desc_plano_saude = $desc_plano_saude;
    }

  if (is_string($hospital_clinica)) {
      $this->hospital_clinica = $hospital_clinica;
    }

  if (is_string($hospital_clinica_endereco)) {
      $this->hospital_clinica_endereco = $hospital_clinica_endereco;
    }

  if (is_string($hospital_clinica_telefone)) {
      $this->hospital_clinica_telefone = $hospital_clinica_telefone;
    }

  if (is_string($responsavel)) {
      $this->responsavel = $responsavel;
    }

  if (is_string($responsavel_parentesco)) {
      $this->responsavel_parentesco = $responsavel_parentesco;
    }

  if (is_string($responsavel_parentesco_telefone)) {
      $this->responsavel_parentesco_telefone = $responsavel_parentesco_telefone;
    }

  if (is_string($responsavel_parentesco_celular)) {
      $this->responsavel_parentesco_celular = $responsavel_parentesco_celular;
    }

  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_aluno))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';    

      $campos .= "{$gruda}ref_cod_aluno";
      $valores .= "{$gruda}{$this->ref_cod_aluno}";
      $gruda = ", ";

      $campos .= "{$gruda}altura";
      $valores .= "{$gruda}'{$this->altura}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}peso";
      $valores .= "{$gruda}'{$this->peso}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}grupo_sanguineo";
      $valores .= "{$gruda}'{$this->grupo_sanguineo}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}fator_rh";
      $valores .= "{$gruda}'{$this->fator_rh}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}alergia_medicamento";
      $valores .= "{$gruda}'{$this->alergia_medicamento}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_alergia_medicamento";
      $valores .= "{$gruda}'{$this->desc_alergia_medicamento}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}alergia_alimento";
      $valores .= "{$gruda}'{$this->alergia_alimento}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_alergia_alimento";
      $valores .= "{$gruda}'{$this->desc_alergia_alimento}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_congenita";
      $valores .= "{$gruda}'{$this->doenca_congenita}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_doenca_congenita";
      $valores .= "{$gruda}'{$this->desc_doenca_congenita}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}fumante";
      $valores .= "{$gruda}'{$this->fumante}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_caxumba";
      $valores .= "{$gruda}'{$this->doenca_caxumba}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_sarampo";
      $valores .= "{$gruda}'{$this->doenca_sarampo}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_rubeola";
      $valores .= "{$gruda}'{$this->doenca_rubeola}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_catapora";
      $valores .= "{$gruda}'{$this->doenca_catapora}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_escarlatina";
      $valores .= "{$gruda}'{$this->doenca_escarlatina}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_coqueluche";
      $valores .= "{$gruda}'{$this->doenca_coqueluche}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}doenca_outras";
      $valores .= "{$gruda}'{$this->doenca_outras}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}epiletico";
      $valores .= "{$gruda}'{$this->epiletico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}epiletico_tratamento";
      $valores .= "{$gruda}'{$this->epiletico_tratamento}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}hemofilico";
      $valores .= "{$gruda}'{$this->hemofilico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}hipertenso";
      $valores .= "{$gruda}'{$this->hipertenso}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}asmatico";
      $valores .= "{$gruda}'{$this->asmatico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}diabetico";
      $valores .= "{$gruda}'{$this->diabetico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}insulina";
      $valores .= "{$gruda}'{$this->insulina}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}tratamento_medico";
      $valores .= "{$gruda}'{$this->tratamento_medico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_tratamento_medico";
      $valores .= "{$gruda}'{$this->desc_tratamento_medico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}medicacao_especifica";
      $valores .= "{$gruda}'{$this->medicacao_especifica}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_medicacao_especifica";
      $valores .= "{$gruda}'{$this->desc_medicacao_especifica}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}acomp_medico_psicologico";
      $valores .= "{$gruda}'{$this->acomp_medico_psicologico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_acomp_medico_psicologico";
      $valores .= "{$gruda}'{$this->desc_acomp_medico_psicologico}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}restricao_atividade_fisica";
      $valores .= "{$gruda}'{$this->restricao_atividade_fisica}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_restricao_atividade_fisica";
      $valores .= "{$gruda}'{$this->desc_restricao_atividade_fisica}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}fratura_trauma";
      $valores .= "{$gruda}'{$this->fratura_trauma}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_fratura_trauma";
      $valores .= "{$gruda}'{$this->desc_fratura_trauma}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}plano_saude";
      $valores .= "{$gruda}'{$this->plano_saude}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}desc_plano_saude";
      $valores .= "{$gruda}'{$this->desc_plano_saude}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}hospital_clinica";
      $valores .= "{$gruda}'{$this->hospital_clinica}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}hospital_clinica_endereco";
      $valores .= "{$gruda}'{$this->hospital_clinica_endereco}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}hospital_clinica_telefone";
      $valores .= "{$gruda}'{$this->hospital_clinica_telefone}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}responsavel";
      $valores .= "{$gruda}'{$this->responsavel}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}responsavel_parentesco";
      $valores .= "{$gruda}'{$this->responsavel_parentesco}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}responsavel_parentesco_celular";
      $valores .= "{$gruda}'{$this->responsavel_parentesco_celular}'";
      $gruda = ", ";
      
      $campos .= "{$gruda}responsavel_parentesco_telefone";
      $valores .= "{$gruda}'{$this->responsavel_parentesco_telefone}'";
      $gruda = ", ";
      
      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $this->ref_cod_aluno;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->ref_cod_aluno)) {
      $db  = new clsBanco();
      $set = '';

      $set .= "altura = '{$this->altura}'";
  
      $set .= ",peso = '{$this->peso}'";
      
      $set .= ",grupo_sanguineo = '{$this->grupo_sanguineo}'";

      $set .= ",fator_rh = '{$this->fator_rh}'";
  
      $set .= ",alergia_medicamento = '{$this->alergia_medicamento}'";
  
      $set .= ",desc_alergia_medicamento = '{$this->desc_alergia_medicamento}'";
  
      $set .= ",alergia_alimento = '{$this->alergia_alimento}'";
  
      $set .= ",desc_alergia_alimento = '{$this->desc_alergia_alimento}'";
  
      $set .= ",doenca_congenita = '{$this->doenca_congenita}'";
  
      $set .= ",desc_doenca_congenita = '{$this->desc_doenca_congenita}'";
  
      $set .= ",fumante = '{$this->fumante}'";
  
      $set .= ",doenca_caxumba = '{$this->doenca_caxumba}'";
  
      $set .= ",doenca_sarampo = '{$this->doenca_sarampo}'";
  
      $set .= ",doenca_rubeola = '{$this->doenca_rubeola}'";
  
      $set .= ",doenca_catapora = '{$this->doenca_catapora}'";
  
      $set .= ",doenca_escarlatina = '{$this->doenca_escarlatina}'";
  
      $set .= ",doenca_coqueluche = '{$this->doenca_coqueluche}'";
  
      $set .= ",doenca_outras = '{$this->doenca_outras}'";
  
      $set .= ",epiletico = '{$this->epiletico}'";

      $set .= ",epiletico_tratamento = '{$this->epiletico_tratamento}'";
  
      $set .= ",hemofilico = '{$this->hemofilico}'";
  
      $set .= ",hipertenso = '{$this->hipertenso}'";
  
      $set .= ",asmatico = '{$this->asmatico}'";
  
      $set .= ",diabetico = '{$this->diabetico}'";
  
      $set .= ",insulina = '{$this->insulina}'";
  
      $set .= ",tratamento_medico = '{$this->tratamento_medico}'";
  
      $set .= ",desc_tratamento_medico = '{$this->desc_tratamento_medico}'";
  
      $set .= ",medicacao_especifica = '{$this->medicacao_especifica}'";
  
      $set .= ",desc_medicacao_especifica = '{$this->desc_medicacao_especifica}'";
  
      $set .= ",acomp_medico_psicologico = '{$this->acomp_medico_psicologico}'";
  
      $set .= ",desc_acomp_medico_psicologico = '{$this->desc_acomp_medico_psicologico}'";
  
      $set .= ",restricao_atividade_fisica = '{$this->restricao_atividade_fisica}'";
  
      $set .= ",desc_restricao_atividade_fisica = '{$this->desc_restricao_atividade_fisica}'";
  
      $set .= ",fratura_trauma = '{$this->fratura_trauma}'";
  
      $set .= ",desc_fratura_trauma = '{$this->desc_fratura_trauma}'";
  
      $set .= ",plano_saude = '{$this->plano_saude}'";
  
      $set .= ",desc_plano_saude = '{$this->desc_plano_saude}'";
  
      $set .= ",hospital_clinica = '{$this->hospital_clinica}'";
  
      $set .= ",hospital_clinica_endereco = '{$this->hospital_clinica_endereco}'";
  
      $set .= ",hospital_clinica_telefone = '{$this->hospital_clinica_telefone}'";
  
      $set .= ",responsavel = '{$this->responsavel}'";
  
      $set .= ",responsavel_parentesco = '{$this->responsavel_parentesco}'";
  
      $set .= ",responsavel_parentesco_telefone = '{$this->responsavel_parentesco_telefone}'";
  
      $set .= ",responsavel_parentesco_celular = '{$this->responsavel_parentesco_celular}'";

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista()
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";
    /*
    $whereAnd = " WHERE ";


    if (is_string($altura)) {
      $filtros .= "{$whereAnd} (LOWER(altura)) LIKE (LOWER('%{$altura}%'))";
      $whereAnd = " AND ";
    }*/

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista))+2;
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $tupla["_total"] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }
    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->ref_cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->ref_cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->ref_cod_aluno)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'";
      $db = new clsBanco();
      $db->Consulta($sql);
      return true;
    }

    return FALSE;
  }

  /**
   * Define quais campos da tabela serão selecionados no método Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o método Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o método Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query responsável pelo limite de
   * registros retornados/afetados.
   *
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado como ordenação no método Lista().
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query responsável pela Ordenação dos
   * registros.
   *
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }
}