<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

ini_set('max_execution_time', 1200);#seconds

require_once 'Core/Controller/Page/EditController.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once( "include/clsBanco.inc.php" );

class PromocaoAjaxController extends Core_Controller_Page_EditController
{
  protected $_titulo   = 'Promocao alunos';
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp = 644; #usando a mesma do boletim por turma...

  public function Gerar()
  {

    $this->msgs = array();

    if (! $this->getSession()->id_pessoa)#TODO verificar se usuário logado tem permissão para alterar / criar nota
      $this->appendMsg('not authorized');
    if (! isset($this->getRequest()->instituicao_id) || ! $this->getRequest()->instituicao_id)
      $this->appendMsg('invalid instituicao_id');
    #else if (! isset($this->getRequest()->escola_id) || ! $this->getRequest()->escola_id)
      #$this->appendMsg('ERROR:invalid escola_id');
    else if (! isset($this->getRequest()->ano_escolar) || ! $this->getRequest()->ano_escolar)
      $this->appendMsg('invalid ano_escolar');
    else if (! isset($this->getRequest()->action) || ! $this->getRequest()->action)
      $this->appendMsg('invalid action');
    
    else if ($this->getRequest()->action == 'savenotasfaltas')
    {

      $matriculas = $this->getMatriculasEmAndamento();

      foreach($matriculas as $matricula)
      {
        $this->ultima_matricula = $matricula['cod_matricula'];
        try
        {
          $service = new Avaliacao_Service_Boletim(array('matricula' => $matricula['cod_matricula'], 'usuario'   => $this->getSession()->id_pessoa));

          if (! $service)
            continue;

          $aprovado = $service->getSituacaoAluno()->aprovado;
          $situacao_antiga = $this->getSituacaoMatricula($matricula['cod_matricula']);
          
          try
          {
            $service->save();

            $situacao_atual = $this->getSituacaoMatricula($matricula['cod_matricula']);
            if ($situacao_antiga != $situacao_atual)
            {
              if ($situacao_atual == 1)
                $this->appendMsg("A matricula: {$matricula['cod_matricula']} foi aprovada - situacao antiga $situacao_antiga");
              else if ($situacao_atual == 2)
                $this->appendMsg("A matricula: {$matricula['cod_matricula']} foi reprovada - situacao antiga $situacao_antiga");
              else
                $this->appendMsg("A matricula: {$matricula['cod_matricula']} teve a situacao alterada de $situacao_antiga para $situacao_atual");
            }
            else
                $this->appendMsg("A matricula: {$matricula['cod_matricula']} continua com situacao $situacao_antiga");
          }
          catch (CoreExtservice_Exception $e) {
            $msg = "Erro ao promover matricula {$matricula['cod_matricula']}, detalhes: {$e->getMessage()}";
            $this->appendMsg($msg);
          }

        }
        catch (Exception $e) 
        {
          $this->appendMsg("Erro durante o processo de salvar notas e faltas da matricula : {$matricula['cod_matricula']} escola: {$matricula['ref_ref_cod_escola']} turma: {$matricula['ref_cod_turma']} serie: {$matricula['ref_ref_cod_serie']}, detalhes: {$e->getMessage()}");
        }
      }
    }
    else if ($this->getRequest()->action != 'get_numero_alunos_em_andamento')
      $this->appendMsg('invalid action');  

    $restante = $this->getNumAlunosEmAndamento();

    echo "<?xml version='1.0' encoding='ISO-8859-1' ?>
    <status>
    <msgs>{$this->msgsToXml()}</msgs>
    <restante num='$restante' />
    <ultima_matricula id='$this->ultima_matricula' />
    </status>";

  }

  function appendMsg($msg)
  {
    $this->msgs[] = $msg;
#    error_log($msg);
  }

  function msgsToXml($tag = 'msg')
  {
#    if (! count($this->msgs))
#      $this->appendMsg('Sem mensagens');
    $x = '';
    foreach($this->msgs as $m)
      $x .= "<$tag text='$m' />";
    return $x;
  }

  function getSituacaoMatricula($matricula_id)
  {
    $situacao_atual = new clsPmieducarMatricula($matricula_id);
    $situacao_atual = $situacao_atual->detalhe();
    return $situacao_atual['aprovado'];
  }

  function getNumAlunosEmAndamento()
  {
    $sql = $this->getSql($fields = 'count(*)', $limit_by = '', $order_by = '1', $ultima_matricula = 0);
    $num = $this->select($sql);
    return $num[0][0];
  }

  function getMatriculasEmAndamento($action = '')
  {
    $fields = 'm.cod_matricula, m.ref_ref_cod_escola, m.ref_ref_cod_serie, mt.ref_cod_turma';

    $limit_by = $this->getRequest()->limit_by;          
    if (! isset($limit_by) || ! $limit_by)
      $limit_by = 1;

    $limit_by = "limit $limit_by";

    $sql = $this->getSql($fields, $limit_by, $order_by = '1');
    return $this->select($sql);
  }

  function getSql($fields = '*', $limit_by = 'limit by 1', $order_by = '1', $ultima_matricula = -1)
  {

    if ($ultima_matricula == -1 && $this->getRequest()->ultima_matricula)
     $ultima_matricula = $this->getRequest()->ultima_matricula;
    else
      $ultima_matricula = 0;

    $s = "select $fields from pmieducar.matricula as m, pmieducar.matricula_turma as mt where m.ano = {$this->getRequest()->ano_escolar} and m.ativo = 1 and m.aprovado = 3 and m.cod_matricula > {$ultima_matricula} and mt.ref_cod_matricula = m.cod_matricula and mt.ativo = 1 order by 1 $limit_by";
    return $s;
}

function select($sql)
{

  $matriculas = array();
  try
  {  
    $db = new clsBanco();
    #$this->appendMsg($sql);
    $db->Consulta($sql);

  while ($db->ProximoRegistro())
    $matriculas[] = $db->Tupla();

/*    $matriculas = new clsPmieducarMatriculaTurma();
    $matriculas = $matriculas->lista(
      null,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      NULL,
      NULL,
      $this->getRequest()->escola_id,
      $this->getRequest()->instituicao_id,
      NULL,
      NULL,
      3,
      NULL,
      NULL,
      $this->getRequest()->ano_escolar,
      NULL,
      TRUE,
      NULL,
      NULL,
      TRUE,
      FALSE,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );

*/
  }
  catch (Exception $e) 
  {
    $this->appendMsg("Erro ao selecionar matriculas, sql: $sql");
  }

    return $matriculas;
  }

  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    header("Content-type: text/xml");
    $instance->Gerar();
  }
}
?>
