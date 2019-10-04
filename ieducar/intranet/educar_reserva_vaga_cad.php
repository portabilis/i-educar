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
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  ReservaVaga
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Reserva Vaga');
    $this->processoAp = '639';
  }
}

class indice extends clsCadastro
{
  /**
   * Referência a usuário da sessão
   * @var int
   */
  var $pessoa_logada = NULL;

  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_aluno;
  var $nm_aluno;
  var $nm_aluno_;

  var $ref_cod_instituicao;
  var $ref_cod_curso;

  var $passo;

  var $nm_aluno_ext;
  var $cpf_responsavel;
  var $tipo_aluno;

  function Inicializar()
  {
    $retorno = 'Novo';
    $this->ref_cod_serie  = $_GET['ref_cod_serie'];
    $this->ref_cod_escola = $_GET['ref_cod_escola'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(639, $this->pessoa_logada, 7,
      'educar_reserva_vaga_lst.php');

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

    $this->breadcrumb($nomeMenu . ' reserva de vaga', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

    return $retorno;
  }

  function Gerar()
  {
    if ($this->ref_cod_aluno) {
      $obj_reserva_vaga = new clsPmieducarReservaVaga();
      $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, NULL, NULL, NULL, NULL,
        $this->ref_cod_aluno, NULL, NULL, NULL, NULL, 1);

      // Verifica se o aluno já possui reserva alguma reserva ativa no sistema
      if (is_array($lst_reserva_vaga)) {
        echo "
          <script type='text/javascript'>
            alert('Aluno já possui reserva de vaga!\\nNão é possivel realizar a reserva.');
            window.location = 'educar_reserva_vaga_lst.php';
          </script>";
        die();
      }

      echo "
        <script type='text/javascript'>
          alert('A reserva do aluno permanecerá ativa por apenas 2 dias!');
        </script>";
    }

    $this->campoOculto('ref_cod_serie', $this->ref_cod_serie);
    $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

    $this->nm_aluno = $this->nm_aluno_;

    $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, FALSE,
      FALSE, FALSE, '', "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/><span style='padding-left:20px;'><input type='button' value='Aluno externo' onclick='showAlunoExt(true);' class='botaolistagem'></span>",
      '', '', TRUE);

    $this->campoOculto('nm_aluno_', $this->nm_aluno_);
    $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

    $this->campoOculto('tipo_aluno', 'i');

    $this->campoTexto('nm_aluno_ext', 'Nome aluno', $this->nm_aluno_ext, 50, 255, FALSE);
    $this->campoCpf('cpf_responsavel', 'CPF respons&aacute;vel',
      $this->cpf_responsavel, FALSE, "<span style='padding-left:20px;'><input type='button' value='Aluno interno' onclick='showAlunoExt(false);' class='botaolistagem'></span>");

    $this->campoOculto('passo', 1);

    $this->acao_enviar = 'acao2()';

    $this->url_cancelar = 'educar_reserva_vaga_lst.php';
    $this->nome_url_cancelar = 'Cancelar';
  }

  function Novo()
  {
    if ($this->passo == 2) {
      return true;
    }

    $obj_reserva_vaga = new clsPmieducarReservaVaga(NULL, $this->ref_cod_escola,
      $this->ref_cod_serie, NULL, $this->pessoa_logada, $this->ref_cod_aluno, NULL,
      NULL, 1, $this->nm_aluno_ext, idFederal2int($this->cpf_responsavel));

    $cadastrou = $obj_reserva_vaga->cadastra();

    if ($cadastrou) {
      $this->mensagem .= 'Reserva de Vaga efetuada com sucesso.<br>';
      $this->simpleRedirect('educar_reservada_vaga_det.php?cod_reserva_vaga=' . $cadastrou);
    }

    $this->mensagem = 'Reserva de Vaga n&atilde;o realizada.<br>';
    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>

<script type='text/javascript'>
function pesquisa_aluno() {
  pesquisa_valores_popless('educar_pesquisa_aluno.php')
}

function showAlunoExt(acao) {
  setVisibility('tr_nm_aluno_ext',acao);
  setVisibility('tr_cpf_responsavel',acao);
  setVisibility('tr_nm_aluno',!acao);

  document.getElementById('nm_aluno_ext').disabled = !acao;
  document.getElementById('cpf_responsavel').disabled = !acao;

  document.getElementById('tipo_aluno').value = (acao == true ? 'e' : 'i');
}

setVisibility('tr_nm_aluno_ext', false);
setVisibility('tr_cpf_responsavel', false);

function acao2() {
  if (document.getElementById('tipo_aluno').value == 'e') {
    if (document.getElementById('nm_aluno_ext').value == '') {
      alert('Preencha o campo \'Nome aluno\' Corretamente');
      return false;
    }

    if (! (/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/.test(document.formcadastro.cpf_responsavel.value))) {
      alert('Preencha o campo \'CPF responsável\' Corretamente');
      return false;
    }
    else {
      if(! DvCpfOk( document.formcadastro.cpf_responsavel) )
        return false;
    }

    document.getElementById('nm_aluno_').value = '';
    document.getElementById('ref_cod_aluno').value = '';

    document.formcadastro.submit();
  }
  else {
    document.getElementById('nm_aluno_ext').value = '';
    document.getElementById('cpf_responsavel').value =  '';
  }
  acao();
}
</script>
