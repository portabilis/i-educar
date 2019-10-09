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
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Servidores - Servidor Substituição');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_servidor_alocacao;
  var $ref_ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_escola;
  var $ref_cod_servidor;
  var $dia_semana;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  var $todos;

  var $alocacao_array = array();
  var $professor;

  function Inicializar()
  {
    $retorno = 'Novo';
    $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 3,
      'educar_servidor_lst.php');

    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
      $retorno = 'Novo';

      $obj_servidor = new clsPmieducarServidor($this->ref_cod_servidor,
        NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_ref_cod_instituicao);
      $det_servidor = $obj_servidor->detalhe();

      // Nenhum servidor com o código de servidor e instituição
      if (!$det_servidor) {
          $this->simpleRedirect('educar_servidor_lst.php');
      }

      $this->professor = $obj_servidor->isProfessor() == TRUE ? 'true' : 'false';

      $obj = new clsPmieducarServidorAlocacao();
      $lista  = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL,
        NULL, NULL, $this->ref_cod_servidor, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, 1);

      if ($lista) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($lista as $campo => $val){
          $temp = array();
          $temp['carga_horaria']  = $val['carga_horaria'];
          $temp['periodo']        = $val['periodo'];
          $temp['ref_cod_escola'] = $val['ref_cod_escola'];

          $this->alocacao_array[] = $temp;
        }

        $retorno = 'Novo';
      }

      $this->carga_horaria = $det_servidor['carga_horaria'];
    }
    else {
        $this->simpleRedirect('educar_servidor_lst.php');
    }

    $this->url_cancelar = sprintf(
      'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);
    $this->nome_url_cancelar = 'Cancelar';

    $this->breadcrumb('Substituir servidor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);

    return $retorno;
  }

  function Gerar()
  {
    $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
    $inst_det = $obj_inst->detalhe();

    $this->campoRotulo('nm_instituicao', 'Instituição', $inst_det['nm_instituicao']);
    $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);

    $opcoes = array('' => 'Selecione');
      $objTemp = new clsPmieducarServidor($this->ref_cod_servidor);
      $det = $objTemp->detalhe();
      if ($det) {
        foreach ($det as $key => $registro) {
          $this->$key =  $registro;
        }
      }

      if ($this->ref_cod_servidor) {
        $objPessoa     = new clsPessoa_($this->ref_cod_servidor);
        $detalhePessoa = $objPessoa->detalhe();
        $nm_servidor = $detalhePessoa['nome'];
      }

    $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
    $this->campoOculto('professor',$this->professor);

    $url = sprintf(
      'educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_todos_&campo2=ref_cod_servidor_todos&ref_cod_instituicao=%d&ref_cod_servidor=%d&tipo=livre&professor=%d',
      $this->ref_ref_cod_instituicao, $this->ref_cod_servidor, $this->professor
    );

    $img = sprintf(
      '<img border="0" onclick="pesquisa_valores_popless(\'%s\', \'nome\')" src="imagens/lupa.png">',
      $url
    );

    $this->campoTextoInv('ref_cod_servidor_todos_', 'Substituir por:', '',
      30, 255, TRUE, FALSE, FALSE, '', $img,
      '', '', '');
    $this->campoOculto('ref_cod_servidor_todos', '');

    $this->campoOculto('alocacao_array', serialize($this->alocacao_array));
    $this->acao_enviar = 'acao2()';
  }

  function Novo()
  {
    $professor  = isset($_POST['professor']) ? strtolower($_POST['professor']) : 'FALSE';
    $substituto = isset($_POST['ref_cod_servidor_todos']) ? $_POST['ref_cod_servidor_todos'] : NULL;

    $permissoes = new clsPermissoes();
    $permissoes->permissao_cadastra(635, $this->pessoa_logada, 3,
      'educar_servidor_alocacao_lst.php');

    $this->alocacao_array = array();
    if ($_POST['alocacao_array']) {
      $this->alocacao_array = unserialize(urldecode($_POST['alocacao_array']));
    }

    if ($this->alocacao_array) {
      // Substitui todas as alocações
      foreach ($this->alocacao_array as $key => $alocacao) {
        $obj = new clsPmieducarServidorAlocacao(NULL, $this->ref_ref_cod_instituicao,
          $this->pessoa_logada, $this->pessoa_logada, $alocacao['ref_cod_escola'],
          $this->ref_cod_servidor, NULL, NULL, NULL, $alocacao['carga_horaria'],
          $alocacao['periodo']);

        $return = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL, NULL,
          $alocacao['ref_cod_escola'], $this->ref_cod_servidor, NULL, NULL, NULL,
          NULL, 1, $alocacao['carga_horaria']);

        if (FALSE !== $return) {
          $substituiu = $obj->substituir_servidor($substituto);
          if (!$substituiu) {
            $this->mensagem = "Substituicao n&atilde;o realizado.<br>";

            return FALSE;
          }
        }
      }

      // Substituição do servidor no quadro de horários (caso seja professor)
      if ('true' == $professor) {
        $quadroHorarios = new clsPmieducarQuadroHorarioHorarios(NULL, NULL, NULL,
          NULL, NULL, NULL, $this->ref_ref_cod_instituicao, NULL, $this->ref_cod_servidor,
          NULL, NULL, NULL, NULL, 1, NULL, NULL);
        $quadroHorarios->substituir_servidor($substituto);
      }
    }

    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
    $destination = 'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s';
    $destination = sprintf($destination, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);
    $this->simpleRedirect($destination);
  }

  function Editar()
  {
    return FALSE;
  }

  function Excluir()
  {
    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
function acao2()
{
  if (document.getElementById('ref_cod_servidor_todos').value == ''){
    alert("Selecione um servidor substituto!");
    return false;
  }

  acao();
}
</script>
